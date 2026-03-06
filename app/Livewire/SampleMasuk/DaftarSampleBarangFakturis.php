<?php

namespace App\Livewire\SampleMasuk;


use App\Enums\StatusSample;
use App\Models\SampleIn;
use App\Models\SampleInDetail;
use DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class DaftarSampleBarangFakturis extends Component
{
  public SampleIn $sample_in;
  #[Locked]
  public $sample_in_details;

  private function getSampleInDetail()
  {
    $this->sample_in_details = SampleInDetail::with(['sampleBarang'])
      ->where('sample_in_id', $this->sample_in->id)
      ->orderByDesc('id')
      ->get();
  }

  private function deleteSampleInDetail(int $id)
  {
    try {
      DB::transaction(function () use ($id) {
        $sample_in_detail = SampleInDetail::with('samplein')->where('id', $id)->first();

        \Gate::authorize('delete', $sample_in_detail);

        $sample_in_detail->delete();
      });

      $this->refreshList();
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $this->js("alert('$msg')");
    }
  }

  public function mount(int $sample_in_id)
  {
    $this->sample_in = SampleIn::findOrFail($sample_in_id);
    $this->getSampleInDetail();
  }

  #[On('refresh-daftar-sample-barang')]
  public function refreshList()
  {
    $this->getSampleInDetail();
  }

  public function delete(int $id)
  {
    $this->deleteSampleInDetail($id);
  }

  public function sendToGudang()
  {
    try {
      \Gate::authorize('sendToGudang', $this->sample_in);

      SampleIn::where('id', $this->sample_in->id)->update([
        'status_sample' => StatusSample::PROCESS_GUDANG,
      ]);

      $this->refreshList();
      $user = auth()->user();
      $prefix = $user->getRoutePrefix();
      $routeName = $prefix . '.sample-in.show';
      return redirect()->route($routeName, ['id' => $this->sample_in->id]);
    } catch (\Exception $th) {
      $this->js('alert("' . $th->getMessage() . '")');
    }
  }

  public function render()
  {
    return view('livewire.sample-masuk.daftar-sample-barang-fakturis');
  }
}
