<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Exports\Supervisor\BrandExport;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.brand.supervisor.index');
  }

  public function exportExcel()
  {
    $fileName = 'brand' . '.xlsx';
    return Excel::download(new BrandExport, $fileName);
  }
}
