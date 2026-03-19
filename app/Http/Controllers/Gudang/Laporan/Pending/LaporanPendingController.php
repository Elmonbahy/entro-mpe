<?php

namespace App\Http\Controllers\Gudang\Laporan\Pending;

use App\Http\Controllers\Controller;
use App\Models\Jual;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPendingController extends Controller
{

  private function getLaporanPendingData(Request $request)
  {

    $query = Jual::with([
      'pelanggan:nama,id',
      'salesman:nama,id',
    ])
      ->whereIn('status_kirim', ['PENDING'])
      ->whereIn('status_faktur', ['PROCESS_GUDANG', 'DONE'])
      ->whereBetween('tgl_faktur', [
        $request->tgl_awal,
        $request->tgl_akhir ? Carbon::parse($request->tgl_akhir)->endOfDay() : null
      ]);

    if ($request->pelanggan_id) {
      $query = $query->where('pelanggan_id', $request->pelanggan_id);
    }

    $data = $query->get()
      ->map(function ($jual) use ($request) {
        return [
          'pelanggan_nama' => $jual->pelanggan->nama,
          'nomor_faktur' => $jual->nomor_faktur,
          'tgl_faktur' => Carbon::parse($jual->tgl_faktur)->format('d/m/Y'),
          'sales' => $jual?->salesman?->nama ?? '-',
          'status_kirim' => $jual->status_kirim,
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

    $data = $this->getLaporanPendingData($request);

    return view('pages.laporan-pending.gudang.index', [
      'pelanggans' => $pelanggans,
      'tgl_awal' => $request->tgl_awal,
      'tgl_akhir' => $request->tgl_akhir,
      'pelanggan_id' => $request->pelanggan_id,
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

    $data = $this->getLaporanPendingData($request);

    $tgl_awal = Carbon::parse($request->tgl_awal)->format('dmY');
    $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('dmY');
    $filename = "laporan_pending $tgl_awal $tgl_akhir.xlsx";

    if ($request->pelanggan_id) {
      $filename = "laporan_pending_pelanggan $tgl_awal $tgl_akhir.xlsx";
    }

    if ($data->isEmpty()) {
      return back()->withInput()->with('error', 'Data tidak tersedia.');
    }

    return Excel::download(
      new LaporanPendingExport($data, $request->tgl_awal, $request->tgl_akhir),
      $filename
    );
  }

}
