<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Constants\SatuanSample;
use App\Models\Barang;
use App\Models\BarangSampleStock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\BarangExport;
use App\Models\SampleBarang;
use Maatwebsite\Excel\Facades\Excel;

class SampleBarangController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.sample-barang.supervisor.index');
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
      'barang_id' => [
        'required',
        'integer',
        'exists:barangs,id',
        Rule::unique('sample_barangs', 'barang_id')->ignore($id),
      ],
      'satuan' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, SatuanSample::all())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $barangs = Barang::select('id', 'kode', 'nama')->orderBy('nama')->get();
    $satuans = SatuanSample::all();

    return view('pages.sample-barang.supervisor.create', compact('barangs', 'satuans'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $this->validation($request);

    try {
      SampleBarang::create([
        'barang_id' => $request->barang_id,
        'satuan' => $request->satuan,
      ]);

      return redirect()->route('supervisor.sample-barang.index')->with('success', 'Berhasil menambahkan data barang sampel.');
    } catch (\Throwable $th) {
      return redirect()->route('supervisor.sample-barang.index')->with('error', 'Gagal menambahkan data barang sampel.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(SampleBarang $sample_barang)
  {
    $barang_stocks = BarangSampleStock::where('barang_id', $sample_barang->barang_id)
      ->where('jumlah_stock', '>', 0)
      ->get();
    return view('pages.sample-barang.supervisor.show', ['sample_barang' => $sample_barang, 'barang_stocks' => $barang_stocks]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(SampleBarang $sample_barang)
  {
    $satuans = SatuanSample::all();

    return view('pages.sample-barang.supervisor.edit', [
      'sample_barang' => $sample_barang,
      'satuans' => $satuans,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id)
  {

    $this->validation($request, $id);

    $sample_barang = SampleBarang::findOrFail($id);
    $sample_barang->satuan = $request->satuan;
    $sample_barang->save();

    return redirect()->route('supervisor.sample-barang.index')->with('success', 'Berhasil mengubah data barang sampel.');
  }

}
