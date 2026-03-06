<?php

namespace App\Http\Controllers;

use App\Exports\PersediaanExport;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Brand;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;


class PersediaanController extends Controller
{
  private function getPersediaan(Request $request)
  {
    // Jika salah satu tanggal tidak diisi, langsung return collection kosong
    if (!$request->filled('tgl_awal') || !$request->filled('tgl_akhir')) {
      return collect(); // kosongkan data
    }

    $tglAwal = $request->tgl_awal;
    $tglAkhir = $request->tgl_akhir;

    $query = Barang::with([
      'brand:nama,id',
      'mutations' => function ($barang) use ($tglAwal, $tglAkhir) {
        $barang->whereBetween('tgl_mutation', [$tglAwal, $tglAkhir])
          ->orderBy('batch')
          ->orderBy('tgl_expired')
          ->orderByDesc('id');
      }
    ]);

    if ($request->brand_id) {
      $query = $query->where('brand_id', $request->brand_id);
    }

    if ($request->barang_id) {
      $query->where('id', $request->barang_id);
    }

    $data = $query->get()
      ->map(function ($barang) use ($tglAwal) {
        $mutasiPerBatchExpired = $barang->mutations
          ->groupBy(function ($item) {
            return $item->batch . '|' . $item->tgl_expired;
          })
          ->map(function ($grouped) {
            return $grouped->sortByDesc('tgl_mutation')->first();
          });
        $totalStockAkhir = $mutasiPerBatchExpired->sum('stock_akhir');

        $stokAwalRecords = \App\Models\BarangStockAwal::where('barang_id', $barang->id)
          ->where('tgl_stock', '<', $tglAwal)
          ->get();

        $totalStockAwal = $stokAwalRecords->sum('jumlah_stock');
        $totalNilaiStokAwal = $stokAwalRecords->sum(function ($item) {
          return $item->jumlah_stock * ($item->harga_beli ?? 0);
        });

        $totalStockMasuk = $barang->mutations
          ->where('mutationable_type', 'App\Models\BeliDetail')
          ->sum('stock_masuk');
        $totalReturBeli = $barang->mutations->sum('stock_retur_beli');
        $totalStockMasukSetelahRetur = $totalStockMasuk - $totalReturBeli;

        $totalnilaipembelian = $barang->mutations
          ->where('mutationable_type', \App\Models\BeliDetail::class)
          ->pluck('mutationable_id')
          ->unique()
          ->sum(function ($id) {
            $beliDetail = \App\Models\BeliDetail::find($id);
            return $beliDetail?->total ?? 0;
          });

        $jumlahStokGabungan = $totalStockAwal + $totalStockMasukSetelahRetur;
        $nilaiGabungan = $totalNilaiStokAwal + $totalnilaipembelian;

        $hpp_avg = $jumlahStokGabungan > 0
          ? $nilaiGabungan / $jumlahStokGabungan
          : ($barang->harga_beli ?? 0);

        $nilai_persedian = $hpp_avg * $totalStockAkhir;

        return [
          'barang_id' => $barang->id,
          'barang_nama' => $barang->nama,
          'satuan' => $barang->satuan,
          'brand' => $barang->brand->nama,
          'stock_akhir' => $totalStockAkhir,
          'total_stock_masuk' => $totalStockMasukSetelahRetur,
          'total_harga_beli' => $totalnilaipembelian,
          'hpp_avg' => $hpp_avg,
          'nilai_persedian' => $nilai_persedian,
          'totalNilaiStokAwal' => $totalNilaiStokAwal,
        ];
      });

    return $data;
  }

  public function index(Request $request)
  {
    $brands = Brand::select('nama', 'id')->get();
    $barangs = Barang::select('nama', 'id')->get();

    $request->validate([
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => 'nullable|date|after_or_equal:tgl_awal',
      'brand_id' => 'nullable|exists:brands,id',
      'barang_id' => 'nullable|exists:barangs,id'
    ]);

    $data = $this->getPersediaan($request);

    $totalNilaiPersediaan = $data->sum('nilai_persedian');

    return view('pages.persediaan.index', [
      'data' => $data,
      'brands' => $brands,
      'barangs' => $barangs,
      'barang_id' => $request->barang_id,
      'brand_id' => $request->brand_id,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'total_nilai_persediaan' => $totalNilaiPersediaan,
    ]);
  }

  public function exportExcel(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'required|date',
      'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      'brand_id' => 'nullable|exists:brands,id',
      'barang_id' => 'nullable|exists:barangs,id'
    ]);

    $data = $this->getPersediaan($request);

    $tglAwal = $request->tgl_awal
      ? Carbon::parse($request->tgl_awal)->format('d-M-Y')
      : null;

    $tglAkhir = $request->tgl_akhir
      ? Carbon::parse($request->tgl_akhir)->format('d-M-Y')
      : null;

    $filename = "laporan_persediaan_all $tglAwal - $tglAkhir.xlsx";

    if ($request->brand_id) {
      $filename = "laporan_persediaan_brand $tglAwal - $tglAkhir.xlsx";
    }

    if ($request->barang_id) {
      $filename = "laporan_persediaan_barang $tglAwal - $tglAkhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new PersediaanExport($data, $request->tglAwal, $request->tglAkhir),
      $filename
    );
  }
}
