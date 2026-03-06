<?php

namespace App\Livewire\SampleKeluar;

use App\Enums\StatusSample;
use App\Models\SampleOut;
use App\Models\SampleOutDetail;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class DaftarSampleBarangFakturis extends Component
{

  public SampleOut $sample_out;
  #[Locked]
  public $sample_out_detail;
  #[Locked]

  private function getSampleOutDetail()
  {
    $this->sample_out_detail = SampleOutDetail::with(['sampleBarang'])
      ->where('sample_out_id', $this->sample_out->id)
      ->orderByDesc('id')
      ->get();
  }

  private function deleteSampleOutDetail(int $id)
  {
    try {
      \DB::transaction(function () use ($id) {

        $sample_out_detail = SampleOutDetail::with('sampleout')->where('id', $id)->first();

        \Gate::authorize('delete', $sample_out_detail);

        $sample_out_detail->delete();
      });

      $this->refreshList();
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('$msg')");
    }
  }

  public function mount(int $sample_out_id)
  {
    $this->sample_out = SampleOut::findOrFail($sample_out_id);
    $this->getSampleOutDetail();
  }


  #[On('refresh-daftar-sample-barang')]
  public function refreshList()
  {
    $this->getSampleOutDetail();
  }

  public function delete(int $id)
  {
    $this->deleteSampleOutDetail($id);
  }

  public function sendToGudang()
  {
    try {
      \Gate::authorize('sendToGudang', $this->sample_out);

      SampleOut::where('id', $this->sample_out->id)->update([
        'status_sample' => StatusSample::PROCESS_GUDANG,
      ]);

      $this->refreshList();
      $user = auth()->user();
      $prefix = $user->getRoutePrefix();
      $routeName = $prefix . '.sample-out.show';
      return redirect()->route($routeName, ['id' => $this->sample_out->id]);
    } catch (\Exception $th) {
      $this->js('alert("' . $th->getMessage() . '")');
    }
  }

  public function render()
  {
    return view('livewire.sample-keluar.daftar-sample-barang-fakturis');
  }
}
