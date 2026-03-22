<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangRusak;
use App\Models\Beli;
use App\Models\Jual;
use App\Models\Pelanggan;
use App\Models\Supplier;
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
  {
    /**
     * Display a listing of the resource.
     */
    $user = Auth::user();

    $beliQuery = Beli::query();
    $jualQuery = Jual::query();

    $counts = [
      'total_barang' => Barang::count(),
      'total_supplier' => Supplier::count(),
      'total_pelanggan' => Pelanggan::count(),
      'total_beli' => $beliQuery->count(),
      'total_jual' => $jualQuery->count(),
      'total_rusak' => BarangRusak::count(),
    ];

    return view('pages.dashboard.index', ['counts' => $counts]);
  }
}
