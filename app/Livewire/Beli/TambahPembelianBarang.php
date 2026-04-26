<?php

namespace App\Livewire\Beli;

use App\Enums\StatusFaktur;
use App\Models\Barang;
use App\Models\Beli;
use App\Models\BeliDetail;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TambahPembelianBarang extends Component
{
  public $brand_id = null;
  public $barang_id = null;
  public $satuan = null;

  #[Locked]
  public $beli_id = null;

  public $batch = null;
  public $tgl_expired = null;
  public $keterangan = null;
  public $jumlah_barang_dipesan = 0;
  public $harga_beli = 0;
  public $harga_beli_terakhir = 0;

  public function mount(int $beli_id)
  {
    $this->beli_id = $beli_id;
  }

  public function rules()
  {
    return [
      'barang_id' => 'required|exists:barangs,id',
      'batch' => 'nullable|string',
      'tgl_expired' => 'nullable|date|after_or_equal:today',
      'keterangan' => 'nullable|string',
      'jumlah_barang_dipesan' => 'required|numeric|min:1',
      'harga_beli' => 'required|numeric|min:0',
      'beli_id' => 'required|exists:belis,id',
    ];
  }

  public function updated($property)
  {
    if ($property === 'harga_beli') {
      // Hapus semua karakter kecuali angka dan koma
      $cleanValue = preg_replace('/[^0-9,]/', '', $this->harga_beli);

      // Pastikan hanya ada satu koma sebagai pemisah desimal
      $decimalParts = explode(',', $cleanValue);
      if (count($decimalParts) > 2) {
        $cleanValue = $decimalParts[0] . ',' . implode('', array_slice($decimalParts, 1));
      }

      // Ubah nilai ke float untuk pemrosesan lebih lanjut
      $floatValue = (float) str_replace(',', '.', $cleanValue);

      // Format dinamis: hanya tampilkan desimal kalau ada koma
      if (fmod($floatValue, 1) == 0) {
        $this->harga_beli = number_format($floatValue, 0, ',', '.');
      } else {
        $this->harga_beli = rtrim(rtrim(number_format($floatValue, 4, ',', '.'), '0'), ',');
      }
    }
  }

  public function formatHargaBeli()
  {
    // Pastikan input tetap terformat saat kehilangan fokus
    $this->updatedHargaBeli();
  }

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
    $item = Barang::select('id', 'nama', 'satuan')->find($this->barang_id);

    if ($item) {
      $lastPurchase = BeliDetail::where('barang_id', $item->id)
        ->latest()
        ->first(['harga_beli']);

      if ($lastPurchase) {
        $harga = (float) $lastPurchase->harga_beli;
        $this->harga_beli_terakhir = (fmod($harga, 1) == 0)
          ? number_format($harga, 0, ',', '.')
          : rtrim(rtrim(number_format($harga, 4, ',', '.'), '0'), ',');
      } else {
        // PENTING: Reset ke 0 jika barang belum pernah dibeli sebelumnya
        $this->harga_beli_terakhir = 0;
      }
    } else {
      // Reset jika tidak ada barang yang dipilih (barang_id null)
      $this->harga_beli_terakhir = 0;
    }

    return $item;
  }

  #[On('Beli.TambahPembelianBarang:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->reset(['barang_id', 'satuan', 'harga_beli_terakhir']);
      $this->dispatch('Beli.TambahPembelianBarang:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('Beli.TambahPembelianBarang:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->barang_id = (int) $id;
      $this->satuan = $this->barang ? $this->barang->satuan : null;
    }
  }

  private function createBeliDetail()
  {
    try {
      \DB::transaction(function () {
        $beli = Beli::findOrFail($this->beli_id);
        \Gate::authorize('createBeliDetail', $beli);

        BeliDetail::create([
          'beli_id' => $this->beli_id,
          'barang_id' => $this->barang_id,
          'jumlah_barang_dipesan' => $this->jumlah_barang_dipesan,
          'batch' => $this->batch ?: null,
          'tgl_expired' => $this->tgl_expired ?: null,
          'keterangan' => $this->keterangan,
          'harga_beli' => $this->harga_beli,
        ]);

        $beli->status_faktur = StatusFaktur::PROCESS_FAKTUR;
        $beli->save();
      });

      $this->dispatch('refresh-daftar-pembelian-barang');

      $this->reset(['harga_beli', 'tgl_expired', 'batch', 'keterangan', 'jumlah_barang_dipesan', 'satuan', 'barang_id', 'brand_id', 'harga_beli_terakhir']);

      $this->dispatch('Beli.TambahPembelianBarang:created');
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('" . $msg . "')");
    }
  }

  public function save()
  {
    if (!$this->barang_id) {
      return $this->js('alert("Gagal! Barang belum dipilih.")');
    }

    // Pastikan harga_beli memiliki format angka yang benar
    $this->harga_beli = (float) str_replace(',', '.', str_replace('.', '', $this->harga_beli));

    $this->validate();
    $this->createBeliDetail();
  }

  public function render()
  {
    return view('livewire.beli.tambah-pembelian-barang')->with([
      'brands' => $this->brands,
    ]);
  }
}
