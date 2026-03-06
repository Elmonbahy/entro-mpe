<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangRetur;
use App\Models\Jual;
use App\Models\Salesman;
use App\Models\Pelanggan;
use App\Constants\TipePenjualan;
use App\Models\JualDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JualController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.jual.supervisor.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.jual.supervisor.create', [
      'pelanggans' => Pelanggan::select('id', 'nama')->get(),
      'tipe_penjualans' => TipePenjualan::all(),
      'salesmans' => Salesman::select('id', 'nama')->get(),
    ]);
  }

  public function edit(int $id)
  {
    $jual = Jual::findOrFail($id);

    \Gate::authorize('update', $jual);

    return view('pages.jual.supervisor.edit', [
      'jual' => $jual,
      'tipe_penjualans' => TipePenjualan::all(),
      'salesmans' => Salesman::select('id', 'nama')->get(),
      'pelanggans' => Pelanggan::select('id', 'nama')->get()
    ]);
  }

  private function validation(Request $request, $id = null)
  {
    $request->validate([
      'pelanggan' => $id ? 'nullable' : 'required|exists:pelanggans,id',
      'salesman' => 'required|exists:salesmen,id',
      'tipe_penjualan' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, TipePenjualan::all())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
      'nomor_pemesanan' => [
        'nullable',
        'string',
        'max:255',
        $id
        ? Rule::unique('juals')->ignore($id) // For updates
        : 'unique:juals,nomor_pemesanan',       // For creation
      ],
      'diskon_faktur' => 'nullable|numeric|min:0|max:100',
      'tgl_faktur' => 'required|date',
      'kredit' => 'nullable|integer|min:0',
      'ppn' => 'required|in:0,11,12',
      'ongkir' => 'nullable|numeric|min:0',
      'keterangan_faktur' => 'nullable|string|max:255',
    ]);
  }

  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);
    $jual = Jual::where('id', $id)->with('jualDetails')->firstOrFail();

    \Gate::authorize('update', $jual);

    try {
      \DB::beginTransaction();

      $jual->update([
        'pelanggan_id' => $request->pelanggan,
        'tipe_penjualan' => $request->tipe_penjualan,
        'salesman_id' => $request->salesman,
        'nomor_pemesanan' => $request->nomor_pemesanan,
        'diskon_faktur' => $request->diskon_faktur,
        'tgl_faktur' => $request->tgl_faktur,
        'kredit' => $request->kredit,
        'ppn' => $request->ppn,
        'keterangan_faktur' => $request->keterangan_faktur,
        'ongkir' => $request->ongkir,
      ]);

      \DB::commit();

      return redirect()->route('supervisor.jual.index')->with('success', 'Berhasil mengubah data jual.');
    } catch (\Exception $e) {
      \DB::rollBack();
      return redirect()->back()->with('error', 'Gagal!' . $e->getMessage());
    }
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $this->validation($request);

    try {
      $jual = Jual::create([
        'nomor_faktur' => Jual::generateNomorFaktur($request->tgl_faktur),
        'nomor_pemesanan' => $request->nomor_pemesanan,
        'tipe_penjualan' => $request->tipe_penjualan,
        'tgl_faktur' => $request->tgl_faktur,
        'diskon_faktur' => $request->input('diskon_faktur', 0),
        'kredit' => $request->input('kredit', 0),
        'ppn' => $request->ppn,
        'keterangan_faktur' => $request->keterangan_faktur,
        'pelanggan_id' => $request->pelanggan,
        'salesman_id' => $request->salesman,
        'ongkir' => $request->ongkir,
      ]);

      return redirect()
        ->route('supervisor.jual.add-item', ['id' => $jual->id])
        ->with('success', 'Berhasil menambahkan data jual.');
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      return back()
        ->withInput()
        ->with('error', "Gagal! $msg");
    }
  }

  public function show(int $id)
  {
    $jual = Jual::with([
      'jualDetails',
      'suratJalanDetails.suratJalan.kendaraan',
    ])->findOrFail($id);

    $jual_details = JualDetail::where('jual_id', $id)->get();

    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [JualDetail::class],
      function ($q) use ($id) {
        $q->where('jual_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id,kode',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'jual_id', 'batch', 'tgl_expired', 'barang_id');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.jual.supervisor.show', [
      'jual' => $jual,
      'jual_details' => $jual_details,
      'returs' => $returs,
    ]);
  }

  public function addItem(int $id)
  {
    $jual = Jual::findOrFail($id);

    \Gate::authorize('update', $jual);

    $tipe_harga = Pelanggan::where('id', $jual->pelanggan_id)->value('tipe_harga');
    return view('pages.jual.supervisor.add-item', compact('jual', 'tipe_harga'));
  }


  public function exportFakur(int $id)
  {
    $jual = Jual::findOrFail($id);
    $jual_detail = JualDetail::where('jual_id', $id)
      ->where('jumlah_barang_dipesan', '>', 0)
      ->with([
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.kode,barangs.brand_id,barangs.nie',
        'barang.brand:id,nama'
      ])->get();

    $sum_sub_nilai = $jual_detail->sum('sub_nilai');
    $sum_harga_diskon1 = $jual_detail->sum('harga_diskon1');
    $sum_nilai_diskon1 = $jual_detail->sum('nilai_diskon1');
    $sum_harga_diskon2 = $jual_detail->sum('harga_diskon2');
    $sum_total = $jual_detail->sum('total');
    $sum_harga_ppn = $jual_detail->sum('harga_ppn');
    $sum_total_tagihan = $jual_detail->sum('total_tagihan');

    $pdf = Pdf::loadView('pages.faktur.supervisor.pdf', [
      'jual' => $jual,
      'jual_detail' => $jual_detail,
      'sum_sub_nilai' => $sum_sub_nilai,
      'sum_harga_diskon1' => $sum_harga_diskon1,
      'sum_nilai_diskon1' => $sum_nilai_diskon1,
      'sum_harga_diskon2' => $sum_harga_diskon2,
      'sum_total' => $sum_total,
      'sum_harga_ppn' => $sum_harga_ppn,
      'sum_total_tagihan' => $sum_total_tagihan,
    ]);

    $pdf->setPaper('legal');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'FAKTUR_' . $jual->nomor_faktur . '_' . $jual->pelanggan->nama;
    $filename = str_replace(' ', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }
  public function exportSpkb(int $id)
  {
    $jual = Jual::findOrFail($id);
    $jual_detail = JualDetail::where('jual_id', $id)
      ->with([
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.kode,barangs.brand_id,barangs.nie',
        'barang.brand:id,nama'
      ])->get();

    $pdf = Pdf::loadView('pages.spkb.supervisor.pdf', [
      'jual' => $jual,
      'jual_detail' => $jual_detail,
      'waktu_cetak' => now()->setTimezone('Asia/Makassar')->format('d/m/Y H:i')
    ]);

    $pdf->setPaper('legal');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'SPKB_' . $jual->nomor_faktur . '_' . $jual->pelanggan->nama;
    $filename = str_replace(' ', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }

}
