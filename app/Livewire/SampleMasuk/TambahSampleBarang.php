<?php

namespace App\Livewire\SampleMasuk;

use App\Enums\StatusSample;
use App\Models\SampleBarang;
use App\Models\SampleIn;
use App\Models\SampleInDetail;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class TambahSampleBarang extends Component
{
  public $brand_id = null;
  public $barang_id = null;
  public $satuan = null;

  #[Locked]
  public $sample_in_id = null;

  public $batch = null;
  public $tgl_expired = null;
  public $jumlah_barang_dipesan = 0;

  public function mount(int $sample_in_id)
  {
    $this->sample_in_id = $sample_in_id;
  }

  public function rules()
  {
    return [
      'barang_id' => 'required|exists:sample_barangs,id',
      'batch' => 'nullable|string',
      'tgl_expired' => 'nullable|date|after_or_equal:today',
      'jumlah_barang_dipesan' => 'required|numeric|min:1',
      'sample_in_id' => 'required|exists:sample_ins,id',
    ];
  }

  #[Computed()]
  public function brands()
  {
    return Brand::select('nama', 'id')->get();
  }

  #[Computed()]
  private function barangs()
  {
    return SampleBarang::select('id', 'barang_id')
      ->with(['barang:id,nama'])
      ->whereHas('barang', function ($q) {
        if ($this->brand_id) {
          $q->where('brand_id', $this->brand_id);
        }
      })
      ->get()
      ->map(function ($item) {
        return (object) [
          'id' => $item->id,
          'nama' => $item->barang->nama ?? '-',
        ];
      });
  }

  #[Computed()]
  private function barang()
  {
    $item = SampleBarang::with('barang:id,nama,satuan')->find($this->barang_id);
    return $item;
  }

  #[On('SampleMasuk.TambahSampleBarang:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->reset(['barang_id', 'satuan']);
      $this->dispatch('SampleMasuk.TambahSampleBarang:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('SampleMasuk.TambahSampleBarang:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->barang_id = (int) $id;
      $this->satuan = $this->barang ? ($this->barang->satuan ?? $this->barang->barang->satuan ?? null) : null;
    }
  }

  private function createSampleInDetail()
  {
    try {
      \DB::transaction(function () {
        $sample_in = SampleIn::findOrFail($this->sample_in_id);
        \Gate::authorize('createSampleInDetail', $sample_in);

        SampleInDetail::create([
          'sample_in_id' => $this->sample_in_id,
          'barang_id' => $this->barang->barang_id, // ambil barang_id asli dari tabel barang
          'jumlah_barang_dipesan' => $this->jumlah_barang_dipesan,
          'batch' => $this->batch ?: null,
          'tgl_expired' => $this->tgl_expired ?: null,
        ]);

        $sample_in->status_sample = StatusSample::PROCESS_SAMPLE;
        $sample_in->save();
      });

      $this->dispatch('refresh-daftar-sample-barang');

      $this->reset(['tgl_expired', 'batch', 'jumlah_barang_dipesan', 'satuan', 'barang_id', 'brand_id']);

      $this->dispatch('SampleMasuk.TambahSampleBarang:created');
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

    $this->validate();
    $this->createSampleInDetail();
  }

  public function render()
  {
    return view('livewire.sample-masuk.tambah-sample-barang')->with([
      'brands' => $this->brands,
    ]);
  }
}
