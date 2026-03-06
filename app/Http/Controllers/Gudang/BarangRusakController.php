<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\BarangRusak;
use App\Models\Jual;
use App\Models\JualDetail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Gudang\BarangRusakExport;
class BarangRusakController extends Controller
{
  public function index()
  {
    return view('pages.barang-rusak.gudang.index');
  }
  public function create()
  {
    return view('pages.barang-rusak.gudang.create');
  }
  public function show(int $id)
  {
    $barang_rusak = BarangRusak::with([
      'barang:barangs.id,barangs.nama,barangs.satuan',
      'barangStock:id,barang_id,batch,tgl_expired'
    ])->findOrFail($id);

    return view('pages.barang-rusak.gudang.show', compact('barang_rusak'));
  }
  public function exportExcel()
  {
    return Excel::download(new BarangRusakExport, "Barang_Rusak.xlsx");
  }

}
