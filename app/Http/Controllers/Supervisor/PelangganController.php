<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Constants\Area;
use App\Constants\TipePelanggan;
use App\Constants\Kota;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\Supervisor\PelangganExport;
use Maatwebsite\Excel\Facades\Excel;

class PelangganController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.pelanggan.supervisor.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $tipePelanggans = TipePelanggan::all();
    $kotas = Kota::all();
    $areas = Area::all();
    return view('pages.pelanggan.supervisor.create', [
      'tipePelanggans' => $tipePelanggans,
      'kotas' => $kotas,
      'areas' => $areas
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
      'kode' => [
        'nullable',
        'string',
        'max:100',
        Rule::unique('pelanggans')->ignore($id)
      ],
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
      'npwp' => ['required', 'max:100'],
      'contact_person' => ['required', 'max:100'],
      'contact_phone' => ['required', 'max:32'],
      'tipe_harga' => 'required|in:SWASTA,PEMERINTAH',
      'tipe' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, TipePelanggan::all())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
      'area' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, Area::all())) {
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
        'kode' => Pelanggan::getNewCode(),
        'nama' => $request->nama,
        'alamat' => $request->alamat,
        'kota' => $request->kota,
        'npwp' => $request->npwp,
        'contact_person' => $request->contact_person,
        'contact_phone' => $request->contact_phone,
        'tipe' => $request->tipe,
        'tipe_harga' => $request->tipe_harga,
        'area' => $request->area,
      ]);

      return redirect()->route('supervisor.pelanggan.index')->with('success', 'Berhasil menambahkan data pelanggan.');
    } catch (\Exception $e) {
      return redirect()->route('supervisor.pelanggan.index')->with('error', 'Gagal menambahkan data pelanggan.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Pelanggan $pelanggan)
  {
    return view('pages.pelanggan.supervisor.show', ['pelanggan' => $pelanggan,]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Pelanggan $pelanggan)
  {
    $tipePelanggans = TipePelanggan::all();
    $kotas = Kota::all();
    $areas = Area::all();
    return view('pages.pelanggan.supervisor.edit', [
      'pelanggan' => $pelanggan,
      'tipePelanggans' => $tipePelanggans,
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
    $pelanggan->nama = $request->nama;
    $pelanggan->alamat = $request->alamat;
    $pelanggan->kota = $request->kota;
    $pelanggan->npwp = $request->npwp;
    $pelanggan->contact_person = $request->contact_person;
    $pelanggan->contact_phone = $request->contact_phone;
    $pelanggan->tipe = $request->tipe;
    $pelanggan->tipe_harga = $request->tipe_harga;
    $pelanggan->area = $request->area;
    $pelanggan->save();

    return redirect()->route('supervisor.pelanggan.index')->with('success', 'Berhasil mengubah data pelanggan.');

  }

  public function exportExcel()
  {
    $fileName = 'pelanggan' . '.xlsx';
    return Excel::download(new PelangganExport, $fileName);
  }
}
