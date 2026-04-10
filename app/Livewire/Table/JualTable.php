<?php

namespace App\Livewire\Table;

use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Enums\StatusKirim;
use App\Constants\TipePenjualan;
use App\Models\Jual;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Facades\Filter;

final class JualTable extends PowerGridComponent
{
  public string $tableName = 'jual-table-ycwfpx-table';
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
    return Jual::with('jualDetails')
      ->join('pelanggans', function ($supplier) {
        $supplier->on('juals.pelanggan_id', '=', 'pelanggans.id');
      })
      ->join('salesmen', function ($salesmen) {
        $salesmen->on('juals.salesman_id', '=', 'salesmen.id');
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
        'juals.id',
        'juals.nomor_faktur',
        'juals.nomor_pemesanan',
        'juals.tgl_faktur',
        'juals.status_faktur',
        'juals.status_bayar',
        'juals.status_kirim',
        'juals.tipe_penjualan',
        'pelanggans.nama as pelanggan_nama',
        'salesmen.nama as salesmen_nama',
      ]);
  }

  public function fields(): PowerGridFields
  {
    $fields = PowerGrid::fields()
      ->add('nomor_faktur')
      ->add('pelanggan_nama')
      ->add('salesmen_nama')
      ->add('tgl_faktur', fn(Jual $model) => Carbon::parse($model->tgl_faktur)->format('d/m/Y'))
      ->add('nomor_pemesanan', fn($nomor_pemesanan) => $nomor_pemesanan->nomor_pemesanan ?? '-')
      ->add('tipe_penjualan', fn(Jual $model) => $model->tipe_penjualan ?? '-')
      ->add('status_faktur_x', function (Jual $model) {
        return \Blade::render('<x-badge.status-faktur :status="$status->value" />', [
          'status' => $model->status_faktur,
        ]);
      });

    if (Auth::user()->hasAnyRole(['af', 'ak', 'aa', 'as', 'ap'])) {
      $fields->add('status_bayar_x', function (Jual $model) {
        return \Blade::render('<x-badge.status-bayar :status="$status->value" />', [
          'status' => $model->status_bayar,
        ]);
      });
      $fields->add(
        'total_faktur_formatted',
        fn(Jual $model) =>
        number_format((float) $model->total_faktur, 0, ',', '.')
      );
    }

    if (Auth::user()->hasAnyRole(['ag', 'aw', 'af', 'as', 'ap'])) {
      $fields->add('status_kirim_x', function (Jual $model) {
        return \Blade::render('<x-badge.status-kirim :status="$status->value" />', [
          'status' => $model->status_kirim,
        ]);
      });
    }

    return $fields;
  }

  public function columns(): array
  {
    $columns = [
      Column::make('Nomor faktur', 'nomor_faktur')
        ->sortable(),
      Column::make('Nama Pelanggan', 'pelanggan_nama', 'pelanggans.nama')
        ->sortable(),
      Column::make('Salesman', 'salesmen_nama', 'salesmen.nama')
        ->sortable(),
      Column::make('Tipe Penjualan', 'tipe_penjualan'),
      Column::make('Nomor pemesanan', 'nomor_pemesanan')
        ->sortable(),
      Column::make('Tgl faktur', 'tgl_faktur'),
      Column::make('Status faktur', 'status_faktur_x'),
    ];

    if (Auth::user()->hasAnyRole(['af', 'ak', 'aa', 'as', 'ap'])) {
      $columns[] = Column::make('Total Faktur', 'total_faktur_formatted');
    }

    if (Auth::user()->hasAnyRole(['af', 'ak', 'aa', 'as', 'ap'])) {
      $columns[] = Column::make('Status bayar', 'status_bayar_x');
    }

    if (Auth::user()->hasAnyRole(['ag', 'aw', 'af', 'as', 'ap'])) {
      $columns[] = Column::make('Status kirim', 'status_kirim_x');
    }

    // Kolom aksi tetap ditambahkan untuk semua role
    $columns[] = Column::action('Action');

    return $columns;
  }
  public function filters(): array
  {
    return [
      Filter::inputText('nomor_pemesanan')->operators(['contains']),
      Filter::inputText('pelanggans.nama')->operators(['contains']),
      Filter::inputText('salesmen.nama')->operators(['contains']),
      Filter::enumSelect('tipe_penjualan', 'tipe_penjualan')
        ->dataSource(
          collect(TipePenjualan::all())
            ->map(fn($item) => (object) ['id' => $item, 'name' => $item])
        )
        ->optionValue('id')
        ->optionLabel('name'),
      Filter::inputText('nomor_faktur')->operators(['contains']),
      Filter::datepicker('tgl_faktur', 'tgl_faktur'),
      Filter::enumSelect('status_faktur_x', 'status_faktur')
        ->datasource(StatusFaktur::cases())
        ->optionLabel('status_faktur_x'),
      Filter::enumSelect('status_kirim_x', 'status_kirim')
        ->datasource(StatusKirim::cases())
        ->optionLabel('status_kirim_x'),
      Filter::enumSelect('status_bayar_x', 'status_bayar')
        ->datasource(StatusBayar::cases())
        ->optionLabel('status_bayar_x'),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId)
  {
    try {
      $jual = Jual::with('jualDetails')->findOrFail($rowId);
      \Gate::authorize('delete', $jual);

      $jual->delete();
    } catch (\Exception $e) {
      $this->js("alert('" . $e->getMessage() . "')");
    }
  }

  public function actions(Jual $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.jual.show',
        'aw' => 'warehouse.jual.show',
        'al' => 'logistik.jual.show',
        'af' => 'fakturis.jual.show',
        'ak' => 'keuangan.jual.show',
        'aa' => 'accounting.jual.show',
        'as' => 'supervisor.jual.show',
        'ap' => 'pajak.jual.show',
      ],
      'edit' => [
        'af' => 'fakturis.jual.edit',
      ],
    ];

    $actions = [];

    // Tombol DETAIL hanya jika route show tersedia
    if (isset($routeMaps['show'][$roleSlug])) {
      $actions[] = Button::add('detail')
        ->slot('<i class="bi-eye text-white"></i>')
        ->id($row->id)
        ->class('btn btn-info')
        ->route($routeMaps['show'][$roleSlug], ['id' => $row->id]);
    }

    // Tombol EDIT hanya untuk role yang punya akses edit
    if (isset($routeMaps['edit'][$roleSlug])) {
      $actions[] = Button::add('edit')
        ->slot('<i class="bi-pencil text-white"></i>')
        ->id($row->id)
        ->class('btn btn-warning')
        ->route($routeMaps['edit'][$roleSlug], ['id' => $row->id]);
    }

    // Tombol DELETE hanya untuk role 'af'
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
