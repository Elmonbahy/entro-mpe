<?php

namespace App\Http\Controllers\Gudang\Laporan\ListPengiriman;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use App\Models\Salesman;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanListPengirimanController extends Controller
{
  private function getLaporanListPengirimanData(Request $request)
  {
    $query = SuratJalan::with([
      'suratJalanDetails.jualDetail.jual:id,nomor_faktur,tgl_faktur,pelanggan_id,salesman_id,status_kirim',
      'suratJalanDetails.jualDetail.jual.pelanggan:id,nama,rayon',
      'suratJalanDetails.jualDetail.jual.salesman:id,nama',
      'kendaraan:id,nama',
    ])
      ->whereBetween('tgl_surat_jalan', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ])
      ->when($request->pelanggan_id, function ($query) use ($request) {
        $query->where('pelanggan_id', $request->pelanggan_id);
      });

    $suratJalans = $query->get();

    return $suratJalans->map(function ($sj) {
      $jual = optional($sj->suratJalanDetails->first()?->jualDetail?->jual);

      return [
        'nomor_surat_jalan' => $sj->nomor_surat_jalan,
        'tgl_surat_jalan' => Carbon::parse($sj->tgl_surat_jalan)->format('d/m/Y'),
        'nomor_faktur' => $jual?->nomor_faktur ?? '-',
        'tgl_faktur' => $jual?->tgl_faktur ? Carbon::parse($jual->tgl_faktur)->format('d/m/Y') : '-',
        'pelanggan' => $jual?->pelanggan?->nama ?? '-',
        'sales' => $jual?->salesman?->nama ?? '-',
        'staf_logistik' => $sj?->staf_logistik ?? '-',
        'kendaraan' => $sj->kendaraan?->nama ?? '-',
        'status_kirim' => $jual->status_kirim,
        'koli' => $sj?->koli ?? '-',
        'rayon' => $jual?->pelanggan?->rayon ?? '-',
      ];
    });
  }

  public function index(Request $request)
  {
    $pelanggans = Pelanggan::select('nama', 'id')->get();

    $rules = [
      'tgl_awal' => 'nullable|date',
      'tgl_akhir' => ['nullable', 'date', 'after_or_equal:tgl_awal'],
      'pelanggan_id' => 'nullable|exists:pelanggans,id',
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


    $data = $this->getLaporanListPengirimanData($request);

    return view('pages.laporan-list-pengiriman.gudang.index', [
      'pelanggans' => $pelanggans,
      'pelanggan_id' => $request->pelanggan_id,
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
      'pelanggan_id' => 'nullable|exists:pelanggans,id',
    ]);

    $data = $this->getLaporanListPengirimanData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dMY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dMY');
    $filename = "list_pengiriman $tgl_awal - $tgl_akhir.xlsx";

    if ($request->supplier_id) {
      $filename = "list_pengiriman_pelanggan $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanListPengirimanExport($data, $request->tgl_awal, $request->tgl_akhir),
      $filename
    );
  }

}
