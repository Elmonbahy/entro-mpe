<?php

namespace App\Livewire\Table;

use App\Enums\StatusSample;
use App\Models\SampleOut;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Facades\Filter;

final class SampleOutTable extends PowerGridComponent
{
  public string $tableName = 'sample-out-table-ycwfpx-table';
  public string $sortDirection = 'desc';

  public function setUp(): array
  {
    return [
      PowerGrid::footer()
        ->showPerPage(10, [10, 25, 50, 100])
        ->showRecordCount('min'),
    ];
  }

  public function datasource(): Builder
  {
    return SampleOut::with('sampleoutDetails')
      ->join('pelanggans', function ($pelanggan) {
        $pelanggan->on('sample_outs.pelanggan_id', '=', 'pelanggans.id');
      })
      ->join('salesmen', function ($salesmen) {
        $salesmen->on('sample_outs.salesman_id', '=', 'salesmen.id');
      })
      ->where(function ($q) {
        if (Auth::User()->hasAnyRole(['ag', 'aw'])) {
          $q->where('status_sample', '!=', StatusSample::NEW);
        }

        if (Auth::User()->hasAnyRole(['ak', 'al'])) {
          $q->where('status_sample', StatusSample::DONE);
        }
      })
      ->select([
        'sample_outs.id',
        'sample_outs.nomor_sample',
        'sample_outs.tanggal',
        'sample_outs.status_sample',
        'pelanggans.nama as pelanggan_nama',
        'salesmen.nama as salesmen_nama',
      ]);
  }

  public function fields(): PowerGridFields
  {
    $fields = PowerGrid::fields()
      ->add('nomor_sample')
      ->add('pelanggan_nama')
      ->add('salesmen_nama')
      ->add('tanggal', fn(SampleOut $model) => Carbon::parse($model->tanggal)->format('d/m/Y'))
      ->add('status_sample_x', function (SampleOut $model) {
        return \Blade::render('<x-badge.status-sample :status="$status->value" />', [
          'status' => $model->status_sample,
        ]);
      });

    return $fields;
  }

  public function columns(): array
  {
    $columns = [
      Column::make('Nomor sampel', 'nomor_sample')
        ->sortable(),
      Column::make('Nama Pelanggan', 'pelanggan_nama', 'pelanggans.nama')
        ->sortable(),
      Column::make('Salesman', 'salesmen_nama', 'salesmen.nama')
        ->sortable(),
      Column::make('Tanggal', 'tanggal'),
      Column::make('Status sampel', 'status_sample_x'),
    ];

    // Kolom aksi tetap ditambahkan untuk semua role
    $columns[] = Column::action('Action');

    return $columns;
  }
  public function filters(): array
  {
    return [
      Filter::inputText('nomor_sample')->operators(['contains']),
      Filter::inputText('pelanggans.nama')->operators(['contains']),
      Filter::inputText('salesmen.nama')->operators(['contains']),
      Filter::datepicker('tanggal', 'tanggal'),
      Filter::enumSelect('status_sample_x', 'status_sample')
        ->datasource(StatusSample::cases())
        ->optionLabel('status_sample_x'),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId)
  {
    try {
      $sample_out = SampleOut::with('sampleoutDetails')->findOrFail($rowId);
      \Gate::authorize('delete', $sample_out);

      $sample_out->delete();
    } catch (\Exception $e) {
      $this->js("alert('" . $e->getMessage() . "')");
    }
  }

  public function actions(SampleOut $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.sample-out.show',
        'af' => 'fakturis.sample-out.show',
        'as' => 'supervisor.sample-out.show',
      ],
      'edit' => [
        'af' => 'fakturis.sample-out.edit',
        'as' => 'supervisor.sample-out.edit',
      ],
    ];

    $actions = [];

    // Tombol DETAIL hanya jika route show tersedia
    if (isset($routeMaps['show'][$roleSlug])) {
      $actions[] = Button::add('detail')
        ->slot('<i class="bi-eye text-white"></i>')
        ->id($row->id)
        ->class('btn btn-info')
        ->route($routeMaps['show'][$roleSlug], ['id' => $row->id]);
    }

    // // Tombol EDIT hanya untuk role yang punya akses edit
    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['id' => $row->id]);
    }

    // Tombol DELETE hanya untuk role 'af'
    if (Auth::user()->hasAnyRole(['af'])) {
      $actions[] = Button::add('delete')
        ->slot('<i class="bi-trash text-white"></i>')
        ->id($row->id)
        ->class('btn btn-danger')
        ->confirm('Hapus data?')
        ->dispatch('delete', ['rowId' => $row->id]);
    }

    return $actions;
  }
}
