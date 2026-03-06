<?php

namespace App\Http\Controllers\Fakturis\Laporan\SlowMoving;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Brand;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class LaporanSlowMovingController extends Controller
{
  public function getSlowMovingData(Request $request)
  {
    $query = Barang::with(['jual_details.jual', 'beli_details.beli', 'brand'])
      ->whereHas('jual_details.jual', function ($subQuery) use ($request) {
        if ($request->tgl_awal && $request->tgl_akhir) {
          $subQuery->whereBetween('tgl_faktur', [
            Carbon::parse($request->tgl_awal)->startOfDay(),
            Carbon::parse($request->tgl_akhir)->endOfDay()
          ]);
        }
      })
      ->whereHas('beli_details.beli', function ($subQuery) use ($request) {
        if ($request->tgl_awal && $request->tgl_akhir) {
          $subQuery->whereBetween('tgl_terima_faktur', [
            Carbon::parse($request->tgl_awal)->startOfDay(),
            Carbon::parse($request->tgl_akhir)->endOfDay()
          ]);
        }
      });

    if ($request->has('brand_id') && $request->brand_id != '') {
      $query->where('brand_id', $request->brand_id);
    }

    $barangData = $query->get();

    return $barangData->filter(function ($item) use ($request) {
      // Hitung total penjualan
      $total_jual = $item->jual_details
        ->filter(function ($detail) use ($request) {
          return optional($detail->jual)->tgl_faktur >= Carbon::parse($request->tgl_awal)->startOfDay()
            && optional($detail->jual)->tgl_faktur <= Carbon::parse($request->tgl_akhir)->endOfDay();
        })
        ->sum('jumlah_barang_keluar');

      // Hitung total pembelian
      $total_beli = $item->beli_details
        ->filter(function ($detail) use ($request) {
          return optional($detail->beli)->tgl_terima_faktur >= Carbon::parse($request->tgl_awal)->startOfDay()
            && optional($detail->beli)->tgl_terima_faktur <= Carbon::parse($request->tgl_akhir)->endOfDay();
        })
        ->sum('jumlah_barang_masuk');

      // Hanya return jika slow moving
      return $total_beli > 0 && $total_jual < ($total_beli * 0.3);
    })->map(function ($item) use ($request) {
      $total_jual = $item->jual_details
        ->filter(function ($detail) use ($request) {
          return optional($detail->jual)->tgl_faktur >= Carbon::parse($request->tgl_awal)->startOfDay()
            && optional($detail->jual)->tgl_faktur <= Carbon::parse($request->tgl_akhir)->endOfDay();
        })
        ->sum('jumlah_barang_keluar');

      $total_beli = $item->beli_details
        ->filter(function ($detail) use ($request) {
          return optional($detail->beli)->tgl_terima_faktur >= Carbon::parse($request->tgl_awal)->startOfDay()
            && optional($detail->beli)->tgl_terima_faktur <= Carbon::parse($request->tgl_akhir)->endOfDay();
        })
        ->sum('jumlah_barang_masuk');

      return [
        'brand' => $item->brand->nama,
        'nama' => $item->nama,
        'satuan' => $item->satuan,
        'total_penjualan' => $total_jual,
        'total_pembelian' => $total_beli,
        'status' => 'Slow Moving',
      ];
    });
  }

  public function index(Request $request)
  {
    // Validasi request untuk tanggal dan brand
    $request->validate([
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => [
        'nullable',
        'date',
        'after_or_equal:tgl_awal',
        'before_or_equal:' . Carbon::parse($request->tgl_awal)->addMonths(3)->toDateString(),
      ],
      'brand_id' => 'nullable|exists:brands,id',
    ], [
      'tgl_akhir.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari 3 bulan setelah tanggal awal.',
    ]);

    // Ambil data brand untuk dropdown
    $brands = Brand::all();

    // Jika semua parameter null, return data kosong
    if (empty($request->tgl_awal) && empty($request->tgl_akhir) && empty($request->brand_id)) {
      $data = collect();
    } else {
      // Ambil data slow moving
      $data = $this->getSlowMovingData($request);
    }

    return view('pages.slow-moving.fakturis.index', [
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'brand_id' => $request->brand_id,
      'brands' => $brands,
      'data' => $data,
    ]);
  }

  public function exportExcel(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'required|date',
      'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      'brand_id' => 'nullable|exists:brands,id',
    ]);

    $data = $this->getSlowMovingData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dmY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dmY');
    $filename = "laporan_slow_moving_all $tgl_awal $tgl_akhir.xlsx";

    if ($request->brand_id) {
      $filename = "laporan_slow_moving_brand $tgl_awal $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanSlowMovingExport($data, $request->tgl_awal, $request->tgl_akhir),
      $filename
    );
  }
}