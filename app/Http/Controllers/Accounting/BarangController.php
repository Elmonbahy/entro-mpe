<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Constants\Satuan;
use App\Models\Barang;
use App\Models\BarangStock;
use App\Models\Brand;
use App\Models\Group;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\Accounting\BarangExport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.barang.accounting.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(Barang $barang)
  {
    $barang_stocks = BarangStock::where('barang_id', $barang->id)
      ->where('jumlah_stock', '>', 0)
      ->get();
    return view('pages.barang.accounting.show', ['barang' => $barang, 'barang_stocks' => $barang_stocks]);
  }

  public function exportExcel()
  {
    $fileName = 'barang' . '.xlsx';
    return Excel::download(new BarangExport, $fileName);
  }
}
