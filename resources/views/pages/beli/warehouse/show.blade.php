@extends('layouts.main-layout')

@section('title')
  Detail barang masuk
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Barang masuk" class="mb-3">
      @if (
          $beli->status_faktur === \App\Enums\StatusFaktur::PROCESS_GUDANG ||
              $beli->status_faktur === \App\Enums\StatusFaktur::DONE)
        <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle" type="button" data-coreui-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-file-pdf-fill"></i>
            Ekspor PDF
          </button>
          <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item" href="{{ route('warehouse.beli.do', ['id' => $beli->id]) }}">
                <i class="bi bi-file-earmark-arrow-down"></i> DO Masuk
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('warehouse.beli.qc-all', ['beli_id' => $beli->id]) }}">
                <i class="bi bi-file-earmark-arrow-down"></i> QC All
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('warehouse.beli.retur', ['id' => $beli->id]) }}">
                <i class="bi bi-file-earmark-arrow-down"></i> Form Retur
              </a>
            </li>
          </ul>
        </div>
      @endif
    </x-page-header>
    <x-alert.session-alert />

    <x-card.faktur-beli-detail :beli="$beli" />

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar pembelian barang</p>
      </div>

      <div class="card-body">
        @if ($beli_details->isEmpty())
          <p class="mb-0 text-center">Tidak ada data.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <th>Brand</th>
                <th>Nama</th>
                <th>Pesanan</th>
                <th>Masuk</th>
                <th>Satuan</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Nomor izin edar</th>
                <th>Status barang masuk</th>
                <th>Action</th>
              </thead>
              <tbody>
                @foreach ($beli_details as $item)
                  <tr wire:key="{{ $item->id }}">
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_masuk ? number_format($item->jumlah_barang_masuk, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}</td>
                    <td>
                      <span class="nie-text" data-id="{{ $item->id }}">
                        {{ $item->barang->nie ?? '-' }}
                        <i class="bi bi-pencil ms-1 text-secondary" style="cursor:pointer;"></i>
                      </span>

                      <form action="{{ route('warehouse.beli.updateNie', $item->id) }}" method="POST"
                        class="d-none nie-form" data-id="{{ $item->id }}">
                        @csrf
                        @method('PUT')
                        <div class="d-flex">
                          <input type="text" name="nie" class="form-control form-control-sm"
                            value="{{ $item->barang->nie }}">
                          <button type="submit" class="btn btn-sm btn-primary ms-1"><i
                              class="bi bi-check-circle"></i></button>
                          <button type="button" class="btn btn-sm btn-secondary ms-1 cancel-btn"><i
                              class="bi bi-x-circle"></i></button>
                        </div>
                      </form>
                    </td>
                    <td>
                      <x-badge.status-barang-masuk :status="$item->status_barang_masuk" />
                    </td>
                    @if (
                        $beli->status_faktur === \App\Enums\StatusFaktur::PROCESS_GUDANG ||
                            $beli->status_faktur === \App\Enums\StatusFaktur::DONE)
                      <td>
                        <div class="d-flex gap-2">
                          <a href="{{ route('warehouse.beli.qc', [$beli->id, $item->id]) }}" class="btn btn btn-primary"
                            title="Cek Kualitas Barang (QC)">
                            <i class="bi bi-list-check"></i></a>
                        </div>
                      </td>
                    @else
                      <td></td>
                    @endif
                  </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        @endif
      </div>
    </div>


    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar barang retur</p>
      </div>

      <div class="card-body">
        @if ($returs->isEmpty())
          <p class="mb-0 text-center">Tidak ada data.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <th>Brand</th>
                <th>Nama</th>
                <th>Retur</th>
                <th>Satuan</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Nomor izin edar</th>
                <th>Keterangan</th>
                <th>Diganti</th>
              </thead>
              <tbody>
                @foreach ($returs as $item)
                  <tr>
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ $item->jumlah_barang_retur }}</td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>{{ $item->returnable->batch ?: '-' }}</td>
                    <td>
                      {{ $item->returnable->tgl_expired ? \Carbon\Carbon::parse($item->returnable->tgl_expired)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $item->barang->nie ?: '-' }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>
                      @if ($item->is_diganti && !$item->diganti_at)
                        <i class="bi bi-check-circle-fill text-warning"></i>
                      @elseif ($item->is_diganti && $item->diganti_at)
                        <i class="bi bi-check-circle-fill text-success"></i>
                      @else
                        <i class="bi bi-dash-circle-fill text-danger"></i>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.nie-text').forEach(span => {
        span.addEventListener('click', function() {
          const id = this.dataset.id;
          this.classList.add('d-none');
          document.querySelector(`.nie-form[data-id="${id}"]`).classList.remove('d-none');
        });
      });

      document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
          const form = this.closest('.nie-form');
          const id = form.dataset.id;
          form.classList.add('d-none');
          document.querySelector(`.nie-text[data-id="${id}"]`).classList.remove('d-none');
        });
      });
    });
  </script>
@endpush
