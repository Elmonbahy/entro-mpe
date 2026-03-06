<?php

namespace App\Livewire\Beli;

use App\Models\Spbeli;
use App\Models\SpbeliDetail;
use DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DaftarPemesananBarangFakturis extends Component
{
  #[Locked]
  public Spbeli $spbeli;
  #[Locked]
  public $spbeli_details;

  private function getspBeliDetail()
  {
    $this->spbeli_details = SpbeliDetail::with('barang')
      ->where('spbeli_id', $this->spbeli->id)
      ->orderByDesc('id')
      ->get();
  }

  private function deletespBeliDetail(int $id)
  {
    try {
      DB::transaction(function () use ($id) {
        $spbeli_detail = SpbeliDetail::with('spbeli')->where('id', $id)->first();

        $spbeli_detail->delete();
      });

      $this->refreshList();
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('$msg')");
    }
  }

  public function mount(int $spbeli_id)
  {
    $this->spbeli = Spbeli::findOrFail($spbeli_id);
    $this->getspBeliDetail();
  }

  #[On('refresh-daftar-pemesanan-barang')]
  public function refreshList()
  {
    $this->getspBeliDetail();
  }

  public function delete(int $id)
  {
    $this->deletespBeliDetail($id);
  }

  public function selesai()
  {
    try {
      $this->refreshList();
      return redirect()->route('fakturis.spbeli.show', ['id' => $this->spbeli->id]);
    } catch (\Exception $th) {
      $this->js('alert("' . $th->getMessage() . '")');
    }
  }

  public function render()
  {
    return view('livewire.beli.daftar-pemesanan-barang-fakturis');
  }
}
