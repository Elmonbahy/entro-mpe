<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\Jual;
use App\Models\Salesman;
use App\Models\Pelanggan;
use App\Models\JualDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JualController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.jual.fakturis.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.jual.fakturis.create', [
      'pelanggans' => Pelanggan::select('id', 'nama')->get(),
      'salesmans' => Salesman::select('id', 'nama')->get(),
    ]);
  }

  public function edit(int $id)
  {
    $jual = Jual::findOrFail($id);

    \Gate::authorize('update', $jual);

    return view('pages.jual.fakturis.edit', [
      'jual' => $jual,
      'salesmans' => Salesman::select('id', 'nama')->get(),
      'pelanggans' => Pelanggan::select('id', 'nama')->get()
    ]);
  }

  private function validation(Request $request, $id = null)
  {
    $request->validate([
      'pelanggan' => $id ? 'nullable' : 'required|exists:pelanggans,id',
      'salesman' => 'required|exists:salesmen,id',
      'nomor_pemesanan' => [
        'nullable',
        'string',
        'max:255',
        $id
        ? Rule::unique('juals')->ignore($id) // For updates
        : 'unique:juals,nomor_pemesanan',       // For creation
      ],
      'tgl_faktur' => 'required|date',
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
        'salesman_id' => $request->salesman,
        'nomor_pemesanan' => $request->nomor_pemesanan,
        'tgl_faktur' => $request->tgl_faktur,
        'keterangan_faktur' => $request->keterangan_faktur,
      ]);

      \DB::commit();

      return redirect()->route('fakturis.jual.index')->with('success', 'Berhasil mengubah data jual.');
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
        'tgl_faktur' => $request->tgl_faktur,
        'keterangan_faktur' => $request->keterangan_faktur,
        'pelanggan_id' => $request->pelanggan,
        'salesman_id' => $request->salesman,
      ]);

      return redirect()
        ->route('fakturis.jual.add-item', ['id' => $jual->id])
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

    return view('pages.jual.fakturis.show', [
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
    return view('pages.jual.fakturis.add-item', compact('jual', 'tipe_harga'));
  }

}
