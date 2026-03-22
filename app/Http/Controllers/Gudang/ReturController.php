<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusRetur;
use App\Enums\StatusBarangKeluar;
use App\Enums\StatusBarangMasuk;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\BarangStock;
use App\Models\BeliDetail;
use App\Models\JualDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReturController extends Controller
{
  public function indexJual()
  {
    return view('pages.retur.gudang.jual');
  }

  public function indexBeli()
  {
    return view('pages.retur.gudang.beli');
  }

  /**
   * Menampilkan Halaman Detail Retur
   */
  public function show($id)
  {
    $retur = BarangRetur::with(['barang', 'returnable'])->findOrFail($id);

    $isAuthorized = false;

    if ($retur->returnable_type === JualDetail::class) {
      $isAuthorized = $retur->returnable->jual;
    } elseif ($retur->returnable_type === BeliDetail::class) {
      $isAuthorized = $retur->returnable->beli;
    }

    if (!$isAuthorized)
      abort(403, 'Akses ditolak.');

    return view('pages.retur.gudang.show', compact('retur'));
  }

  /**
   * Proses Approve / Reject
   */
  public function verify(Request $request, $id)
  {
    $request->validate([
      'action' => 'required|in:approve,reject',
      'keterangan_gudang' => 'required_if:action,reject|nullable|string',
    ]);

    $retur = BarangRetur::findOrFail($id);

    if ($retur->status !== StatusRetur::PENDING) {
      return back()->with('error', 'Data ini sudah diproses sebelumnya.');
    }

    DB::beginTransaction();
    try {
      if ($request->action === 'reject') {
        $retur->update([
          'status' => StatusRetur::REJECTED,
          'keterangan_gudang' => $request->keterangan_gudang,
          'verified_at' => now(),
        ]);
        DB::commit();
        return redirect()->route($retur->returnable_type === JualDetail::class ? 'gudang.retur.jual' : 'gudang.retur.beli')
          ->with('success', 'Pengajuan retur berhasil DITOLAK.');
      }

      // --- LOGIKA APPROVE (Eksekusi Stok) ---
      $parent = $retur->returnable;

      $barangStock = BarangStock::firstOrNew([
        'barang_id' => $retur->barang_id,
        'batch' => $parent->batch,
        'tgl_expired' => $parent->tgl_expired,
      ]);

      $stock_awal = (int) $barangStock->jumlah_stock;

      if ($parent instanceof JualDetail) {
        // JUAL: Stok Bertambah (Masuk Gudang)
        $barangStock->jumlah_stock += $retur->jumlah_barang_retur;

        $parent->jumlah_barang_keluar -= $retur->jumlah_barang_retur;

        if (!$retur->is_diganti) {
          $parent->jumlah_barang_dipesan -= $retur->jumlah_barang_retur;
        }

        if ($parent->jumlah_barang_keluar == $parent->jumlah_barang_dipesan) {
          $parent->status_barang_keluar = StatusBarangKeluar::LENGKAP;
        }

        if ($parent->jumlah_barang_keluar < $parent->jumlah_barang_dipesan) {
          $parent->status_barang_keluar = StatusBarangKeluar::BELUM_LENGKAP;
        }

        $parent->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_retur_jual' => $retur->jumlah_barang_retur,
          'stock_akhir' => $barangStock->jumlah_stock,
          'barang_id' => $retur->barang_id,
          'batch' => $parent->batch,
          'tgl_expired' => $parent->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);

      } elseif ($parent instanceof BeliDetail) {
        // BELI: Stok Berkurang (Keluar ke Supplier)
        if ($barangStock->jumlah_stock < $retur->jumlah_barang_retur) {
          throw new \Exception('Stok fisik di gudang tidak cukup untuk dikembalikan ke supplier.');
        }

        $barangStock->jumlah_stock -= $retur->jumlah_barang_retur;

        $parent->jumlah_barang_masuk -= $retur->jumlah_barang_retur;

        if (!$retur->is_diganti) {
          $parent->jumlah_barang_dipesan -= $retur->jumlah_barang_retur;
        }

        if ($parent->jumlah_barang_masuk == $parent->jumlah_barang_dipesan) {
          $parent->status_barang_masuk = StatusBarangMasuk::LENGKAP;
        }

        if ($parent->jumlah_barang_masuk < $parent->jumlah_barang_dipesan) {
          $parent->status_barang_masuk = StatusBarangMasuk::BELUM_LENGKAP;
        }

        $parent->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_retur_beli' => $retur->jumlah_barang_retur,
          'stock_akhir' => $barangStock->jumlah_stock,
          'barang_id' => $retur->barang_id,
          'batch' => $parent->batch,
          'tgl_expired' => $parent->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      }

      $barangStock->save();
      $parent->save();

      $retur->update([
        'status' => StatusRetur::APPROVED,
        'verified_at' => now(),
      ]);

      DB::commit();
      return redirect()->route($retur->returnable_type === JualDetail::class ? 'gudang.retur.jual' : 'gudang.retur.beli')
        ->with('success', 'Retur disetujui, Stok telah disesuaikan.');

    } catch (\Exception $e) {
      DB::rollBack();
      return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
    }
  }
}