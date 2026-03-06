<?php

namespace App\Http\Controllers\Cetak;

use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\Jual;
use App\Models\Pelanggan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TitipFakturController extends Controller
{
  public function index()
  {
    return view('pages.titip-faktur.keuangan.index', [
      'pelanggans' => Pelanggan::select(['id', 'nama'])->orderBy('nama')->get(),
    ]);
  }

  private function validate(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'required|date',
      'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      'pelanggan' => 'required|exists:pelanggans,id',
      'status_cetak' => 'nullable|in:all,belum,sudah',
    ]);
  }

  private function getData(Request $request)
  {
    $juals = Jual::select('*')
      ->selectRaw('DATE_ADD(tgl_faktur, INTERVAL kredit DAY) AS tgl_jatuh_tempo')
      ->where('pelanggan_id', $request->pelanggan)
      ->where('status_bayar', StatusBayar::UNPAID)
      ->where('status_faktur', StatusFaktur::DONE)
      ->whereBetween('tgl_faktur', [$request->tgl_awal, $request->tgl_akhir])
      ->has('jualDetails');

    // Filter status cetak titip faktur
    if ($request->status_cetak === 'belum') {
      $juals->whereNull('cetak_titip_faktur_at');
    } elseif ($request->status_cetak === 'sudah') {
      $juals->whereNotNull('cetak_titip_faktur_at');
    }

    return $juals->get();
  }

  private function trackExporPDF($ids)
  {
    Jual::whereIn('id', $ids)
      ->whereNull('cetak_titip_faktur_at')
      ->update([
        'cetak_titip_faktur_at' => Carbon::now()
      ]);
  }

  public function show(Request $request)
  {
    $this->validate($request);
    $juals = $this->getData($request);

    return view('pages.titip-faktur.keuangan.index', [
      'pelanggans' => Pelanggan::select(['id', 'nama'])->orderBy('nama')->get(),
      'pelanggan' => $request->pelanggan,
      'juals' => $juals,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'status_cetak' => $request->status_cetak,
      'isPdf' => false
    ]);
  }

  public function exportPdf(Request $request)
  {
    $this->validate($request);

    if ($request->has('selected_ids')) {
      // Ambil hanya faktur yang dipilih
      $juals = Jual::whereIn('id', $request->selected_ids)
        ->select('*')
        ->selectRaw('DATE_ADD(tgl_faktur, INTERVAL kredit DAY) AS tgl_jatuh_tempo')
        ->get();
    } else {
      // Default: ambil sesuai filter
      $juals = $this->getData($request);
    }

    if ($juals->isEmpty()) {
      return redirect()->route('keuangan.titip-faktur.index')->with('error', 'Gagal! Data tidak tersedia!');
    }

    $pelanggan = Pelanggan::select('nama')->find($request->pelanggan);

    $pdf = Pdf::loadView('pages.titip-faktur.keuangan.pdf', [
      'pelanggan' => $pelanggan,
      'juals' => $juals,
      'status_cetak' => $request->status_cetak,
      'isPdf' => true
    ]);
    $pdf->setPaper('legal');

    $this->trackExporPDF($juals->pluck('id'));

    $filename = 'Titip_Faktur_' . $pelanggan->nama . '.pdf';
    return $pdf->stream($filename);
  }

}
