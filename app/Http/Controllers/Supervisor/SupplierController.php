<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Exports\Supervisor\SupplierExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.supplier.supervisor.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(Supplier $supplier)
  {
    return view('pages.supplier.supervisor.show', ['supplier' => $supplier,]);
  }

  public function exportExcel()
  {
    $fileName = 'supplier' . '.xlsx';
    return Excel::download(new SupplierExport, $fileName);
  }
}
