<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;

class BarangStockController extends Controller
{
  public function index()
  {
    return view('pages.barang-stock.gudang.index');
  }

}
