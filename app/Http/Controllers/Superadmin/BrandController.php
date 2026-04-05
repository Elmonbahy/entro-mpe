<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\BrandExport;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.brand.superadmin.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.brand.superadmin.create');
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
        Rule::unique('brands')->ignore($id)
      ],
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $this->validation($request);

    try {
      Brand::create([
        'nama' => $request->nama
      ]);

      return redirect()->route('superadmin.brand.index')->with('success', 'Berhasil menambahkan data brand.');
    } catch (\Throwable $th) {
      return redirect()->route('superadmin.brand.index')->with('error', 'Gagal mengubah data brand.');
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(int $id)
  {
    $brand = Brand::findOrFail($id);
    return view('pages.brand.superadmin.edit', [
      'brand' => $brand
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $this->validation($request, $id);

    $brand = Brand::findOrFail($id);
    $brand->nama = $request->nama;
    $brand->save();

    return redirect()->route('superadmin.brand.index')->with('success', 'Berhasil mengubah data brand.');
  }

  public function exportExcel()
  {
    $fileName = 'brand' . '.xlsx';
    return Excel::download(new BrandExport, $fileName);
  }
}
