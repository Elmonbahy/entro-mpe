<?php

namespace App\Livewire\Jual;

use App\Enums\StatusFaktur;
use App\Models\Jual;
use App\Models\JualDetail;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class DaftarPenjualanBarangFakturis extends Component
{
  public $editingId = null;
  public $editHargaJual = null;
  public $editHargaJualFormatted;
  public $editDiskon1 = null;
  public $editDiskon2 = null;

  #[Locked]
  public Jual $jual;
  #[Locked]
  public $jual_detail;
  #[Locked]

  private function getJualDetail()
  {
    $this->jual_detail = JualDetail::with(['barang'])
      ->where('jual_id', $this->jual->id)
      ->orderByDesc('id')
      ->get();
  }

  private function deleteJualDetail(int $id)
  {
    try {
      \DB::transaction(function () use ($id) {

        $jual_detail = JualDetail::with('jual')->where('id', $id)->first();

        \Gate::authorize('delete', $jual_detail);

        $jual_detail->delete();
      });

      $this->refreshList();
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('$msg')");
    }
  }

  public function mount(int $jual_id)
  {
    $this->jual = Jual::findOrFail($jual_id);
    $this->getJualDetail();
  }


  #[On('refresh-daftar-penjualan-barang')]
  public function refreshList()
  {
    $this->getJualDetail();
  }

  public function delete(int $id)
  {
    $this->deleteJualDetail($id);
  }

  public function sendToGudang()
  {
    try {
      \Gate::authorize('sendToGudang', $this->jual);

      Jual::where('id', $this->jual->id)->update([
        'status_faktur' => StatusFaktur::PROCESS_GUDANG,
      ]);

      $this->refreshList();
      $user = auth()->user();
      $prefix = $user->getRoutePrefix();
      $routeName = $prefix . '.jual.show';
      return redirect()->route($routeName, ['id' => $this->jual->id]);
    } catch (\Exception $th) {
      $this->js('alert("' . $th->getMessage() . '")');
    }
  }

  public function edit(int $id)
  {
    $jual_detail = JualDetail::findOrFail($id);
    $this->editingId = $id;
    $this->editHargaJual = $jual_detail->harga_jual;
    $this->editHargaJualFormatted = number_format($jual_detail->harga_jual, 2, ',', '.');
    $this->editDiskon1 = $jual_detail->diskon1;
    $this->editDiskon2 = $jual_detail->diskon2;
  }

  public function updatedEditHargaJualFormatted($value)
  {
    $this->editHargaJual = (float) str_replace(['.', ','], ['', '.'], $value);
  }

  public function updateHargaJual()
  {
    try {
      \DB::transaction(function () {

        $jual_detail = JualDetail::findOrFail($this->editingId);

        \Gate::authorize('edit', $jual_detail);

        $jual_detail->harga_jual = $this->editHargaJual;
        $jual_detail->diskon1 = $this->editDiskon1;
        $jual_detail->diskon2 = $this->editDiskon2;
        $jual_detail->save();
      });

      $this->reset(['editingId', 'editHargaJual', 'editDiskon1']);
      $this->getJualDetail();

    } catch (\Exception $e) {
      $this->js("alert('{$e->getMessage()}')");
    }
  }


  public function render()
  {
    return view('livewire.jual.daftar-penjualan-barang-fakturis');
  }
}
