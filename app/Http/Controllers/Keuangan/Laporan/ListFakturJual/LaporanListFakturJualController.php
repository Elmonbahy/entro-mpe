<?php

namespace App\Http\Controllers\Keuangan\Laporan\ListFakturJual;

use App\Http\Controllers\Controller;
use App\Models\Jual;
use App\Models\Salesman;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanListFakturJualController extends Controller
{
  private function getLaporanListFakturJualData(Request $request)
  {
    $query = Jual::query()
      ->select([
        'id',
        'nomor_faktur',
        'tgl_faktur',
        'pelanggan_id',
        'salesman_id',
        'status_bayar',
        'kredit',
        'bayar',
        'is_pungut_ppn'
      ])
      ->with([
        'pelanggan:id,nama,area',
        'salesman:id,nama',
      ])
      ->where('status_faktur', 'DONE')
      ->whereBetween('tgl_faktur', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ])
      ->when($request->pelanggan_id, function ($query) use ($request) {
        $query->where('pelanggan_id', $request->pelanggan_id);
      })
      ->when($request->sales_id, function ($query) use ($request) {
        $query->where('salesman_id', $request->sales_id);
      })
      ->when($request->status_bayar, function ($query) use ($request) {
        $query->where('status_bayar', $request->status_bayar);
      });

    $data = $query->get()
      ->groupBy('pelanggan_id')
      ->map(function ($juals, $pelanggan_id) {
        $pelanggan_nama = optional($juals->first()->pelanggan)->nama ?? 'Unknown';
        $total_faktur_pelanggan = $juals->sum('total_faktur');
        $total_dpp_pelanggan = $juals->sum('total_dpp');
        $total_harga_ppn_pelanggan = $juals->sum('total_ppn');
        $total_total_tagihan_pelanggan = $juals->sum('total_tagihan_laporan');
        $total_sisa_tagihan_pelanggan = $juals->sum('sisa_tagihan');

        $fakturs = $juals->map(function ($jual) {
          $bayar = collect($jual->bayar);
          return [
            'nomor_faktur' => $jual->nomor_faktur,
            'sales_nama' => $jual->salesman->nama,
            'area' => $jual->pelanggan->area ?? '-',
            'tgl_faktur' => Carbon::parse($jual->tgl_faktur)->format('d/m/Y'),
            'jatuh_tempo' => ($jual->kredit && $jual->tgl_faktur)
              ? Carbon::parse($jual->tgl_faktur)->addDays((int) $jual->kredit)->format('d/m/Y')
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
            'status_bayar' => $jual->status_bayar,
            'status_bayar_label' => $jual->status_bayar_label,
            'total_faktur' => $jual->total_faktur,
            'total_dpp' => $jual->total_dpp,
            'total_ppn' => $jual->total_ppn,
            'total_tagihan' => $jual->total_tagihan_laporan,
            'sisa_tagihan' => $jual->sisa_tagihan,
            'is_pungut_ppn' => $jual->is_pungut_ppn ? 'Ya' : 'Tidak'
          ];
        })->values();

        return [
          'pelanggan_nama' => $pelanggan_nama,
          'total_faktur_pelanggan' => $total_faktur_pelanggan,
          'total_dpp_pelanggan' => $total_dpp_pelanggan,
          'total_harga_ppn_pelanggan' => $total_harga_ppn_pelanggan,
          'total_total_tagihan_pelanggan' => $total_total_tagihan_pelanggan,
          'total_sisa_tagihan_pelanggan' => $total_sisa_tagihan_pelanggan,
          'fakturs' => $fakturs
        ];
      })->values();

    return $data;
  }

  public function index(Request $request)
  {
    $pelanggans = Pelanggan::select('nama', 'id')->get();
    $sales = Salesman::select('nama', 'id')->get();

    $rules = [
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => ['nullable', 'date', 'after_or_equal:tgl_awal'],
      'pelanggan_id' => 'nullable|exists:pelanggans,id',
      'status_bayar' => 'nullable|in:PAID,UNPAID',
    ];

    $messages = [
      'tgl_akhir.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari 1 bulan setelah tanggal awal.',
    ];

    if (!$request->filled('pelanggan_id')) {
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

    $data = $this->getLaporanListFakturJualData($request);

    return view('pages.laporan-list-faktur-jual.keuangan.index', [
      'pelanggans' => $pelanggans,
      'pelanggan_id' => $request->pelanggan_id,
      'sales' => $sales,
      'sales_id' => $request->sales_id,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'status_bayar' => $request->status_bayar,
      'data' => $data
    ]);
  }

  public function exportExcel(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'required|date',
      'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      'pelanggan_id' => 'nullable|exists:pelanggans,id',
      'sales_id' => 'nullable|exists:salesmen,id',
      'status_bayar' => 'nullable|in:PAID,UNPAID'
    ]);

    $data = $this->getLaporanListFakturJualData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dMY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dMY');
    $filename = "list_faktur_jual $tgl_awal - $tgl_akhir.xlsx";

    if ($request->supplier_id) {
      $filename = "list_faktur_jual_pelanggan $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($request->sales_id) {
      $filename = "list_faktur_jual_sales $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($request->status_bayar) {
      $status = $request->status_bayar == 'PAID' ? 'Lunas' : 'Belum Lunas';
      $filename = "list_faktur_jual_status {$status} $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanListFakturJualExport($data, $request->tgl_awal, $request->tgl_akhir),
      $filename
    );
  }

}
