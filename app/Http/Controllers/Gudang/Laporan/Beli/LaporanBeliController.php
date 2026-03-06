<?php

namespace App\Http\Controllers\Gudang\Laporan\Beli;

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
    $query = Beli::with([
      'beliDetails',
      'beliDetails.barang:nama,id,satuan,brand_id',
      'beliDetails.barang.brand:nama,id',
      'supplier:nama,id',
    ])
      ->withCount('beliDetails')
      ->whereBetween('tgl_terima_faktur', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ]);


    if ($request->supplier_id) {
      $query = $query->where('supplier_id', $request->supplier_id);
    }
    if ($request->status_faktur) {
      $query->where('status_faktur', $request->status_faktur);
    } else {
      $query->whereIn('status_faktur', ['PROCESS_GUDANG', 'DONE']);
    }

    $data = $query->get()
      ->map(function ($beli) use ($request) {
        return [
          'supplier_nama' => $beli->supplier->nama,
          'nomor_faktur' => $beli->nomor_faktur,
          'tgl_faktur' => Carbon::parse($beli->tgl_faktur)->format('d/m/Y'),
          'tgl_terima_faktur' => Carbon::parse($beli->tgl_terima_faktur)->format('d/m/Y'),
          'status_faktur_label' => $beli->status_faktur_label,
          'beli_details_count' => $beli->beli_details_count,
          'beli_details' => $beli->beliDetails->map(fn($item): array => [
            'barang_nama' => $item->barang->nama,
            'brand_nama' => $item->barang->brand->nama,
            'barang_satuan' => $item->barang->satuan,
            'barang_id' => $item->barang->id,
            'jumlah_barang_masuk' => $item->jumlah_barang_masuk,
            'jumlah_barang_dipesan' => $item->jumlah_barang_dipesan,
            'status_barang_masuk_label' => $item->status_barang_masuk_label
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
      'status_faktur' => 'nullable|in:PROCESS_GUDANG,DONE',
    ];

    $messages = [
      'tgl_akhir.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari 1 bulan setelah tanggal awal.',
    ];

    if (!$request->filled('supplier_id')) {
      if ($request->filled('tgl_awal')) {
        $rules['tgl_akhir'] = array_merge(
          $rules['tgl_akhir'],
          ['before_or_equal:' . Carbon::parse($request->tgl_awal)->addMonths(1)->toDateString()]
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

    return view('pages.laporan-beli.gudang.index', [
      'suppliers' => $suppliers,
      'status_faktur' => $request->status_faktur,
      'supplier_id' => $request->supplier_id,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'data' => $data
    ]);
  }

  public function exportExcel(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'required|date',
      'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      'supplier_id' => 'nullable|exists:suppliers,id',
      'status_faktur' => 'nullable|in:PROCESS_GUDANG,DONE'
    ]);

    $data = $this->getLaporanBeliData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dmY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dmY');
    $filename = "laporan_barang_masuk_all $tgl_awal $tgl_akhir.xlsx";

    if ($request->supplier_id) {
      $filename = "laporan_barang_masuk_supplier $tgl_awal $tgl_akhir.xlsx";
    }

    if ($request->status_faktur) {
      $status = $request->status_faktur == 'DONE' ? 'Selesai' : 'Proses Gudang';
      $filename = "laporan_barang_masuk_{$status} $tgl_awal $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanBeliExport($data, $request->tgl_awal, $request->tgl_akhir),
      $filename
    );
  }
}
