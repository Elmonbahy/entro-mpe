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
      $hasTransaction = $barang->mutations()->exists();

      if (!$hasTransaction) {
        return [
          'barang_nama' => $barang->nama,
          'satuan' => $barang->satuan,
          'stok_saat_ini' => $barang->stocks->sum('jumlah_stock'),
          'prediksi_keluar' => 0,
          'tren_data' => [],
          'labels' => [],
          'status' => 'DATA KOSONG',
          'saran_beli' => 0,
          'mape' => 0, // Tambahan untuk data kosong
          'keterangan' => 'Belum ada riwayat transaksi'
        ];
      }

      $predictionResult = $this->predictionService->predictNextMonth($barang->id);
      $currentStock = $barang->stocks->sum('jumlah_stock');

      // === LOGIKA EVALUASI MAPE ===
      // $predictionResult['history'] berisi data riil: [3 bulan lalu, 2 bulan lalu, 1 bulan lalu]
      $aktualBulanLalu = $predictionResult['history'][2] ?? 0;

      // Buat simulasi prediksi bulan lalu menggunakan data 3 bulan lalu & 2 bulan lalu
      $bobotMpe = [0.571, 0.286]; // Bobot disesuaikan untuk 2 periode sebelumnya
      $simulasiPrediksiBulanLalu = ($bobotMpe[0] * ($predictionResult['history'][1] ?? 0)) +
        ($bobotMpe[1] * ($predictionResult['history'][0] ?? 0));
      $simulasiPrediksiBulanLalu = round($simulasiPrediksiBulanLalu);

      // Hitung nilai MAPE untuk barang ini
      $mape = 0;
      if ($aktualBulanLalu > 0) {
        $mape = (abs($aktualBulanLalu - $simulasiPrediksiBulanLalu) / $aktualBulanLalu) * 100;
      }
      // ============================

      return [
        'barang_nama' => $barang->nama,
        'satuan' => $barang->satuan,
        'stok_saat_ini' => $currentStock,
        'prediksi_keluar' => $predictionResult['forecast'],
        'tren_data' => $predictionResult['history'],
        'labels' => $predictionResult['labels'],
        'status' => $currentStock < $predictionResult['forecast'] ? 'KRITIS' : 'AMAN',
        'saran_beli' => $currentStock < $predictionResult['forecast'] ? ($predictionResult['forecast'] - $currentStock) : 0,
        'mape' => round($mape, 2), // Tambahkan nilai MAPE ke array response
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