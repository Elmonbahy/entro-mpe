<?php

namespace App\Http\Controllers\Pajak;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.surat-jalan.pajak.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $surat_jalan = SuratJalan::findOrFail($id);
    $surat_jalan_details = SuratJalanDetail::where('surat_jalan_id', $surat_jalan->id)
      ->with([
        'jual:juals.id,juals.nomor_faktur',
        'jualDetail:jual_details.id,jual_details.jumlah_barang_keluar',
        'barang:barangs.id,barangs.nama,barangs.satuan',
      ])
      ->get();

    return view('pages.surat-jalan.pajak.show', [
      'surat_jalan' => $surat_jalan,
      'surat_jalan_details' => $surat_jalan_details
    ]);
  }

}
