<?php

namespace App\Http\Controllers\Keuangan\Laporan\Beli;

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
        'status_bayar',
        'supplier_id',
        'bayar'
      ])
      ->with([
        'supplier:id,nama',
        'beliDetails' => function ($q) {
          $q->select('id', 'beli_id', 'barang_id', 'jumlah_barang_masuk', 'jumlah_barang_dipesan', 'harga_beli', 'diskon1', 'diskon2');
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

    if ($request->status_bayar) {
      $query->where('status_bayar', $request->status_bayar);
    }

    $data = $query->get()
      ->map(function ($beli) use ($request) {
        $bayar = $beli->bayar ?? [];
        $tgl_bayar = !empty($bayar)
          ? collect($bayar)->pluck('tgl_bayar')->filter()
            ->map(fn($tgl) => Carbon::parse($tgl)->format('d/m/Y'))
            ->implode(', ')
          : '-';
        $tipe_bayar = !empty($bayar)
          ? collect($bayar)->pluck('tipe_bayar')->filter()->first()
          : '-';

        return [
          'supplier_nama' => $beli->supplier->nama,
          'nomor_faktur' => $beli->nomor_faktur,
          'tgl_faktur' => Carbon::parse($beli->tgl_faktur)->format('d/m/Y'),
          'tgl_terima_faktur' => Carbon::parse($beli->tgl_terima_faktur)->format('d/m/Y'),
          'status_bayar' => $beli->status_bayar,
          'status_bayar_label' => $beli->status_bayar_label,
          'tgl_bayar' => $tgl_bayar,
          'tipe_bayar' => $tipe_bayar,
          'total_tagihan' => $beli->beliDetails->sum('total_tagihan'),
          'total_dpp' => $beli->beliDetails->sum('total'),
          'total_harga_ppn' => $beli->beliDetails->sum('harga_ppn'),
          'beli_details_count' => $beli->beli_details_count,
          'beli_details' => $beli->beliDetails->map(fn($item) => [
            'barang_nama' => $item->barang->nama,
            'barang_satuan' => $item->barang->satuan,
            'barang_id' => $item->barang->id,
            'jumlah_barang_masuk' => $item->jumlah_barang_masuk,
            'diskon1' => $item->diskon1,
            'harga_diskon1' => $item->harga_diskon1,
            'diskon2' => $item->diskon2,
            'harga_diskon2' => $item->harga_diskon2,
            'harga_beli' => $item->harga_beli,
            'total_tagihan' => $item->total_tagihan,
            'dpp' => $item->total,
            'harga_ppn' => $item->harga_ppn
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
      'status_bayar' => 'nullable|in:PAID,UNPAID',
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

    // Return ke view
    return view('pages.laporan-beli.keuangan.index', [
      'suppliers' => $suppliers,
      'supplier_id' => $request->supplier_id,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'status_bayar' => $request->status_bayar,
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
      'status_bayar' => 'nullable|in:PAID,UNPAID',
      'filter_berdasarkan' => 'nullable|in:tgl_faktur,tgl_terima'
    ]);

    $data = $this->getLaporanBeliData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dMY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dMY');
    $filename = "laporan_faktur_beli_all $tgl_awal - $tgl_akhir.xlsx";

    if ($request->supplier_id) {
      $filename = "laporan_faktur_beli_supplier $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($request->status_bayar) {
      $status = $request->status_bayar == 'PAID' ? 'Lunas' : 'Belum Lunas';
      $filename = "laporan_faktur_beli_{$status} $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanBeliExport($data, $request->tgl_awal, $request->tgl_akhir, $request->filter_berdasarkan ?? 'tgl_faktur'),
      $filename
    );
  }

}
