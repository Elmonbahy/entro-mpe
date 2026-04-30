<?php

namespace App\Livewire\Table;

use App\Enums\StatusRetur;
use App\Models\BarangRetur;
use App\Models\BeliDetail;
use App\Models\JualDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ReturVerifikasiTable extends PowerGridComponent
{
  // Menerima parameter 'jual' atau 'beli' dari View
  public string $tipeTransaksi = '';
  public string $tableName = '';
  public string $sortDirection = 'desc';
  public string $sortField = 'created_at';

  /**
   * Gunakan mount() untuk men-set tableName secara dinamis SEBELUM PowerGrid loading.
   */
  public function mount(): void
  {
    // 1. Set Table Name Unik
    $this->tableName = 'retur-verifikasi-' . $this->tipeTransaksi;

    // 2. Panggil Parent Mount (Inisialisasi PowerGrid)
    parent::mount();
  }

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
    $query = BarangRetur::query()
      ->with(['barang', 'returnable']);

    // Filter Logic berdasarkan parameter
    if ($this->tipeTransaksi === 'jual') {
      $query->where('returnable_type', JualDetail::class)
        ->whereHasMorph('returnable', [JualDetail::class]);
    } elseif ($this->tipeTransaksi === 'beli') {
      $query->where('returnable_type', BeliDetail::class)
        ->whereHasMorph('returnable', [BeliDetail::class]);
    }

    return $query;
  }

  public function fields(): PowerGridFields
  {
    return PowerGrid::fields()
      ->add('id')
      ->add('status_label', fn($model) => $model->status->label()) // Untuk Filter Dropdown
      ->add('status_badge', function ($model) {
        $color = $model->status->color(); // warning, success, danger
        $label = $model->status->label();
        return "<span class='badge bg-{$color}'>{$label}</span>";
      })
      ->add('nomor_faktur', function ($model) {
        if ($model->returnable_type === JualDetail::class) {
          return $model->returnable->jual->nomor_faktur ?? '-';
        }
        return $model->returnable->beli->nomor_faktur ?? '-';
      })
      ->add('barang_nama', fn($model) => $model->barang->nama)
      ->add('batch_info', function ($model) {
        $batch = $model->returnable->batch ?? '-';
        $exp = $model->returnable->tgl_expired ? Carbon::parse($model->returnable->tgl_expired)->format('d/m/Y') : '-';
        return "Batch: <strong>$batch</strong><br><small class='text-muted'>Exp: $exp</small>";
      })
      ->add('jumlah_retur_formatted', fn($model) => "<span class='fw-bold text-danger'>{$model->jumlah_barang_retur} {$model->barang->satuan}</span>")
      ->add('jenis_ganti', fn($model) => $model->is_diganti
        ? "<span class='badge bg-warning text-dark'>Ganti Barang</span>"
        : "<span class='badge bg-secondary'>Tidak Diganti</span>")
      ->add('keterangan');
  }

  public function columns(): array
  {
    return [
      Column::make('No Faktur', 'nomor_faktur'),
      Column::make('Status', 'status_badge', 'status'),
      Column::make('Nama Barang', 'barang_nama', 'barangs.nama')->sortable()->searchable(),
      Column::make('Batch / Exp', 'batch_info'),
      Column::make('Jumlah', 'jumlah_retur_formatted'),
      Column::make('Jenis', 'jenis_ganti'),
      Column::action('Aksi'),
    ];
  }

  public function filters(): array
  {
    return [
      Filter::datepicker('created_at_formatted', 'created_at'),
      Filter::enumSelect('status_badge', 'status')
        ->dataSource(collect(StatusRetur::cases())->map(fn($status) => (object) [
          'value' => $status->value,
          'label' => $status->label(),
        ]))
        ->optionValue('value')
        ->optionLabel('label'),
      Filter::inputText('barang_nama')->operators(['contains']),
    ];
  }

  public function actions(BarangRetur $row): array
  {
    $roleSlug = Auth::user()->role->slug;

    $routeMaps = [
      'show' => [
        'ag' => 'gudang.retur.show',
        'ap' => 'pajak.retur.show',
      ]
    ];

    $actions = [];

    if (isset($routeMaps['show'][$roleSlug])) {
      $actions[] = Button::add('detail')
        ->slot('<i class="bi bi-eye"></i> Detail & Proses')
        ->id($row->id)
        ->class('btn btn-primary btn-sm')
        ->route($routeMaps['show'][$roleSlug], ['id' => $row->id]);
    }

    return $actions;
  }

}