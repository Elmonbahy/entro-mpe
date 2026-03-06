<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusSample;
use App\Http\Controllers\Controller;
use App\Models\BarangSampleRetur;
use App\Models\SampleOut;
use App\Models\SampleOutDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SampleOutController extends Controller
{
  public function index()
  {
    return view('pages.sample-out.gudang.index');
  }

  public function show(int $id)
  {
    $sample_out = SampleOut::with([
      'sampleoutDetails',
    ])->findOrFail($id);
    if ($sample_out->status_sample === StatusSample::NEW) {
      abort(403);
    }

    $sample_out_detail = SampleOutDetail::with(['sampleBarang'])->where('sample_out_id', $id)->orderByDesc('id')->get();

    $returs = BarangSampleRetur::whereHasMorph(
      'returnable',
      [SampleOutDetail::class],
      function ($q) use ($id) {
        $q->where('sample_out_id', $id);
      }
    )
      ->with([
        'sampleBarang.barang.brand:id,nama',
        'sampleBarang:barang_id,satuan',
        'returnable' => function ($q) {
          $q->select('id', 'sample_out_id', 'batch', 'tgl_expired', 'barang_id');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.sample-out.gudang.show', compact('sample_out', 'sample_out_detail', 'returs'));
  }


  public function done($id)
  {
    try {
      $sample_out = SampleOut::findOrFail($id);
      \Gate::authorize('done', $sample_out);
      $sample_out->status_sample = StatusSample::DONE;
      $sample_out->save();

      return redirect()->route('gudang.sample-out.show', ['id' => $sample_out->id]);
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());

    }
  }

  public function exportPdf(int $id)
  {
    $sample_out = SampleOut::findOrFail($id);
    $sample_out_detail = SampleOutDetail::where('sample_out_id', $id)
      ->with([
        'sampleBarang.barang.brand:id,nama',
        'sampleBarang:barang_id,satuan',
      ])->get();

    $pdf = Pdf::loadView('pages.spkb-sample.gudang.pdf', [
      'sample_out' => $sample_out,
      'sample_out_detail' => $sample_out_detail,
      'waktu_cetak' => now()->setTimezone('Asia/Makassar')->format('d/m/Y H:i')
    ]);

    $pdf->setPaper('legal');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'SPKB_' . $sample_out->nomor_sample . '_' . $sample_out->pelanggan->nama;
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
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
