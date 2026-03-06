<?php

namespace App\Http\Controllers\Logistik;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Http\Request;

class BeliController extends Controller
{
  public function index()
  {
    return view('pages.beli.logistik.index');
  }

  public function show(int $id)
  {
    $beli = Beli::with('beliDetails')->findOrFail($id);
    if ($beli->status_faktur !== StatusFaktur::DONE) {
      abort(403, 'Masih diproses fakturis/gudang.');
    }

    $beli_details = $beli->beliDetails;

    return view('pages.beli.logistik.show', compact('beli', 'beli_details'));
  }

}
