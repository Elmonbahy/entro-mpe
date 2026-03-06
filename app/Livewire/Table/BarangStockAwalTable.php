<?php

namespace App\Livewire\Table;

use App\Enums\JenisPerubahan;
use App\Models\BarangStock;
use App\Models\BarangStockAwal;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Illuminate\Support\Facades\Auth;

final class BarangStockAwalTable extends PowerGridComponent
{
  public string $tableName = 'barang-stock-awal-table-kdpieu-table';

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
    return BarangStockAwal::query()
      ->join('barangs', 'barang_stock_awals.barang_id', '=', 'barangs.id')
      ->leftJoin('brands', 'barangs.brand_id', '=', 'brands.id')
      ->select([
        'barang_stock_awals.*',
        'barangs.nama as barang_nama',
        'barangs.satuan as barang_satuan',
        'barangs.id as barang_id',
        'brands.nama as brand_nama'
      ]);
  }

  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('barang_nama')
      ->add('brand_nama')
      ->add('barang_satuan')
      ->add('tgl_stock', fn(BarangStockAwal $model) => Carbon::parse($model->tgl_stock)->format('d/m/Y'))
      ->add('jumlah_stock', function ($row) {
        return number_format($row->jumlah_stock, 0, ',', '.'); // Format angka dengan titik pemisah ribuan
      })
      ->add('batch')
      ->add('jenis_perubahan_x', fn($r) => $r->jenis_perubahan->value)
      ->add('tgl_expired', function (BarangStockAwal $model) {
        return $model->tgl_expired
          ? Carbon::parse($model->tgl_expired)->format('d/m/Y')
          : '-';
      })
    ;
  }

  public function columns(): array
  {
    return [
      Column::make('Perubahan', 'jenis_perubahan_x', 'jenis_perubahan')
        ->sortable(),
      Column::make('Tanggal Penyesuaian', 'tgl_stock')
        ->sortable(),
      Column::make('Brand', 'brand_nama')
        ->sortable(),
      Column::make('Nama Barang', 'barang_nama', 'barangs.nama')
        ->sortable(),
      Column::make('Batch', 'batch')
        ->sortable(),
      Column::make('Tgl expired', 'tgl_expired')
        ->sortable(),
      Column::make('Jumlah stock', 'jumlah_stock')
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
      Filter::inputText('brand_nama', 'brands.nama')->operators(['contains']),
      Filter::inputText('batch')->operators(['contains']),
      Filter::datepicker('tgl_stock', 'tgl_stock'),
      Filter::enumSelect('jenis_perubahan_x', 'jenis_perubahan')
        ->datasource(JenisPerubahan::cases())
        ->optionLabel('jenis_perubahan'),

    ];
  }

  #[On('delete')]
  public function delete($rowId): void
  {
    if (!\Auth::user()->hasRole('af')) {
      abort(403);
    }

    try {
      \DB::transaction(function () use ($rowId) {

        $barang_stock_awal = BarangStockAwal::findOrFail($rowId);

        $barang_stock = BarangStock::where('barang_id', $barang_stock_awal->barang_id)
          ->where('batch', $barang_stock_awal->batch)
          ->where('tgl_expired', $barang_stock_awal->tgl_expired)
          ->firstOrFail();

        $stock_awal = (int) $barang_stock->jumlah_stock;

        // Ambil nilai tgl_stock dari BarangStockAwal
        $tgl_stock = $barang_stock_awal->tgl_stock;

        // kembalikan stock
        if ($barang_stock_awal->jenis_perubahan === JenisPerubahan::KURANG) {
          $barang_stock->jumlah_stock += $barang_stock_awal->jumlah_stock;
        } else {

          if ($barang_stock->jumlah_stock < $barang_stock_awal->jumlah_stock) {
            $diff = $barang_stock->jumlah_stock - $barang_stock_awal->jumlah_stock;
            abort(410, "Tidak bisa menghapus stock awal, dapat menyebabkan minus pada stock saaat ini, hasil akhir stock: $diff");
          }

          $barang_stock->jumlah_stock -= $barang_stock_awal->jumlah_stock;
        }

        $barang_stock->save();
        $barang_stock_awal->mutation()->create([
          'stock_awal' => $stock_awal,
          'stock_keluar' => $barang_stock_awal->jumlah_stock,
          'stock_akhir' => $barang_stock->jumlah_stock,
          'barang_id' => $barang_stock->barang_id,
          'batch' => $barang_stock->batch,
          'tgl_expired' => $barang_stock->tgl_expired,
          'tgl_mutation' => Carbon::parse($tgl_stock)->format('Y-m-d')
        ]);

        $barang_stock_awal->delete();
      });
    } catch (\Exception $e) {
      $this->js("alert('Gagal! " . $e->getMessage() . "')");
    }
  }

  public function actions(BarangStockAwal $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'af' => 'fakturis.stock-awal.show',
        'aa' => 'accounting.stock-awal.show',
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
