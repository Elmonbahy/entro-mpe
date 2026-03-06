<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Livewire\Table\BeliTable;
use App\Models\BarangRetur;
use App\Models\Jual;
use App\Models\JualDetail;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class JualDetailController extends Controller
{
  public function exportQc(int $jual_id, int $jual_detail_id)
  {
    $jual = Jual::findOrFail($jual_id);

    $jual_detail = JualDetail::with(['barang:id,nama,satuan,kode,nie,brand_id', 'barang.brand:id,nama'])
      ->where('id', $jual_detail_id)
      ->where('jual_id', $jual_id)
      ->firstOrFail();

    $pdf = Pdf::loadView('pages.qc-jual.warehouse.pdf', [
      'jual' => $jual,
      'jual_detail' => $jual_detail,
    ]);
    $pdf->setPaper('A4');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'QC_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $jual_detail->barang->nama) . '.pdf';

    return $pdf->stream($filename);
  }

  public function exportQcAll(int $jual_id)
  {
    $jual = Jual::findOrFail($jual_id);

    // Ambil semua jual_detail untuk jual tersebut beserta relasi barang dan brand
    $jual_details = JualDetail::with(['barang:id,nama,satuan,kode,nie,brand_id', 'barang.brand:id,nama'])
      ->where('jual_id', $jual_id)
      ->get();

    $pdf = Pdf::loadView('pages.qc-jual.warehouse.pdf-all', [
      'jual' => $jual,
      'jual_details' => $jual_details,
    ]);
    $pdf->setPaper('A4');
    $pdf->setOption('isJavascriptEnabled', false);
    $pdf->setOption('isRemoteEnabled', true);

    $filename = 'QC_All_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $jual->nomor_faktur) . '.pdf';

    return $pdf->stream($filename);
  }
}