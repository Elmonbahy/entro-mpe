<?php

namespace App\Http\Controllers\Keuangan;

use App\Constants\Bayar;
use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Http\Controllers\Controller;
use App\Models\Jual;
use App\Models\JualDetail;
use App\Models\BarangRetur;
use Illuminate\Http\Request;

class JualController extends Controller
{
  public function index()
  {
    return view('pages.jual.keuangan.index');
  }

  public function show(int $id)
  {
    $jual = Jual::with('jualDetails')->findOrFail($id);

    if ($jual->status_faktur !== StatusFaktur::DONE) {
      abort(403, 'Masih diproses fakturis/gudang.');
    }

    $jual_details = $jual->jualDetails;
    $metode_bayars = Bayar::getAllMetodeBayar();
    $tipe_bayars = Bayar::getAllTipeBayar();

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
          $q->select('id', 'jual_id', 'batch', 'tgl_expired', 'barang_id');
        }
      ])
      ->orderBy('id', 'desc')
      ->get();

    return view('pages.jual.keuangan.show', compact('jual', 'jual_details', 'metode_bayars', 'tipe_bayars', 'returs'));
  }
  private function kontanPayment(Request $request, int $id)
  {
    $jual = Jual::findOrFail($id);

    if ($jual->bayar) {
      throw new \Exception('Gagal! Faktur harus dibayar dengan cara cicil!');
    }

    $data_bayar = [
      [
        "metode_bayar" => $request->metode_bayar,
        "tipe_bayar" => $request->tipe_bayar,
        "tgl_bayar" => $request->tgl_bayar,
        "x_cicil" => 0,
        "terbayar" => $jual->total_tagihan,
      ],
    ];

    $jual->bayar = $data_bayar;
    $jual->status_bayar = StatusBayar::PAID;
    $jual->save();
  }
  private function cicilPayment(Request $request, int $id)
  {
    if ($request->terbayar <= 0) {
      throw new \Exception('Gagal! Jumlah bayar tidak benar!');
    }

    $jual = Jual::findOrFail($id);

    if ($request->terbayar > $jual->total_tagihan) {
      throw new \Exception('Gagal! Melebihi total tagihan!');
    }

    if ($jual->bayar === null) {
      $data_bayar = [
        [
          "metode_bayar" => $request->metode_bayar,
          "tipe_bayar" => $request->tipe_bayar,
          "tgl_bayar" => $request->tgl_bayar,
          "x_cicil" => 1,
          "terbayar" => $request->terbayar,
        ],
      ];

      $jual->bayar = $data_bayar;
      $jual->save();
    } else {
      $data_bayar = $jual->bayar;

      $x_cicil = count($data_bayar) + 1;

      array_push($data_bayar, [
        "metode_bayar" => $request->metode_bayar,
        "tipe_bayar" => $request->tipe_bayar,
        "tgl_bayar" => $request->tgl_bayar,
        "x_cicil" => $x_cicil,
        "terbayar" => $request->terbayar,
      ]);

      $total_terbayar = array_sum(array_column($data_bayar, 'terbayar'));

      if ($total_terbayar > $jual->total_tagihan) {
        throw new \Exception('Gagal! Melebihi total tagihan!');
      }

      $jual->status_bayar = $jual->total_tagihan == $total_terbayar ? StatusBayar::PAID : StatusBayar::UNPAID;
      $jual->bayar = $data_bayar;
      $jual->save();
    }
  }

  public function update(int $id, Request $request)
  {
    $request->validate([
      'keterangan_bayar' => 'nullable|string|max:255',
      'is_pungut_ppn' => 'required|in:0,1',
    ]);

    $jual = Jual::findOrFail($id);

    if ($jual->is_pungut_ppn !== (int) $request->is_pungut_ppn) {
      $jual->bayar = null;
      $jual->status_bayar = StatusBayar::UNPAID;
    }

    $jual->keterangan_bayar = $request->keterangan_bayar;
    $jual->is_pungut_ppn = $request->is_pungut_ppn;
    $jual->save();

    return redirect()->route('keuangan.jual.show', ['id' => $jual->id])->with("success", "Berhasil mengubah data penjualan.");
  }

  public function payment(int $id, Request $request)
  {
    $request->validate([
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

    $jual = Jual::findOrFail($id);

    try {
      \Gate::authorize("payment", $jual);

      if ($request->tipe_bayar == Bayar::KONTAN) {
        $this->kontanPayment($request, $id);
      } else {
        $this->cicilPayment($request, $id);
      }

      return redirect()->route('keuangan.jual.show', ['id' => $jual->id])->with("success", "Berhasil melakukan pembayaran.");
    } catch (\Exception $e) {
      return back()->withInput()->with("error", $e->getMessage());
    }
  }
}
