<?php

namespace App\Http\Controllers\Gudang\Laporan\Jual;

use App\Http\Controllers\Controller;
use App\Models\Jual;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

use function Symfony\Component\String\s;

class LaporanJualController extends Controller
{

  private function getLaporanJualData(Request $request)
  {

    $query = Jual::query()
      ->select([
        'id',
        'nomor_faktur',
        'tgl_faktur',
        'pelanggan_id',
        'status_faktur'
      ])
      ->with([
        'pelanggan:id,nama',
        'salesman:id,nama',
        'jualDetails' => function ($q) {
          $q->select('id', 'jual_id', 'barang_id', 'jumlah_barang_keluar', 'jumlah_barang_dipesan', 'status_barang_keluar');
        },
        'jualDetails.barang' => function ($q) {
          $q->select(['id', 'nama', 'satuan', 'brand_id']);
        },
        'jualDetails.barang.brand:id,nama'
      ])
      ->withCount('jualDetails')
      ->whereBetween('tgl_faktur', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ]);

    if ($request->pelanggan_id) {
      $query = $query->where('pelanggan_id', $request->pelanggan_id);
    }

    if ($request->status_faktur) {
      $query->where('status_faktur', $request->status_faktur);
    } else {
      $query->whereIn('status_faktur', ['PROCESS_GUDANG', 'DONE']);
    }

    $data = $query->get()
      ->map(function ($jual) use ($request) {
        return [
          'pelanggan_nama' => $jual->pelanggan->nama,
          'nomor_faktur' => $jual->nomor_faktur,
          'tgl_faktur' => Carbon::parse($jual->tgl_faktur)->format('d/m/Y'),
          'status_faktur_label' => $jual->status_faktur_label,
          'jual_details_count' => $jual->jual_details_count,
          'jual_details' => $jual->jualDetails->map(fn($item): array => [
            'barang_nama' => $item->barang->nama,
            'brand_nama' => $item->barang->brand->nama,
            'barang_satuan' => $item->barang->satuan,
            'barang_id' => $item->barang->id,
            'jumlah_barang_dipesan' => $item->jumlah_barang_dipesan,
            'jumlah_barang_keluar' => $item->jumlah_barang_keluar,
            'status_barang_keluar_label' => $item->status_barang_keluar_label
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
      'status_faktur' => 'nullable|in:PROCESS_GUDANG,DONE',
    ];

    $messages = [
      'tgl_akhir.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari 2 bulan setelah tanggal awal.',
    ];

    if (!$request->filled('pelanggan_id')) {
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

    $data = $this->getLaporanJualData($request);

    return view('pages.laporan-jual.gudang.index', [
      'pelanggans' => $pelanggans,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'pelanggan_id' => $request->pelanggan_id,
      'status_faktur' => $request->status_faktur,
      'data' => $data
    ]);
  }

  public function exportExcel(Request $request)
  {
    $request->validate([
      'tgl_awal' => 'required|date',
      'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
      'pelanggan_id' => 'nullable|exists:pelanggans,id',
      'status_faktur' => 'nullable|in:PROCESS_GUDANG,DONE'
    ]);

    $data = $this->getLaporanJualData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dmY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dmY');
    $filename = "laporan_barang_keluar_all $tgl_awal $tgl_akhir.xlsx";

    if ($request->pelanggan_id) {
      $filename = "laporan_barang_keluar_pelanggan $tgl_awal $tgl_akhir.xlsx";
    }

    if ($request->status_faktur) {
      $status = $request->status_faktur == 'DONE' ? 'Selesai' : 'Proses Gudang';
      $filename = "laporan_barang_keluar_{$status} $tgl_awal $tgl_akhir.xlsx";
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
