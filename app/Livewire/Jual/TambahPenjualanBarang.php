<?php

namespace App\Livewire\Jual;

use App\Enums\StatusFaktur;
use App\Models\Barang;
use App\Models\BarangStock;
use App\Models\BeliDetail;
use App\Models\Brand;
use App\Models\Jual;
use App\Models\JualDetail;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class TambahPenjualanBarang extends Component
{
  // data jual, set on mount
  #[Locked]
  public int $ppn = 0;
  #[Locked]
  public int $jual_id = 0;
  #[Locked]
  public ?string $tipe_harga = null;

  public $brand_id = null;
  public $barang_id = null;
  public $satuan = null;

  public $harga_beli_formatted = null;
  public $total = 0;
  public $total_formatted = 0;

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

  #[Locked]
  public $total_tagihan = 0;
  #[Locked]
  public $total_tagihan_formatted = 0;

  public $keterangan;
  public $jumlah_barang_dipesan = 0;
  public $harga_jual = 0;
  public $diskon1 = 0;
  public $diskon2 = 0;

  public function mount(int $jual_id, int $ppn, ?string $tipe_harga)
  {
    $this->jual_id = $jual_id;
    $this->ppn = $ppn;
    $this->tipe_harga = $tipe_harga;
  }

  public function rules()
  {
    return [
      'barang_id' => 'required|exists:barangs,id',
      'jual_id' => 'required|exists:juals,id',
      'batch' => 'nullable|string',
      'tgl_expired' => 'nullable|date',
      'keterangan' => 'nullable|string',
      'jumlah_barang_dipesan' => 'required|numeric|min:1',
      'harga_jual' => 'required|numeric|min:0',
      'diskon1' => 'required|numeric|min:0|max:100',
      'diskon2' => 'required|numeric|min:0|max:100',
    ];
  }

  public function updated($property)
  {
    if ($property === 'harga_jual') {
      // Hapus semua karakter kecuali angka dan koma
      $cleanValue = preg_replace('/[^0-9,]/', '', $this->harga_jual);

      // Pastikan hanya ada satu koma sebagai pemisah desimal
      $decimalParts = explode(',', $cleanValue);
      if (count($decimalParts) > 2) {
        $cleanValue = $decimalParts[0] . ',' . implode('', array_slice($decimalParts, 1));
      }

      // Ubah nilai ke float untuk pemrosesan lebih lanjut
      $floatValue = (float) str_replace(',', '.', $cleanValue);

      // Format kembali ke Rupiah dengan desimal
      $this->harga_jual = number_format($floatValue, 2, ',', '.');
    }

    if (in_array($property, ['harga_jual', 'jumlah_barang_dipesan', 'diskon1', 'diskon2'])) {
      $this->calculateTotalTagihan();
    }
  }

  public function formatHargaJual()
  {
    // Pastikan input tetap terformat saat kehilangan fokus
    $this->updatedHargaJual();
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
    $item = Barang::select('id', 'nama', 'satuan', 'harga_jual_pemerintah', 'harga_jual_swasta')->find($this->barang_id);

    if ($item) {
      $harga_jual = 0;

      if ($this->tipe_harga === 'SWASTA') {
        $harga_jual = (float) $item->harga_jual_swasta;
      }

      if ($this->tipe_harga === 'PEMERINTAH') {
        $harga_jual = (float) $item->harga_jual_pemerintah;
      }

      $harga_beli = (float) BeliDetail::where('barang_id', $item->id)->latest()->value('harga_beli');

      $this->harga_jual = number_format((float) $harga_jual, 2, ',', '.');
      $this->harga_beli_formatted = \Number::currency($harga_beli, 'IDR', 'id_ID');

      $this->setBarangStock($item->id);
      $this->calculateTotalTagihan();
    }

    return $item;
  }

  private function setBarangStock(int $barang_id)
  {
    $this->stock_all = BarangStock::where('barang_id', $barang_id)
      ->where('jumlah_stock', '>', 0)
      ->sum('jumlah_stock');

    if (!$this->stock_all) {
      // No stock available
      $this->reset(['tgl_expired', 'batch', 'stock', 'stock_all']);
      $this->js('alert("Stock tidak tersedia!")');
    } else {
      // Attempt to find the stock with the earliest expiration date
      $barang_stock = BarangStock::where('barang_id', $barang_id)
        ->where('jumlah_stock', '>', 0)
        ->orderByRaw('ISNULL(tgl_expired) DESC, tgl_expired ASC, created_at DESC')
        ->first();

      $this->tgl_expired = $barang_stock->tgl_expired;
      $this->tgl_expired_formatter = $barang_stock->tgl_expired ? Carbon::parse($barang_stock->tgl_expired)->format('d/m/Y') : null;
      $this->batch = $barang_stock->batch;
      $this->stock = $barang_stock->jumlah_stock;
    }
  }

  #[On('Jual.TambahPenjualanBarang:onBrandChange')]
  public function onBrandChange($id)
  {
    if ($id) {
      $this->brand_id = (int) $id;
      $this->reset(['barang_id', 'satuan', 'harga_beli_formatted', 'batch', 'tgl_expired', 'tgl_expired_formatter', 'stock', 'stock_all']);
      $this->dispatch('Jual.TambahPenjualanBarang:brandChanged', ['data' => $this->barangs]);
    }
  }

  #[On('Jual.TambahPenjualanBarang:onBarangChange')]
  public function onBarangChange($id)
  {
    if ($id) {
      $this->reset(['harga_beli_formatted', 'batch', 'tgl_expired', 'tgl_expired_formatter', 'stock', 'stock_all']);
      $this->barang_id = (int) $id;
      $this->satuan = $this->barang?->satuan;
    }
  }

  private function calculateTotalTagihan()
  {
    $jumlah_barang_dipesan = (float) $this->jumlah_barang_dipesan;
    $harga_jual = (float) str_replace(',', '.', str_replace('.', '', $this->harga_jual));
    $diskon1 = (float) $this->diskon1;
    $diskon2 = (float) $this->diskon2;
    $ppn = (int) $this->ppn;

    $sub_nilai = $jumlah_barang_dipesan * $harga_jual;
    $harga_diskon1 = $sub_nilai * $diskon1 / 100;
    $nilai_diskon1 = $sub_nilai - $harga_diskon1;

    $harga_diskon2 = $nilai_diskon1 * ($diskon2 / 100);
    $total = $nilai_diskon1 - $harga_diskon2;
    $this->total = $total;
    $this->total_formatted = \Number::currency($total, 'IDR', 'id_ID');

    $harga_ppn = $total * ($ppn / 100);
    $total_tagihan = $total + $harga_ppn;

    $this->total_tagihan = $total_tagihan;
    $this->total_tagihan_formatted = \Number::currency($total_tagihan, 'IDR', 'id_ID');
  }

  // ambil stock utama & stock lainnya
  private function getAllocateStocks()
  {
    $barangId = (int) $this->barang_id;
    $batch = $this->batch ?: null;
    $tglExpired = $this->tgl_expired ?: null;
    $jumlahBarang = (int) $this->jumlah_barang_dipesan;

    $stockQuery = BarangStock::where('barang_id', $barangId)->where('jumlah_stock', '>', 0);

    if ($batch) {
      $stockQuery->where('batch', $batch);
    }

    if ($tglExpired) {
      $stockQuery->where('tgl_expired', $tglExpired)->orderBy('tgl_expired', 'asc');
    }

    $prioritizedStock = $stockQuery->get();
    $totalStock = (int) $prioritizedStock->sum('jumlah_stock');

    if ($totalStock === 0) {
      throw new \Exception('Gagal! stock barang tidak tersedia.');
    }

    if ($totalStock < $jumlahBarang) {
      // ambil stock lainnya jika stock utama tidak cukup
      $additionalStock = BarangStock::where('barang_id', $barangId)
        ->where('jumlah_stock', '>', 0)
        ->where('id', '!=', $prioritizedStock->pluck('id'))
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

  private function createJualDetailRecords($stocks)
  {
    $remaining = $this->jumlah_barang_dipesan;

    foreach ($stocks as $item) {
      if ($remaining <= 0) {
        break;
      }

      $usableStock = min($remaining, $item->jumlah_stock);
      // ini akan mengurangi jumlah stock
      // $item->decrement('jumlah_stock', $usableStock);

      JualDetail::create([
        'jumlah_barang_dipesan' => $usableStock,
        'batch' => $item->batch,
        'tgl_expired' => $item->tgl_expired,
        'jual_id' => $this->jual_id,
        'barang_id' => $this->barang_id,
        'diskon1' => $this->diskon1,
        'diskon2' => $this->diskon2,
        'keterangan' => $this->keterangan,
        'harga_jual' => $this->harga_jual,
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
    return JualDetail::where('jual_id', $this->jual_id)
      ->where('jumlah_barang_dipesan', '>', 0)
      ->count();
  }


  private function createJualDetail()
  {
    try {
      \DB::transaction(function () {
        $jual = Jual::findOrFail($this->jual_id);
        \Gate::authorize('createJualDetail', $jual);

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
        $this->createJualDetailRecords($allocatedStocks);

        // Update status faktur
        $jual->status_faktur = StatusFaktur::PROCESS_FAKTUR;
        $jual->save();
      });

      $this->resetAllState();
      $this->dispatch('refresh-daftar-penjualan-barang');
      $this->dispatch('Jual.TambahPenjualanBarang:created');
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

    // Pastikan harga_jual memiliki format angka yang benar
    $this->harga_jual = (float) str_replace(',', '.', str_replace('.', '', $this->harga_jual));

    $this->validate();
    $this->createJualDetail();
  }


  private function resetAllState()
  {
    $this->reset(['barang_id', 'brand_id', 'satuan', 'harga_beli_formatted', 'batch', 'tgl_expired', 'tgl_expired_formatter', 'stock', 'stock_all', 'total_tagihan', 'total_tagihan_formatted', 'keterangan', 'jumlah_barang_dipesan', 'harga_jual', 'diskon1', 'diskon2', 'total_formatted']);
  }

  public function render()
  {
    return view('livewire.jual.tambah-penjualan-barang')->with([
      'brands' => $this->brands,
    ]);
  }
}
