<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KendaraanController extends Controller
{
  public function index()
  {
    return view('pages.kendaraan.gudang.index');
  }

  public function create()
  {
    return view('pages.kendaraan.gudang.create');
  }

  protected function validation(Request $request, int $id = null)
  {
    $request->validate([
      'nama' => [
        'required',
        'string',
        'max:255',
        Rule::unique('kendaraans')->ignore($id)
      ],
      'alamat' => 'max:255',
      'contact_person' => 'max:100',
      'contact_phone' => 'max:32',
    ]);
  }

  public function store(Request $request)
  {
    $this->validation($request);

    try {
      Kendaraan::create([
        'nama' => $request->nama,
        'alamat' => $request->alamat,
        'contact_person' => $request->contact_person,
        'contact_phone' => $request->contact_phone
      ]);
      return redirect()->route('gudang.kendaraan.index')->with('success', 'Berhasil menambahkan data kendaraan.');
    } catch (\Exception $e) {
      return redirect()->route('gudang.kendaraan.index')->with('error', 'Gagal menambahkan data kendaraan.');
    }
  }

  public function show(Kendaraan $kendaraan)
  {
    return view('pages.kendaraan.gudang.show', ['kendaraan' => $kendaraan,]);
  }

  public function edit(int $id)
  {
    $kendaraan = Kendaraan::findOrFail($id);
    return view('pages.kendaraan.gudang.edit', [
      'kendaraan' => $kendaraan
    ]);
  }

  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);

    $kendaraan = Kendaraan::findOrFail($id);
    $kendaraan->nama = $request->nama;
    $kendaraan->alamat = $request->alamat;
    $kendaraan->contact_person = $request->contact_person;
    $kendaraan->contact_phone = $request->contact_phone;
    $kendaraan->save();

    return redirect()->route('gudang.kendaraan.index')->with('success', 'Berhasil mengubah data kendaraan.');
  }
}
