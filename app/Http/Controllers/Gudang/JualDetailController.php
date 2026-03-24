<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusBarangKeluar;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\BarangStock;
use App\Models\Jual;
use App\Models\JualDetail;
use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class JualDetailController extends Controller
{
  // Fitur ini sudah dipindah ke modul Retur terpisah
  public function retur(int $jual_id, int $jual_detail_id)
  {
    abort(403);
  }

  public function returDone(int $id)
  {
    abort(403);
  }

  public function returUpdate(int $jual_id, int $jual_detail_id, Request $request)
  {
    abort(403);
  }

  public function stock(int $jual_id, int $jual_detail_id)
  {
    $jual = Jual::findOrFail($jual_id);
    $jual_detail = $jual->jualDetails()->with('jual')->findOrFail($jual_detail_id);

    \Gate::authorize('stock', $jual_detail);

    // Ambil batch untuk barang yang sesuai dan stok > 0
    $daftarBatch = BarangStock::where('barang_id', $jual_detail->barang_id)
      ->where(function ($query) use ($jual_detail) {
        $query->where('jumlah_stock', '>', 0)
          ->orWhere('batch', $jual_detail->batch);
      })
      ->get(['batch', 'tgl_expired', 'jumlah_stock']);

    return view('pages.jual.gudang.item.stock-item', compact('jual_detail', 'daftarBatch'));
  }

  /**
   * Tentukan status barang keluar
   */
  private function determineStatusBarangKeluar(JualDetail $jual_detail)
  {
    if ((int) $jual_detail->jumlah_barang_keluar === (int) $jual_detail->jumlah_barang_dipesan) {
      return StatusBarangKeluar::LENGKAP;
    }

    return StatusBarangKeluar::BELUM_LENGKAP;
  }

  public function stockUpdate(int $jual_id, int $jual_detail_id, Request $request)
  {
    $request->validate([
      'jumlah_barang_keluar' => 'required|numeric|gt:0',
      'batch' => 'required|string',
      'tgl_expired' => 'nullable|date',
    ]);

    $jual = Jual::findOrFail($jual_id);
    $jual_detail = $jual->jualDetails()->with('jual')->findOrFail($jual_detail_id);

    \Gate::authorize('stock', $jual_detail);

    DB::beginTransaction();
    try {
      // 1. VALIDASI KONSISTENSI BATCH (PENTING)
      // Jika sudah ada barang keluar sebelumnya, Batch & Expired TIDAK BOLEH BERUBAH.
      if ($jual_detail->jumlah_barang_keluar > 0) {
        if ($jual_detail->batch !== $request->batch) {
          throw new \Exception("Gagal! Barang ini sebelumnya diambil dari Batch: {$jual_detail->batch}. Anda tidak boleh mencampur Batch dalam satu baris. Silakan nol-kan dulu jika ingin ganti Batch, atau minta Fakturis memecah item ini.");
        }
        // Opsional: Validasi Expired juga jika perlu ketat
        // Normalisasi tanggal database
        $dbDate = $jual_detail->tgl_expired
          ? Carbon::parse($jual_detail->tgl_expired)->format('Y-m-d')
          : null;

        // Normalisasi tanggal input user
        $reqDate = $request->tgl_expired
          ? Carbon::parse($request->tgl_expired)->format('Y-m-d')
          : null;

        if ($dbDate !== $reqDate) {
          // Tampilkan format tanggal yang mudah dibaca di pesan error
          $showDbDate = $dbDate ? Carbon::parse($dbDate)->format('d/m/Y') : 'Kosong';

          throw new \Exception("Gagal! Tanggal Expired berbeda dengan inputan sebelumnya ({$showDbDate}).");
        }
      }

      // 2. Hitung Selisih
      $input_total_keluar = (int) $request->jumlah_barang_keluar;
      $current_total_keluar = (int) $jual_detail->jumlah_barang_keluar;
      $selisih = $input_total_keluar - $current_total_keluar;

      if ($selisih < 0) {
        throw new \Exception('Gagal! Gunakan fitur Retur untuk mengurangi barang.');
      }

      if ($input_total_keluar > $jual_detail->jumlah_barang_dipesan) {
        throw new \Exception('Gagal! Melebihi jumlah dipesan.');
      }

      // 3. Update Detail
      // Kita aman melakukan update batch/expired di sini karena validasi di atas menjamin datanya sama (atau data awal masih 0)
      $jual_detail->jumlah_barang_keluar = $input_total_keluar;
      $jual_detail->batch = $request->batch;
      $jual_detail->tgl_expired = $request->tgl_expired;
      $jual_detail->status_barang_keluar = $this->determineStatusBarangKeluar($jual_detail);
      $jual_detail->save();

      // 4. Update Stock & Mutasi (Hanya Selisih)
      if ($selisih > 0) {
        $stock = BarangStock::where('barang_id', $jual_detail->barang_id)
          ->where('batch', $request->batch)
          ->where(function ($q) use ($request) {
            if (empty($request->tgl_expired)) {
              $q->whereNull('tgl_expired');
            } else {
              $q->whereDate('tgl_expired', $request->tgl_expired);
            }
          })
          ->first();

        if (!$stock || $stock->jumlah_stock < $selisih) {
          throw new \Exception("Stok Batch {$request->batch} tidak cukup! Sisa: " . ($stock?->jumlah_stock ?? 0));
        }

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock -= $selisih;
        $stock->save();

        $jual_detail->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_keluar' => $selisih,
          'stock_akhir' => $stock->jumlah_stock,
          'barang_id' => $stock->barang_id,
          'batch' => $stock->batch,
          'tgl_expired' => $stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      }

      DB::commit();
      return redirect()->route('gudang.jual.show', $jual_id)->with('success', 'Barang keluar berhasil diupdate.');

    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  /**
   * Memecah JualDetail yang memiliki sisa barang yang belum masuk
   * menjadi dua baris: satu baris LENGKAP dan satu baris BELUM_LENGKAP.
   * * @param int $bjual_id
   * @param int $jual_detail_id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function splitRemainingStock(int $jual_id, int $jual_detail_id)
  {
    // 1. Ambil data Jual dan JualDetail
    $jual = Jual::findOrFail($jual_id);
    $jual_detail = $jual->jualDetails()->findOrFail($jual_detail_id);

    // Otorisasi jika diperlukan, asumsi 'split' diizinkan
    // \Gate::authorize('split', $jual_detail); 

    DB::beginTransaction();
    try {
      $jumlah_keluar = (int) $jual_detail->jumlah_barang_keluar;
      $jumlah_dipesan = (int) $jual_detail->jumlah_barang_dipesan;

      // 2. Hitung sisa barang masuk (selisih)
      $selisih_belum_keluar = $jumlah_dipesan - $jumlah_keluar;

      // Jika tidak ada selisih, tidak perlu dipecah/split.
      if ($selisih_belum_keluar <= 0) {
        DB::rollBack();
        return back()->with('error', 'Item ini sudah lengkap atau jumlah masuk melebihi pesanan. Tidak perlu dipecah.');
      }

      if ($jumlah_keluar <= 0) {
        DB::rollBack();
        return back()->with('error', 'Belum ada barang masuk. Tidak perlu dipecah.');
      }

      // 3. Update JualDetail yang lama:
      // - Ubah jumlah_barang_dipesan agar sama dengan jumlah_barang_masuk
      // - Set status_barang_keluar menjadi LENGKAP
      $jual_detail->jumlah_barang_dipesan = $jumlah_keluar;
      $jual_detail->status_barang_keluar = StatusBarangKeluar::LENGKAP;
      $jual_detail->save();

      // 4. Buat JualDetail baru untuk sisa barang yang belum masuk
      $new_jual_detail = $jual_detail->replicate(); // Salin semua atribut

      // Atribut yang disesuaikan untuk baris baru (sisa barang)
      $new_jual_detail->jumlah_barang_dipesan = $selisih_belum_keluar;
      $new_jual_detail->jumlah_barang_keluar = 0; // Barang keluar di baris baru ini 0
      $new_jual_detail->batch = null; // Reset batch
      $new_jual_detail->tgl_expired = null; // Reset tgl_expired
      $new_jual_detail->status_barang_keluar = StatusBarangKeluar::BELUM_LENGKAP; // Set status BELUM_LENGKAP

      // Simpan baris baru (akan otomatis terkait ke Jual yang sama)
      $new_jual_detail->save();

      DB::commit();

      return redirect()->route('gudang.jual.show', $jual_id)->with('success', "Barang berhasil dipecah. Item lama LENGKAP ({$jumlah_keluar} unit). Item baru BELUM LENGKAP ({$selisih_belum_keluar} unit) dibuat.");

    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withInput()->with('error', 'Gagal memecah detail barang: ' . $e->getMessage());
    }
  }
}