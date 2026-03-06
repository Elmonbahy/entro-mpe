<?php

namespace App\Livewire\SuratJalan;

use App\Models\SuratJalanDetail;
use App\Services\SuratJalanService;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Enums\StatusKirim;

class DaftarBarangDikirim extends Component
{
  #[Locked]
  public int $surat_jalan_id;

  #[Locked]
  public $surat_jalan_details;

  public $role;

  protected SuratJalanService $suratJalanService;

  public function boot(SuratJalanService $suratJalanService)
  {
    $this->suratJalanService = $suratJalanService;
  }


  private function getSuratJalanDetail()
  {
    $this->surat_jalan_details = SuratJalanDetail::where('surat_jalan_id', $this->surat_jalan_id)
      ->with(['barang:barangs.nama,barangs.satuan', 'jual:juals.nomor_faktur'])
      ->get();
  }

  #[On('SuratJalan.DaftarBarangDikirim:refreshList')]
  public function refreshList()
  {
    $this->getSuratJalanDetail();
  }

  public function deleteItem(int $id)
  {
    $item = SuratJalanDetail::findOrFail($id);
    $jual_id = optional($item->jualDetail)->jual_id;

    $item->delete();

    $this->getSuratJalanDetail();

    if ($jual_id) {
      $this->suratJalanService->updateStatusKirimByJual($jual_id);
    }

    $this->dispatch('SuratJalan.DaftarBarang:refetchJualDetail');
  }


  public function mount(int $surat_jalan_id)
  {
    $this->surat_jalan_id = $surat_jalan_id;
    $this->role = auth()->user()->role;
    $this->getSuratJalanDetail();
  }

  public function render()
  {
    return view('livewire.surat-jalan.daftar-barang-dikirim', [
      'role' => $this->role
    ]);
  }
}
