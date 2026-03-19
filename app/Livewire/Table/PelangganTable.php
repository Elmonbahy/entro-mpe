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
      ->add('kode')
      ->add('nama')
      ->add('kota')
      ->add('npwp')
      ->add('contact_phone');
    if (Auth::user()->hasRole('ak')) {
      // Kolom tambahan khusus untuk role 'ak'
      $fields
        ->add('plafon_hutang', fn($row) => Number::currency($row->plafon_hutang ?? 0, in: 'IDR', locale: 'id_ID'))
        ->add('limit_hari');
    }
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
      Column::make('Contact phone', 'contact_phone'),
      Column::make('Npwp', 'npwp')
    ];

    if (Auth::user()->hasRole('ak')) {
      // Kolom tambahan khusus untuk role 'af'
      $columns[] = Column::make('Plafon Hutang', 'plafon_hutang');
      $columns[] = Column::make('Limit Hari', 'limit_hari');
    }
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
    if (!Auth::user()->hasAnyRole(['af'])) {
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
        'al' => 'logistik.pelanggan.show',
        'ak' => 'keuangan.pelanggan.show',
        'af' => 'fakturis.pelanggan.show',
        'ag' => 'gudang.pelanggan.show',
        'as' => 'supervisor.pelanggan.show',
      ],
      'edit' => [
        'al' => 'logistik.pelanggan.edit',
        'ak' => 'keuangan.pelanggan.edit',
        'af' => 'fakturis.pelanggan.edit',
        'ag' => 'gudang.pelanggan.edit',
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

    // Tombol Delete (hanya role 'af')
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
            $barang->juals()->exists();
        })
        ->hide(),
    ];
  }

}
