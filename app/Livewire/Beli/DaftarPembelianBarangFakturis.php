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
  public $editingId = null;
  public $editHargaBeli = null;
  public $editHargaBeliFormatted;
  public $editDiskon1 = null;
  public $editDiskon2 = null;

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

  public function edit(int $id)
  {
    $beli_detail = BeliDetail::findOrFail($id);
    $this->editingId = $id;
    $this->editHargaBeli = $beli_detail->harga_beli;
    $this->editHargaBeliFormatted = number_format($beli_detail->harga_beli, 4, ',', '.');
    $this->editDiskon1 = $beli_detail->diskon1;
    $this->editDiskon2 = $beli_detail->diskon2;
  }
  public function updatedEditHargaBeliFormatted($value)
  {
    $this->editHargaBeli = (float) str_replace(['.', ','], ['', '.'], $value);
  }

  public function updateHargaBeli()
  {
    try {
      \DB::transaction(function () {

        $beli_detail = BeliDetail::findOrFail($this->editingId);

        \Gate::authorize('edit', $beli_detail);

        $beli_detail->harga_beli = $this->editHargaBeli;
        $beli_detail->diskon1 = $this->editDiskon1;
        $beli_detail->diskon2 = $this->editDiskon2;
        $beli_detail->save();
      });

      $this->reset(['editingId', 'editHargaBeli', 'editDiskon1']);
      $this->getBeliDetail();

    } catch (\Exception $e) {
      $this->js("alert('{$e->getMessage()}')");
    }
  }

  public function render()
  {
    return view('livewire.beli.daftar-pembelian-barang-fakturis');
  }
}
