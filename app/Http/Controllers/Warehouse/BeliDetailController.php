<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Livewire\Table\BeliTable;
use App\Models\BarangRetur;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BeliDetailController extends Controller
{
  public function exportQc(int $beli_id, int $beli_detail_id)
  {
    $beli = Beli::findOrFail($beli_id);

    $beli_detail = BeliDetail::with(['barang:id,nama,satuan,kode,nie,brand_id', 'barang.brand:id,nama'])
      ->where('id', $beli_detail_id)
      ->where('beli_id', $beli_id)
      ->firstOrFail();

    $pdf = Pdf::loadView('pages.qc-beli.warehouse.pdf', [
      'beli' => $beli,
      'beli_detail' => $beli_detail,
    ]);
    $pdf->setPaper('A4');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'QC_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $beli_detail->barang->nama) . '.pdf';

    return $pdf->stream($filename);
  }

  public function exportQcAll(int $beli_id)
  {
    $beli = Beli::findOrFail($beli_id);

    // Ambil semua beli_detail untuk beli tersebut beserta relasi barang dan brand
    $beli_details = BeliDetail::with(['barang:id,nama,satuan,kode,nie,brand_id', 'barang.brand:id,nama'])
      ->where('beli_id', $beli_id)
      ->get();

    $pdf = Pdf::loadView('pages.qc-beli.warehouse.pdf-all', [
      'beli' => $beli,
      'beli_details' => $beli_details,
    ]);
    $pdf->setPaper('A4');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'QC_All_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $beli->nomor_faktur) . '.pdf';

    return $pdf->stream($filename);
  }
}