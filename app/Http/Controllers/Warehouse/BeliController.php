<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BeliController extends Controller
{
  public function index()
  {
    return view('pages.beli.warehouse.index');
  }

  public function show(int $id)
  {
    $beli = Beli::findOrFail($id);
    if ($beli->status_faktur === StatusFaktur::NEW) {
      abort(403);
    }

    $beli_details = BeliDetail::with(['barang'])->where('beli_id', $id)->orderBy('id')->get();

    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [BeliDetail::class],
      function ($q) use ($id) {
        $q->where('beli_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id,nie',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'beli_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.beli.warehouse.show', compact('beli', 'beli_details', 'returs'));
  }

  public function exportDo(int $id)
  {
    $beli = Beli::findOrFail($id);
    $beli_detail = BeliDetail::where('beli_id', $id)
      ->with([
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.kode,barangs.brand_id,barangs.nie',
        'barang.brand:id,nama'
      ])->get();

    $pdf = Pdf::loadView('pages.do-masuk.warehouse.pdf', [
      'beli' => $beli,
      'beli_detail' => $beli_detail,
    ]);
    $pdf->setPaper('A4');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'DO_MASUK_' . $beli->nomor_faktur . '_' . $beli->supplier->nama;
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }

  public function exportRetur(int $id)
  {
    $beli = Beli::findOrFail($id);
    $beli_detail = BeliDetail::where('beli_id', $id)
      ->with([
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.kode,barangs.brand_id,barangs.nie',
        'barang.brand:id,nama'
      ])->get();
    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [BeliDetail::class],
      function ($q) use ($id) {
        $q->where('beli_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id,nie',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'beli_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    if ($returs->isEmpty()) {
      return redirect()->back()->with('error', 'Tidak ada data retur untuk transaksi ini.');
    }

    $pdf = Pdf::loadView('pages.form-retur-beli.warehouse.pdf', [
      'beli' => $beli,
      'beli_detail' => $beli_detail,
      'returs' => $returs,
    ]);
    $pdf->setPaper('A4');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'FORM_RETUR_' . $beli->nomor_faktur . '_' . $beli->supplier->nama;
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }

  public function updateNie(Request $request, $id)
  {
    $request->validate([
      'nie' => 'nullable|string|max:255',
    ]);

    $beliDetail = BeliDetail::findOrFail($id);

    if ($beliDetail->barang) {
      $beliDetail->barang->nie = $request->nie;
      $beliDetail->barang->save();
    }

    return back()->with('success', 'Nomor izin edar berhasil diperbarui.');
  }

}
