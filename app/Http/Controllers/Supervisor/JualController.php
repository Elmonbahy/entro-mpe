<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangRetur;
use App\Models\Jual;
use App\Models\Salesman;
use App\Models\Pelanggan;
use App\Constants\TipePenjualan;
use App\Models\JualDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JualController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return view('pages.jual.supervisor.index');
  }

  public function show(int $id)
  {
    $jual = Jual::with([
      'jualDetails',
      'suratJalanDetails.suratJalan.kendaraan',
    ])->findOrFail($id);

    $jual_details = JualDetail::where('jual_id', $id)->get();

    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [JualDetail::class],
      function ($q) use ($id) {
        $q->where('jual_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id,kode',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'jual_id', 'batch', 'tgl_expired', 'barang_id');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.jual.supervisor.show', [
      'jual' => $jual,
      'jual_details' => $jual_details,
      'returs' => $returs,
    ]);
  }

}
