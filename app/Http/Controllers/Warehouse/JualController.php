<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\Jual;
use App\Models\JualDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JualController extends Controller
{
  public function index()
  {
    return view('pages.jual.warehouse.index');
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

    $jual_detail = JualDetail::with(['barang'])->where('jual_id', $id)->orderBy('id')->get();

    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [JualDetail::class],
      function ($q) use ($id) {
        $q->where('jual_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id,nie',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'jual_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.jual.warehouse.show', compact('jual', 'jual_detail', 'returs'));
  }

  public function exportDo(int $id)
  {
    $jual = Jual::findOrFail($id);
    $jual_detail = JualDetail::where('jual_id', $id)
      ->with([
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.kode,barangs.brand_id,barangs.nie',
        'barang.brand:id,nama'
      ])->get();

    $nomor_surat_jalan = $jual->suratJalanDetails()

      ->with('suratJalan')
      ->get()
      ->pluck('suratJalan.nomor_surat_jalan')
      ->unique()
      ->implode(', ');

    $tanggal_surat_jalan = $jual->suratJalanDetails()
      ->with('suratJalan')
      ->get()
      ->pluck('suratJalan.tgl_surat_jalan')
      ->unique()
      ->map(fn($tgl) => Carbon::parse($tgl)->translatedFormat('d/m/Y'))
      ->implode(', ');

    $pdf = Pdf::loadView('pages.do-keluar.warehouse.pdf', [
      'jual' => $jual,
      'jual_detail' => $jual_detail,
      'nomor_surat_jalan' => $nomor_surat_jalan,
      'tanggal_surat_jalan' => $tanggal_surat_jalan,
    ]);
    $pdf->setPaper('A4');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'DO_KELUAR_' . $jual->nomor_faktur . '_' . $jual->pelanggan->nama;
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }

  public function exportRetur(int $id)
  {
    $jual = Jual::findOrFail($id);
    $jual_detail = JualDetail::where('jual_id', $id)
      ->with([
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.kode,barangs.brand_id,barangs.nie',
        'barang.brand:id,nama'
      ])->get();
    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [JualDetail::class],
      function ($q) use ($id) {
        $q->where('jual_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id,nie',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'jual_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    if ($returs->isEmpty()) {
      return redirect()->back()->with('error', 'Tidak ada data retur untuk transaksi ini.');
    }

    $pdf = Pdf::loadView('pages.form-retur-jual.warehouse.pdf', [
      'jual' => $jual,
      'jual_detail' => $jual_detail,
      'returs' => $returs,
    ]);
    $pdf->setPaper('A4');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'FORM_RETUR_' . $jual->nomor_faktur . '_' . $jual->pelanggan->nama;
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename) . '.pdf';

    return $pdf->stream($filename);
  }

  public function updateNie(Request $request, $id)
  {
    $request->validate([
      'nie' => 'nullable|string|max:255',
    ]);

    $jualDetail = JualDetail::findOrFail($id);

    if ($jualDetail->barang) {
      $jualDetail->barang->nie = $request->nie;
      $jualDetail->barang->save();
    }

    return back()->with('success', 'Nomor izin edar berhasil diperbarui.');
  }

}
