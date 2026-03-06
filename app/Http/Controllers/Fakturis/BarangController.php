<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Constants\Satuan;
use App\Models\Barang;
use App\Models\BarangStock;
use App\Models\Brand;
use App\Models\Group;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\BarangExport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.barang.fakturis.index');
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
        'nullable',
        'string',
        'max:100',
        Rule::unique('barangs')->ignore($id)
      ],
      'nama' => [
        'required',
        'string',
        'max:255',
        Rule::unique('barangs')->ignore($id)
      ],
      'satuan' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, Satuan::all())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
      'nie' => ['nullable', 'string', 'max:100'],
      'harga_jual_pemerintah' => ['nullable', 'numeric', 'min:0'],
      'harga_jual_swasta' => ['nullable', 'numeric', 'min:0'],
      'harga_beli' => ['nullable', 'numeric', 'min:0'],
      'group' => ['nullable', 'string', 'exists:' . Group::class . ',id'],
      'brand' => ['required', 'string', 'exists:' . Brand::class . ',id'],
      'supplier' => ['nullable', 'string', 'exists:' . Supplier::class . ',id'],
      'kegunaan' => ['nullable']
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $satuans = Satuan::all();
    $suppliers = Supplier::select(['id', 'nama'])->orderBy('nama', 'asc')->get();
    $groups = Group::select(['id', 'nama'])->orderBy('nama', 'asc')->get();
    $brands = Brand::select(['id', 'nama'])->orderBy('nama', 'asc')->get();
    return view('pages.barang.fakturis.create', [
      'satuans' => $satuans,
      'suppliers' => $suppliers,
      'groups' => $groups,
      'brands' => $brands
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->merge([
      'harga_jual_pemerintah' => $request->harga_jual_pemerintah ? str_replace(',', '.', str_replace('.', '', $request->harga_jual_pemerintah)) : 0,
      'harga_jual_swasta' => $request->harga_jual_swasta ? str_replace(',', '.', str_replace('.', '', $request->harga_jual_swasta)) : 0,
      'harga_beli' => $request->harga_beli ? str_replace(',', '.', str_replace('.', '', $request->harga_beli)) : 0,
    ]);

    $this->validation($request);

    try {
      Barang::create([
        'kode' => $request->kode,
        'nama' => $request->nama,
        'satuan' => $request->satuan,
        'nie' => $request->nie,
        'harga_jual_pemerintah' => $request->harga_jual_pemerintah,
        'harga_jual_swasta' => $request->harga_jual_swasta,
        'harga_beli' => $request->harga_beli,
        'group_id' => $request->group,
        'brand_id' => $request->brand,
        'supplier_id' => $request->supplier,
        'kegunaan' => $request->kegunaan,
      ]);

      return redirect()->route('fakturis.barang.index')->with('success', 'Berhasil menambahkan data barang.');
    } catch (\Throwable $th) {
      return redirect()->route('fakturis.barang.index')->with('error', 'Gagal menambahkan data barang.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Barang $barang)
  {
    $barang_stocks = BarangStock::where('barang_id', $barang->id)
      ->where('jumlah_stock', '>', 0)
      ->get();
    return view('pages.barang.fakturis.show', ['barang' => $barang, 'barang_stocks' => $barang_stocks]);
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

    return view('pages.barang.fakturis.edit', [
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
    $request->merge([
      'harga_jual_pemerintah' => $request->harga_jual_pemerintah ? str_replace(',', '.', str_replace('.', '', $request->harga_jual_pemerintah)) : 0,
      'harga_jual_swasta' => $request->harga_jual_swasta ? str_replace(',', '.', str_replace('.', '', $request->harga_jual_swasta)) : 0,
      'harga_beli' => $request->harga_beli ? str_replace(',', '.', str_replace('.', '', $request->harga_beli)) : 0,
    ]);

    $this->validation($request, $id);

    $barang = Barang::findOrFail($id);
    $barang->nama = $request->nama;
    $barang->kode = $request->kode;
    $barang->satuan = $request->satuan;
    $barang->nie = $request->nie;
    $barang->harga_jual_pemerintah = $request->harga_jual_pemerintah;
    $barang->harga_jual_swasta = $request->harga_jual_swasta;
    $barang->harga_beli = $request->harga_beli;
    $barang->group_id = $request->group;
    $barang->brand_id = $request->brand;
    $barang->supplier_id = $request->supplier;
    $barang->kegunaan = $request->kegunaan;
    $barang->save();

    return redirect()->route('fakturis.barang.index')->with('success', 'Berhasil mengubah data barang.');
  }

  public function exportExcel()
  {
    $fileName = 'barang' . '.xlsx';
    return Excel::download(new BarangExport, $fileName);
  }
}
