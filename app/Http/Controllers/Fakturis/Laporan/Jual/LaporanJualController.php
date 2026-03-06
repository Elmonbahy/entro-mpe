<?php

namespace App\Http\Controllers\Fakturis\Laporan\Jual;

use App\Enums\StatusBarangMasuk;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BeliDetail;
use App\Models\Jual;
use App\Models\Salesman;
use App\Models\Pelanggan;
use App\Constants\TipePenjualan;
use App\Models\JualDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class LaporanJualController extends Controller
{

  private function getLaporanJualData(Request $request)
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

    $data = $query->get()
      ->map(function ($jual) use ($request) {
        $jual_details = $jual->jualDetails->map(function ($item) {
          $hna_beli = (float) BeliDetail::getHnaBeli($item->barang->id);
          $hna_jual = $item->total;

          return [
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
            'hna_jual' => $hna_jual,
            'hna_beli' => $hna_beli * $item->jumlah_barang_keluar / 1.11,
            'ppn_beli' => $hna_beli * 11 / 100,
            'ppn_jual' => $item->harga_ppn,
            'profit' => $hna_jual - $hna_beli
          ];
        });


        $total_hna_beli = $jual_details->sum(function ($item) {
          return $item['hna_beli'];
        });

        $total_ppn_beli = $jual_details->sum(function ($item) {
          return $item['ppn_beli'];
        });

        $total_profit = $jual_details->sum(function ($item) {
          return $item['profit'];
        });

        return [
          'pelanggan_nama' => $jual->pelanggan->nama,
          'nomor_faktur' => $jual->nomor_faktur,
          'tipe_penjualan' => $jual->tipe_penjualan,
          'ongkir' => $jual->ongkir,
          'tgl_faktur' => Carbon::parse($jual->tgl_faktur)->format('d/m/Y'),
          'total_tagihan' => $jual->jualDetails->sum('total_tagihan'),
          'total_hna_jual' => $jual->jualDetails->sum('total'),
          'total_ppn_jual' => $jual->jualDetails->sum('harga_ppn'),
          'total_hna_beli' => $total_hna_beli,
          'total_ppn_beli' => $total_ppn_beli,
          'total_profit' => $total_profit,
          'jual_details_count' => $jual->jual_details_count,
          'jual_details' => $jual_details,
          'persen' => $jual->jualDetails->sum('total') > 0
            ? ($total_profit / $jual->jualDetails->sum('total') * 100)
            : 0,
          'net_profit' => $total_profit - $jual->ongkir
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

    $data = $this->getLaporanJualData($request);

    return view('pages.laporan-jual.fakturis.index', [
      'pelanggans' => $pelanggans,
      'sales' => $sales,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'pelanggan_id' => $request->pelanggan_id,
      'sales_id' => $request->sales_id,
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
    ]);

    $data = $this->getLaporanJualData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dMY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dMY');
    $filename = "laporan_profit_jual_all $tgl_awal $tgl_akhir.xlsx";

    if ($request->pelanggan_id) {
      $filename = "laporan_profit_jual_pelanggan $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($request->sales_id) {
      $filename = "laporan_profit_jual_sales $tgl_awal - $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanJualExport($data, $request->tgl_awal, $request->tgl_akhir),
      $filename
    );
  }

}
