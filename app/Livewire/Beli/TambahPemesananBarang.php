<?php

namespace App\Livewire\Beli;

use App\Models\Barang;
use App\Models\Brand;
use App\Models\Spbeli;
use App\Models\SpbeliDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TambahPemesananBarang extends Component
{
  #[Locked]
  public $satuan;

  // from table beli
  #[Locked]
  public $spbeli_id;

  #[Locked]
  public $harga_beli_formatted;

  public $brand_id = null;
  public $barang_id;
  public $keterangan;
  public $jumlah_barang_dipesan = 0;
  public $harga_beli = 0;

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

  public function mount(int $spbeli_id)
  {
    $this->spbeli_id = $spbeli_id;
  }

  private function setHargaBeli(int $barang_id)
  {
    $result = (float) SpbeliDetail::where('barang_id', $barang_id)
      ->latest()
      ->value('harga_beli') ?? 0.00;

    $this->harga_beli_formatted = \Number::currency($result, 'IDR', 'id_ID');
  }

  private function createSpBeliDetail()
  {
    try {
      \DB::transaction(function () {
        SpbeliDetail::create([
          'spbeli_id' => $this->spbeli_id,
          'barang_id' => $this->barang_id,
          'jumlah_barang_dipesan' => $this->jumlah_barang_dipesan,
          'keterangan' => $this->keterangan,
          'harga_beli' => $this->harga_beli,
        ]);
      });

      $this->dispatch('refresh-daftar-pemesanan-barang');
      $this->reset(['harga_beli', 'keterangan', 'jumlah_barang_dipesan', 'satuan']);
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('" . $msg . "')");
    }
  }

  #[On('Beli.TambahPemesananBarang:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->reset(['barang_id', 'satuan']);
      $this->dispatch('Beli.TambahPemesananBarang:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('Beli.TambahPemesananBarang:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->barang_id = (int) $id;
      $this->satuan = $this->barang ? $this->barang->satuan : null;
      $this->setHargaBeli($id);
    }
  }

  public function rules()
  {
    return [
      'barang_id' => 'required|exists:barangs,id',
      'keterangan' => 'nullable|string',
      'jumlah_barang_dipesan' => 'required|numeric|min:1',
      'harga_beli' => 'required|numeric|min:1',
      'spbeli_id' => 'required|exists:spbelis,id',
    ];
  }

  public function save()
  {
    $this->validate();
    $this->createSpBeliDetail();
  }

  public function render()
  {
    return view('livewire.beli.tambah-pemesanan-barang')->with([
      'brands' => $this->brands,
    ]);
  }
}
