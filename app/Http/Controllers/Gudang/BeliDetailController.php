<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusBarangMasuk;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\BarangStock;
use App\Models\Beli;
use App\Models\BeliDetail;
use Auth;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BeliDetailController extends Controller
{
  // Fitur ini sudah dipindah ke modul Retur terpisah
  public function retur(int $beli_id, int $beli_detail_id)
  {
    abort(403);
  }

  public function returDone(int $id)
  {
    abort(403);
  }
  public function returUpdate(int $beli_id, int $beli_detail_id, Request $request)
  {
    abort(403);
  }

  public function stock(int $beli_id, int $beli_detail_id)
  {
    $beli = Beli::findOrFail($beli_id);
    $beli_detail = $beli->beliDetails()->with('beli')->findOrFail($beli_detail_id);

    \Gate::authorize('stock', $beli_detail);

    return view('pages.beli.gudang.item.stock-item', compact('beli_detail'));
  }

  /**
   * Tentukan status barang masuk
   */
  private function determineStatusBarangMasuk(BeliDetail $beli_detail)
  {
    if ((int) $beli_detail->jumlah_barang_masuk === (int) $beli_detail->jumlah_barang_dipesan) {
      return StatusBarangMasuk::LENGKAP;
    }

    return StatusBarangMasuk::BELUM_LENGKAP;
  }

  public function stockUpdate(int $beli_id, int $beli_detail_id, Request $request)
  {
    $request->validate([
      'jumlah_barang_masuk' => 'required|numeric|gt:0',
      'batch' => 'required|string',
      'tgl_expired' => 'nullable|date|after_or_equal:today',
    ]);

    $beli = Beli::findOrFail($beli_id);
    $beli_detail = $beli->beliDetails()->with('beli')->findOrFail($beli_detail_id);

    \Gate::authorize('stock', $beli_detail);

    DB::beginTransaction();
    try {
      // 1. VALIDASI KONSISTENSI BATCH
      // Jika sudah ada barang keluar sebelumnya, Batch & Expired TIDAK BOLEH BERUBAH.
      if ($beli_detail->jumlah_barang_masuk > 0) {
        if ($beli_detail->batch !== $request->batch) {
          throw new \Exception("Gagal! Batch berubah. Item ini sudah tercatat menggunakan Batch: {$beli_detail->batch}. Tidak boleh mencampur batch dalam satu baris.");
        }
        // Normalisasi tanggal database
        $dbDate = $beli_detail->tgl_expired
          ? Carbon::parse($beli_detail->tgl_expired)->format('Y-m-d')
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
      $input_total_masuk = (int) $request->jumlah_barang_masuk;
      $current_total_masuk = (int) $beli_detail->jumlah_barang_masuk;
      $selisih = $input_total_masuk - $current_total_masuk;

      if ($selisih < 0) {
        throw new \Exception('Gagal! Gunakan fitur Retur untuk mengurangi/koreksi barang.');
      }

      if ($input_total_masuk > $beli_detail->jumlah_barang_dipesan) {
        throw new \Exception('Gagal! Melebihi pesanan.');
      }

      // 3. Update Detail
      $beli_detail->jumlah_barang_masuk = $input_total_masuk;
      $beli_detail->batch = $request->batch;
      $beli_detail->tgl_expired = $request->tgl_expired;
      $beli_detail->status_barang_masuk = $this->determineStatusBarangMasuk($beli_detail);
      $beli_detail->save();

      // 4. Update Stock & Mutasi
      if ($selisih > 0) {
        $stock = BarangStock::firstOrNew([
          'barang_id' => $beli_detail->barang_id,
          'batch' => $request->batch,
          'tgl_expired' => $request->tgl_expired,
        ]);

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock += $selisih;
        $stock->save();

        $beli_detail->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_masuk' => $selisih,
          'stock_akhir' => $stock->jumlah_stock,
          'barang_id' => $stock->barang_id,
          'batch' => $stock->batch,
          'tgl_expired' => $stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      }

      DB::commit();
      return redirect()->route('gudang.beli.show', $beli_id)->with('success', 'Barang masuk berhasil diupdate.');

    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  /**
   * Memecah BeliDetail yang memiliki sisa barang yang belum masuk
   * menjadi dua baris: satu baris LENGKAP dan satu baris BELUM_LENGKAP.
   * * @param int $beli_id
   * @param int $beli_detail_id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function splitRemainingStock(int $beli_id, int $beli_detail_id)
  {
    // 1. Ambil data Beli dan BeliDetail
    $beli = Beli::findOrFail($beli_id);
    $beli_detail = $beli->beliDetails()->findOrFail($beli_detail_id);

    // Otorisasi jika diperlukan, asumsi 'split' diizinkan
    // \Gate::authorize('split', $beli_detail); 

    DB::beginTransaction();
    try {
      $jumlah_masuk = (int) $beli_detail->jumlah_barang_masuk;
      $jumlah_dipesan = (int) $beli_detail->jumlah_barang_dipesan;

      // 2. Hitung sisa barang masuk (selisih)
      $selisih_belum_masuk = $jumlah_dipesan - $jumlah_masuk;

      // Jika tidak ada selisih, tidak perlu dipecah/split.
      if ($selisih_belum_masuk <= 0) {
        DB::rollBack();
        return back()->with('error', 'Item ini sudah lengkap atau jumlah masuk melebihi pesanan. Tidak perlu dipecah.');
      }

      if ($jumlah_masuk <= 0) {
        DB::rollBack();
        return back()->with('error', 'Belum ada barang masuk. Tidak perlu dipecah.');
      }

      // 3. Update BeliDetail yang lama:
      // - Ubah jumlah_barang_dipesan agar sama dengan jumlah_barang_masuk
      // - Set status_barang_masuk menjadi LENGKAP
      $beli_detail->jumlah_barang_dipesan = $jumlah_masuk;
      $beli_detail->status_barang_masuk = StatusBarangMasuk::LENGKAP;
      $beli_detail->save();

      // 4. Buat BeliDetail baru untuk sisa barang yang belum masuk
      $new_beli_detail = $beli_detail->replicate(); // Salin semua atribut

      // Atribut yang disesuaikan untuk baris baru (sisa barang)
      $new_beli_detail->jumlah_barang_dipesan = $selisih_belum_masuk;
      $new_beli_detail->jumlah_barang_masuk = 0; // Barang masuk di baris baru ini 0
      $new_beli_detail->batch = null; // Reset batch
      $new_beli_detail->tgl_expired = null; // Reset tgl_expired
      $new_beli_detail->status_barang_masuk = StatusBarangMasuk::BELUM_LENGKAP; // Set status BELUM_LENGKAP

      // Simpan baris baru (akan otomatis terkait ke Beli yang sama)
      $new_beli_detail->save();

      DB::commit();

      return redirect()->route('gudang.beli.show', $beli_id)->with('success', "Barang berhasil dipecah. Item lama LENGKAP ({$jumlah_masuk} unit). Item baru BELUM LENGKAP ({$selisih_belum_masuk} unit) dibuat.");

    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withInput()->with('error', 'Gagal memecah detail barang: ' . $e->getMessage());
    }
  }
}
