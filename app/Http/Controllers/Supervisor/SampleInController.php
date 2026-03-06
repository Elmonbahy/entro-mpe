<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\BarangSampleRetur;
use App\Models\SampleIn;
use App\Models\SampleInDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SampleInController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.sample-in.supervisor.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.sample-in.supervisor.create', ['suppliers' => Supplier::select('id', 'nama')->get()]);
  }

  /**
   * add items to corresponding fraktur.
   */
  public function addItem(int $id)
  {
    $sample_in = SampleIn::findOrFail($id);

    \Gate::authorize('update', $sample_in);

    return view('pages.sample-in.supervisor.add-item', compact('sample_in'));
  }

  private function validation(Request $request, $id = null)
  {
    $request->validate([
      'supplier' => 'required|exists:suppliers,id',
      'nomor_sample' => [
        'nullable',
        'string',
        'max:255',
        $id
        ? Rule::unique('sample_ins')->ignore($id) // For updates
        : 'unique:sample_ins,nomor_sample',       // For creation
      ],
      'tanggal' => 'required|date',
      'keterangan' => 'nullable|string|max:255',
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(int $id)
  {
    $sample_in = SampleIn::findOrFail($id);
    $sample_in_details = SampleInDetail::where('sample_in_id', $id)->get();

    $returs = BarangSampleRetur::whereHasMorph(
      'returnable',
      [SampleInDetail::class],
      function ($q) use ($id) {
        $q->where('sample_in_id', $id);
      }
    )
      ->with([
        'sampleBarang.barang.brand:id,nama',
        'sampleBarang:barang_id,satuan,barang_id',
        'returnable' => function ($q) {
          $q->select('id', 'sample_in_id', 'batch', 'tgl_expired', 'barang_id');
        },
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.sample-in.supervisor.show', [
      'sample_in' => $sample_in,
      'sample_in_details' => $sample_in_details,
      'returs' => $returs
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(int $id)
  {
    $sample_in = SampleIn::findOrFail($id);

    \Gate::authorize('update', $sample_in);

    return view('pages.sample-in.supervisor.edit', ['suppliers' => Supplier::select('id', 'nama')->get(), 'sample_in' => $sample_in]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $this->validation($request);

    try {
      $sample_in = SampleIn::create([
        'nomor_sample' => $request->nomor_sample,
        'tanggal' => $request->tanggal,
        'supplier_id' => $request->supplier,
        'keterangan' => $request->keterangan,
      ]);

      // Jika nomor_sample kosong, generate otomatis
      if (empty($sample_in->nomor_sample)) {
        $generated = SampleIn::generateNomorSample($request->tanggal, $sample_in->id);
        $sample_in->update(['nomor_sample' => $generated]);
      }

      return redirect()->route('supervisor.sample-in.add-item', ['id' => $sample_in->id])
        ->with('success', 'Berhasil menambahkan data sampel masuk.');
    } catch (\Exception $e) {
      return redirect()->route('supervisor.sample-in.index')
        ->with('error', 'Gagal menambahkan data beli.');
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);

    $sample_in = SampleIn::with('sampleinDetails')->findOrFail($id);

    try {
      \DB::beginTransaction();

      \Gate::authorize('update', $sample_in);

      $sample_in->update([
        'supplier_id' => $request->supplier,
        'nomor_sample' => $request->nomor_sample,
        'tanggal' => $request->tanggal,
        'keterangan' => $request->keterangan,
      ]);

      \DB::commit();

      return redirect()->route('supervisor.sample-in.index')->with('success', 'Berhasil mengubah data sampel masuk.');
    } catch (\Exception $e) {
      \DB::rollBack();
      return redirect()->back()->with('error', 'Gagal! ' . $e->getMessage());
    }
  }

}
