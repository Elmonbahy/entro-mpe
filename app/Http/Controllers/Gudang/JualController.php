<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\Jual;
use App\Models\JualDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class JualController extends Controller
{
  public function index()
  {
    return view('pages.jual.gudang.index');
  }

  public function show(int $id)
  {
    $jual = Jual::with([
      'jualDetails',
      'suratJalanDetails.suratJalan.kendaraan',
    ])->findOrFail($id);
    if ($jual->status_faktur === StatusFaktur::NEW) {
      abort(403);
    }

    $jual_detail = JualDetail::with(['barang'])->where('jual_id', $id)->orderByDesc('id')->get();

    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [JualDetail::class],
      function ($q) use ($id) {
        $q->where('jual_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id,kode',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'jual_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.jual.gudang.show', compact('jual', 'jual_detail', 'returs'));
  }


  public function done($id)
  {
    try {
      $jual = Jual::findOrFail($id);
      \Gate::authorize('done', $jual);
      $jual->status_faktur = StatusFaktur::DONE;
      $jual->save();

      return redirect()->route('gudang.jual.show', ['id' => $jual->id]);
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());

    }
  }

  public function exportPdf(int $id)
  {
    $jual = Jual::findOrFail($id);
    $jual_detail = JualDetail::where('jual_id', $id)
      ->where('jumlah_barang_dipesan', '>', 0)
      ->with([
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.kode,barangs.brand_id,barangs.nie',
        'barang.brand:id,nama'
      ])->get();

    $pdf = Pdf::loadView('pages.spkb.gudang.pdf', [
      'jual' => $jual,
      'jual_detail' => $jual_detail,
      'waktu_cetak' => now()->setTimezone('Asia/Makassar')->format('d/m/Y H:i')
    ]);
    $pdf->setPaper('legal');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'SPKB_' . $jual->nomor_faktur . '_' . $jual->pelanggan->nama;
    $filename = str_replace(' ', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }

  public function updateKeterangan(Request $request, $id)
  {
    $request->validate([
      'keterangan' => 'required|string|max:255',
    ]);

    $retur = BarangRetur::findOrFail($id);
    $retur->keterangan = $request->keterangan;
    $retur->save();

    return redirect()->back()->with('success', 'Keterangan berhasil diperbarui.');
  }

}
