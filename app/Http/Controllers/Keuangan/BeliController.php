<?php

namespace App\Http\Controllers\Keuangan;

use App\Constants\Bayar;
use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\Beli;
use App\Models\BeliDetail;
use App\Models\BarangRetur;
use Illuminate\Http\Request;

class BeliController extends Controller
{
  public function index()
  {
    return view('pages.beli.keuangan.index');
  }
  public function show(int $id)
  {
    $beli = Beli::with('beliDetails')->findOrFail($id);
    if ($beli->status_faktur !== StatusFaktur::DONE) {
      abort(403, 'Masih diproses fakturis/gudang.');
    }

    $beli_details = $beli->beliDetails;
    $metode_bayars = Bayar::getAllMetodeBayar();
    $tipe_bayars = Bayar::getAllTipeBayar();

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
          $q->select('id', 'beli_id', 'batch', 'tgl_expired', 'barang_id', 'keterangan');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.beli.keuangan.show', compact('beli', 'beli_details', 'metode_bayars', 'tipe_bayars', 'returs'));
  }

  private function kontanPayment(Request $request, int $id)
  {
    $beli = Beli::findOrFail($id);

    if ($beli->bayar) {
      throw new \Exception('Gagal! Faktur harus dibayar dengan cara cicil!');
    }

    $data_bayar = [
      [
        "metode_bayar" => $request->metode_bayar,
        "tipe_bayar" => $request->tipe_bayar,
        "tgl_bayar" => $request->tgl_bayar,
        "x_cicil" => 0,
        "terbayar" => $beli->total_tagihan,
      ],
    ];

    $beli->bayar = $data_bayar;
    $beli->status_bayar = StatusBayar::PAID;
    $beli->save();
  }

  private function cicilPayment(Request $request, int $id)
  {
    if ($request->terbayar <= 0) {
      throw new \Exception('Gagal! Jumlah bayar tidak benar!');
    }

    $beli = Beli::findOrFail($id);

    if ($request->terbayar > $beli->total_tagihan) {
      throw new \Exception('Gagal! Melebihi total tagihan!');
    }

    if ($beli->bayar === null) {
      $data_bayar = [
        [
          "metode_bayar" => $request->metode_bayar,
          "tipe_bayar" => $request->tipe_bayar,
          "tgl_bayar" => $request->tgl_bayar,
          "x_cicil" => 1,
          "terbayar" => $request->terbayar,
        ],
      ];

      $beli->bayar = $data_bayar;
      $beli->save();
    } else {
      $data_bayar = $beli->bayar;

      array_push($data_bayar, [
        "metode_bayar" => $request->metode_bayar,
        "tipe_bayar" => $request->tipe_bayar,
        "tgl_bayar" => $request->tgl_bayar,
        "x_cicil" => $beli->bayar[0]['x_cicil'] + 1,
        "terbayar" => $request->terbayar,
      ]);

      $total_terbayar = array_sum(array_column($data_bayar, 'terbayar'));

      if ($total_terbayar > $beli->total_tagihan) {
        throw new \Exception('Gagal! Melebihi total tagihan!');
      }

      $beli->status_bayar = $beli->total_tagihan == $total_terbayar ? StatusBayar::PAID : StatusBayar::UNPAID;
      $beli->bayar = $data_bayar;
      $beli->save();
    }
  }

  public function payment(int $id, Request $request)
  {
    $request->validate([
      'keterangan_bayar' => 'nullable|string|max:255',
      'tgl_bayar' => 'required|date',
      'metode_bayar' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, Bayar::getAllMetodeBayar())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
      'tipe_bayar' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!in_array($value, Bayar::getAllTipeBayar())) {
            $fail('The selected ' . $attribute . ' is invalid.');
          }
        }
      ],
    ]);

    $beli = Beli::findOrFail($id);
    $beli->keterangan_bayar = $request->keterangan_bayar;
    $beli->save();

    try {
      \Gate::authorize("payment", $beli);

      if ($request->tipe_bayar == Bayar::KONTAN) {
        $this->kontanPayment($request, $id);
      } else {
        $this->cicilPayment($request, $id);
      }

      return redirect()->route('keuangan.beli.show', ['id' => $beli->id])->with("success", "Berhasil melakukan pembayaran.");

    } catch (\Exception $e) {
      return back()->withInput()->with("error", $e->getMessage());
    }

  }
}
