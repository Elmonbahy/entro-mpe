<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusBarangMasuk;
use App\Http\Controllers\Controller;
use App\Models\BarangSampleRetur;
use App\Models\BarangSampleStock;
use App\Models\SampleIn;
use App\Models\SampleInDetail;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SampleInDetailController extends Controller
{
  public function retur(int $sample_in_id, int $sample_in_detail_id)
  {
    $sample_in_detail = SampleInDetail::where('id', $sample_in_detail_id)
      ->where('sample_in_id', $sample_in_id)
      ->with(['samplein'])
      ->firstOrFail();

    return view('pages.sample-in.gudang.retur-item', compact('sample_in_detail'));
  }

  public function returDone(int $id)
  {
    $barang_retur = BarangSampleRetur::findOrFail($id);
    $sample_in_detail = SampleInDetail::findOrFail($barang_retur->returnable_id);

    try {
      DB::transaction(function () use ($sample_in_detail, $barang_retur) {
        $barang_retur->diganti_at = now();
        $barang_retur->save();

        if ($sample_in_detail->status_barang_masuk === StatusBarangMasuk::LENGKAP) {
          abort(403);
        }

        $barang_stock = BarangSampleStock::where('barang_id', $sample_in_detail->barang_id)
          ->where('batch', $sample_in_detail->batch)
          ->where('tgl_expired', $sample_in_detail->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;
        $barang_stock->jumlah_stock += $barang_retur->jumlah_barang_retur;
        $barang_stock->save();

        $sample_in_detail->jumlah_barang_masuk += $barang_retur->jumlah_barang_retur;
        $sample_in_detail->status_barang_masuk = StatusBarangMasuk::LENGKAP;
        $sample_in_detail->save();

        $sample_in_detail->samplemutation()->create([
          'stock_awal' => $stock_awal,
          'stock_masuk' => $barang_retur->jumlah_barang_retur,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      });

      return back()->with('success', 'Jumlah barang sampel masuk berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }
  public function returUpdate(int $sample_in_id, int $sample_in_detail_id, Request $request)
  {
    $request->validate([
      'jumlah_barang_retur' => 'required|numeric|gt:0',
      'keterangan' => 'required|string',
      'jenis_retur' => 'required|in:0,1',
    ]);

    $sample_in_detail = SampleInDetail::where('id', $sample_in_detail_id)
      ->where('sample_in_id', $sample_in_id)
      ->with('samplein')
      ->firstOrFail();

    \Gate::authorize('retur', $sample_in_detail);

    try {
      DB::transaction(function () use ($sample_in_detail, $request) {
        $jumlah_barang_retur = (int) $request->jumlah_barang_retur;
        $keterangan = $request->keterangan ?: null;
        $is_diganti = (bool) $request->jenis_retur;

        if ($jumlah_barang_retur > $sample_in_detail->jumlah_barang_masuk) {
          throw new \Exception('Gagal! Melebihi jumlah barang masuk.');
        }

        $barang_stock = BarangSampleStock::where('barang_id', $sample_in_detail->barang_id)
          ->where('batch', $sample_in_detail->batch)
          ->where('tgl_expired', $sample_in_detail->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;
        $barang_stock->jumlah_stock -= $jumlah_barang_retur;
        if ($barang_stock->jumlah_stock < 0) {
          throw new \Exception('Gagal! Stock tidak cukup.');
        }

        $barang_stock->save();

        if ($is_diganti) {
          $sample_in_detail->jumlah_barang_masuk -= $jumlah_barang_retur;
          $sample_in_detail->status_barang_masuk = StatusBarangMasuk::BELUM_LENGKAP;
          $sample_in_detail->save();
        } else {
          $sample_in_detail->jumlah_barang_masuk -= $jumlah_barang_retur;
          $sample_in_detail->jumlah_barang_dipesan -= $jumlah_barang_retur;
          $sample_in_detail->save();
        }

        $sample_in_detail->samplemutation()->create([
          'stock_awal' => $stock_awal,
          'stock_retur_masuk' => $jumlah_barang_retur,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);

        $sample_in_detail->returs()->create([
          'jumlah_barang_retur' => $jumlah_barang_retur,
          'keterangan' => $keterangan,
          'is_diganti' => $is_diganti,
          'barang_id' => $sample_in_detail->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired
        ]);
      });

      return redirect()->route('gudang.sample-in.show', $sample_in_id)->with('success', 'Jumlah barang sampel masuk berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  public function stock(int $sample_in_id, int $sample_in_detail_id)
  {
    $sample_in_detail = SampleInDetail::where('id', $sample_in_detail_id)
      ->where('sample_in_id', $sample_in_id)
      ->with(['samplein'])
      ->firstOrFail();

    \Gate::authorize('stock', $sample_in_detail);

    return view('pages.sample-in.gudang.item.stock-item', compact('sample_in_detail'));
  }

  /**
   * Tentukan status barang masuk
   */
  private function determineStatusBarangMasuk(SampleInDetail $sample_in_detail)
  {
    if ((int) $sample_in_detail->jumlah_barang_masuk === (int) $sample_in_detail->jumlah_barang_dipesan) {
      return StatusBarangMasuk::LENGKAP;
    }

    return StatusBarangMasuk::BELUM_LENGKAP;
  }

  public function stockUpdate(int $sample_in_id, int $sample_in_detail_id, Request $request)
  {
    $request->validate([
      'jumlah_barang_masuk' => 'required|numeric|gt:0',
      'batch' => 'required|string',
      'tgl_expired' => 'nullable|date|after_or_equal:today',
    ]);

    $sample_in_detail = SampleInDetail::where('id', $sample_in_detail_id)
      ->where('sample_in_id', $sample_in_id)
      ->with('samplein')
      ->firstOrFail();

    \Gate::authorize('stock', $sample_in_detail);

    DB::beginTransaction();
    try {
      if ($request->jumlah_barang_masuk < $sample_in_detail->jumlah_barang_masuk) {
        throw new \Exception('Gagal! Tidak bisa mengurangi jumlah barang masuk.');
      }

      $masuk_diff = $request->jumlah_barang_masuk - $sample_in_detail->jumlah_barang_masuk;
      $remaining_masuk = $sample_in_detail->jumlah_barang_dipesan - $sample_in_detail->jumlah_barang_masuk;

      if (
        ($request->batch !== $sample_in_detail->batch || $request->tgl_expired !== $sample_in_detail->tgl_expired)
        && $request->jumlah_barang_masuk > 0
      ) {
        $remaining_masuk = $sample_in_detail->jumlah_barang_dipesan - $sample_in_detail->jumlah_barang_masuk;

        if ($request->jumlah_barang_masuk > $remaining_masuk) {
          throw new \Exception('Jumlah masuk melebihi jumlah dipesan.');
        }

        // Kurangi dari batch lama
        $sample_in_detail->jumlah_barang_dipesan -= $request->jumlah_barang_masuk;

        // Jika jumlah_barang_dipesan dan jumlah_barang_masuk sudah nol → hapus record lama
        if ($sample_in_detail->jumlah_barang_dipesan <= 0 && $sample_in_detail->jumlah_barang_masuk == 0) {
          $sample_in_detail->delete();
        } else {
          $sample_in_detail->save(); // tetap simpan kalau tidak nol
        }

        // Buat batch baru
        $newDetail = $sample_in_detail->replicate();
        $newDetail->batch = $request->batch;
        $newDetail->tgl_expired = $request->tgl_expired;
        $newDetail->jumlah_barang_masuk = $request->jumlah_barang_masuk;
        $newDetail->jumlah_barang_dipesan = $request->jumlah_barang_masuk;
        $newDetail->status_barang_masuk = $this->determineStatusBarangMasuk($newDetail);
        $newDetail->save();

        // Update stok
        $stock = BarangSampleStock::firstOrNew([
          'barang_id' => $newDetail->barang_id,
          'batch' => $newDetail->batch,
          'tgl_expired' => $newDetail->tgl_expired,
        ]);

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock += $request->jumlah_barang_masuk;
        $stock->save();

        $newDetail->samplemutation()->create([
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
        $sample_in_detail->jumlah_barang_masuk = $request->jumlah_barang_masuk;
        $sample_in_detail->status_barang_masuk = $this->determineStatusBarangMasuk($sample_in_detail);
        $sample_in_detail->save();

        $stock = BarangSampleStock::firstOrNew([
          'barang_id' => $sample_in_detail->barang_id,
          'batch' => $sample_in_detail->batch,
          'tgl_expired' => $sample_in_detail->tgl_expired,
        ]);

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock += $masuk_diff;
        $stock->save();

        $sample_in_detail->samplemutation()->create([
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
      return redirect()->route('gudang.sample-in.show', $sample_in_id)->with('success', 'Jumlah barang masuk berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

}
