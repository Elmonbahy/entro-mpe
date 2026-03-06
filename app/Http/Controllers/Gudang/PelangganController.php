<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Exports\Gudang\PelangganExport;
use Maatwebsite\Excel\Facades\Excel;

class PelangganController extends Controller
{
  public function index()
  {
    return view('pages.pelanggan.gudang.index');
  }

  protected function validation(Request $request)
  {
    $request->validate([
      'contact_person' => ['required', 'max:100'],
      'contact_phone' => ['required', 'max:32'],
    ]);
  }

  public function show(Pelanggan $pelanggan)
  {
    return view('pages.pelanggan.gudang.show', ['pelanggan' => $pelanggan,]);
  }

  public function edit(Pelanggan $pelanggan)
  {

    return view('pages.pelanggan.gudang.edit', ['pelanggan' => $pelanggan,]);
  }

  public function update(Request $request, int $id)
  {
    $this->validation($request);

    $pelanggan = Pelanggan::findOrFail($id);
    $pelanggan->contact_person = $request->contact_person;
    $pelanggan->contact_phone = $request->contact_phone;
    $pelanggan->save();

    return redirect()->route('gudang.pelanggan.index')->with('success', 'Berhasil mengubah data pelanggan.');

  }

  public function exportExcel()
  {
    $fileName = 'pelanggan' . '.xlsx';
    return Excel::download(new PelangganExport, $fileName);
  }
}
