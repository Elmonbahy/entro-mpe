<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusBarangKeluar;
use App\Http\Controllers\Controller;
use App\Models\BarangSampleRetur;
use App\Models\BarangSampleStock;
use App\Models\Jual;
use App\Models\SampleOutDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class SampleOutDetailController extends Controller
{
  public function retur(int $sample_out_id, int $sample_out_detail_id)
  {
    $sample_out_detail = SampleOutDetail::where('id', $sample_out_detail_id)
      ->where('sample_out_id', $sample_out_id)
      ->with(['sampleout'])
      ->firstOrFail();

    return view('pages.sample-out.gudang.retur-item', compact('sample_out_detail'));
  }

  public function returDone(int $id)
  {
    $barang_retur = BarangSampleRetur::findOrFail($id);
    $sample_out_detail = SampleOutDetail::findOrFail($barang_retur->returnable_id);

    try {
      DB::transaction(function () use ($sample_out_detail, $barang_retur) {
        $barang_retur->diganti_at = now();
        $barang_retur->save();

        if ($sample_out_detail->status_barang_keluar === StatusBarangKeluar::LENGKAP) {
          abort(403);
        }

        $barang_stock = BarangSampleStock::where('barang_id', $sample_out_detail->barang_id)
          ->where('batch', $sample_out_detail->batch)
          ->where('tgl_expired', $sample_out_detail->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;
        $barang_stock->jumlah_stock -= $barang_retur->jumlah_barang_retur;
        $barang_stock->save();

        $sample_out_detail->jumlah_barang_keluar += $barang_retur->jumlah_barang_retur;
        $sample_out_detail->status_barang_keluar = StatusBarangKeluar::LENGKAP;
        $sample_out_detail->save();

        $sample_out_detail->samplemutation()->create([
          'stock_awal' => $stock_awal,
          'stock_keluar' => $barang_retur->jumlah_barang_retur,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      });

      return back()->with('success', 'Jumlah barang sampel keluar berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  public function returUpdate(int $sample_out_id, int $sample_out_detail_id, Request $request)
  {
    $request->validate([
      'jumlah_barang_retur' => 'required|numeric|gt:0',
      'keterangan' => 'required|string',
      'jenis_retur' => 'required|in:0,1',
    ]);

    $sample_out_detail = SampleOutDetail::where('id', $sample_out_detail_id)
      ->where('sample_out_id', $sample_out_id)
      ->with('sampleout')
      ->firstOrFail();

    \Gate::authorize('retur', $sample_out_detail);

    try {
      DB::transaction(function () use ($sample_out_detail, $request) {
        $jumlah_barang_retur = (int) $request->jumlah_barang_retur;
        $keterangan = $request->keterangan ?: null;
        $is_diganti = (bool) $request->jenis_retur;

        if ($jumlah_barang_retur > $sample_out_detail->jumlah_barang_keluar) {
          throw new \Exception('Gagal! Melebihi jumlah barang keluar.');
        }

        $barang_stock = BarangSampleStock::where('barang_id', $sample_out_detail->barang_id)
          ->where('batch', $sample_out_detail->batch)
          ->where('tgl_expired', $sample_out_detail->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;
        $barang_stock->jumlah_stock += $jumlah_barang_retur;
        $barang_stock->save();

        if ($is_diganti) {
          $sample_out_detail->jumlah_barang_keluar -= $jumlah_barang_retur;
          $sample_out_detail->status_barang_keluar = StatusBarangKeluar::BELUM_LENGKAP;
          $sample_out_detail->save();
        } else {
          $sample_out_detail->jumlah_barang_keluar -= $jumlah_barang_retur;
          $sample_out_detail->jumlah_barang_dipesan -= $jumlah_barang_retur;
          $sample_out_detail->save();
        }

        $sample_out_detail->samplemutation()->create([
          'stock_awal' => $stock_awal,
          'stock_retur_keluar' => $jumlah_barang_retur,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);

        $sample_out_detail->returs()->create([
          'jumlah_barang_retur' => $jumlah_barang_retur,
          'keterangan' => $keterangan,
          'is_diganti' => $is_diganti,
          'barang_id' => $sample_out_detail->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired
        ]);
      });

      return redirect()->route('gudang.sample-out.show', $sample_out_id)->with('success', 'Jumlah barang keluar sampel berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  public function stock(int $sample_out_id, int $sample_out_detail_id)
  {
    $sample_out_detail = SampleOutDetail::where('id', $sample_out_detail_id)
      ->where('sample_out_id', $sample_out_id)
      ->with(['sampleout'])
      ->firstOrFail();

    \Gate::authorize('stock', $sample_out_detail);

    // Ambil batch untuk barang yang sesuai dan stok > 0
    $daftarBatch = BarangSampleStock::where('barang_id', $sample_out_detail->barang_id)
      ->where('jumlah_stock', '>', 0)
      ->get(['batch', 'tgl_expired']);



    return view('pages.sample-out.gudang.item.stock-item', compact('sample_out_detail', 'daftarBatch'));
  }

  /**
   * Tentukan status barang keluar
   */
  private function determineStatusBarangKeluar(SAmpleOutDetail $sample_out_detail)
  {
    if ((int) $sample_out_detail->jumlah_barang_keluar === (int) $sample_out_detail->jumlah_barang_dipesan) {
      return StatusBarangKeluar::LENGKAP;
    }

    return StatusBarangKeluar::BELUM_LENGKAP;
  }

  public function stockUpdate(int $sample_out_id, int $sample_out_detail_id, Request $request)
  {
    $request->validate([
      'jumlah_barang_keluar' => 'required|numeric|gt:0',
      'batch' => 'nullable|string',
      'tgl_expired' => 'nullable|date',
    ]);

    $sample_out_detail = SampleOutDetail::where('id', $sample_out_detail_id)
      ->where('sample_out_id', $sample_out_id)
      ->with('sampleout')
      ->firstOrFail();

    \Gate::authorize('stock', $sample_out_detail);

    DB::beginTransaction();
    try {
      $total_dipesan = $sample_out_detail->jumlah_barang_dipesan + $sample_out_detail->jumlah_barang_keluar;
      if ($request->jumlah_barang_keluar > $total_dipesan) {
        throw new \Exception('Gagal! Jumlah barang keluar melebihi jumlah yang dipesan.');
      }

      if ($request->jumlah_barang_keluar < $sample_out_detail->jumlah_barang_keluar) {
        throw new \Exception('Gagal! Tidak bisa mengurangi jumlah barang keluar.');
      }

      $keluar_diff = $request->jumlah_barang_keluar - $sample_out_detail->jumlah_barang_keluar;
      $remaining_keluar = $sample_out_detail->jumlah_barang_dipesan - $sample_out_detail->jumlah_barang_keluar;

      // kondisi split batch jika batch/expired berbeda atau jumlah keluar < jumlah dipesan
      $shouldSplit = (
        $request->batch !== $sample_out_detail->batch ||
        $request->tgl_expired !== $sample_out_detail->tgl_expired ||
        $request->jumlah_barang_keluar < $sample_out_detail->jumlah_barang_dipesan
      );

      if ($shouldSplit && $request->jumlah_barang_keluar > 0) {
        // tidak boleh melebihi jumlah dipesan yang tersisa
        if ($request->jumlah_barang_keluar > $remaining_keluar) {
          throw new \Exception('Gagal! Jumlah barang keluar melebihi jumlah yang dipesan.');
        }

        // Kurangi jumlah_barang_dipesan dari detail lama
        $sample_out_detail->jumlah_barang_dipesan -= $request->jumlah_barang_keluar;

        if ($sample_out_detail->jumlah_barang_dipesan <= 0 && $sample_out_detail->jumlah_barang_keluar == 0) {
          $sample_out_detail->delete(); // hapus record kosong
        } else {
          $sample_out_detail->save();
        }

        // Duplikasi detail untuk batch baru
        $newDetail = $sample_out_detail->replicate();
        $newDetail->batch = $request->batch;
        $newDetail->tgl_expired = $request->tgl_expired;
        $newDetail->jumlah_barang_keluar = $request->jumlah_barang_keluar;
        $newDetail->jumlah_barang_dipesan = $request->jumlah_barang_keluar;
        $newDetail->status_barang_keluar = $this->determineStatusBarangKeluar($newDetail);
        $newDetail->save();

        // Update stok untuk batch baru
        $stock = BarangSampleStock::firstOrNew([
          'barang_id' => $newDetail->barang_id,
          'batch' => $newDetail->batch,
          'tgl_expired' => $newDetail->tgl_expired,
        ]);

        if (!$stock || $stock->jumlah_stock < $request->jumlah_barang_keluar) {
          throw new \Exception('Gagal! Stock tidak cukup atau tidak ditemukan.');
        }

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock -= $request->jumlah_barang_keluar;
        $stock->save();

        $newDetail->samplemutation()->create([
          'stock_awal' => $stock_awal,
          'stock_keluar' => $request->jumlah_barang_keluar,
          'stock_akhir' => $stock->jumlah_stock,
          'barang_id' => $stock->barang_id,
          'batch' => $stock->batch,
          'tgl_expired' => $stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      } else {
        // Jika tidak perlu split (batch dan expired sama, jumlah sesuai)
        $sample_out_detail->jumlah_barang_keluar = $request->jumlah_barang_keluar;
        $sample_out_detail->status_barang_keluar = $this->determineStatusBarangKeluar($sample_out_detail);
        $sample_out_detail->save();

        $stock = BarangSampleStock::firstOrNew([
          'barang_id' => $sample_out_detail->barang_id,
          'batch' => $sample_out_detail->batch,
          'tgl_expired' => $sample_out_detail->tgl_expired,
        ]);

        if (!$stock || $stock->jumlah_stock < $keluar_diff) {
          throw new \Exception('Gagal! Stock tidak cukup atau tidak ditemukan.');
        }

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock -= $keluar_diff;
        $stock->save();

        $sample_out_detail->samplemutation()->create([
          'stock_awal' => $stock_awal,
          'stock_keluar' => $keluar_diff,
          'stock_akhir' => $stock->jumlah_stock,
          'barang_id' => $stock->barang_id,
          'batch' => $stock->batch,
          'tgl_expired' => $stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      }

      DB::commit();
      return redirect()->route('gudang.sample-out.show', $sample_out_id)->with('success', 'Jumlah barang keluar berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withInput()->with('error', $e->getMessage());
    }
  }



}
