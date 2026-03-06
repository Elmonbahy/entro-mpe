<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangRusak;
use App\Models\Beli;
use App\Models\Jual;
use App\Models\Pelanggan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
  public function index()
  {
    $today = Carbon::today();

    $counts = [
      'total_barang' => Barang::count(),
      'today_barang' => Barang::whereDate('created_at', $today)->count(),

      'total_supplier' => Supplier::count(),
      'today_supplier' => Supplier::whereDate('created_at', $today)->count(),

      'total_pelanggan' => Pelanggan::count(),
      'today_pelanggan' => Pelanggan::whereDate('created_at', $today)->count(),

      'total_beli' => Beli::count(),
      'today_beli' => Beli::whereDate('created_at', $today)->count(),

      'total_jual' => Jual::count(),
      'today_jual' => Jual::whereDate('created_at', $today)->count(),

      'total_rusak' => BarangRusak::count(),
      'today_rusak' => BarangRusak::whereDate('created_at', $today)->count(),
    ];

    return view('pages.dashboard.index', ['counts' => $counts]);
  }
}
