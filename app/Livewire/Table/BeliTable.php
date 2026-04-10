<?php

namespace App\Livewire\Table;

use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Models\Beli;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\Filter;

final class BeliTable extends PowerGridComponent
{
  public string $tableName = 'beli-table-ycwfpx-table';
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
    return Beli::with('beliDetails')
      ->join('suppliers', function ($supplier) {
        $supplier->on('belis.supplier_id', '=', 'suppliers.id');
      })
      ->where(function ($q) {
        if (Auth::User()->hasAnyRole(['ag', 'aw'])) {
          $q->where('status_faktur', '!=', StatusFaktur::NEW);
        }

        if (Auth::User()->hasAnyRole(['ak', 'al'])) {
          $q->where('status_faktur', StatusFaktur::DONE);
        }
      })
      ->select([
        'belis.id',
        'belis.nomor_pemesanan',
        'belis.nomor_faktur',
        'belis.tgl_faktur',
        'belis.tgl_terima_faktur',
        'belis.status_faktur',
        'belis.status_bayar',
        'suppliers.nama as supplier_nama',
      ]);
  }

  public function fields(): PowerGridFields
  {
    $fields = PowerGrid::fields()
      ->add('id')
      ->add('nomor_pemesanan')
      ->add('supplier_nama')
      ->add('tgl_faktur', fn(Beli $model) => Carbon::parse($model->tgl_faktur)->format('d/m/Y'))
      ->add('nomor_faktur', fn($nomor_faktur) => $nomor_faktur->nomor_faktur ?? '-')
      ->add('tgl_terima_faktur', fn(Beli $model) => $model->tgl_terima_faktur
        ? Carbon::parse($model->tgl_terima_faktur)->format('d/m/Y')
        : '-')
      ->add('status_faktur_x', function (Beli $model) {
        return \Blade::render('<x-badge.status-faktur :status="$status->value" />', [
          'status' => $model->status_faktur,
        ]);
      });

    if (Auth::user()->hasAnyRole(['af', 'ak', 'aa', 'as', 'ap'])) {
      $fields->add('status_bayar_x', function (Beli $model) {
        return \Blade::render('<x-badge.status-bayar :status="$status->value" />', [
          'status' => $model->status_bayar,
        ]);
      });
      $fields->add(
        'total_faktur_formatted',
        fn(Beli $model) =>
        number_format((float) $model->total_faktur, 0, ',', '.')
      );
    }

    return $fields;
  }

  public function columns(): array
  {
    $columns = [
      Column::make('Nomor faktur', 'nomor_faktur')
        ->sortable(),
      Column::make('Nama supplier', 'supplier_nama', 'suppliers.nama')
        ->sortable(),
      Column::make('Nomor pemesanan', 'nomor_pemesanan')
        ->sortable(),
      Column::make('Tgl faktur', 'tgl_faktur'),
      Column::make('Tgl terima faktur', 'tgl_terima_faktur'),
      Column::make('Status faktur', 'status_faktur_x'),
    ];

    if (Auth::user()->hasAnyRole(['af', 'ak', 'aa', 'as', 'ap'])) {
      $columns[] = Column::make('Total Faktur', 'total_faktur_formatted');
    }

    if (Auth::user()->hasAnyRole(['af', 'ak', 'aa', 'as', 'ap'])) {
      $columns[] = Column::make('Status bayar', 'status_bayar_x');
    }

    // Kolom aksi tetap ditambahkan untuk semua role
    $columns[] = Column::action('Action');

    return $columns;
  }

  public function filters(): array
  {
    return [
      Filter::inputText('suppliers.nama')->operators(['contains']),
      Filter::inputText('nomor_pemesanan')->operators(['contains']),
      Filter::inputText('nomor_faktur')->operators(['contains']),
      Filter::datepicker('tgl_faktur', 'tgl_faktur'),
      Filter::datepicker('tgl_terima_faktur', 'tgl_terima_faktur'),
      Filter::enumSelect('status_faktur_x', 'status_faktur')
        ->datasource(StatusFaktur::cases())
        ->optionLabel('status_faktur_x'),
      Filter::enumSelect('status_bayar_x', 'status_bayar')
        ->datasource(StatusBayar::cases())
        ->optionLabel('status_bayar_x'),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId)
  {
    try {
      $beli = Beli::with('beliDetails')->findOrFail($rowId);
      \Gate::authorize('delete', $beli);

      $beli->delete();
    } catch (\Exception $e) {
      $this->js("alert('" . $e->getMessage() . "')");
    }
  }

  public function actions(Beli $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.beli.show',
        'aw' => 'warehouse.beli.show',
        'al' => 'logistik.beli.show',
        'af' => 'fakturis.beli.show',
        'ak' => 'keuangan.beli.show',
        'aa' => 'accounting.beli.show',
        'as' => 'supervisor.beli.show',
        'ap' => 'pajak.beli.show',
      ],
      'edit' => [
        'af' => 'fakturis.beli.edit',
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

    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['id' => $row->id]);
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
      Rule::button('edit')
        ->when(fn($row) => $row->status_bayar === StatusBayar::PAID)
        ->hide(),
      Rule::button('delete')
        ->when(fn($row) => $row->status_bayar === StatusBayar::PAID)
        ->hide(),
    ];
  }
}
