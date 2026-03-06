<?php

namespace App\Livewire;

use App\Models\SampleBarang;
use App\Models\Brand;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Carbon\Carbon;

class SampleKartuStock extends Component
{
  public $brand_id = null;
  public $barang_id = null;
  public $barangs = [];
  public $tgl_awal = null;
  public $tgl_akhir = null;
  public $brand = null;

  public function mount(?int $barang_id = null, ?int $brand_id = null, $tgl_awal = null, $tgl_akhir = null)
  {
    $this->brand_id = $brand_id;
    $this->barang_id = $barang_id;
    $this->tgl_awal = $tgl_awal ?? '2025-01-01';
    $this->tgl_akhir = $tgl_akhir ?? Carbon::now()->toDateString();

    // Ambil daftar barang berdasarkan brand
    $this->getBarangs();

    // Set nama brand berdasarkan barang_id jika ada
    if ($this->barang_id) {
      $barang = SampleBarang::with('barang.brand')->find($this->barang_id);
      $this->brand = $barang?->barang?->brand?->nama;
    } elseif ($this->brand_id) {
      $brand = Brand::find($this->brand_id);
      $this->brand = $brand?->nama;
    }
  }

  #[Computed()]
  public function brands()
  {
    return Brand::select('id', 'nama')->get();
  }

  private function getBarangs()
  {
    if (!$this->brand_id) {
      $this->barangs = [];
      return;
    }

    $this->barangs = SampleBarang::select('id', 'barang_id')
      ->with([
        'barang' => fn($q) => $q->select('id', 'nama', 'brand_id')
      ])
      ->whereHas('barang', fn($q) => $q->where('brand_id', $this->brand_id))
      ->get()
      ->map(fn($sample) => [
        'id' => $sample->id,
        'nama' => $sample->barang->nama,
      ]);
  }

  #[Computed()]
  private function barang()
  {
    return SampleBarang::with(['barang.brand'])
      ->where('id', $this->barang_id)
      ->select('id', 'barang_id', 'satuan')
      ->first();
  }

  #[On('SampleKartuStock:onBrandChange')]
  public function onBrandChange($id)
  {
    $this->brand_id = (int) $id;
    $this->getBarangs();
    $brand = Brand::find($this->brand_id);
    $this->brand = $brand?->nama;

    // Kosongkan barang saat brand berubah
    $this->barang_id = null;

    $this->dispatch('SampleKartuStock:brandChanged', ['data' => $this->barangs]);
  }

  #[On('SampleKartuStock:onBarangChange')]
  public function onBarangChange($id)
  {
    $this->barang_id = (int) $id;
    $this->dispatch('SampleKartuStock:barangChanged', ['data' => $this->barang]);
  }

  public function render()
  {
    return view('livewire.sample-kartu-stock', [
      'brands' => $this->brands,
      'barangs' => $this->barangs,
      'brand' => $this->brand,
    ]);
  }
}
