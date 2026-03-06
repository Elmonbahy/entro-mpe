<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusBarangKeluar;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\BarangStock;
use App\Models\Jual;
use App\Models\JualDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class JualDetailController extends Controller
{
  public function retur(int $jual_id, int $jual_detail_id)
  {
    $jual_detail = JualDetail::where('id', $jual_detail_id)
      ->where('jual_id', $jual_id)
      ->with(['jual'])
      ->firstOrFail();

    return view('pages.jual.gudang.retur-item', compact('jual_detail'));
  }

  public function returDone(int $id)
  {
    $barang_retur = BarangRetur::findOrFail($id);
    $jual_detail = JualDetail::findOrFail($barang_retur->returnable_id);

    try {
      DB::transaction(function () use ($jual_detail, $barang_retur) {
        $barang_retur->diganti_at = now();
        $barang_retur->save();

        if ($jual_detail->status_barang_keluar === StatusBarangKeluar::LENGKAP) {
          abort(403);
        }

        $barang_stock = BarangStock::where('barang_id', $jual_detail->barang_id)
          ->where('batch', $jual_detail->batch)
          ->where('tgl_expired', $jual_detail->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;
        $barang_stock->jumlah_stock -= $barang_retur->jumlah_barang_retur;
        $barang_stock->save();

        $jual_detail->jumlah_barang_keluar += $barang_retur->jumlah_barang_retur;
        $jual_detail->status_barang_keluar = StatusBarangKeluar::LENGKAP;
        $jual_detail->save();

        $jual_detail->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_keluar' => $barang_retur->jumlah_barang_retur,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);
      });

      return back()->with('success', 'Jumlah barang keluar berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  public function returUpdate(int $jual_id, int $jual_detail_id, Request $request)
  {
    $request->validate([
      'jumlah_barang_retur' => 'required|numeric|gt:0',
      'keterangan' => 'required|string',
      'jenis_retur' => 'required|in:0,1',
    ]);

    $jual_detail = JualDetail::where('id', $jual_detail_id)
      ->where('jual_id', $jual_id)
      ->with('jual')
      ->firstOrFail();

    \Gate::authorize('retur', $jual_detail);

    try {
      DB::transaction(function () use ($jual_detail, $request) {
        $jumlah_barang_retur = (int) $request->jumlah_barang_retur;
        $keterangan = $request->keterangan ?: null;
        $is_diganti = (bool) $request->jenis_retur;

        if ($jumlah_barang_retur > $jual_detail->jumlah_barang_keluar) {
          throw new \Exception('Gagal! Melebihi jumlah barang keluar.');
        }

        $barang_stock = BarangStock::where('barang_id', $jual_detail->barang_id)
          ->where('batch', $jual_detail->batch)
          ->where('tgl_expired', $jual_detail->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;
        $barang_stock->jumlah_stock += $jumlah_barang_retur;
        $barang_stock->save();

        if ($is_diganti) {
          $jual_detail->jumlah_barang_keluar -= $jumlah_barang_retur;
          $jual_detail->status_barang_keluar = StatusBarangKeluar::BELUM_LENGKAP;
          $jual_detail->save();
        } else {
          $jual_detail->jumlah_barang_keluar -= $jumlah_barang_retur;
          $jual_detail->jumlah_barang_dipesan -= $jumlah_barang_retur;
          $jual_detail->save();
        }

        $jual_detail->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_retur_jual' => $jumlah_barang_retur,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::now(),
        ]);

        $jual_detail->returs()->create([
          'jumlah_barang_retur' => $jumlah_barang_retur,
          'keterangan' => $keterangan,
          'is_diganti' => $is_diganti,
          'barang_id' => $jual_detail->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired
        ]);
      });

      return redirect()->route('gudang.jual.show', $jual_id)->with('success', 'Jumlah barang keluar berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  public function stock(int $jual_id, int $jual_detail_id)
  {
    $jual_detail = JualDetail::where('id', $jual_detail_id)
      ->where('jual_id', $jual_id)
      ->with(['jual'])
      ->firstOrFail();

    \Gate::authorize('stock', $jual_detail);

    // Ambil batch untuk barang yang sesuai dan stok > 0
    $daftarBatch = BarangStock::where('barang_id', $jual_detail->barang_id)
      ->where('jumlah_stock', '>', 0)
      ->get(['batch', 'tgl_expired']);



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
      'batch' => 'nullable|string',
      'tgl_expired' => 'nullable|date',
    ]);

    $jual_detail = JualDetail::where('id', $jual_detail_id)
      ->where('jual_id', $jual_id)
      ->with('jual')
      ->firstOrFail();

    \Gate::authorize('stock', $jual_detail);

    DB::beginTransaction();
    try {
      $total_dipesan = $jual_detail->jumlah_barang_dipesan + $jual_detail->jumlah_barang_keluar;
      if ($request->jumlah_barang_keluar > $total_dipesan) {
        throw new \Exception('Gagal! Jumlah barang keluar melebihi jumlah yang dipesan.');
      }

      if ($request->jumlah_barang_keluar < $jual_detail->jumlah_barang_keluar) {
        throw new \Exception('Gagal! Tidak bisa mengurangi jumlah barang keluar.');
      }

      $keluar_diff = $request->jumlah_barang_keluar - $jual_detail->jumlah_barang_keluar;
      $remaining_keluar = $jual_detail->jumlah_barang_dipesan - $jual_detail->jumlah_barang_keluar;

      // kondisi split batch jika batch/expired berbeda atau jumlah keluar < jumlah dipesan
      $shouldSplit = (
        $request->batch !== $jual_detail->batch ||
        $request->tgl_expired !== $jual_detail->tgl_expired ||
        $request->jumlah_barang_keluar < $jual_detail->jumlah_barang_dipesan
      );

      if ($shouldSplit && $request->jumlah_barang_keluar > 0) {
        // tidak boleh melebihi jumlah dipesan yang tersisa
        if ($request->jumlah_barang_keluar > $remaining_keluar) {
          throw new \Exception('Gagal! Jumlah barang keluar melebihi jumlah yang dipesan.');
        }

        // Kurangi jumlah_barang_dipesan dari detail lama
        $jual_detail->jumlah_barang_dipesan -= $request->jumlah_barang_keluar;

        if ($jual_detail->jumlah_barang_dipesan <= 0 && $jual_detail->jumlah_barang_keluar == 0) {
          $jual_detail->delete(); // hapus record kosong
        } else {
          $jual_detail->save();
        }

        // Duplikasi detail untuk batch baru
        $newDetail = $jual_detail->replicate();
        $newDetail->batch = $request->batch;
        $newDetail->tgl_expired = $request->tgl_expired;
        $newDetail->jumlah_barang_keluar = $request->jumlah_barang_keluar;
        $newDetail->jumlah_barang_dipesan = $request->jumlah_barang_keluar;
        $newDetail->status_barang_keluar = $this->determineStatusBarangKeluar($newDetail);
        $newDetail->save();

        // Update stok untuk batch baru
        $stock = BarangStock::firstOrNew([
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

        $newDetail->mutation()->create([
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
        $jual_detail->jumlah_barang_keluar = $request->jumlah_barang_keluar;
        $jual_detail->status_barang_keluar = $this->determineStatusBarangKeluar($jual_detail);
        $jual_detail->save();

        $stock = BarangStock::firstOrNew([
          'barang_id' => $jual_detail->barang_id,
          'batch' => $jual_detail->batch,
          'tgl_expired' => $jual_detail->tgl_expired,
        ]);

        if (!$stock || $stock->jumlah_stock < $keluar_diff) {
          throw new \Exception('Gagal! Stock tidak cukup atau tidak ditemukan.');
        }

        $stock_awal = (int) $stock->jumlah_stock;
        $stock->jumlah_stock -= $keluar_diff;
        $stock->save();

        $jual_detail->mutation()->create([
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
      return redirect()->route('gudang.jual.show', $jual_id)->with('success', 'Jumlah barang keluar berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withInput()->with('error', $e->getMessage());
    }
  }



}
