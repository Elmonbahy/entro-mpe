<?php

namespace App\Livewire\Table;

use App\Models\Salesman;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Rule;

final class SalesmanTable extends PowerGridComponent
{
  public string $tableName = 'salesman-table-s4qjmp-table';

  /**
   * Tell the table to sort by id-desc
   */
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
    return Salesman::query();
  }

  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('kode')
      ->add('nama');
  }

  public function columns(): array
  {
    return [
      Column::make('Kode', 'kode')
        ->sortable(),
      Column::make('Nama', 'nama')
        ->sortable(),

      Column::action('Action')
    ];
  }

  public function filters(): array
  {
    return [
      Filter::inputText('kode')->operators(['contains']),
      Filter::inputText('nama')->operators(['contains']),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId): void
  {
    if (!Auth::user()->hasAnyRole(['af', 'su'])) {
      abort(403);
    }

    $salesman = Salesman::find($rowId);
    if ($salesman) {
      $salesman->delete();
    }
  }

  public function actions(Salesman $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'edit' => [
        'af' => 'fakturis.salesman.edit',
        'su' => 'superadmin.salesman.edit',
      ]
    ];

    $actions = [];

    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['salesman' => $row->id]);
    }

    if (Auth::user()->hasAnyRole(['af', 'su'])) {
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
