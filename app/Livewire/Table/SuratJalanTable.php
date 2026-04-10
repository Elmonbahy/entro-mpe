<?php

namespace App\Livewire\Table;

use App\Models\SuratJalan;
use App\Services\SuratJalanService;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SuratJalanTable extends PowerGridComponent
{
  public string $tableName = 'surat-jalan-table-qvleke-table';
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
    return SuratJalan::query()
      ->join('pelanggans', function ($supplier) {
        $supplier->on('surat_jalans.pelanggan_id', '=', 'pelanggans.id');
      })
      ->join('kendaraans', function ($supplier) {
        $supplier->on('surat_jalans.kendaraan_id', '=', 'kendaraans.id');
      })
      ->select([
        'surat_jalans.id',
        'surat_jalans.tgl_surat_jalan',
        'surat_jalans.tgl_kembali_surat_jalan',
        'surat_jalans.staf_logistik',
        'surat_jalans.koli',
        'surat_jalans.created_at',
        'surat_jalans.nomor_surat_jalan',
        'pelanggans.nama as pelanggan_nama',
        'kendaraans.nama as kendaraan_nama',
      ]);
  }

  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('nomor_surat_jalan')
      ->add('pelanggan_nama')
      ->add('kendaraan_nama')
      ->add('staf_logistik')
      ->add('koli', fn(SuratJalan $row) => $row->koli . ' Koli')
      ->add('tgl_surat_jalan', fn(SuratJalan $row) => Carbon::parse($row->tgl_surat_jalan)->format('d/m/Y'))
      ->add(
        'tgl_kembali_surat_jalan',
        fn(SuratJalan $row) =>
        $row->tgl_kembali_surat_jalan
        ? \Carbon\Carbon::parse($row->tgl_kembali_surat_jalan)->format('d/m/Y')
        : '-'
      )
    ;
  }

  public function columns(): array
  {
    return [
      Column::make('Nomor Surat Jalan', 'nomor_surat_jalan')
        ->sortable(),
      Column::make('Nama Pelanggan', 'pelanggan_nama')
        ->sortable(),
      Column::make('Nama Kendaraan', 'kendaraan_nama')
        ->sortable(),
      Column::make('Tanggal Surat Jalan', 'tgl_surat_jalan')
        ->sortable(),
      Column::make('Terima Kembali', 'tgl_kembali_surat_jalan')
        ->sortable(),
      Column::make('Penanggung Jawab', 'staf_logistik'),
      Column::make('Jumlah Koli', 'koli'),

      Column::action('Action')
    ];
  }

  public function filters(): array
  {
    return [
      Filter::inputText('nomor_surat_jalan')->operators(['contains']),
      Filter::inputText('pelanggan_nama', 'pelanggans.nama')->operators(['contains']),
      Filter::inputText('kendaraan_nama', 'kendaraans.nama')->operators(['contains']),
      Filter::datepicker('tgl_surat_jalan', 'tgl_surat_jalan'),
    ];
  }

  #[\Livewire\Attributes\On('delete')]
  public function delete($rowId)
  {
    if (!Auth::user()->hasRole('ag')) {
      abort(403);
    }

    $suratJalan = SuratJalan::with('suratJalanDetails.jualDetail')->findOrFail($rowId);

    foreach ($suratJalan->suratJalanDetails as $detail) {
      $jual_id = $detail->jualDetail->jual_id ?? null;
      $detail->delete();

      if ($jual_id) {
        (new SuratJalanService())->updateStatusKirimByJual($jual_id);
      }
    }

    $suratJalan->delete();
  }

  public function actions(SuratJalan $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.surat-jalan.show',
        'al' => 'logistik.surat-jalan.show',
        'af' => 'fakturis.surat-jalan.show',
        'aa' => 'accounting.surat-jalan.show',
        'aw' => 'warehouse.surat-jalan.show',
        'as' => 'supervisor.surat-jalan.show',
        'ap' => 'pajak.surat-jalan.show',
      ],
      'edit' => [
        'ag' => 'gudang.surat-jalan.edit',
        'al' => 'logistik.surat-jalan.edit',
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

    if (Auth::user()->hasRole('ag')) {
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
