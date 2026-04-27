<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\Jual;
use App\Models\JualDetail;

class JualController extends Controller
{
  public function index()
  {
    return view('pages.jual.gudang.index');
  }

  public function show(int $id)
  {
    $jual = Jual::with([
      'jualDetails',
    ])->findOrFail($id);
    if ($jual->status_faktur === StatusFaktur::NEW) {
      abort(403);
    }

    $jual_detail = JualDetail::with(['barang'])->where('jual_id', $id)->orderByDesc('id')->get();

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
          $q->select('id', 'jual_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.jual.gudang.show', compact('jual', 'jual_detail', 'returs'));
  }


  public function done($id)
  {
    try {
      $jual = Jual::findOrFail($id);
      \Gate::authorize('done', $jual);
      $jual->status_faktur = StatusFaktur::DONE;
      $jual->save();

      return redirect()->route('gudang.jual.show', ['id' => $jual->id]);
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());

    }
  }


}
