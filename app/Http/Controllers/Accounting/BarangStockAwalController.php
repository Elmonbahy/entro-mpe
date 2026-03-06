<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\BarangStockAwal;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Accounting\BarangStockAwalExport;

class BarangStockAwalController extends Controller
{
  public function index()
  {
    return view('pages.barang-stock-awal.accounting.index');
  }

  public function show(int $id)
  {
    $barang_stock_awal = BarangStockAwal::with([
      'barang:barangs.id,barangs.nama,barangs.satuan,barangs.brand_id',
      'barang.brand:id,nama'
    ])->findOrFail($id);

    return view('pages.barang-stock-awal.accounting.show', compact('barang_stock_awal'));
  }
  public function exportExcel()
  {
    return Excel::download(new BarangStockAwalExport, "Penyesuaian.xlsx");
  }
}
