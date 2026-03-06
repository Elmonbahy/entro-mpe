<?php

namespace App\Livewire\SuratJalan;

use App\Models\Jual;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class SelectFaktur extends Component
{
  public int $pelanggan_id;
  public ?int $jual_id;

  #[Computed]
  public function juals()
  {
    return Jual::where('pelanggan_id', $this->pelanggan_id)
      ->whereIn('status_kirim', ['PENDING', 'PARTIAL'])
      ->select('id', 'nomor_faktur')
      ->get();
  }

  public function mount(int $pelanggan_id)
  {
    $this->$pelanggan_id = $pelanggan_id;
  }

  public function resetFaktur()
  {
    $this->jual_id = null;
    $this->dispatch('reset-jual-id');
    $this->dispatch('SuratJalan.DaftarBarang:changeJualDetail', 0);
  }

  #[On('SuratJalan.SelectFaktur:onJualChange')]
  public function onJualChange(int $id)
  {
    $this->jual_id = $id;
    $this->dispatch('SuratJalan.DaftarBarang:changeJualDetail', $id);
  }

  public function render()
  {
    return view('livewire.surat-jalan.select-faktur', ['juals' => $this->juals]);
  }
}
