<?php

namespace App\Livewire\Jual;

use App\Models\Spjual;
use App\Models\SpjualDetail;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class DaftarPemesananBarangFakturis extends Component
{
  #[Locked]
  public Spjual $spjual;
  #[Locked]
  public $spjual_detail;
  #[Locked]

  private function getSpJualDetail()
  {
    $this->spjual_detail = SpjualDetail::with(['barang'])
      ->where('spjual_id', $this->spjual->id)
      ->orderByDesc('id')
      ->get();
  }

  private function deleteSpJualDetail(int $id)
  {
    try {
      $spjual_detail = SpjualDetail::where('id', $id)->first();
      $spjual_detail->delete();

      $this->refreshList();
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('$msg')");
    }
  }

  public function mount(int $spjual_id)
  {
    $this->spjual = Spjual::findOrFail($spjual_id);
    $this->getSpJualDetail();
  }


  #[On('refresh-daftar-pemesanan-barang')]
  public function refreshList()
  {
    $this->getSpJualDetail();
  }

  public function delete(int $id)
  {
    $this->deleteSpJualDetail($id);
  }

  public function selesai()
  {
    try {
      $this->refreshList();
      return redirect()->route('fakturis.spjual.show', ['id' => $this->spjual->id]);
    } catch (\Exception $th) {
      $this->js('alert("' . $th->getMessage() . '")');
    }
  }

  public function render()
  {
    return view('livewire.jual.daftar-pemesanan-barang-fakturis');
  }
}
