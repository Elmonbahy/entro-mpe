<?php

namespace App\Livewire\Table;

use App\Models\Kendaraan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Filter;

class KendaraanTable extends PowerGridComponent
{
  public string $tableName = 'kendaraan-table-ithvyz-table';

  public string $sortDirection = 'desc';
  public function setUp(): array
  {
    return [
      PowerGrid::footer()
        ->showPerPage(10, [10, 25, 50, 100])
        ->showRecordCount('min')
    ];
  }

  public function datasource(): Builder
  {
    return Kendaraan::query();
  }

  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('nama')
      ->add('contact_person')
      ->add('contact_phone');
  }

  public function columns(): array
  {
    return [
      Column::make('Nama', 'nama')
        ->sortable(),
      Column::make('Contact person', 'contact_person'),
      Column::make('Contact phone', 'contact_phone'),

      Column::action('Action')
    ];
  }

  public function filters(): array
  {
    return [
      Filter::inputText('nama')->operators(['contains']),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId): void
  {
    if (!Auth::user()->hasAnyRole(['al', 'ag'])) {
      abort(403);
    }

    $kendaraan = Kendaraan::find($rowId);
    if ($kendaraan) {
      $kendaraan->delete();
    }

  }

  public function actions(Kendaraan $row): array
  {
    $routeMaps = [
      'show' => [
        'al' => 'logistik.kendaraan.show',
        'ag' => 'gudang.kendaraan.show',
      ],
      'edit' => [
        'al' => 'logistik.kendaraan.edit',
        'ag' => 'gudang.kendaraan.edit',
      ],
    ];

    $showRoute = $routeMaps['show'][Auth::user()->role->slug];
    $editRoute = $routeMaps['edit'][Auth::user()->role->slug];

    return [
      Button::add('detail')
        ->slot('<i class="bi-eye text-white"></i>')
        ->id($row->id)
        ->class('btn btn-info')
        ->route($showRoute, ['kendaraan' => $row->id]),
      Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($editRoute, ['kendaraan' => $row->id]),
      Button::add('delete')
        ->slot('<i class="bi-trash text-white"></i>')
        ->id($row->id)
        ->class('btn btn-danger')
        ->confirm('Hapus data?')
        ->dispatch('delete', ['rowId' => $row->id])
        ->can(Auth::User()->hasAnyRole(['al', 'ag'])),
    ];
  }
}
