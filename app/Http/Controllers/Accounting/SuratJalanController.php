<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Illuminate\Http\Request;
use App\Exports\Accounting\SuratJalanExport;
use Maatwebsite\Excel\Facades\Excel;

class SuratJalanController extends Controller
{
  public function index()
  {
    return view('pages.surat-jalan.accounting.index');
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

    return view('pages.surat-jalan.accounting.show', [
      'surat_jalan' => $surat_jalan,
      'surat_jalan_details' => $surat_jalan_details
    ]);
  }

  public function exportExcel()
  {
    return Excel::download(new SuratJalanExport, 'Surat_jalan.xlsx');
  }

}
