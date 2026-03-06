<?php

namespace App\Http\Controllers\Accounting;

use App\Constants\Bayar;
use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Models\JualDetail;
use App\Http\Controllers\Controller;
use App\Models\Jual;
use App\Models\BarangRetur;
use Illuminate\Http\Request;

class JualController extends Controller
{
  public function index()
  {
    return view('pages.jual.accounting.index');
  }

  public function show(int $id)
  {
    $jual = Jual::with([
      'jualDetails',
      'suratJalanDetails.suratJalan.kendaraan',
    ])->findOrFail($id);
    if (!in_array($jual->status_faktur, [StatusFaktur::DONE, StatusFaktur::PROCESS_GUDANG, StatusFaktur::PROCESS_FAKTUR])) {
      abort(403, 'Masih diproses fakturis.');
    }

    $jual_details = JualDetail::where('jual_id', $id)->get();

    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [JualDetail::class],
      function ($q) use ($id) {
        $q->where('jual_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'jual_id', 'batch', 'tgl_expired', 'barang_id', 'created_at');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();


    return view('pages.jual.accounting.show', [
      'jual' => $jual,
      'jual_details' => $jual_details,
      'returs' => $returs
    ]);
  }

}
