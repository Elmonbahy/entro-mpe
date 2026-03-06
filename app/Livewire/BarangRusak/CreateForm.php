<?php

namespace App\Livewire\BarangRusak;

use App\Enums\PenyebabBarangRusak;
use App\Enums\TindakanBarangRusak;
use App\Models\Barang;
use App\Models\BarangRusak;
use App\Models\BarangStock;
use App\Models\Brand;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CreateForm extends Component
{
  public $brand_id = null;
  public $barang_id = null;

  public $batch;
  public $tgl_expired;
  public $penyebab;
  public $tindakan;
  public $jumlah_barang_rusak;
  public $keterangan;
  public $tgl_rusak;


  private function create()
  {
    \DB::beginTransaction();
    try {
      $barang_stock = BarangStock::where('barang_id', $this->barang_id)
        ->where('batch', $this->batch ?: null)
        ->where('tgl_expired', $this->tgl_expired ?: null)
        ->first();

      if (!$barang_stock) {
        abort(404, 'Stock barang tidak ditemukan.');
      }

      if ($this->jumlah_barang_rusak > $barang_stock->jumlah_stock) {
        abort(404, 'Melebihi stock barang.');
      }

      $stock_awal = (int) $barang_stock->jumlah_stock;
      $barang_stock->update([
        'jumlah_stock' => $barang_stock->jumlah_stock - $this->jumlah_barang_rusak
      ]);

      $barang_rusak = BarangRusak::create([
        "penyebab" => $this->penyebab,
        "tindakan" => $this->tindakan,
        "jumlah_barang_rusak" => $this->jumlah_barang_rusak,
        "keterangan" => $this->keterangan ?: null,
        "barang_stock_id" => $barang_stock->id,
        'tgl_rusak' => Carbon::now(),
      ]);

      $barang_rusak->mutation()->create([
        'stock_awal' => $stock_awal,
        'stock_rusak' => $this->jumlah_barang_rusak,
        'stock_akhir' => $barang_stock->jumlah_stock,
        'barang_id' => $barang_stock->barang_id,
        'batch' => $barang_stock->batch,
        'tgl_expired' => $barang_stock->tgl_expired,
        'tgl_mutation' => Carbon::now(),
      ]);

      \DB::commit();

      return redirect()
        ->route('gudang.barang-rusak.index')
        ->with('success', 'Berhasil menambah data barang rusak.');
    } catch (\Exception $e) {
      \DB::rollBack();

      $msg = $e->getMessage();
      $this->js("alert('Gagal! " . $msg . "')");
    }
  }

  #[Computed()]
  public function brands()
  {
    return Brand::select("id", "nama")->get();
  }

  #[Computed()]
  private function barangs()
  {
    return Barang::select("id", "nama")
      ->where('brand_id', $this->brand_id ?? null)
      ->get();
  }

  #[Computed()]
  private function barang()
  {
    return Barang::with('brand')
      ->where('id', $this->barang_id)
      ->select('id', 'satuan', 'brand_id')
      ->first();
  }

  #[Computed()]
  public function penyebabs()
  {
    return PenyebabBarangRusak::toObjectArray();
  }

  #[Computed()]
  public function tindakans()
  {
    return TindakanBarangRusak::toObjectArray();
  }

  #[On('BarangRusak.CreateForm:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->barang_id = null;
      $this->dispatch('BarangRusak.CreateForm:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('BarangRusak.CreateForm:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->barang_id = (int) $id;
      $this->dispatch('BarangRusak.CreateForm:barangChanged', ['data' => $this->barang]);
    }
  }

  public function rules()
  {
    return [
      'barang_id' => 'required|exists:barangs,id',
      'batch' => 'nullable|string',
      'tgl_expired' => 'nullable|date',
      // 'tgl_rusak' => 'required|date',
      'keterangan' => 'nullable|string',
      'jumlah_barang_rusak' => 'required|numeric|min:1',
      'penyebab' => [
        'required',
        'string',
        Rule::in(array_map(fn($case) => $case->value, PenyebabBarangRusak::cases())),
      ],
      'tindakan' => [
        'required',
        'string',
        Rule::in(array_map(fn($case) => $case->value, TindakanBarangRusak::cases())),
      ],
    ];
  }

  public function save()
  {
    if (!$this->barang_id) {
      return $this->js('alert("Barang belum dipilih")');
    }

    $this->validate();
    $this->create();
  }

  public function render()
  {

    return view('livewire.barang-rusak.create-form', [
      'brands' => $this->brands,
      'penyebabs' => $this->penyebabs,
      'tindakans' => $this->tindakans,
    ]);
  }
}
