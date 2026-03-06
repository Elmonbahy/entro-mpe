<?php

namespace App\Http\Controllers;

use App\Exports\MutationExport;
use App\Http\Controllers\Controller;
use App\Models\BarangStock;
use App\Models\BeliDetail;
use App\Models\JualDetail;
use App\Models\Mutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MutationController extends Controller
{
  public function kartuStock(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => 'nullable|date|after_or_equal:tgl_awal',
      'barang_id' => 'nullable|exists:barangs,id',
      'brand_id' => 'nullable|exists:brands,id',
    ]);

    $mutations = Mutation::where('barang_id', $request->barang_id)
      ->whereBetween('tgl_mutation', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ])
      ->with('mutationable')
      ->orderBy('tgl_mutation', 'asc')
      ->orderBy('id', 'asc')
      ->get();

    $barangStocks = BarangStock::where('barang_id', $request->barang_id)
      ->where('jumlah_stock', '>', 0)
      ->get();

    $total_jumlah_stock = $barangStocks->sum('jumlah_stock');

    return view('pages.mutation.kartu-stock', [
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'mutations' => $mutations,
      'barang_id' => $request->barang_id,
      'brand_id' => $request->brand_id,
      'barang_stocks' => $barangStocks,
      'total_jumlah_stock' => $total_jumlah_stock,
    ]);
  }

  private function getMutation(Request $request)
  {
    return Mutation::with(['barang', 'mutationable'])
      ->select(
        'barang_id',
        \DB::raw('SUM(stock_masuk) as total_stock_masuk'),
        \DB::raw('SUM(stock_keluar) as total_stock_keluar'),
        \DB::raw('SUM(stock_retur_jual) as total_stock_retur_jual'),
        \DB::raw('SUM(stock_retur_beli) as total_stock_retur_beli'),
        \DB::raw('SUM(stock_rusak) as total_stock_rusak'),
        \DB::raw('SUM(stock_akhir) as total_stock_akhir'),
      )
      ->whereBetween('tgl_mutation', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ])
      ->groupBy(['barang_id'])
      ->get()
      ->map(function ($mutation) {
        $barangStock = BarangStock::where('barang_id', $mutation->barang_id)
          ->sum('jumlah_stock');
        $harga_jual = JualDetail::where('barang_id', $mutation->barang_id)
          ->sum('harga_jual');
        $harga_beli = BeliDetail::where('barang_id', $mutation->barang_id)
          ->sum('harga_beli');

        $stock_awal = Mutation::where('barang_id', $mutation->barang_id)
          ->orderBy('tgl_mutation', 'asc')
          ->value('stock_masuk');

        return [
          'barang_id' => $mutation->barang_id,
          'barang_nama' => $mutation->barang->nama,
          'barang_satuan' => $mutation->barang->satuan,
          'batch' => $mutation->batch,
          'total_harga_jual' => $harga_jual ?? 0,
          'total_harga_beli' => $harga_beli ?? 0,
          'tgl_expired' => $mutation->tgl_expired,
          'total_stock_awal' => $stock_awal,
          'total_stock_masuk' => $mutation->total_stock_masuk,
          'total_stock_keluar' => $mutation->total_stock_keluar,
          'total_stock_retur_jual' => $mutation->total_stock_retur_jual,
          'total_stock_retur_beli' => $mutation->total_stock_retur_beli,
          'total_stock_rusak' => $mutation->total_stock_rusak,
          'total_stock_akhir' => $mutation->total_stock_akhir,
          'sisa_stock' => $barangStock
        ];
      });
  }

  public function index(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => 'nullable|date|after_or_equal:tgl_awal',
    ]);

    $mutations = $this->getMutation($request);

    return view('pages.mutation.index', [
      'mutations' => $mutations,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
    ]);
  }

  public function exportExcelMutation(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => 'nullable|date|after_or_equal:tgl_awal',
    ]);

    $mutations = $this->getMutation($request);

    return Excel::download(new MutationExport($mutations), 'mutations_' . now()->format('YmdHis') . '.xlsx');
  }
}
