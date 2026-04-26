<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangRetur;
use App\Models\Beli;
use App\Models\BeliDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BeliController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.beli.fakturis.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.beli.fakturis.create', ['suppliers' => Supplier::select('id', 'nama')->get()]);
  }

  /**
   * add items to corresponding fraktur.
   */
  public function addItem(int $id)
  {
    $beli = Beli::findOrFail($id);

    \Gate::authorize('update', $beli);

    return view('pages.beli.fakturis.add-item', compact('beli'));
  }

  private function validation(Request $request, $id = null)
  {
    $request->validate([
      'supplier' => 'required|exists:suppliers,id',
      'nomor_faktur' => [
        'required',
        'string',
        'max:255',
        $id
        ? Rule::unique('belis')->ignore($id) // For updates
        : 'unique:belis,nomor_faktur',       // For creation
      ],
      'nomor_pemesanan' => [
        'nullable',
        'string',
        'max:255',
        $id
        ? Rule::unique('belis')->ignore($id) // For updates
        : 'unique:belis,nomor_pemesanan',       // For creation
      ],
      'tgl_faktur' => 'required|date',
      'tgl_terima_faktur' => 'required|date|after_or_equal:tgl_faktur',
      'keterangan_faktur' => 'nullable|string|max:255',
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(int $id)
  {
    $beli = Beli::findOrFail($id);
    $beli_details = $beli->beliDetails;

    $returs = $beli->barangReturs()
      ->with([
        'barang:id,nama,satuan,brand_id',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'beli_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.beli.fakturis.show', [
      'beli' => $beli,
      'beli_details' => $beli_details,
      'returs' => $returs,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(int $id)
  {
    $beli = Beli::findOrFail($id);

    \Gate::authorize('update', $beli);

    return view('pages.beli.fakturis.edit', ['suppliers' => Supplier::select('id', 'nama')->get(), 'beli' => $beli]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $this->validation($request);

    try {
      $beli = Beli::create([
        'nomor_pemesanan' => Beli::generateNomorPemesanan($request->tgl_faktur),
        'nomor_faktur' => $request->nomor_faktur,
        'tgl_faktur' => $request->tgl_faktur,
        'tgl_terima_faktur' => $request->tgl_terima_faktur,
        'supplier_id' => $request->supplier,
        'keterangan_faktur' => $request->keterangan_faktur,
      ]);

      return redirect()->route('fakturis.beli.add-item', ['id' => $beli->id])
        ->with('success', 'Berhasil menambahkan data beli.');
    } catch (\Exception $e) {
      return redirect()->route('fakturis.beli.index')
        ->with('error', 'Gagal menambahkan data beli.');
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);

    $beli = Beli::with('beliDetails')->findOrFail($id);

    try {
      \DB::beginTransaction();

      \Gate::authorize('update', $beli);

      $beli->update([
        'supplier_id' => $request->supplier,
        'nomor_faktur' => $request->nomor_faktur,
        'nomor_pemesanan' => $request->nomor_pemesanan,
        'tgl_faktur' => $request->tgl_faktur,
        'tgl_terima_faktur' => $request->tgl_terima_faktur,
        'keterangan_faktur' => $request->keterangan_faktur,
      ]);

      \DB::commit();

      return redirect()->route('fakturis.beli.index')->with('success', 'Berhasil mengubah data beli.');
    } catch (\Exception $e) {
      \DB::rollBack();
      return redirect()->back()->with('error', 'Gagal! ' . $e->getMessage());
    }
  }

}
