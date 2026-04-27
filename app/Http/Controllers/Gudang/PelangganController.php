<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Exports\Gudang\PelangganExport;
use Maatwebsite\Excel\Facades\Excel;

class PelangganController extends Controller
{
  public function index()
  {
    return view('pages.pelanggan.gudang.index');
  }

  public function show(Pelanggan $pelanggan)
  {
    return view('pages.pelanggan.gudang.show', ['pelanggan' => $pelanggan,]);
  }
  public function exportExcel()
  {
    $fileName = 'pelanggan' . '.xlsx';
    return Excel::download(new PelangganExport, $fileName);
  }
}
