<?php

namespace App\Livewire\Table;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;

final class SupplierTable extends PowerGridComponent
{
  public string $tableName = 'supplier-table-ithvyz-table';

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
    return Supplier::query();
  }

  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('nama')
      ->add('kota')
      ->add('contact_person');
  }

  public function columns(): array
  {
    return [
      Column::make('Nama', 'nama')
        ->sortable(),
      Column::make('Kota', 'kota'),
      Column::make('Contact person', 'contact_person'),
      Column::action('Action')
    ];
  }

  public function filters(): array
  {
    return [
      Filter::inputText('contact_person')->operators(['contains']),
      Filter::inputText('kota')->operators(['contains']),
      Filter::inputText('nama')->operators(['contains']),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId): void
  {
    if (!Auth::user()->hasAnyRole(['af'])) {
      abort(403);
    }

    $supplier = Supplier::find($rowId);
    if ($supplier) {
      $supplier->delete();
    }
  }

  public function actions(Supplier $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ak' => 'keuangan.supplier.show',
        'af' => 'fakturis.supplier.show',
        'as' => 'supervisor.supplier.show',
        'su' => 'superadmin.supplier.show',
      ],
      'edit' => [
        'af' => 'fakturis.supplier.edit',
      ],
    ];

    $actions = [];

    // Tombol Detail
    if (isset($routeMaps['show'][$roleSlug])) {
      $actions[] = Button::add('detail')
        ->slot('<i class="bi-eye text-white"></i>')
        ->id($row->id)
        ->class('btn btn-info')
        ->route($routeMaps['show'][$roleSlug], ['supplier' => $row->id]);
    }

    // Tombol Edit
    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['supplier' => $row->id]);
    }

    // Tombol Delete (hanya untuk 'af' )
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

  public function actionRules(): array
  {
    return [
      Rule::button('delete')
        ->when(function ($barang) {
          return
            $barang->barangs()->exists() ||
            $barang->belis()->exists();
        })
        ->hide(),
    ];
  }
}
