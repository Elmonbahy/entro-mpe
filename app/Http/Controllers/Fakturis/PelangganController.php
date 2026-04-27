<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Constants\TipePelanggan;
use App\Constants\Kota;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\PelangganExport;
use Maatwebsite\Excel\Facades\Excel;

class PelangganController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.pelanggan.fakturis.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $tipePelanggans = TipePelanggan::all();
    $kotas = Kota::all();
    return view('pages.pelanggan.fakturis.create', [
      'tipePelanggans' => $tipePelanggans,
      'kotas' => $kotas,
    ]);
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
        Rule::unique('pelanggans')->ignore($id)
      ],
      'kota' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, Kota::all())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
      'alamat' => ['required', 'max:255'],
      'contact_person' => ['required', 'max:100'],
      'contact_phone' => ['required', 'max:32'],
      'tipe' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, TipePelanggan::all())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
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
      Pelanggan::create([
        'nama' => $request->nama,
        'alamat' => $request->alamat,
        'kota' => $request->kota,
        'contact_person' => $request->contact_person,
        'contact_phone' => $request->contact_phone,
        'tipe' => $request->tipe,
      ]);

      return redirect()->route('fakturis.pelanggan.index')->with('success', 'Berhasil menambahkan data pelanggan.');
    } catch (\Exception $e) {
      return redirect()->route('fakturis.pelanggan.index')->with('error', 'Gagal menambahkan data pelanggan.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Pelanggan $pelanggan)
  {
    return view('pages.pelanggan.fakturis.show', ['pelanggan' => $pelanggan,]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Pelanggan $pelanggan)
  {
    $tipePelanggans = TipePelanggan::all();
    $kotas = Kota::all();
    return view('pages.pelanggan.fakturis.edit', [
      'pelanggan' => $pelanggan,
      'tipePelanggans' => $tipePelanggans,
      'kotas' => $kotas,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);

    $pelanggan = Pelanggan::findOrFail($id);
    $pelanggan->nama = $request->nama;
    $pelanggan->alamat = $request->alamat;
    $pelanggan->kota = $request->kota;
    $pelanggan->contact_person = $request->contact_person;
    $pelanggan->contact_phone = $request->contact_phone;
    $pelanggan->tipe = $request->tipe;
    $pelanggan->save();

    return redirect()->route('fakturis.pelanggan.index')->with('success', 'Berhasil mengubah data pelanggan.');

  }

  public function exportExcel()
  {
    $fileName = 'pelanggan' . '.xlsx';
    return Excel::download(new PelangganExport, $fileName);
  }
}
