<?php

namespace App\Livewire\Table;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;

final class BrandTable extends PowerGridComponent
{
  public string $tableName = 'brand-table-lx0llc-table';


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
    return Brand::query();
  }

  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('nama');
  }

  public function columns(): array
  {
    return [
      Column::make('Nama', 'nama')
        ->sortable(),
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
    if (!Auth::user()->hasAnyRole(['as', 'af'])) {
      abort(403);
    }

    $post = Brand::find($rowId);
    if ($post) {
      $post->delete();
    }
  }

  public function actions(Brand $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'edit' => [
        'af' => 'fakturis.brand.edit',
        'as' => 'supervisor.brand.edit',
      ]
    ];

    $actions = [];

    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['brand' => $row->id]);
    }

    if (Auth::user()->hasAnyRole(['as', 'af'])) {
      $actions[] = Button::add('delete')
        ->slot('<i class="bi-trash text-white"></i>')
        ->id($row->id)
        ->class('btn btn-danger')
        ->confirm('Hapus data?')
        ->dispatch('delete', ['rowId' => $row->id]);
    }

    return $actions;
  }

  public function actionRules(): array
  {
    return [
      Rule::button('delete')
        ->when(function ($barang) {
          return
            $barang->barangs()->exists();
        })
        ->hide(),
    ];
  }
}
