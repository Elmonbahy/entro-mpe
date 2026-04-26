<?php

namespace App\Http\Controllers\Fakturis\Laporan\Beli;

use App\Http\Controllers\Controller;
use App\Models\Beli;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanBeliController extends Controller
{
  private function getLaporanBeliData(Request $request)
  {
    $column = $request->filter_berdasarkan === 'tgl_terima' ? 'tgl_terima_faktur' : 'tgl_faktur';

    $query = Beli::query()
      ->select([
        'id',
        'nomor_faktur',
        'tgl_faktur',
        'tgl_terima_faktur',
        'supplier_id',
      ])
      ->with([
        'supplier:id,nama',
        'beliDetails' => function ($q) {
          $q->select('id', 'beli_id', 'barang_id', 'jumlah_barang_masuk', 'jumlah_barang_dipesan', 'harga_beli');
        },
        'beliDetails.barang:id,nama,satuan',
      ])
      ->withCount('beliDetails')
      ->whereBetween($column, [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ]);

    if ($request->supplier_id) {
      $query = $query->where('supplier_id', $request->supplier_id);
    }

    $data = $query->get()
      ->map(function ($beli) use ($request) {

        return [
          'supplier_nama' => $beli->supplier->nama,
          'nomor_faktur' => $beli->nomor_faktur,
          'tgl_faktur' => Carbon::parse($beli->tgl_faktur)->format('d/m/Y'),
          'tgl_terima_faktur' => Carbon::parse($beli->tgl_terima_faktur)->format('d/m/Y'),
          'beli_details_count' => $beli->beli_details_count,
          'beli_details' => $beli->beliDetails->map(fn($item) => [
            'barang_nama' => $item->barang->nama,
            'barang_satuan' => $item->barang->satuan,
            'barang_id' => $item->barang->id,
            'jumlah_barang_masuk' => $item->jumlah_barang_masuk,
            'harga_beli' => $item->harga_beli,
          ])
        ];
      });

    return $data;
  }

  public function index(Request $request)
  {
    $suppliers = Supplier::select('nama', 'id')->get();

    $rules = [
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => ['nullable', 'date', 'after_or_equal:tgl_awal'],
      'supplier_id' => 'nullable|exists:suppliers,id',
      'filter_berdasarkan' => 'nullable|in:tgl_faktur,tgl_terima'
    ];

    $messages = [
      'tgl_akhir.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari 2 bulan setelah tanggal awal.',
    ];

    if (!$request->filled('supplier_id')) {
      if ($request->filled('tgl_awal')) {
        $rules['tgl_akhir'] = array_merge(
          $rules['tgl_akhir'],
          ['before_or_equal:' . Carbon::parse($request->tgl_awal)->addMonths(2)->toDateString()]
        );
      }
    } else {
      $rules = array_merge($rules, [
        'tgl_awal' => 'required|date',
        'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      ]);
    }

    $request->validate($rules, $messages);

    $data = $this->getLaporanBeliData($request);

    return view('pages.laporan-beli.fakturis.index', [
      'suppliers' => $suppliers,
      'supplier_id' => $request->supplier_id,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'filter_berdasarkan' => $request->filter_berdasarkan ?? 'tgl_faktur',
      'data' => $data
    ]);
  }

  public function exportExcel(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'required|date',
      'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      'supplier_id' => 'nullable|exists:suppliers,id',
      'filter_berdasarkan' => 'nullable|in:tgl_faktur,tgl_terima'
    ]);

    $data = $this->getLaporanBeliData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dMY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dMY');
    $filename = "laporan_faktur_beli $tgl_awal - $tgl_akhir.xlsx";

    if ($request->supplier_id) {
      $filename = "laporan_faktur_beli_supplier $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanBeliExport(
        $data,
        $request->tgl_awal,
        $request->tgl_akhir,
        $request->filter_berdasarkan ?? 'tgl_faktur'
      ),
      $filename
    );
  }

}
