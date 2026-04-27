<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangStock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\Gudang\BarangExport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.barang.gudang.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(Barang $barang)
  {
    $barang_stocks = BarangStock::where('barang_id', $barang->id)
      ->where('jumlah_stock', '>', 0)
      ->get();

    return view('pages.barang.gudang.show', ['barang' => $barang, 'barang_stocks' => $barang_stocks]);
  }

  public function exportExcel()
  {
    $fileName = 'barang' . '.xlsx';
    return Excel::download(new BarangExport, $fileName);
  }
}
