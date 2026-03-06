<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusBarangMasuk;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\BarangStock;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BeliDetailController extends Controller
{
  public function retur(int $beli_id, int $beli_detail_id)
  {
    $beli_detail = BeliDetail::where('id', $beli_detail_id)
      ->where('beli_id', $beli_id)
      ->with(['beli'])
      ->firstOrFail();

    return view('pages.beli.gudang.retur-item', compact('beli_detail'));
  }

  public function returDone(int $id)
  {
    $barang_retur = BarangRetur::findOrFail($id);
    $beli_detail = BeliDetail::findOrFail($barang_retur->returnable_id);

    try {
      DB::transaction(function () use ($beli_detail, $barang_retur) {
        $barang_retur->diganti_at = now();
        $barang_retur->save();

        if ($beli_detail->status_barang_masuk === StatusBarangMasuk::LENGKAP) {
          abort(403);
        }

        $barang_stock = BarangStock::where('barang_id', $beli_detail->barang_id)
          ->where('batch', $beli_detail->batch)
          ->where('tgl_expired', $beli_detail->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;
        $barang_stock->jumlah_stock += $barang_retur->jumlah_barang_retur;
        $barang_stock->save();

        $beli_detail->jumlah_barang_masuk += $barang_retur->jumlah_barang_retur;
        $beli_detail->status_barang_masuk = StatusBarangMasuk::LENGKAP;
        $beli_detail->save();

        $beli_detail->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_masuk' => $barang_retur->jumlah_barang_retur,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      });

      return back()->with('success', 'Jumlah barang masuk berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }
  public function returUpdate(int $beli_id, int $beli_detail_id, Request $request)
  {
    $request->validate([
      'jumlah_barang_retur' => 'required|numeric|gt:0',
      'keterangan' => 'required|string',
      'jenis_retur' => 'required|in:0,1',
    ]);

    $beli_detail = BeliDetail::where('id', $beli_detail_id)
      ->where('beli_id', $beli_id)
      ->with('beli')
      ->firstOrFail();

    \Gate::authorize('retur', $beli_detail);

    try {
      DB::transaction(function () use ($beli_detail, $request) {
        $jumlah_barang_retur = (int) $request->jumlah_barang_retur;
        $keterangan = $request->keterangan ?: null;
        $is_diganti = (bool) $request->jenis_retur;

        if ($jumlah_barang_retur > $beli_detail->jumlah_barang_masuk) {
          throw new \Exception('Gagal! Melebihi jumlah barang masuk.');
        }

        $barang_stock = BarangStock::where('barang_id', $beli_detail->barang_id)
          ->where('batch', $beli_detail->batch)
          ->where('tgl_expired', $beli_detail->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;
        $barang_stock->jumlah_stock -= $jumlah_barang_retur;
        if ($barang_stock->jumlah_stock < 0) {
          throw new \Exception('Gagal! Stock tidak cukup.');
        }

        $barang_stock->save();

        if ($is_diganti) {
          $beli_detail->jumlah_barang_masuk -= $jumlah_barang_retur;
          $beli_detail->status_barang_masuk = StatusBarangMasuk::BELUM_LENGKAP;
          $beli_detail->save();
        } else {
          $beli_detail->jumlah_barang_masuk -= $jumlah_barang_retur;
          $beli_detail->jumlah_barang_dipesan -= $jumlah_barang_retur;
          $beli_detail->save();
        }

        $beli_detail->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_retur_beli' => $jumlah_barang_retur,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);

        $beli_detail->returs()->create([
          'jumlah_barang_retur' => $jumlah_barang_retur,
          'keterangan' => $keterangan,
          'is_diganti' => $is_diganti,
          'barang_id' => $beli_detail->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired
        ]);
      });

      return redirect()->route('gudang.beli.show', $beli_id)->with('success', 'Jumlah barang masuk berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  public function stock(int $beli_id, int $beli_detail_id)
  {
    $beli_detail = BeliDetail::where('id', $beli_detail_id)
      ->where('beli_id', $beli_id)
      ->with(['beli'])
      ->firstOrFail();

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

    $beli_detail = BeliDetail::where('id', $beli_detail_id)
      ->where('beli_id', $beli_id)
      ->with('beli')
      ->firstOrFail();

    \Gate::authorize('stock', $beli_detail);

    DB::beginTransaction();
    try {
      if ($request->jumlah_barang_masuk < $beli_detail->jumlah_barang_masuk) {
        throw new \Exception('Gagal! Tidak bisa mengurangi jumlah barang masuk.');
      }

      $masuk_diff = $request->jumlah_barang_masuk - $beli_detail->jumlah_barang_masuk;
      $remaining_masuk = $beli_detail->jumlah_barang_dipesan - $beli_detail->jumlah_barang_masuk;

      if (
        ($request->batch !== $beli_detail->batch || $request->tgl_expired !== $beli_detail->tgl_expired)
        && $request->jumlah_barang_masuk > 0
      ) {
        $remaining_masuk = $beli_detail->jumlah_barang_dipesan - $beli_detail->jumlah_barang_masuk;

        if ($request->jumlah_barang_masuk > $remaining_masuk) {
          throw new \Exception('Jumlah masuk melebihi jumlah dipesan.');
        }

        // Kurangi dari batch lama
        $beli_detail->jumlah_barang_dipesan -= $request->jumlah_barang_masuk;

        // Jika jumlah_barang_dipesan dan jumlah_barang_masuk sudah nol → hapus record lama
        if ($beli_detail->jumlah_barang_dipesan <= 0 && $beli_detail->jumlah_barang_masuk == 0) {
          $beli_detail->delete();
        } else {
          $beli_detail->save(); // tetap simpan kalau tidak nol
        }

        // Buat batch baru
        $newDetail = $beli_detail->replicate();
        $newDetail->batch = $request->batch;
        $newDetail->tgl_expired = $request->tgl_expired;
        $newDetail->jumlah_barang_masuk = $request->jumlah_barang_masuk;
        $newDetail->jumlah_barang_dipesan = $request->jumlah_barang_masuk;
        $newDetail->status_barang_masuk = $this->determineStatusBarangMasuk($newDetail);
        $newDetail->save();

        // Update stok
        $stock = BarangStock::firstOrNew([
          'barang_id' => $newDetail->barang_id,
          'batch' => $newDetail->batch,
          'tgl_expired' => $newDetail->tgl_expired,
        ]);

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock += $request->jumlah_barang_masuk;
        $stock->save();

        $newDetail->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_masuk' => $request->jumlah_barang_masuk,
          'stock_akhir' => $stock->jumlah_stock,
          'barang_id' => $stock->barang_id,
          'batch' => $stock->batch,
          'tgl_expired' => $stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      } else {
        // Batch sama → update langsung
        $beli_detail->jumlah_barang_masuk = $request->jumlah_barang_masuk;
        $beli_detail->status_barang_masuk = $this->determineStatusBarangMasuk($beli_detail);
        $beli_detail->save();

        $stock = BarangStock::firstOrNew([
          'barang_id' => $beli_detail->barang_id,
          'batch' => $beli_detail->batch,
          'tgl_expired' => $beli_detail->tgl_expired,
        ]);

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock += $masuk_diff;
        $stock->save();

        $beli_detail->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_masuk' => $masuk_diff,
          'stock_akhir' => $stock->jumlah_stock,
          'barang_id' => $stock->barang_id,
          'batch' => $stock->batch,
          'tgl_expired' => $stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      }

      DB::commit();
      return redirect()->route('gudang.beli.show', $beli_id)->with('success', 'Jumlah barang masuk berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

}
