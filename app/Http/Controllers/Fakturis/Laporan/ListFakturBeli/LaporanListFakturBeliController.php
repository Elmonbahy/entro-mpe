<?php

namespace App\Http\Controllers\Fakturis\Laporan\ListFakturBeli;

use App\Http\Controllers\Controller;
use App\Models\Beli;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanListFakturBeliController extends Controller
{
  private function getLaporanListFakturBeliData(Request $request)
  {
    $column = $request->filter_berdasarkan === 'tgl_terima' ? 'tgl_terima_faktur' : 'tgl_faktur';

    $query = Beli::query()
      ->select([
        'id',
        'nomor_faktur',
        'tgl_faktur',
        'tgl_terima_faktur',
        'supplier_id',
        'status_bayar',
        'kredit',
        'bayar'
      ])
      ->with([
        'supplier:id,nama',
        'beliDetails' => function ($q) {
          $q->select('id', 'beli_id', );
        }
      ])
      ->where('status_faktur', 'DONE')
      ->whereBetween($column, [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ])
      ->when($request->supplier_id, function ($query) use ($request) {
        $query->where('supplier_id', $request->supplier_id);
      })
      ->when($request->status_bayar, function ($query) use ($request) {
        $query->where('status_bayar', $request->status_bayar);
      });

    $data = $query->get()
      ->groupBy('supplier_id')
      ->map(function ($belis, $supplier_id) {
        $supplier_nama = optional($belis->first()->supplier)->nama ?? 'Unknown';
        $total_tagihan_supplier = $belis->sum('total_tagihan_laporan');
        $total_sisa_tagihan_supplier = $belis->sum('sisa_tagihan');
        $total_faktur_supplier = $belis->sum('total_faktur');
        $total_dpp_supplier = $belis->sum('total_dpp');
        $total_ppn_supplier = $belis->sum('total_ppn');

        $fakturs = $belis->map(function ($beli) {
          $bayar = collect($beli->bayar);
          return [
            'nomor_faktur' => $beli->nomor_faktur,
            'tgl_faktur' => Carbon::parse($beli->tgl_faktur)->format('d/m/Y'),
            'tgl_terima_faktur' => Carbon::parse($beli->tgl_terima_faktur)->format('d/m/Y'),
            'jatuh_tempo' => ($beli->kredit && $beli->tgl_faktur)
              ? Carbon::parse($beli->tgl_faktur)->addDays((int) $beli->kredit)->format('d/m/Y')
              : '-',
            'tgl_bayar' => $bayar->isNotEmpty()
              ? $bayar->pluck('tgl_bayar')->filter()->map(fn($tgl) => Carbon::parse($tgl)->format('d/m/Y'))->implode(', ')
              : '-',
            'tipe_bayar' => $bayar->isNotEmpty()
              ? $bayar->pluck('tipe_bayar')->filter()->first()
              : '-',
            'metode_bayar' => $bayar->isNotEmpty()
              ? $bayar->pluck('metode_bayar')->filter()->flatten()->implode(', ')
              : '-',
            'terbayar' => $bayar->isNotEmpty()
              ? $bayar->pluck('terbayar')->filter()->flatten()
                ->map(fn($val) => number_format((float) $val, 0, ',', '.'))
                ->implode(', ')
              : '-',
            'status_bayar' => $beli->status_bayar,
            'status_bayar_label' => $beli->status_bayar_label,
            'total_tagihan' => $beli->total_tagihan_laporan,
            'sisa_tagihan' => $beli->sisa_tagihan,
            'total_faktur' => $beli->total_faktur,
            'total_dpp' => $beli->total_dpp,
            'total_ppn' => $beli->total_ppn,
          ];
        })->values();

        return [
          'supplier_nama' => $supplier_nama,
          'total_tagihan_supplier' => $total_tagihan_supplier,
          'total_sisa_tagihan_supplier' => $total_sisa_tagihan_supplier,
          'total_faktur_supplier' => $total_faktur_supplier,
          'total_dpp_supplier' => $total_dpp_supplier,
          'total_ppn_supplier' => $total_ppn_supplier,
          'fakturs' => $fakturs
        ];
      })->values();

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
      'filter_berdasarkan' => 'nullable|in:tgl_faktur,tgl_terima',
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

    $data = $this->getLaporanListFakturBeliData($request);

    return view('pages.laporan-list-faktur-beli.fakturis.index', [
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

    $data = $this->getLaporanListFakturBeliData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dMY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dMY');
    $filename = "list_faktur_beli $tgl_awal - $tgl_akhir.xlsx";

    if ($request->supplier_id) {
      $filename = "list_faktur_beli_supplier $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($request->status_bayar) {
      $status = $request->status_bayar == 'PAID' ? 'Lunas' : 'Belum Lunas';
      $filename = "list_faktur_beli_status {$status} $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanListFakturBeliExport(
        $data,
        $request->tgl_awal,
        $request->tgl_akhir,
        $request->filter_berdasarkan ?? 'tgl_faktur'
      ),
      $filename
    );
  }

}
