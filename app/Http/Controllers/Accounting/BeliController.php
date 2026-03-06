<?php

namespace App\Http\Controllers\Accounting;

use App\Constants\Bayar;
use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\Beli;
use App\Models\BarangRetur;
use App\Models\BeliDetail;
use Illuminate\Http\Request;

class BeliController extends Controller
{
  public function index()
  {
    return view('pages.beli.accounting.index');
  }
  public function show(int $id)
  {
    $beli = Beli::with('beliDetails')->findOrFail($id);

    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [BeliDetail::class],
      function ($q) use ($id) {
        $q->where('beli_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'beli_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan', 'created_at');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    if (!in_array($beli->status_faktur, [StatusFaktur::DONE, StatusFaktur::PROCESS_GUDANG, StatusFaktur::PROCESS_FAKTUR])) {
      abort(403, 'Masih diproses fakturis.');
    }

    $beli_details = $beli->beliDetails;

    return view('pages.beli.accounting.show', [
      'beli' => $beli,
      'beli_details' => $beli_details,
      'returs' => $returs
    ]);
  }
}
