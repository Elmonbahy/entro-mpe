<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesmanController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.salesman.fakturis.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.salesman.fakturis.create');
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
        Rule::unique('salesmen')->ignore($id)
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
      Salesman::create([
        'kode' => Salesman::getNewCode(),
        'nama' => $request->nama
      ]);

      return redirect()->route('fakturis.salesman.index')->with('success', 'Berhasil menambahkan data salesman.');
    } catch (\Throwable $th) {
      return redirect()->route('fakturis.salesman.index')->with('error', 'Gagal mengubah data salesman.');
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(int $id)
  {
    $salesman = Salesman::findOrFail($id);
    return view('pages.salesman.fakturis.edit', [
      'salesman' => $salesman
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);

    $salesman = Salesman::findOrFail($id);
    $salesman->nama = $request->nama;
    $salesman->save();

    return redirect()->route('fakturis.salesman.index')->with('success', 'Berhasil mengubah data salesman.');
  }
}
