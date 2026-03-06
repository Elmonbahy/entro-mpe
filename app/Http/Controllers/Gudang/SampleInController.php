<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusSample;
use App\Http\Controllers\Controller;
use App\Models\BarangSampleRetur;
use App\Models\SampleIn;
use App\Models\SampleInDetail;
use DB;
use Illuminate\Http\Request;

class SampleInController extends Controller
{
  public function index()
  {
    return view('pages.sample-in.gudang.index');
  }

  public function done($id)
  {
    try {
      $sample_in = SampleIn::findOrFail($id);
      \Gate::authorize('done', $sample_in);
      $sample_in->status_sample = StatusSample::DONE;
      $sample_in->save();

      return redirect()->route('gudang.sample-in.show', ['id' => $sample_in->id]);
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());

    }
  }

  public function show(int $id)
  {
    $sample_in = SampleIn::findOrFail($id);
    if ($sample_in->status_sample === StatusSample::NEW) {
      abort(403);
    }

    $sample_in_details = SampleInDetail::with(['sampleBarang'])->where('sample_in_id', $id)->orderByDesc('id')->get();

    $returs = BarangSampleRetur::whereHasMorph(
      'returnable',
      [SampleInDetail::class],
      function ($q) use ($id) {
        $q->where('sample_in_id', $id);
      }
    )
      ->with([
        'sampleBarang.barang.brand:id,nama',
        'sampleBarang:barang_id,satuan',
        'returnable' => function ($q) {
          $q->select('id', 'sample_in_id', 'batch', 'tgl_expired', 'barang_id');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.sample-in.gudang.show', compact('sample_in', 'sample_in_details', 'returs'));
  }

  public function updateKeterangan(Request $request, $id)
  {
    $request->validate([
      'keterangan' => 'required|string|max:255',
    ]);

    $retur = BarangSampleRetur::findOrFail($id);
    $retur->keterangan = $request->keterangan;
    $retur->save();

    return redirect()->back()->with('success', 'Keterangan berhasil diperbarui.');
  }

}
