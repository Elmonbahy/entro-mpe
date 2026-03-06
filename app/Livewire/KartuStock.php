<?php

namespace App\Livewire;

use App\Models\Barang;
use App\Models\BarangRusak;
use App\Models\BarangStockAwal;
use App\Models\BeliDetail;
use App\Models\Brand;
use App\Models\JualDetail;
use App\Models\Mutation;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Carbon\Carbon;

class KartuStock extends Component
{

  public $brand_id = null;
  public $barang_id = null;
  public $barangs = null;
  public $tgl_awal = null;
  public $tgl_akhir = null;
  public $brand = null;

  public function mount(?int $barang_id, ?int $brand_id, $tgl_awal, $tgl_akhir)
  {
    $this->brand_id = $brand_id;
    $this->barang_id = $barang_id;
    $this->tgl_awal = $tgl_awal ?? '2025-01-01';
    $this->tgl_akhir = $tgl_akhir ?? Carbon::now()->toDateString();
    $this->brand = $this->barang?->brand->nama;
    $this->getBarangs();

  }

  #[Computed()]
  public function brands()
  {
    return Brand::select("id", "nama")->get();
  }

  private function getBarangs()
  {
    $this->barangs = Barang::select("id", "nama")
      ->where('brand_id', $this->brand_id)
      ->get();
  }

  #[Computed()]
  private function barang()
  {
    $barang = Barang::with('brand')
      ->where('id', $this->barang_id)
      ->select('id', 'satuan', 'brand_id')
      ->first();

    return $barang;
  }

  #[On('KartuStock:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->getBarangs();
      $this->barang_id = null;
      $this->dispatch('KartuStock:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('KartuStock:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->barang_id = (int) $id;
      $this->dispatch('KartuStock:barangChanged', ['data' => $this->barang]);
    }
  }

  public function render()
  {
    // dd($this->brand);
    return view('livewire.kartu-stock', [
      'brands' => $this->brands,
      'barangs' => $this->barangs,
      'brand' => $this->brand
    ]);
  }
}
