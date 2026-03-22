<?php

namespace App\Http\Controllers\Fakturis;

use App\Enums\StatusRetur;
use App\Http\Controllers\Controller;
use App\Models\BarangStock;
use App\Models\BeliDetail;
use App\Models\JualDetail;
use Auth;
use Illuminate\Http\Request;

class ReturController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'returnable_id' => 'required|integer',
      'returnable_type' => 'required|string', // 'App\Models\JualDetail' atau 'App\Models\BeliDetail'
      'jumlah_barang_retur' => 'required|numeric|gt:0',
      'keterangan' => 'nullable|string|max:255',
      'jenis_retur' => 'required|boolean', // 0: Tidak Diganti, 1: Diganti
    ]);

    try {
      // Tentukan Model Parent (JualDetail / BeliDetail)
      $modelClass = $request->returnable_type;
      $detail = $modelClass::findOrFail($request->returnable_id);

      // HITUNG RIWAYAT RETUR SEBELUMNYA (Approved + Pending)
      // Abaikan yang Rejected, karena yang ditolak bisa diajukan ulang.
      $totalSudahDiretur = $detail->returs()
        ->where('status', '!=', StatusRetur::REJECTED)
        ->sum('jumlah_barang_retur');

      $potensiTotalRetur = $totalSudahDiretur + $request->jumlah_barang_retur;

      // Logika Jual (Barang Keluar)
      if ($detail instanceof JualDetail) {
        // Cek progress gudang
        if ($detail->jumlah_barang_keluar <= 0) {
          throw new \Exception('Gagal! Gudang belum mengeluarkan barang ini.');
        }

        // Cek Limit: (History + Request Baru) > Barang Keluar
        if ($potensiTotalRetur > $detail->jumlah_barang_keluar) {
          $sisaBisaDiretur = $detail->jumlah_barang_keluar - $totalSudahDiretur;
          throw new \Exception("Gagal! Total retur melebihi jumlah barang keluar. Sisa yang bisa diretur: {$sisaBisaDiretur}");
        }
      }
      // Logika Beli (Barang Masuk)
      elseif ($detail instanceof BeliDetail) {
        // Cek progress gudang
        if ($detail->jumlah_barang_masuk <= 0) {
          throw new \Exception('Gagal! Gudang belum menerima barang ini.');
        }

        // Cek Limit: (History + Request Baru) > Barang Masuk
        if ($potensiTotalRetur > $detail->jumlah_barang_masuk) {
          $sisaBisaDiretur = $detail->jumlah_barang_masuk - $totalSudahDiretur;
          throw new \Exception("Gagal! Total retur melebihi jumlah barang masuk. Sisa yang bisa diretur: {$sisaBisaDiretur}");
        }

        // Cek Stok Fisik (Hanya cek request baru vs stok saat ini)
        $stokTersedia = BarangStock::where('barang_id', $detail->barang_id)
          ->where('batch', $detail->batch)
          ->where('tgl_expired', $detail->tgl_expired)
          ->value('jumlah_stock') ?? 0;

        if ($stokTersedia < $request->jumlah_barang_retur) {
          throw new \Exception("Gagal! Stok tidak mencukupi, stok di gudang tersedia: ({$stokTersedia}).");
        }
      }

      $detail->returs()->create([
        'barang_id' => $detail->barang_id,
        'jumlah_barang_retur' => $request->jumlah_barang_retur,
        'keterangan' => $request->keterangan,
        'is_diganti' => $request->jenis_retur,
        'status' => StatusRetur::PENDING,
      ]);

      return back()->with('success', 'Pengajuan retur berhasil dikirim ke Gudang.');

    } catch (\Exception $e) {
      return back()->with('error', $e->getMessage());
    }
  }
}