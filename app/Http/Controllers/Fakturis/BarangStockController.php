<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Exports\BarangStockExport;
use App\Exports\BarangStockPerBatchExport;
use Maatwebsite\Excel\Facades\Excel;

class BarangStockController extends Controller
{
  public function index()
  {
    return view('pages.barang-stock.fakturis.index');
  }

  public function exportExcel()
  {
    return Excel::download(new BarangStockExport, 'barang_stock.xlsx');
  }

  public function exportbarangperbatchExcel()
  {
    $tanggal = date('d-m-Y');
    return Excel::download(new BarangStockPerBatchExport, "stock_barang_per_batch{$tanggal}.xlsx");
  }
}
