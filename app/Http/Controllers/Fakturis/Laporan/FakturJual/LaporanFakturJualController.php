<?php

namespace App\Http\Controllers\Fakturis\Laporan\FakturJual;

use App\Http\Controllers\Controller;
use App\Models\Jual;
use App\Models\Salesman;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanFakturJualController extends Controller
{

  private function getLaporanFakturJualData(Request $request)
  {

    $query = Jual::with([
      'jualDetails',
      'jualDetails.barang:nama,id,satuan',
      'pelanggan:nama,id',
      'salesman:nama,id'
    ])
      ->withCount('jualDetails')
      ->where('status_faktur', 'DONE')
      ->whereBetween('tgl_faktur', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ]);

    if ($request->sales_id) {
      $query = $query->where('salesman_id', $request->sales_id);
    }

    if ($request->pelanggan_id) {
      $query = $query->where('pelanggan_id', $request->pelanggan_id);
    }

    if ($request->status_bayar) {
      $query->where('status_bayar', $request->status_bayar);
    }

    $data = $query->get()
      ->map(function ($jual) {
        $bayar = $jual->bayar ?? [];
        $tgl_bayar = !empty($bayar)
          ? collect($bayar)->pluck('tgl_bayar')->filter()
            ->map(fn($tgl) => Carbon::parse($tgl)->format('d/m/Y'))
            ->implode(', ')
          : '-';
        $tipe_bayar = !empty($bayar)
          ? collect($bayar)->pluck('tipe_bayar')->filter()->first()
          : '-';

        return [
          'pelanggan_nama' => $jual->pelanggan->nama,
          'nomor_faktur' => $jual->nomor_faktur,
          'sales_nama' => $jual->salesman->nama,
          'ongkir' => $jual->ongkir,
          'tgl_faktur' => Carbon::parse($jual->tgl_faktur)->format('d/m/Y'),
          'status_bayar' => $jual->status_bayar,
          'status_bayar_label' => $jual->status_bayar_label,
          'tipe_bayar' => $tipe_bayar,
          'tgl_bayar' => $tgl_bayar,
          'total_tagihan' => $jual->jualDetails->sum('total_tagihan'),
          'total_dpp' => $jual->jualDetails->sum('total'),
          'total_harga_ppn' => $jual->jualDetails->sum('harga_ppn'),
          'jual_details_count' => $jual->jual_details_count,
          'jual_details' => $jual->jualDetails->map(fn($item) => [
            'barang_nama' => $item->barang->nama,
            'barang_satuan' => $item->barang->satuan,
            'barang_id' => $item->barang->id,
            'jumlah_barang_keluar' => $item->jumlah_barang_keluar,
            'diskon1' => $item->diskon1,
            'harga_diskon1' => $item->harga_diskon1,
            'diskon2' => $item->diskon2,
            'harga_diskon2' => $item->harga_diskon2,
            'harga_jual' => $item->harga_jual,
            'total_tagihan' => $item->total_tagihan,
            'dpp' => $item->total,
            'harga_ppn' => $item->harga_ppn,
          ])
        ];
      });

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
      'sales_id' => 'nullable|exists:salesmen,id',
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

    $data = $this->getLaporanFakturJualData($request);

    return view('pages.laporan-jual-faktur.fakturis.index', [
      'pelanggans' => $pelanggans,
      'sales' => $sales,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'pelanggan_id' => $request->pelanggan_id,
      'sales_id' => $request->sales_id,
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

    $data = $this->getLaporanFakturJualData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dMY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dMY');
    $filename = "laporan_faktur_jual $tgl_awal - $tgl_akhir.xlsx";

    if ($request->pelanggan_id) {
      $filename = "laporan_faktur_jual_pelanggan $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($request->sales_id) {
      $filename = "laporan_faktur_jual_Sales $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($request->status_bayar) {
      $status = $request->status_bayar == 'PAID' ? 'Lunas' : 'Belum Lunas';
      $filename = "laporan_faktur_jual_{$status} $tgl_awal $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanFakturJualExport($data, $request->tgl_awal, $request->tgl_akhir),
      $filename
    );
  }

}
