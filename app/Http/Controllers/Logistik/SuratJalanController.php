<?php

namespace App\Http\Controllers\Logistik;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
  public function index()
  {
    return view('pages.surat-jalan.logistik.index');
  }

  public function create()
  {
    $pelanggans = Pelanggan::select('nama', 'id')->get();
    $kendaraan = Kendaraan::select('nama', 'id')->get();

    return view('pages.surat-jalan.logistik.create', [
      'pelanggans' => $pelanggans,
      'kendaraans' => $kendaraan,
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'pelanggan' => 'required|exists:pelanggans,id',
      'kendaraan' => 'required|exists:kendaraans,id',
      'tgl_surat_jalan' => 'required|date',
    ]);

    $surat_jalan = SuratJalan::create([
      'nomor_surat_jalan' => SuratJalan::generateNomorSuratJalan(),
      'pelanggan_id' => $request->pelanggan,
      'kendaraan_id' => $request->kendaraan,
      'tgl_surat_jalan' => $request->tgl_surat_jalan
    ]);

    return redirect()->route('logistik.surat-jalan.add-item', ['id' => $surat_jalan->id]);
  }

  public function addItem(int $id)
  {
    $surat_jalan = SuratJalan::with(['pelanggan', 'kendaraan'])->findOrFail($id);

    return view('pages.surat-jalan.logistik.add-item', [
      'surat_jalan' => $surat_jalan,
    ]);
  }

  public function show(int $id)
  {
    $surat_jalan = SuratJalan::findOrFail($id);
    $surat_jalan_details = SuratJalanDetail::where('surat_jalan_id', $surat_jalan->id)
      ->with([
        'jual:juals.id,juals.nomor_faktur',
        'jualDetail:jual_details.id,jual_details.jumlah_barang_keluar',
        'barang:barangs.id,barangs.nama,barangs.satuan',
      ])
      ->get();

    return view('pages.surat-jalan.logistik.show', [
      'surat_jalan' => $surat_jalan,
      'surat_jalan_details' => $surat_jalan_details
    ]);
  }

  public function exportPdf(int $id)
  {
    $surat_jalan = SuratJalan::findOrFail($id);
    $surat_jalan_details = SuratJalanDetail::where('surat_jalan_id', $id)
      ->with(['jual:juals.nomor_faktur,juals.id',
        'barang:barangs.nama,barangs.id,barangs.satuan,barangs.nie',
        'jualDetail:jual_details.id,jual_details.batch,jual_details.tgl_expired'])->get();
    $fakturs = $surat_jalan_details->pluck('jual.nomor_faktur')->unique()->join(', ');

    $pdf = Pdf::loadView('pages.surat-jalan.logistik.pdf', [
      'surat_jalan' => $surat_jalan,
      'fakturs' => $fakturs,
      'surat_jalan_details' => $surat_jalan_details
    ]);
    $pdf->setPaper('legal');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);
    $pdf->setOption('debugLayoutBlocks', true);
    $filename = 'Surat_Jalan' . $id . '_' . now()->format('dmY') . '.pdf';

    return $pdf->stream($filename);

    // return view('pages.surat-jalan.logistik.pdf', [
    //   'surat_jalan' => $surat_jalan,
    //   'fakturs' => ''
    // ]);
  }
}
