<?php

namespace App\Livewire\Table;

use App\Models\Pelanggan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Number;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Rule;

final class PelangganTable extends PowerGridComponent
{
  public string $tableName = 'pelanggan-table-vpbzbd-table';

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
    return Pelanggan::query();
  }

  public function relationSearch(): array
  {
    return [];
  }

  public function fields(): PowerGridFields
  {
    $fields = PowerGrid::fields()
      ->add('id')
      ->add('nama')
      ->add('kota')
      ->add('contact_phone');
    return $fields;
  }

  public function columns(): array
  {
    $columns = [
      Column::make('Id', 'id')
        ->sortable(),
      Column::make('Nama', 'nama')
        ->sortable(),
      Column::make('Kota', 'kota')
        ->sortable(),
    ];

    // Kolom aksi tetap ditambahkan untuk semua role
    $columns[] = Column::action('Action');

    return $columns;
  }

  public function filters(): array
  {
    return [
      Filter::inputText('id')->operators(['contains']),
      Filter::inputText('nama')->operators(['contains']),
      Filter::inputText('kota')->operators(['contains']),
      Filter::inputText('contact_phone')->operators(['contains']),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId): void
  {
    if (!Auth::user()->hasAnyRole(['af', 'su'])) {
      abort(403);
    }

    $pelanggan = Pelanggan::find($rowId);
    if ($pelanggan) {
      $pelanggan->delete();
    }
  }


  public function actions(Pelanggan $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'af' => 'fakturis.pelanggan.show',
        'ag' => 'gudang.pelanggan.show',
        'as' => 'supervisor.pelanggan.show',
        'su' => 'superadmin.pelanggan.show',
      ],
      'edit' => [
        'af' => 'fakturis.pelanggan.edit',
      ],
    ];

    $actions = [];

    // Tombol Detail
    if (isset($routeMaps['show'][$roleSlug])) {
      $actions[] = Button::add('detail')
        ->slot('<i class="bi-eye text-white"></i>')
        ->id($row->id)
        ->class('btn btn-info')
        ->route($routeMaps['show'][$roleSlug], ['pelanggan' => $row->id]);
    }

    // Tombol Edit
    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['pelanggan' => $row->id]);
    }

    // Tombol Delete (hanya role 'af' dan 'su')
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

  public function actionRules(): array
  {
    return [
      Rule::button('delete')
        ->when(function ($barang) {
          return
            $barang->juals()->exists();
        })
        ->hide(),
    ];
  }

}
