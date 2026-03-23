<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use App\Constants\StafLogistik;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Exports\Gudang\SuratJalanExport;
use Maatwebsite\Excel\Facades\Excel;

class SuratJalanController extends Controller
{
  public function index()
  {
    return view('pages.surat-jalan.gudang.index');
  }

  public function create()
  {
    $pelanggans = Pelanggan::select('nama', 'id')->get();
    $kendaraan = Kendaraan::select('nama', 'id')->get();
    $staf_logistiks = StafLogistik::all();

    return view('pages.surat-jalan.gudang.create', [
      'pelanggans' => $pelanggans,
      'kendaraans' => $kendaraan,
      'staf_logistiks' => $staf_logistiks
    ]);
  }

  public function edit(int $id)
  {
    $surat_jalan = SuratJalan::findOrFail($id);
    $kendaraan = Kendaraan::select('nama', 'id')->get();
    $staf_logistiks = StafLogistik::all();

    return view('pages.surat-jalan.gudang.edit', [
      'surat_jalan' => $surat_jalan,
      'kendaraans' => $kendaraan,
      'staf_logistiks' => $staf_logistiks
    ]);
  }

  private function validation(Request $request, $id = null)
  {
    $request->validate([
      'pelanggan' => $id ? 'nullable' : 'required|exists:pelanggans,id',
      'kendaraan' => $id ? 'nullable' : 'required|exists:kendaraans,id',
      'tgl_surat_jalan' => 'required|date',
      'tgl_kembali_surat_jalan' => [
        'nullable',
        'date',
        function ($attribute, $value, $fail) use ($request) {
          if ($value && $request->tgl_surat_jalan && $value < $request->tgl_surat_jalan) {
            $fail('Tanggal kembali harus sama dengan atau setelah tanggal surat jalan.');
          }
        }
      ],
      'koli' => 'required|string|max:255',
      'staf_logistik' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, StafLogistik::all())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
      'keterangan' => 'nullable|string|max:255'
    ]);
  }

  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);
    $surat_jalan = SuratJalan::where('id', $id);

    try {
      \DB::beginTransaction();

      $surat_jalan->update([
        'kendaraan_id' => $request->kendaraan,
        'tgl_surat_jalan' => $request->tgl_surat_jalan,
        'tgl_kembali_surat_jalan' => $request->tgl_kembali_surat_jalan,
        'koli' => $request->koli,
        'staf_logistik' => $request->staf_logistik,
        'keterangan' => $request->keterangan,
      ]);

      \DB::commit();

      return redirect()->route('gudang.surat-jalan.index')->with('success', 'Berhasil mengubah data surat jalan.');
    } catch (\Exception $e) {
      \DB::rollBack();
      return redirect()->back()->with('error', 'Gagal!' . $e->getMessage());
    }
  }

  public function store(Request $request)
  {
    $this->validation($request);

    try {
      $surat_jalan = SuratJalan::create([
        'nomor_surat_jalan' => SuratJalan::generateNomorSuratJalan($request->tgl_surat_jalan),
        'pelanggan_id' => $request->pelanggan,
        'kendaraan_id' => $request->kendaraan,
        'tgl_surat_jalan' => $request->tgl_surat_jalan,
        'koli' => $request->koli,
        'staf_logistik' => $request->staf_logistik,
        'keterangan' => $request->keterangan,
      ]);

      return redirect()->route('gudang.surat-jalan.add-item', ['id' => $surat_jalan->id])
        ->with('success', 'Berhasil menambahkan data surat jalan.');
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      return back()
        ->withInput()
        ->with('error', "Gagal! $msg");
    }
  }
  public function show(int $id)
  {
    $surat_jalan = SuratJalan::findOrFail($id);
    $surat_jalan_details = SuratJalanDetail::where('surat_jalan_id', $surat_jalan->id)
      ->with([
        'jual:juals.id,juals.nomor_faktur',
        'jualDetail:jual_details.id,jual_details.jumlah_barang_keluar',
        'barang:barangs.id,barangs.nama,barangs.satuan',
      ])
      ->get();

    return view('pages.surat-jalan.gudang.show', [
      'surat_jalan' => $surat_jalan,
      'surat_jalan_details' => $surat_jalan_details
    ]);
  }

  public function addItem(int $id)
  {
    $surat_jalan = SuratJalan::with(['pelanggan', 'kendaraan'])->findOrFail($id);

    return view('pages.surat-jalan.gudang.add-item', [
      'surat_jalan' => $surat_jalan,
    ]);
  }


  public function exportPdf(int $id)
  {
    $surat_jalan = SuratJalan::findOrFail($id);
    $surat_jalan_details = SuratJalanDetail::where('surat_jalan_id', $id)
      ->with([
        'jual:juals.nomor_faktur,juals.id',
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.nie',
        'jualDetail:id,batch,tgl_expired,jumlah_barang_keluar'
      ])->get()
      ->filter(function ($item) {
        return optional($item->jualDetail)->jumlah_barang_keluar > 0;
      });
    $fakturs = $surat_jalan_details->pluck('jual.nomor_faktur')->unique()->join(', ');

    $pdf = Pdf::loadView('pages.surat-jalan.gudang.pdf', [
      'surat_jalan' => $surat_jalan,
      'fakturs' => $fakturs,
      'surat_jalan_details' => $surat_jalan_details
    ]);
    $pdf->setPaper('legal');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);
    $pdf->setOption('debugLayoutBlocks', true);

    $filename = 'SURAT_JALAN_' . $surat_jalan->nomor_surat_jalan . '_' . $surat_jalan->pelanggan->nama;
    $filename = str_replace([' ', '/', '\\'], '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }

  public function exportExcel()
  {
    return Excel::download(new SuratJalanExport, 'Surat_jalan.xlsx');
  }
}
