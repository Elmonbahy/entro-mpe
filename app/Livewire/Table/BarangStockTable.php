<?php

namespace App\Livewire\Table;

use App\Models\BarangStock;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Rule;

final class BarangStockTable extends PowerGridComponent
{
  public string $tableName = 'barang-stock-table-inv1yr-table';

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
    return BarangStock::query()
      ->join('barangs', 'barang_stocks.barang_id', '=', 'barangs.id')
      ->leftJoin('brands', 'barangs.brand_id', '=', 'brands.id')
      ->select([
        'barangs.id as barang_id',
        'barangs.nama as barang_nama',
        'barangs.satuan as satuan',
        'brands.nama as brand_nama',
        \DB::raw('SUM(COALESCE(barang_stocks.jumlah_stock, 0)) as jumlah_stock')
      ])
      ->groupBy('barangs.id', 'barangs.nama', 'brands.nama', 'barangs.satuan')
      ->orderBy('brand_nama', 'asc')
      ->orderBy('barang_nama', 'asc');
  }

  public string $sortField = 'barang_id';



  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('barang_nama', function ($row) {
        $prefix = auth()->user()->getRoutePrefix();

        return \Blade::render(
          '<x-button.link-button href="{{ $href }}">{{ $label }}</x-button.link-button>',
          [
            'href' => route("{$prefix}.barang.show", $row->barang_id),
            'label' => $row->barang_nama
          ]
        );
      })
      ->add('brand_nama')
      ->add('jumlah_stock', function ($row) {
        return number_format($row->jumlah_stock, 0, ',', '.');
      })
      ->add('satuan');
  }

  public function columns(): array
  {
    return [
      Column::make('Brand', 'brand_nama')
        ->sortable(),
      Column::make('Nama Barang', 'barang_nama', 'barangs.nama')
        ->sortable(),
      Column::make('Jumlah stock', 'jumlah_stock')
        ->sortable(),
      Column::make('Satuan', 'satuan')
        ->sortable()
    ];
  }

  public function filters(): array
  {
    return [
      Filter::inputText('barang_nama', 'barangs.nama')->operators(['contains']),
      Filter::inputText('brand_nama', 'brands.nama')->operators(['contains']),
      Filter::inputText('brand_nama', 'brands.nama')->operators(['contains'])
    ];
  }

}
