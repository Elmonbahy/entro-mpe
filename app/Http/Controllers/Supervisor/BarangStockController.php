<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\BarangStock;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Http\Request;
use App\Exports\Supervisor\BarangStockExport;
use Maatwebsite\Excel\Facades\Excel;

class BarangStockController extends Controller
{
  public function index()
  {
    return view('pages.barang-stock.supervisor.index');
  }

  public function exportExcel()
  {
    return Excel::download(new BarangStockExport, 'barang_stock.xlsx');
  }
}
