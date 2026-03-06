<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\BarangStock;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Http\Request;
use App\Exports\Gudang\BarangStockExport;
use Maatwebsite\Excel\Facades\Excel;

class BarangStockController extends Controller
{
  public function index()
  {
    return view('pages.barang-stock.gudang.index');
  }

  public function exportExcel()
  {
    $tanggal = date('d-m-Y');
    return Excel::download(new BarangStockExport, "Barang_stock_{$tanggal}.xlsx");
  }

}
