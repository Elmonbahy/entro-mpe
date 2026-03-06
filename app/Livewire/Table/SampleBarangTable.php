<?php

namespace App\Livewire\Table;


use App\Models\SampleBarang;
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
final class SampleBarangTable extends PowerGridComponent
{
  public string $tableName = 'sample-barang-table-ayssut-table';

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
    return SampleBarang::query()
      ->join('barangs', 'sample_barangs.barang_id', '=', 'barangs.id')
      ->leftJoin('brands', 'barangs.brand_id', '=', 'brands.id')
      ->select(
        'sample_barangs.id as id',
        'sample_barangs.barang_id',
        'sample_barangs.satuan',
        'barangs.kode',
        'barangs.nama',
        'brands.nama as brand_nama'
      )
      ->orderBy('sample_barangs.id', $this->sortDirection);
  }

  public function fields(): PowerGridFields
  {
    $fields = PowerGrid::fields()
      ->add('barang_id')
      ->add('brand_nama')
      ->add('kode', fn($row) => $row->kode ?? '-')
      ->add('nama')
      ->add('satuan');

    return $fields;
  }

  public function columns(): array
  {
    $columns = [
      Column::make('Barang Id', 'barang_id')
        ->sortable(),
      Column::make('Brand', 'brand_nama')
        ->sortable(),
      Column::make('Kode', 'kode'),
      Column::make('Nama', 'nama')
        ->sortable(),
      Column::make('Satuan', 'satuan')
        ->sortable(),
    ];

    // Kolom aksi tetap ditambahkan untuk semua role
    $columns[] = Column::action('Action');

    return $columns;
  }

  public function filters(): array
  {
    return [
      Filter::inputText('brand_nama', 'brands.nama')->operators(['contains']),
      Filter::inputText('kode')->operators(['contains']),
      Filter::inputText('nama', 'barangs.nama')->operators(['contains']),
      Filter::inputText('barang_id', 'sample_barangs.id')->operators(['contains']),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId): void
  {
    if (!Auth::user()->hasAnyRole(['as', 'af'])) {
      abort(403);
    }

    $sample_barang = SampleBarang::find($rowId);
    if ($sample_barang) {
      $sample_barang->delete();
    }
  }

  public function actions(SampleBarang $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.barang-sample.show',
        'af' => 'fakturis.sample-barang.show',
        'as' => 'supervisor.sample-barang.show',
      ],
      'edit' => [
        'as' => 'supervisor.sample-barang.edit',
        'af' => 'fakturis.sample-barang.edit',
      ],
    ];

    $actions = [];

    if (isset($routeMaps['show'][$roleSlug])) {
      $actions[] = Button::add('detail')
        ->slot('<i class="bi-eye text-white"></i>')
        ->id($row->id)
        ->class('btn btn-info')
        ->route($routeMaps['show'][$roleSlug], ['sample_barang' => $row->id]);
    }

    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['sample_barang' => $row->id]);
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
      // Sembunyikan tombol delete jika barang sudah digunakan
      Rule::button('delete')
        ->when(function ($sample_barang) {
          return
            $sample_barang->sampleInDetails()->exists() ||
            $sample_barang->sampleOutDetails()->exists() ||
            $sample_barang->sampleMutations()->exists();
        })
        ->hide(),
    ];
  }

}
