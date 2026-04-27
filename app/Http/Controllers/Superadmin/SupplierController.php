<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Exports\SupplierExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.supplier.superadmin.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(Supplier $supplier)
  {
    return view('pages.supplier.superadmin.show', ['supplier' => $supplier,]);
  }

  public function exportExcel()
  {
    $fileName = 'supplier' . '.xlsx';
    return Excel::download(new SupplierExport, $fileName);
  }
}
