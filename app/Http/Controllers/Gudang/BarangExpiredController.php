<?php

namespace App\Http\Controllers\Gudang;

use App\Exports\Gudang\BarangExpiredExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangStock;
use Carbon\Carbon;

class BarangExpiredController extends Controller
{
  private function getBarangExpiredData(Request $request)
  {
    $query = BarangStock::with([
      'barang:id,kode,nama,satuan,brand_id',
      'barang.brand:nama,id'
    ])
      ->whereNotNull('tgl_expired')
      ->where('jumlah_stock', '>', 0);

    if ($request->tgl_akhir) {
      $query->whereDate('tgl_expired', '<=', Carbon::parse($request->tgl_akhir)->endOfDay());
    } else {
      // Default batas maksimal 6 bulan dari sekarang
      $query->whereDate('tgl_expired', '<=', now()->addMonths(6)->endOfDay());
    }

    return $query->get()->map(function ($item) {
      $tgl_exp = Carbon::parse($item->tgl_expired)->startOfDay();
      $now = now()->startOfDay();
      $diff = $now->diff($tgl_exp);

      $sisa_waktu = $now->greaterThan($tgl_exp)
        ? 'Kadaluarsa ' . $diff->m . ' bulan ' . $diff->d . ' hari lalu'
        : $diff->m . ' bulan ' . $diff->d . ' hari';

      return [
        'brand_nama' => $item->barang->brand->nama,
        'barang_nama' => $item->barang->nama,
        'satuan' => $item->barang->satuan,
        'batch' => $item->batch,
        'jumlah_stock' => $item->jumlah_stock,
        'tgl_expired' => $tgl_exp->format('d/m/Y'),
        'sisa_waktu' => $sisa_waktu,
      ];
    });
  }

  public function index(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => 'nullable|date|after_or_equal:tgl_awal',
    ]);

    $data = $this->getBarangExpiredData($request);

    return view('pages.barang-expired.index', [
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'data' => $data,
    ]);
  }

  public function exportExcel(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => 'nullable|date|after_or_equal:tgl_awal',
    ]);

    $data = $this->getBarangExpiredData($request);

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data barang expired tidak tersedia.');
    }

    $tgl_awal = $request->tgl_awal ? Carbon::parse($request->tgl_awal)->format('dmY') : now()->format('dmY');
    $tgl_akhir = $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->format('dmY') : now()->addMonths(3)->format('dmY');

    $filename = "laporan_barang_expired_{$tgl_awal}_{$tgl_akhir}.xlsx";

    return Excel::download(new BarangExpiredExport($data), $filename);
  }
}