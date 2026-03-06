<?php

namespace App\Livewire\Jual;

use App\Models\Barang;
use App\Models\BarangStock;
use App\Models\Brand;
use App\Models\SpbeliDetail;
use App\Models\Spjual;
use App\Models\SpjualDetail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class TambahPemesananBarang extends Component
{
  #[Locked]
  public $satuan;
  #[Locked]
  public int $spjual_id;

  public $barang_id;
  public $brand_id;

  // inputan user untuk sp jual detail
  public $keterangan;
  public $jumlah_barang_dipesan = 0;

  #[Computed()]
  public function brands()
  {
    return Brand::select('nama', 'id')->get();
  }

  #[Computed()]
  private function barangs()
  {
    return Barang::select('id', 'nama')->where('brand_id', $this->brand_id)->get();
  }

  #[Computed()]
  private function barang()
  {
    return Barang::select('id', 'nama', 'satuan')->find($this->barang_id);
  }
  public function mount(int $spjual_id)
  {
    $this->spjual_id = $spjual_id;
  }

  #[On('Jual.TambahPemesananBarang:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->reset(['barang_id', 'satuan']);
      $this->dispatch('Jual.TambahPemesananBarang:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('Jual.TambahPemesananBarang:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->barang_id = (int) $id;
      $this->satuan = $this->barang ? $this->barang->satuan : null;
    }
  }

  /**
   * Reset all state except on mount
   */
  private function resetAllState()
  {
    $this->reset(['barang_id', 'satuan', 'keterangan', 'jumlah_barang_dipesan']);
  }

  private function createSpJualDetail()
  {
    try {
      \DB::transaction(function () {
        SpjualDetail::create([
          'jumlah_barang_dipesan' => $this->jumlah_barang_dipesan,
          'spjual_id' => $this->spjual_id,
          'barang_id' => $this->barang_id,
          'keterangan' => $this->keterangan,
        ]);
      });

      $this->dispatch('refresh-daftar-pemesanan-barang');
      $this->resetAllState();
      $this->dispatch('reset-barang');
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('" . $msg . "')");
    }
  }

  public function rules()
  {
    return [
      'barang_id' => 'required|exists:barangs,id',
      'spjual_id' => 'required|exists:spjuals,id',
      'keterangan' => 'nullable|string',
      'jumlah_barang_dipesan' => 'required|numeric|min:1',
    ];
  }

  public function save()
  {
    $this->validate();
    $this->createSpJualDetail();
  }
  public function render()
  {
    return view('livewire.jual.tambah-pemesanan-barang')->with([
      'brands' => $this->brands,
    ]);
  }
}
