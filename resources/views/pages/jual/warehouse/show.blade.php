@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Barang Keluar" class="mb-3">
      @if (
          $jual->status_faktur === \App\Enums\StatusFaktur::PROCESS_GUDANG ||
              $jual->status_faktur === \App\Enums\StatusFaktur::DONE)
        <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle" type="button" data-coreui-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-file-pdf-fill"></i>
            Ekspor PDF
          </button>
          <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item" href="{{ route('warehouse.jual.do', ['id' => $jual->id]) }}">
                <i class="bi bi-file-earmark-arrow-down"></i> DO Keluar
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('warehouse.jual.qc-all', ['jual_id' => $jual->id]) }}">
                <i class="bi bi-file-earmark-arrow-down"></i> QC All
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('warehouse.jual.retur', ['id' => $jual->id]) }}">
                <i class="bi bi-file-earmark-arrow-down"></i> Form Retur
              </a>
            </li>
          </ul>
        </div>
      @endif
    </x-page-header>
    <x-alert.session-alert />

    <div class="mb-3">
      <x-card.faktur-jual-detail :jual="$jual" />
    </div>

    <div class="mb-3">
      <x-card.faktur-jual-surat-jalan :jual="$jual" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar penjualan barang</p>
      </div>

      <div class="card-body">
        @if ($jual_detail->isEmpty())
          <p class="mb-0 text-center">Tidak ada data.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <th>Brand</th>
                <th>Nama</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Nomor izin edar</th>
                <th>Pesanan</th>
                <th>Keluar</th>
                <th>Satuan</th>
                <th>Status barang keluar</th>
                <th>Action</th>
              </thead>
              <tbody>
                @foreach ($jual_detail as $item)
                  <tr>
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}</td>
                    <td>
                      <span class="nie-text" data-id="{{ $item->id }}">
                        {{ $item->barang->nie ?? '-' }}
                        <i class="bi bi-pencil ms-1 text-secondary" style="cursor:pointer;"></i>
                      </span>

                      <form action="{{ route('warehouse.jual.updateNie', $item->id) }}" method="POST"
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
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_keluar ? number_format($item->jumlah_barang_keluar, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>
                      <x-badge.status-barang-keluar :status="$item->status_barang_keluar" />
                    </td>
                    @if (
                        $jual->status_faktur === \App\Enums\StatusFaktur::PROCESS_GUDANG ||
                            $jual->status_faktur === \App\Enums\StatusFaktur::DONE)
                      <td>
                        <div class="d-flex gap-2">
                          <a href="{{ route('warehouse.jual.qc', [$jual->id, $item->id]) }}" class="btn btn btn-primary"
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
                <th>Nomor Izin Edar</th>
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
