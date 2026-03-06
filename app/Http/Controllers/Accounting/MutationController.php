<?php

namespace App\Http\Controllers\Accounting;

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

    return view('pages.mutation.accounting.kartu-stock', [
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'mutations' => $mutations,
      'barang_id' => $request->barang_id,
      'brand_id' => $request->brand_id,
      'barang_stocks' => $barangStocks,
      'total_jumlah_stock' => $total_jumlah_stock,
    ]);
  }
}
