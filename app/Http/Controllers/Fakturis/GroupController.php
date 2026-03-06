<?php

namespace App\Http\Controllers\Fakturis;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\GroupExport;
use Maatwebsite\Excel\Facades\Excel;

class GroupController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.group.fakturis.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.group.fakturis.create');
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
        Rule::unique('groups')->ignore($id)
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
      Group::create([
        'nama' => $request->nama
      ]);

      return redirect()->route('fakturis.group.index')->with('success', 'Berhasil menambahkan data group.');
    } catch (\Throwable $th) {
      return redirect()->route('fakturis.group.index')->with('error', 'Gagal menambahkan data group.');
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    $group = Group::findOrFail($id);
    return view('pages.group.fakturis.edit', [
      'group' => $group
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $this->validation($request, $id);

    $group = Group::findOrFail($id);
    $group->nama = $request->nama;
    $group->save();

    return redirect()->route('fakturis.group.index')->with('success', 'Berhasil mengubah data group.');
  }

  public function exportExcel()
  {
    $fileName = 'group' . '.xlsx';
    return Excel::download(new GroupExport, $fileName);
  }
}
