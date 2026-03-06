<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\Keuangan\SupplierExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.supplier.keuangan.index');
  }

  /**
   * Validate the request
   * @param \Illuminate\Http\Request $request
   * @return void
   */
  protected function validation(Request $request, int $id = null)
  {
    $request->validate([
      'alamat' => 'max:255',
      'kota' => 'max:100',
      'npwp' => 'max:100',
      'contact_person' => 'max:100',
      'contact_phone' => 'max:32',
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Supplier $supplier)
  {
    return view('pages.supplier.keuangan.show', ['supplier' => $supplier,]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(int $id)
  {
    $supplier = Supplier::findOrFail($id);
    return view('pages.supplier.keuangan.edit', [
      'supplier' => $supplier
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);
    $supplier = Supplier::findOrFail($id);
    $supplier->alamat = $request->alamat;
    $supplier->kota = $request->kota;
    $supplier->npwp = $request->npwp;
    $supplier->contact_person = $request->contact_person;
    $supplier->contact_phone = $request->contact_phone;
    $supplier->save();

    return redirect()->route('keuangan.supplier.index')->with('success', 'Berhasil mengubah data supplier.');
  }

  public function exportExcel()
  {
    $fileName = 'supplier' . '.xlsx';
    return Excel::download(new SupplierExport, $fileName);
  }
}
