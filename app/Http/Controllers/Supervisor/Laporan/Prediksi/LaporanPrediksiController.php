<?php

namespace App\Http\Controllers\Supervisor\Laporan\Prediksi;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Brand;
use App\Models\Mutation;
use App\Services\PredictionService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanPrediksiController extends Controller
{
  protected $predictionService;

  public function __construct(PredictionService $service)
  {
    $this->predictionService = $service;
  }

  private function getLaporanPrediksiData(Request $request)
  {
    $query = Barang::query()->with('stocks', 'brand');

    if ($request->barang_id) {
      $query->where('id', $request->barang_id);
    }

    if ($request->brand_id) {
      $query->where('brand_id', $request->brand_id);
    }

    $barangs = $query->get();

    return $barangs->map(function ($barang) {
      // Cek apakah ada mutasi
      $hasTransaction = $barang->mutations()->exists();

      if (!$hasTransaction) {
        return [
          'barang_nama' => $barang->nama,
          'satuan' => $barang->satuan,
          'stok_saat_ini' => $barang->stocks->sum('jumlah_stock'),
          'prediksi_keluar' => 0,
          'tren_data' => [],
          'labels' => [],
          'status' => 'DATA KOSONG', // Status khusus
          'saran_beli' => 0,
          'keterangan' => 'Belum ada riwayat transaksi'
        ];
      }

      $predictionResult = $this->predictionService->predictNextMonth($barang->id);
      $currentStock = $barang->stocks->sum('jumlah_stock');

      return [
        'barang_nama' => $barang->nama,
        'satuan' => $barang->satuan,
        'stok_saat_ini' => $currentStock,
        'prediksi_keluar' => $predictionResult['forecast'],
        'tren_data' => $predictionResult['history'],
        'labels' => $predictionResult['labels'],
        'status' => $currentStock < $predictionResult['forecast'] ? 'KRITIS' : 'AMAN',
        'saran_beli' => $currentStock < $predictionResult['forecast'] ? ($predictionResult['forecast'] - $currentStock) : 0,
        'keterangan' => null
      ];
    });
  }

  public function index(Request $request)
  {
    $listBarang = Barang::select('id', 'nama')->orderBy('nama')->get();
    $listBrand = Brand::select('id', 'nama')->orderBy('nama')->get();

    // UBAH INI: dari [] menjadi collect()
    $data = collect();

    if ($request->filled('barang_id') || $request->filled('brand_id')) {
      $data = $this->getLaporanPrediksiData($request);
    }

    return view('pages.laporan-prediksi.supervisor.index', [
      'listBarang' => $listBarang,
      'listBrand' => $listBrand,
      'barang_id' => $request->barang_id,
      'brand_id' => $request->brand_id,
      'data' => $data
    ]);
  }
}