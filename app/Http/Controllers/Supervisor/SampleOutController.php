<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\BarangSampleRetur;
use App\Models\SampleOut;
use App\Models\Salesman;
use App\Models\Pelanggan;
use App\Models\SampleOutDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SampleOutController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.sample-out.supervisor.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.sample-out.supervisor.create', [
      'pelanggans' => Pelanggan::select('id', 'nama')->get(),
      'salesmans' => Salesman::select('id', 'nama')->get(),
    ]);
  }

  public function edit(int $id)
  {
    $sample_out = SampleOut::findOrFail($id);

    \Gate::authorize('update', $sample_out);

    return view('pages.sample-out.supervisor.edit', [
      'sample_out' => $sample_out,
      'salesmans' => Salesman::select('id', 'nama')->get(),
      'pelanggans' => Pelanggan::select('id', 'nama')->get()
    ]);
  }

  private function validation(Request $request, $id = null)
  {
    $request->validate([
      'pelanggan' => $id ? 'nullable' : 'required|exists:pelanggans,id',
      'salesman' => 'required|exists:salesmen,id',
      'tanggal' => 'required|date',
      'keterangan' => 'nullable|string|max:255',
    ]);
  }

  public function update(Request $request, int $id)
  {
    $this->validation($request, $id);
    $sample_out = SampleOut::where('id', $id)->with('sampleoutDetails')->firstOrFail();

    \Gate::authorize('update', $sample_out);

    try {
      \DB::beginTransaction();

      $sample_out->update([
        'pelanggan_id' => $request->pelanggan,
        'salesman_id' => $request->salesman,
        'tanggal' => $request->tanggal,
        'keterangan' => $request->keterangan,
      ]);

      \DB::commit();

      return redirect()->route('supervisor.sample-out.index')->with('success', 'Berhasil mengubah data sampel keluar.');
    } catch (\Exception $e) {
      \DB::rollBack();
      return redirect()->back()->with('error', 'Gagal!' . $e->getMessage());
    }
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $this->validation($request);

    try {
      $sample_out = SampleOut::create([
        'nomor_sample' => SampleOut::generateNomorSample($request->tanggal),
        'tanggal' => $request->tanggal,
        'keterangan' => $request->keterangan,
        'pelanggan_id' => $request->pelanggan,
        'salesman_id' => $request->salesman,
      ]);

      return redirect()
        ->route('supervisor.sample-out.add-item', ['id' => $sample_out->id])
        ->with('success', 'Berhasil menambahkan data sampel keluar.');
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      return back()
        ->withInput()
        ->with('error', "Gagal! $msg");
    }
  }

  public function show(int $id)
  {
    $sample_out = SampleOut::with([
      'sampleoutDetails'
    ])->findOrFail($id);

    $sample_out_details = SampleOutDetail::where('sample_out_id', $id)->get();

    $returs = BarangSampleRetur::whereHasMorph(
      'returnable',
      [SampleOutDetail::class],
      function ($q) use ($id) {
        $q->where('sample_out_id', $id);
      }
    )
      ->with([
        'sampleBarang.barang.brand:id,nama',
        'sampleBarang:barang_id,satuan,barang_id',
        'returnable' => function ($q) {
          $q->select('id', 'sample_out_id', 'batch', 'tgl_expired', 'barang_id');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.sample-out.supervisor.show', [
      'sample_out' => $sample_out,
      'sample_out_details' => $sample_out_details,
      'returs' => $returs,
    ]);
  }

  public function addItem(int $id)
  {
    $sample_out = SampleOut::findOrFail($id);

    \Gate::authorize('update', $sample_out);

    return view('pages.sample-out.supervisor.add-item', compact('sample_out'));
  }


  public function exportSuratSample(int $id)
  {
    $sample_out = SampleOut::findOrFail($id);
    $sample_out_detail = SampleOutDetail::where('sample_out_id', $id)
      ->with([
        'sampleBarang.barang.brand:id,nama',
        'sampleBarang:barang_id,satuan,barang_id',
      ])->get();

    $pdf = Pdf::loadView('pages.surat-sample.supervisor.pdf', [
      'sample_out' => $sample_out,
      'sample_out_detail' => $sample_out_detail,
      'waktu_cetak' => now()->setTimezone('Asia/Makassar')->format('d/m/Y H:i')
    ]);

    $pdf->setPaper('legal');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'SPKB_SAMPEL_' . $sample_out->nomor_sample . '_' . $sample_out->pelanggan->nama;
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }
  public function exportSpkb(int $id)
  {
    $sample_out = SampleOut::findOrFail($id);
    $sample_out_detail = SampleOutDetail::where('sample_out_id', $id)
      ->with([
        'sampleBarang.barang.brand:id,nama',
        'sampleBarang:barang_id,satuan,barang_id',
      ])->get();

    $pdf = Pdf::loadView('pages.spkb-sample.supervisor.pdf', [
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

}
