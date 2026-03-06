<?php

namespace App\Livewire\Table;

use App\Enums\PenyebabBarangRusak;
use App\Enums\TindakanBarangRusak;
use App\Models\BarangRusak;
use App\Models\BarangStock;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BarangRusakTable extends PowerGridComponent
{
  public string $tableName = 'barang-rusak-table-u1ncnu-table';
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
    return BarangRusak::query()
      ->join('barang_stocks', 'barang_stocks.id', '=', 'barang_rusaks.barang_stock_id')
      ->leftJoin('barangs', 'barang_stocks.barang_id', '=', 'barangs.id')
      ->select([
        'barang_rusaks.id',
        'barang_rusaks.jumlah_barang_rusak',
        'barang_rusaks.tindakan',
        'barang_rusaks.penyebab',
        'barang_rusaks.tgl_rusak',
        'barangs.nama as barang_nama',
        'barangs.satuan as barang_satuan',
        'barangs.id as barang_id'
      ]);
  }

  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('barang_nama')
      ->add('tgl_rusak', fn(BarangRusak $model) => Carbon::parse($model->tgl_rusak)->format('d/m/Y'))
      ->add('jumlah_barang_rusak', function ($row) {
        return number_format($row->jumlah_barang_rusak, 0, ',', '.'); // Format angka dengan titik pemisah ribuan
      })
      ->add('penyebab_x', fn(BarangRusak $r) => $r->penyebab->value)
      ->add('tindakan_x', fn(BarangRusak $r) => $r->tindakan->value)
      ->add('barang_satuan');
  }

  public function columns(): array
  {
    return [
      Column::make('Nama Barang', 'barang_nama', 'barangs.nama')->sortable(),
      Column::make('Tanggal Input', 'tgl_rusak'),
      Column::make('Penyebab', 'penyebab_x', 'penyebab')
        ->sortable(),
      Column::make('Tindakan', 'tindakan_x', 'tindakan')
        ->sortable(),
      Column::make('Jumlah', 'jumlah_barang_rusak')
        ->sortable(),
      Column::make('Satuan', 'barang_satuan')
        ->sortable(),
      Column::action('Action')
    ];
  }

  public function filters(): array
  {
    return [
      Filter::inputText('barang_nama', 'barangs.nama')->operators(['contains']),
      Filter::datepicker('tgl_rusak', 'tgl_rusak'),
      Filter::enumSelect('penyebab_x', 'penyebab')
        ->datasource(PenyebabBarangRusak::cases())
        ->optionLabel('penyebab'),
      Filter::enumSelect('tindakan_x', 'tindakan')
        ->datasource(TindakanBarangRusak::cases())
        ->optionLabel('tindakan'),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId): void
  {
    if (!\Auth::user()->hasRole('ag')) {
      abort(403);
    }

    try {
      \DB::beginTransaction();
      $barang_rusak = BarangRusak::findOrFail($rowId);
      $barang_stock = BarangStock::where('id', $barang_rusak->barang_stock_id)
        ->firstOrFail();

      $stock_awal = $barang_stock->jumlah_stock;

      // Ambil nilai tgl_rusak dari Barang rusak
      $tgl_rusak = $barang_rusak->tgl_rusak;

      // kembalikan stock
      $barang_stock->jumlah_stock += $barang_rusak->jumlah_barang_rusak;
      $barang_stock->save();

      $barang_rusak->mutation()->create([
        'stock_awal' => $stock_awal,
        'stock_masuk' => $barang_rusak->jumlah_barang_rusak,
        'stock_akhir' => $barang_stock->jumlah_stock,
        'barang_id' => $barang_stock->barang_id,
        'batch' => $barang_stock->batch,
        'tgl_expired' => $barang_stock->tgl_expired,
        'tgl_mutation' => $tgl_rusak
      ]);

      $barang_rusak->delete();
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollBack();

      $this->js("alert('Gagal! " . $e->getMessage() . "')");
    }
  }

  public function actions(BarangRusak $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.barang-rusak.show',
        'aa' => 'accounting.barang-rusak.show',
        'af' => 'fakturis.barang-rusak.show',
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

    if (Auth::user()->hasRole('su')) {
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
