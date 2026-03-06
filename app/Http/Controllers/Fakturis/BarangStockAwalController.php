<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Models\BarangStockAwal;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarangStockAwalExport;

class BarangStockAwalController extends Controller
{
  public function index()
  {
    return view('pages.barang-stock-awal.fakturis.index');
  }
  public function create()
  {
    return view('pages.barang-stock-awal.fakturis.create');
  }

  public function show(int $id)
  {
    $barang_stock_awal = BarangStockAwal::with([
      'barang:barangs.id,barangs.nama,barangs.satuan,barangs.brand_id',
      'barang.brand:id,nama'
    ])->findOrFail($id);

    return view('pages.barang-stock-awal.fakturis.show', compact('barang_stock_awal'));
  }
  public function exportExcel()
  {
    return Excel::download(new BarangStockAwalExport, "Penyesuaian.xlsx");
  }
}
