<?php

namespace App\Http\Controllers\Gudang\Laporan\Pengiriman;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPengirimanController extends Controller
{
  private function getLaporanPengirimanData(Request $request)
  {
    $query = SuratJalan::with([
      'pelanggan:id,nama',
      'kendaraan:id,nama',
      'suratJalanDetails.jualDetail.jual:id,nomor_faktur,tgl_faktur,status_kirim',
      'suratJalanDetails.jualDetail.barang:id,nama,satuan,brand_id',
      'suratJalanDetails.jualDetail.barang.brand:id,nama',
    ])
      ->whereBetween('tgl_surat_jalan', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : now()->endOfDay()
      ]);

    if ($request->pelanggan_id) {
      $query->where('pelanggan_id', $request->pelanggan_id);
    }

    $data = $query->get()
      ->map(function ($sj) {
        return [
          'nomor_surat_jalan' => $sj->nomor_surat_jalan,
          'tgl_surat_jalan' => Carbon::parse($sj->tgl_surat_jalan)->format('d/m/Y'),
          'pelanggan_nama' => $sj->pelanggan->nama,
          'kendaraan' => $sj->kendaraan->nama,
          'koli' => $sj->koli,
          'staf_logistik' => $sj->staf_logistik,
          'sj_details_count' => $sj->sj_details_count,
          'sj_details' => $sj->suratJalanDetails->map(fn($item): array => [
            'nomor_faktur' => $item->jualDetail->jual->nomor_faktur,
            'status_kirim' => $item->jualDetail->jual->status_kirim,
            'tgl_faktur' => optional($item->jualDetail->jual)->tgl_faktur
              ? Carbon::parse($item->jualDetail->jual->tgl_faktur)->format('d/m/Y')
              : '-',
            'barang_nama' => $item->jualDetail->barang->nama,
            'brand' => $item->jualDetail->barang->brand->nama,
            'satuan' => $item->jualDetail->barang->satuan,
            'jumlah_barang_keluar' => $item->jualDetail->jumlah_barang_keluar,
            'jumlah_barang_dikirim' => $item->jumlah_barang_dikirim,
          ])
        ];
      });

    return $data;
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

    $data = $this->getLaporanPengirimanData($request);

    return view('pages.laporan-pengiriman.gudang.index', [
      'pelanggans' => $pelanggans,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'pelanggan_id' => $request->pelanggan_id,
      'data' => $data,
    ]);
  }

  public function exportExcel(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'required|date',
      'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      'pelanggan_id' => 'nullable|exists:pelanggans,id',
    ]);

    $data = $this->getLaporanPengirimanData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dmY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dmY');
    $filename = "laporan_pengiriman_all $tgl_awal $tgl_akhir.xlsx";

    if ($request->pelanggan_id) {
      $filename = "laporan_pengiriman_pelanggan $tgl_awal $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanPengirimanExport($data, $request->tgl_awal, $request->tgl_akhir),
      $filename
    );
  }
}