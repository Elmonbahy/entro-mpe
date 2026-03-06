<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\SupplierExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.supplier.fakturis.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.supplier.fakturis.create');
  }

  /**
   * Validate the request
   * @param \Illuminate\Http\Request $request
   * @return void
   */
  protected function validation(Request $request, int $id = null)
  {
    $request->validate([
      'nama' => [
        'required',
        'string',
        'max:255',
        Rule::unique('suppliers')->ignore($id)
      ],
      'alamat' => 'max:255',
      'kota' => 'max:100',
      'npwp' => 'max:100',
      'contact_person' => 'max:100',
      'contact_phone' => 'max:32',
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $this->validation($request);

    try {
      Supplier::create([
        'kode' => Supplier::getNewCode(),
        'nama' => $request->nama,
        'alamat' => $request->alamat,
        'kota' => $request->kota,
        'npwp' => $request->npwp,
        'contact_person' => $request->contact_person,
        'contact_phone' => $request->contact_phone
      ]);
      return redirect()->route('fakturis.supplier.index')->with('success', 'Berhasil menambahkan data supplier.');
    } catch (\Exception $e) {
      return redirect()->route('fakturis.supplier.index')->with('error', 'Gagal menambahkan data supplier.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Supplier $supplier)
  {
    return view('pages.supplier.fakturis.show', ['supplier' => $supplier,]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(int $id)
  {
    $supplier = Supplier::findOrFail($id);
    return view('pages.supplier.fakturis.edit', [
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
    $supplier->nama = $request->nama;
    $supplier->alamat = $request->alamat;
    $supplier->kota = $request->kota;
    $supplier->npwp = $request->npwp;
    $supplier->contact_person = $request->contact_person;
    $supplier->contact_phone = $request->contact_phone;
    $supplier->save();

    return redirect()->route('fakturis.supplier.index')->with('success', 'Berhasil mengubah data supplier.');
  }

  public function exportExcel()
  {
    $fileName = 'supplier' . '.xlsx';
    return Excel::download(new SupplierExport, $fileName);
  }
}
