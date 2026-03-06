<?php

namespace App\Http\Controllers\Logistik;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\Jual;
use App\Models\JualDetail;
class JualController extends Controller
{
  public function index()
  {
    return view('pages.jual.logistik.index');
  }

  public function show(int $id)
  {
    $jual = Jual::findOrFail($id);

    if ($jual->status_faktur !== StatusFaktur::DONE) {
      abort(403, 'Masih diproses fakturis/gudang.');
    }

    $jual_detail = JualDetail::with(['barang'])->where('jual_id', $id)->orderByDesc('id')->get();

    return view('pages.jual.logistik.show', compact('jual', 'jual_detail'));
  }

}
