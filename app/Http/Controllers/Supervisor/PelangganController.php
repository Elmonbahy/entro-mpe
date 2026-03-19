<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Exports\Supervisor\PelangganExport;
use Maatwebsite\Excel\Facades\Excel;

class PelangganController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.pelanggan.supervisor.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(Pelanggan $pelanggan)
  {
    return view('pages.pelanggan.supervisor.show', ['pelanggan' => $pelanggan,]);
  }

  public function exportExcel()
  {
    $fileName = 'pelanggan' . '.xlsx';
    return Excel::download(new PelangganExport, $fileName);
  }
}
