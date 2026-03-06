<?php

namespace App\Livewire\Table;

use App\Enums\StatusSample;
use App\Models\SampleIn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\Filter;

final class SampleInTable extends PowerGridComponent
{
  public string $tableName = 'sample-in-table-ycwfpx-table';
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
    return SampleIn::with('sampleinDetails')
      ->join('suppliers', function ($supplier) {
        $supplier->on('sample_ins.supplier_id', '=', 'suppliers.id');
      })
      ->where(function ($q) {
        if (Auth::User()->hasAnyRole(['ag', 'aw'])) {
          $q->where('status_sample', '!=', StatusSample::NEW);
        }
      })
      ->select([
        'sample_ins.id',
        'sample_ins.nomor_sample',
        'sample_ins.tanggal',
        'sample_ins.status_sample',
        'suppliers.nama as supplier_nama',
      ]);
  }

  public function fields(): PowerGridFields
  {
    $fields = PowerGrid::fields()
      ->add('id')
      ->add('nomor_sample')
      ->add('supplier_nama')
      ->add('tanggal', fn(SampleIn $model) => Carbon::parse($model->tanggal)->format('d/m/Y'))
      ->add('nomor_sample', fn($nomor_sample) => $nomor_sample->nomor_sample ?? '-')
      ->add('status_sample_x', function (SampleIn $model) {
        return \Blade::render('<x-badge.status-sample :status="$status->value" />', [
          'status' => $model->status_sample,
        ]);
      });

    return $fields;
  }

  public function columns(): array
  {
    $columns = [
      Column::make('Nomor Sampel', 'nomor_sample')
        ->sortable(),
      Column::make('Nama supplier', 'supplier_nama', 'suppliers.nama')
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
      Filter::inputText('suppliers.nama')->operators(['contains']),
      Filter::inputText('nomor_sample')->operators(['contains']),
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
      $sample_in = SampleIn::with('sampleinDetails')->findOrFail($rowId);
      \Gate::authorize('delete', $sample_in);

      $sample_in->delete();
    } catch (\Exception $e) {
      $this->js("alert('" . $e->getMessage() . "')");
    }
  }

  public function actions(SampleIn $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.sample-in.show',
        'af' => 'fakturis.sample-in.show',
        'as' => 'supervisor.sample-in.show',
      ],
      'edit' => [
        'af' => 'fakturis.sample-in.edit',
        'as' => 'supervisor.sample-in.edit',
      ],
    ];

    $actions = [];

    if (isset($routeMaps['show'][$roleSlug])) {
      $actions[] = Button::add('detail')
        ->slot('<i class="bi-eye text-white"></i>')
        ->id($row->id)
        ->class('btn btn-info')
        ->route($routeMaps['show'][$roleSlug], ['id' => $row->id]);
    }

    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['id' => $row->id]);
    }

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
