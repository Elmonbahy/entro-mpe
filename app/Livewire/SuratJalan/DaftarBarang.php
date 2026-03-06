<?php

namespace App\Livewire\SuratJalan;

use App\Models\JualDetail;
use App\Models\SuratJalanDetail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Services\SuratJalanService;


class DaftarBarang extends Component
{
  public ?int $jual_id = null;
  public int $surat_jalan_id;

  public function mount(int $surat_jalan_id)
  {
    $this->surat_jalan_id = $surat_jalan_id;
  }

  #[Computed()]
  public function jualDetails()
  {
    if ($this->jual_id) {
      return JualDetail::where('jual_id', $this->jual_id)
        ->with(['barang:id,nama,satuan'])
        ->withSum('suratJalanDetails', 'jumlah_barang_dikirim')
        ->get();
    } else {
      return null;
    }
  }

  #[On('SuratJalan.DaftarBarang:changeJualDetail')]
  public function changeJualDetail(int $jual_id)
  {
    $this->jual_id = $jual_id;
  }

  #[On('SuratJalan.DaftarBarang:refetchJualDetail')]
  public function refetchJualDetail()
  {
    unset($this->jualDetails);
  }

  public function submitBarangDikirim(int $qnt, int $jual_detail_id)
  {
    try {
      if ($qnt <= 0) {
        throw new \Exception('Jumlah barang dikirim tidak benar.');
      }

      // Cek apakah sudah 40 item dikirim
      $jumlahBarangTerkirim = SuratJalanDetail::where('surat_jalan_id', $this->surat_jalan_id)->count();
      if ($jumlahBarangTerkirim >= 40) {
        throw new \Exception('Maksimum 40 item barang dikirim per surat jalan.');
      }


      $jualDetail = JualDetail::findOrFail($jual_detail_id);

      $result = SuratJalanDetail::where('surat_jalan_id', $this->surat_jalan_id)
        ->where('jual_detail_id', $jual_detail_id)
        ->first();

      if ($result) {
        throw new \Exception('Tidak bisa menambahkan barang yang sama.');
      }

      $total_jumlah_barang_terkirim = SuratJalanDetail::where('jual_detail_id', $jual_detail_id)
        ->sum('jumlah_barang_dikirim');
      $diff = $jualDetail->jumlah_barang_keluar - $total_jumlah_barang_terkirim;

      if ($diff === 0) {
        throw new \Exception('Semua barng telah dikirim.');
      }

      if ($qnt > $diff) {
        throw new \Exception('Melebihi jumlah barang.');
      }

      SuratJalanDetail::create([
        'surat_jalan_id' => $this->surat_jalan_id,
        'jual_detail_id' => $jual_detail_id,
        'jumlah_barang_dikirim' => $qnt
      ]);

      app(SuratJalanService::class)->updateStatusKirimBySuratJalan($this->surat_jalan_id);

      $this->dispatch('SuratJalan.DaftarBarangDikirim:refreshList');
    } catch (\Exception $e) {
      $this->js('alert("Gagal!. ' . $e->getMessage() . '")');
    }
  }

  public function submitSemuaBarang()
  {
    try {
      // Cek batas 40 item
      $jumlahBarangTerkirim = SuratJalanDetail::where('surat_jalan_id', $this->surat_jalan_id)->count();
      if ($jumlahBarangTerkirim >= 40) {
        throw new \Exception('Maksimum 40 item barang dikirim per surat jalan.');
      }

      $jualDetails = JualDetail::where('jual_id', $this->jual_id)->get();

      foreach ($jualDetails as $jualDetail) {
        // cek apakah sudah ada di surat jalan
        $exist = SuratJalanDetail::where('surat_jalan_id', $this->surat_jalan_id)
          ->where('jual_detail_id', $jualDetail->id)
          ->first();
        if ($exist) {
          continue; // skip kalau sudah ada
        }

        $totalTerkirim = SuratJalanDetail::where('jual_detail_id', $jualDetail->id)
          ->sum('jumlah_barang_dikirim');
        $sisa = $jualDetail->jumlah_barang_keluar - $totalTerkirim;

        if ($sisa > 0) {
          SuratJalanDetail::create([
            'surat_jalan_id' => $this->surat_jalan_id,
            'jual_detail_id' => $jualDetail->id,
            'jumlah_barang_dikirim' => $sisa,
          ]);
        }
      }

      app(SuratJalanService::class)->updateStatusKirimBySuratJalan($this->surat_jalan_id);

      $this->dispatch('SuratJalan.DaftarBarangDikirim:refreshList');
    } catch (\Exception $e) {
      $this->js('alert("Gagal! ' . $e->getMessage() . '")');
    }
  }

  public function render()
  {
    return view('livewire.surat-jalan.daftar-barang', ['jualDetails' => $this->jualDetails]);
  }
}
