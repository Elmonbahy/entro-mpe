<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Constants\Satuan;
use App\Models\Barang;
use App\Models\BarangStock;
use App\Models\Brand;
use App\Models\Group;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\Gudang\BarangExport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.barang.gudang.index');
  }


  /**
   * Validate the request
   * @param \Illuminate\Http\Request $request
   * @param int|null $id ID for update operation
   * @return void
   */
  protected function validation(Request $request, $id = null)
  {
    $request->validate([
      'kode' => [
        'required',
        'string',
        'max:100',
        Rule::unique('barangs')->ignore($id)
      ],
      'nie' => ['required', 'string', 'max:100'],
      'group' => ['required', 'string', 'exists:' . Group::class . ',id'],
      'supplier' => ['required', 'string', 'exists:' . Supplier::class . ',id'],
      'kegunaan' => ['required']
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Barang $barang)
  {
    $barang_stocks = BarangStock::where('barang_id', $barang->id)
      ->where('jumlah_stock', '>', 0)
      ->get();

    return view('pages.barang.gudang.show', ['barang' => $barang, 'barang_stocks' => $barang_stocks]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Barang $barang)
  {
    $satuans = Satuan::all();
    $suppliers = Supplier::select(['id', 'nama'])->orderBy('nama', 'asc')->get();
    $groups = Group::select(['id', 'nama'])->orderBy('nama', 'asc')->get();
    $brands = Brand::select(['id', 'nama'])->orderBy('nama', 'asc')->get();

    return view('pages.barang.gudang.edit', [
      'barang' => $barang,
      'satuans' => $satuans,
      'suppliers' => $suppliers,
      'groups' => $groups,
      'brands' => $brands
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);

    try {
      $barang = Barang::findOrFail($id);
      $barang->kode = $request->kode;
      $barang->nie = $request->nie;
      $barang->group_id = $request->group;
      $barang->supplier_id = $request->supplier;
      $barang->kegunaan = $request->kegunaan;
      $barang->save();

      return redirect()->route('gudang.barang.index')->with('success', 'Berhasil mengubah data barang.');
    } catch (\Throwable $th) {
      return redirect()->route('gudang.barang.index')->with('error', 'Gagal mengubah data barang.');
    }
  }

  public function exportExcel()
  {
    $fileName = 'barang' . '.xlsx';
    return Excel::download(new BarangExport, $fileName);
  }
}
