<?php

namespace App\Livewire\Beli;

use App\Enums\StatusFaktur;
use App\Models\Beli;
use App\Models\BeliDetail;
use DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class DaftarPembelianBarangFakturis extends Component
{


  #[Locked]
  public Beli $beli;
  #[Locked]
  public $beli_details;

  private function getBeliDetail()
  {
    $this->beli_details = BeliDetail::with('barang')
      ->where('beli_id', $this->beli->id)
      ->orderByDesc('id')
      ->get();
  }

  private function deleteBeliDetail(int $id)
  {
    try {
      DB::transaction(function () use ($id) {
        $beli_detail = BeliDetail::with('beli')->where('id', $id)->first();

        \Gate::authorize('delete', $beli_detail);

        $beli_detail->delete();
      });

      $this->refreshList();
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('$msg')");
    }
  }

  public function mount(int $beli_id)
  {
    $this->beli = Beli::findOrFail($beli_id);
    $this->getBeliDetail();
  }

  #[On('refresh-daftar-pembelian-barang')]
  public function refreshList()
  {
    $this->getBeliDetail();
  }

  public function delete(int $id)
  {
    $this->deleteBeliDetail($id);
  }

  public function sendToGudang()
  {
    try {
      \Gate::authorize('sendToGudang', $this->beli);

      Beli::where('id', $this->beli->id)->update([
        'status_faktur' => StatusFaktur::PROCESS_GUDANG,
      ]);

      $this->refreshList();
      $user = auth()->user();
      $prefix = $user->getRoutePrefix();
      $routeName = $prefix . '.beli.show';
      return redirect()->route($routeName, ['id' => $this->beli->id]);
    } catch (\Exception $th) {
      $this->js('alert("' . $th->getMessage() . '")');
    }
  }

  public function render()
  {
    return view('livewire.beli.daftar-pembelian-barang-fakturis');
  }
}
