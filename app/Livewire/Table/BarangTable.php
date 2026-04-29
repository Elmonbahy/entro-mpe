<?php

namespace App\Livewire\Table;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Number;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
final class BarangTable extends PowerGridComponent
{
  public string $tableName = 'barang-table-ayssut-table';

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
    return Barang::query()
      ->join('brands', 'barangs.brand_id', '=', 'brands.id')
      ->select('barangs.*', 'brands.nama as brand_nama', 'brands.id as brand_id')
      ->orderBy('barangs.id', $this->sortDirection); // urut desc
  }

  public function fields(): PowerGridFields
  {
    $fields = PowerGrid::fields()
      ->add('id')
      ->add('brand_nama')
      ->add('kode', fn($row) => $row->kode ?? '-')
      ->add('nama')
      ->add('satuan');

    return $fields;
  }

  public function columns(): array
  {
    $columns = [
      Column::make('ID', 'id')
        ->sortable(),
      Column::make('Brand', 'brand_nama')
        ->sortable(),
      Column::make('Kode', 'kode'),
      Column::make('Nama', 'nama')
        ->sortable(),
      Column::make('Satuan', 'satuan')
        ->sortable(),
    ];
    $columns[] = Column::action('Action');

    return $columns;
  }

  public function filters(): array
  {
    return [
      Filter::inputText('brand_nama', 'brands.nama')->operators(['contains']),
      Filter::inputText('kode')->operators(['contains']),
      Filter::inputText('nama', 'barangs.nama')->operators(['contains']),
      Filter::inputText('id', 'barangs.id')->operators(['contains']),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId): void
  {
    if (!Auth::user()->hasAnyRole(['af'])) {
      abort(403);
    }

    $barang = Barang::find($rowId);
    if ($barang) {
      $barang->delete();
    }
  }

  public function actions(Barang $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.barang.show',
        'af' => 'fakturis.barang.show',
        'as' => 'supervisor.barang.show',
        'su' => 'superadmin.barang.show',
      ],
      'edit' => [
        'af' => 'fakturis.barang.edit',
      ],
    ];

    $actions = [];

    if (isset($routeMaps['show'][$roleSlug])) {
      $actions[] = Button::add('detail')
        ->slot('<i class="bi-eye text-white"></i>')
        ->id($row->id)
        ->class('btn btn-info')
        ->route($routeMaps['show'][$roleSlug], ['barang' => $row->id]);
    }

    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['barang' => $row->id]);
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

  public function actionRules(): array
  {
    return [
      // Sembunyikan tombol delete jika barang sudah digunakan
      Rule::button('delete')
        ->when(function ($barang) {
          return
            $barang->jual_details()->exists() ||
            $barang->beli_details()->exists() ||
            $barang->mutations()->exists();
        })
        ->hide(),
    ];
  }

}
