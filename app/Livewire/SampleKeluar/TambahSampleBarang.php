<?php

namespace App\Livewire\SampleKeluar;

use App\Enums\StatusSample;
use App\Models\SampleBarang;
use App\Models\BarangSampleStock;
use App\Models\Brand;
use App\Models\SampleOut;
use App\Models\SampleOutDetail;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class TambahSampleBarang extends Component
{
  #[Locked]
  public int $sample_out_id = 0;
  #[Locked]

  public $brand_id = null;
  public $barang_id = null;
  public $satuan = null;


  #[Locked]
  public $batch = null;
  #[Locked]
  public $tgl_expired = null;
  #[Locked]
  public $tgl_expired_formatter = null;
  #[Locked]
  public $stock = null;
  #[Locked]
  public $stock_all = null;

  public $jumlah_barang_dipesan = 0;

  public function mount(int $sample_out_id)
  {
    $this->sample_out_id = $sample_out_id;
  }

  public function rules()
  {
    return [
      'barang_id' => 'required|exists:sample_barangs,id',
      'sample_out_id' => 'required|exists:sample_outs,id',
      'batch' => 'nullable|string',
      'tgl_expired' => 'nullable|date',
      'jumlah_barang_dipesan' => 'required|numeric|min:1',
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
    $item = SampleBarang::with('barang:id,nama,satuan')
      ->find($this->barang_id);

    if ($item) {
      // panggil pakai barang_id dari record sample_barang
      $this->setBarangStock($item->barang_id);
    }

    return $item;
  }

  private function setBarangStock(int $barang_id)
  {
    $this->stock_all = BarangSampleStock::where('barang_id', $barang_id)
      ->where('jumlah_stock', '>', 0)
      ->sum('jumlah_stock');

    if (!$this->stock_all) {
      // No stock available
      $this->reset(['tgl_expired', 'batch', 'stock', 'stock_all']);
      $this->js('alert("Stock tidak tersedia!")');
    } else {
      // Attempt to find the stock with the earliest expiration date
      $barang_stock = BarangSampleStock::where('barang_id', $barang_id)
        ->where('jumlah_stock', '>', 0)
        ->orderByRaw('ISNULL(tgl_expired) DESC, tgl_expired ASC, created_at DESC')
        ->first();

      $this->tgl_expired = $barang_stock->tgl_expired;
      $this->tgl_expired_formatter = $barang_stock->tgl_expired ? Carbon::parse($barang_stock->tgl_expired)->format('d/m/Y') : null;
      $this->batch = $barang_stock->batch;
      $this->stock = $barang_stock->jumlah_stock;
    }
  }

  #[On('SampleKeluar.TambahSampleBarang:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->reset(['barang_id', 'satuan', 'batch', 'tgl_expired', 'tgl_expired_formatter', 'stock', 'stock_all']);
      $this->dispatch('SampleKeluar.TambahSampleBarang:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('SampleKeluar.TambahSampleBarang:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->reset(['batch', 'tgl_expired', 'tgl_expired_formatter', 'stock', 'stock_all']);
      $this->barang_id = (int) $id;
      $this->satuan = $this->barang?->satuan;
    }
  }

  // ambil stock utama & stock lainnya
  private function getAllocateStocks()
  {
    // ambil barang_id sebenarnya dari sample_barang
    $sample = SampleBarang::select('barang_id')->find($this->barang_id);

    if (!$sample) {
      throw new \Exception('Gagal! data sample barang tidak ditemukan.');
    }

    $barangId = $sample->barang_id;
    $batch = $this->batch ?: null;
    $tglExpired = $this->tgl_expired ?: null;
    $jumlahBarang = (int) $this->jumlah_barang_dipesan;

    $stockQuery = BarangSampleStock::where('barang_id', $barangId)
      ->where('jumlah_stock', '>', 0);

    if ($batch) {
      $stockQuery->where('batch', $batch);
    }

    if ($tglExpired) {
      $stockQuery->where('tgl_expired', $tglExpired)->orderBy('tgl_expired', 'asc');
    }

    $prioritizedStock = $stockQuery->get();
    $totalStock = (int) $prioritizedStock->sum('jumlah_stock');

    if ($totalStock === 0) {
      throw new \Exception("Gagal! stock barang tidak tersedia untuk barang_id: {$barangId}");
    }

    if ($totalStock < $jumlahBarang) {
      $additionalStock = BarangSampleStock::where('barang_id', $barangId)
        ->where('jumlah_stock', '>', 0)
        ->whereNotIn('id', $prioritizedStock->pluck('id'))
        ->orderBy('tgl_expired', 'asc')
        ->get();

      $prioritizedStock = $prioritizedStock->merge($additionalStock);
    }

    $totalStock = (int) $prioritizedStock->sum('jumlah_stock');
    if ($totalStock < $jumlahBarang) {
      throw new \Exception('Gagal! stock barang tidak cukup.');
    }

    return $prioritizedStock;
  }


  private function createSampleOutDetailRecords($stocks)
  {
    $remaining = $this->jumlah_barang_dipesan;

    foreach ($stocks as $item) {
      if ($remaining <= 0) {
        break;
      }

      $usableStock = min($remaining, $item->jumlah_stock);
      // ini akan mengurangi jumlah stock
      // $item->decrement('jumlah_stock', $usableStock);

      SampleOutDetail::create([
        'jumlah_barang_dipesan' => $usableStock,
        'batch' => $item->batch,
        'tgl_expired' => $item->tgl_expired,
        'sample_out_id' => $this->sample_out_id,
        'barang_id' => $this->barang->barang_id, // ambil barang_id asli dari tabel barang
      ]);

      $remaining -= $usableStock;
    }

    if ($remaining > 0) {
      throw new \Exception('Gagal! Stock barang tidak cukup setelah menggunakan semua stock yang tersedia.');
    }
  }

  //untuk jumlah barang yang dapat dipesan hanya 40 item
  private function getJumlahDetailAktif(): int
  {
    return SampleOutDetail::where('sample_out_id', $this->sample_out_id)
      ->where('jumlah_barang_dipesan', '>', 0)
      ->count();
  }


  private function createSampleOutDetail()
  {
    try {
      \DB::transaction(function () {
        $sample_out = SampleOut::findOrFail($this->sample_out_id);
        \Gate::authorize('createSampleOutDetail', $sample_out);

        // Hitung total semua detail barang (tidak peduli barang_id-nya)
        $jumlahDetail = $this->getJumlahDetailAktif();

        // Estimasi jumlah detail yang akan ditambahkan
        $allocatedStocks = $this->getAllocateStocks();
        $jumlahYangAkanDitambah = 0;
        $remaining = $this->jumlah_barang_dipesan;

        foreach ($allocatedStocks as $item) {
          if ($remaining <= 0)
            break;
          $usableStock = min($remaining, $item->jumlah_stock);
          $jumlahYangAkanDitambah++;
          $remaining -= $usableStock;
        }

        if ($jumlahDetail + $jumlahYangAkanDitambah > 40) {
          throw new \Exception("Maksimal hanya boleh 40 item penjualan per transaksi.");
        }

        // Buat detail
        $this->createSampleOutDetailRecords($allocatedStocks);

        // Update status faktur
        $sample_out->status_sample = StatusSample::PROCESS_SAMPLE;
        $sample_out->save();
      });

      $this->resetAllState();
      $this->dispatch('refresh-daftar-sample-barang');
      $this->dispatch('SampleKeluar.TambahSampleBarang:created');
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
    $this->createSampleOutDetail();
  }


  private function resetAllState()
  {
    $this->reset(['barang_id', 'brand_id', 'satuan', 'batch', 'tgl_expired', 'tgl_expired_formatter', 'stock', 'stock_all', 'jumlah_barang_dipesan',]);
  }

  public function render()
  {
    return view('livewire.sample-keluar.tambah-sample-barang')->with([
      'brands' => $this->brands,
    ]);
  }
}
