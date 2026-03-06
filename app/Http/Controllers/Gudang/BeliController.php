<?php

namespace App\Http\Controllers\Gudang;

use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\BarangRetur;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Http\Request;

class BeliController extends Controller
{
  public function index()
  {
    return view('pages.beli.gudang.index');
  }

  public function done($id)
  {
    try {
      $beli = Beli::findOrFail($id);
      \Gate::authorize('done', $beli);
      $beli->status_faktur = StatusFaktur::DONE;
      $beli->save();

      return redirect()->route('gudang.beli.show', ['id' => $beli->id]);
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());

    }
  }

  public function show(int $id)
  {
    $beli = Beli::findOrFail($id);
    if ($beli->status_faktur === StatusFaktur::NEW) {
      abort(403);
    }

    $beli_details = BeliDetail::with(['barang'])->where('beli_id', $id)->orderByDesc('id')->get();

    $returs = BarangRetur::whereHasMorph(
      'returnable',
      [BeliDetail::class],
      function ($q) use ($id) {
        $q->where('beli_id', $id);
      }
    )
      ->with([
        'barang:id,nama,satuan,brand_id,kode',
        'barang.brand:id,nama',
        'returnable' => function ($q) {
          $q->select('id', 'beli_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.beli.gudang.show', compact('beli', 'beli_details', 'returs'));
  }

  public function updateKeterangan(Request $request, $id)
  {
    $request->validate([
      'keterangan' => 'required|string|max:255',
    ]);

    $retur = BarangRetur::findOrFail($id);
    $retur->keterangan = $request->keterangan;
    $retur->save();

    return redirect()->back()->with('success', 'Keterangan berhasil diperbarui.');
  }

}
