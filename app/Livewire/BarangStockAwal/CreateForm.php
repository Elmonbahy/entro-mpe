<?php

namespace App\Livewire\BarangStockAwal;

use App\Enums\JenisPerubahan;
use App\Models\Barang;
use App\Models\BarangStock;
use App\Models\BarangStockAwal;
use App\Models\Brand;
use DB;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateForm extends Component
{
  public $brand_id = null;
  public $barang_id = null;
  public $batch = null;
  public $tgl_expired = null;
  public $jumlah_stock;
  public $jenis_perubahan;
  // public $tgl_stock;
  public $keterangan;

  private function create()
  {
    try {
      DB::transaction(function () {
        $batch = $this->batch ?: null;
        $tgl_expired = $this->tgl_expired ?: null;
        $jenis_perubahan = $this->jenis_perubahan;
        $barang_id = $this->barang_id;
        $input_stock = $this->jumlah_stock;
        // $tgl_stock = $this->tgl_stock;
        $keterangan = $this->keterangan;

        $barang_stock_awal = BarangStockAwal::create([
          'barang_id' => $barang_id,
          'jumlah_stock' => $input_stock,
          'batch' => $batch,
          'tgl_expired' => $tgl_expired,
          'jenis_perubahan' => $jenis_perubahan,
          'tgl_stock' => Carbon::now(),
          'keterangan' => $keterangan
        ]);

        $barang_stock = BarangStock::where('barang_id', $barang_id)
          ->where('batch', $batch)
          ->where('tgl_expired', $tgl_expired)
          ->exists();

        if (
          !$barang_stock &&
          ($jenis_perubahan === JenisPerubahan::KURANG->value || $jenis_perubahan === JenisPerubahan::TAMBAH->value)
        ) {
          abort(404, 'Stock barang tidak ditemukan. Harap tambahkan data stock awal terlebih dahulu.');
        }

        $barang_stock = BarangStock::firstOrNew([
          'barang_id' => $barang_id,
          'batch' => $batch,
          'tgl_expired' => $tgl_expired,
        ]);

        $stock_awal = (int) $barang_stock->jumlah_stock;

        if ($jenis_perubahan === JenisPerubahan::KURANG->value) {
          if ($input_stock > $barang_stock->jumlah_stock) {
            abort(403, 'Stock barang tidak cukup. ');
          }

          $barang_stock->jumlah_stock -= $input_stock;
        } else {
          $barang_stock->jumlah_stock += $input_stock;
        }
        $barang_stock->save();
        //  track mutation
        if ($jenis_perubahan === JenisPerubahan::KURANG->value) {
          $barang_stock_awal->mutation()->create([
            'stock_awal' => $stock_awal,
            'stock_keluar' => $input_stock,
            'stock_akhir' => $barang_stock->jumlah_stock,
            'barang_id' => $barang_stock->barang_id,
            'batch' => $barang_stock->batch,
            'tgl_expired' => $barang_stock->tgl_expired,
            'tgl_mutation' => Carbon::now()
          ]);
        } else {
          $barang_stock_awal->mutation()->create([
            'stock_awal' => $stock_awal,
            'stock_masuk' => $input_stock,
            'stock_akhir' => $barang_stock->jumlah_stock,
            'barang_id' => $barang_stock->barang_id,
            'batch' => $barang_stock->batch,
            'tgl_expired' => $barang_stock->tgl_expired,
            'tgl_mutation' => Carbon::now()
          ]);
        }
      }, 3);

      return redirect()->route('fakturis.stock-awal.index')->with('success', 'Berhasil menambah data stock awal.');
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('Gagal! " . $msg . "')");
    }
  }

  #[Computed()]
  public function jenisPerubahans()
  {
    return JenisPerubahan::toObjectArray();
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

  #[On('BarangStockAwal.CreateForm:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->barang_id = null;
      $this->dispatch('BarangStockAwal.CreateForm:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('BarangStockAwal.CreateForm:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->barang_id = (int) $id;
      $this->dispatch('BarangStockAwal.CreateForm:barangChanged', ['data' => $this->barang]);
    }
  }

  public function rules()
  {
    $rules = [
      'barang_id' => 'required|exists:barangs,id',
      'tgl_expired' => 'nullable|date|after_or_equal:today',
      'jumlah_stock' => 'required|numeric|min:1',
      'jenis_perubahan' => 'required',
      // 'tgl_stock' => 'required|date',
      'keterangan' => 'required|string|max:255',
    ];

    if ($this->jenis_perubahan === JenisPerubahan::AWAL->value) {
      $rules['batch'] = 'required|string';
    } else {
      $rules['batch'] = 'nullable|string';
    }

    return $rules;
  }

  public function updatedJenisPerubahan()
  {
    $this->validateOnly('batch');
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
    return view('livewire.barang-stock-awal.create-form', [
      'brands' => $this->brands,
      'jenisPerubahans' => $this->jenisPerubahans,
    ]);
  }
}
