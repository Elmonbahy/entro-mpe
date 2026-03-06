<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Constants\Rayon;
use App\Constants\Area;
use App\Constants\TipePelanggan;
use App\Constants\Kota;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\Keuangan\PelangganExport;
use Maatwebsite\Excel\Facades\Excel;

class PelangganController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.pelanggan.keuangan.index');
  }

  /**
   * Validate the request
   * @param \Illuminate\Http\Request $request
   * @return void
   */
  protected function validation(Request $request, int $id = null)
  {
    $request->validate([
      'area' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, Area::all())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
      'limit_hari' => 'required|integer|min:0',
      'plafon_hutang' => 'required|numeric|min:0'
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Pelanggan $pelanggan)
  {
    return view('pages.pelanggan.keuangan.show', ['pelanggan' => $pelanggan,]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Pelanggan $pelanggan)
  {
    $kotas = Kota::all();
    $areas = Area::all();
    return view('pages.pelanggan.keuangan.edit', [
      'pelanggan' => $pelanggan,
      'kotas' => $kotas,
      'areas' => $areas
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);

    $pelanggan = Pelanggan::findOrFail($id);
    $pelanggan->area = $request->area;
    $pelanggan->limit_hari = $request->limit_hari;
    $pelanggan->plafon_hutang = $request->plafon_hutang;
    $pelanggan->save();

    return redirect()->route('keuangan.pelanggan.index')->with('success', 'Berhasil mengubah data pelanggan.');
  }

  public function exportExcel()
  {
    $fileName = 'pelanggan' . '.xlsx';
    return Excel::download(new PelangganExport, $fileName);
  }
}
