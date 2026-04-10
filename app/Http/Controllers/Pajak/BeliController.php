<?php

namespace App\Http\Controllers\Pajak;

use App\Http\Controllers\Controller;
use App\Models\Beli;

class BeliController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.beli.pajak.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
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

    return view('pages.beli.pajak.show', [
      'beli' => $beli,
      'beli_details' => $beli_details,
      'returs' => $returs,
    ]);
  }
}
