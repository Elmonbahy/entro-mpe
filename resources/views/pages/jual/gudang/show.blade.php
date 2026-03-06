@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Barang Keluar" class="mb-3">
      <a href="{{ route('gudang.jual.pdf', ['id' => $jual->id]) }}" class="btn btn-primary" target="_blank">
        <i class="bi bi-file-pdf-fill"></i>
        Ekspor PDF
      </a>
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

        @if ($jual->status_faktur == \App\Enums\StatusFaktur::PROCESS_GUDANG)
          <form method="POST" action="{{ route('gudang.jual.done', ['id' => $jual->id]) }}"
            onsubmit="return confirm('Apakah barang keluar sudah sesuai?')">
            @csrf
            @method('PATCH')

            <button class="btn btn-primary">
              Selesai
            </button>
          </form>
        @endif
      </div>

      <div class="card-body">
        @if ($jual_detail->isEmpty())
          <p class="mb-0 text-center">Tidak ada data.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <th>Brand</th>
                <th>Kode Barang</th>
                <th>Nama</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
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
                    <td>{{ $item->barang->kode }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}</td>
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_keluar ? number_format($item->jumlah_barang_keluar, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>
                      <x-badge.status-barang-keluar :status="$item->status_barang_keluar" />
                    </td>

                    <td>
                      <div class="d-flex gap-2">
                        @unless ($jual->status_bayar === \App\Enums\StatusBayar::PAID)
                          @can('retur', $item)
                            <a href="{{ route('gudang.jual.retur-item', ['jual_detail_id' => $item->id, 'id' => $jual->id]) }}"
                              class="btn btn-warning" title="retur barang keluar">
                              <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                          @endcan

                          @can('stock', $item)
                            <a href="{{ route('gudang.jual.stock-item', ['jual_detail_id' => $item->id, 'id' => $jual->id]) }}"
                              class="btn btn-primary" title="stock barang keluar">
                              <i class="bi bi-plus-slash-minus"></i>
                            </a>
                          @endcan
                        @endunless
                      </div>
                    </td>
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
                <th>Kode Barang</th>
                <th>Nama</th>
                <th>Retur</th>
                <th>Satuan</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Keterangan</th>
                <th>Diganti</th>
                <th>Action</th>
              </thead>
              <tbody>
                @foreach ($returs as $item)
                  <tr>
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->kode }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ $item->jumlah_barang_retur }}</td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>{{ $item->returnable->batch ?: '-' }}</td>
                    <td>
                      {{ $item->returnable->tgl_expired ? \Carbon\Carbon::parse($item->returnable->tgl_expired)->format('d/m/Y') : '-' }}
                    </td>
                    <td>
                      <span class="keterangan-text" data-id="{{ $item->id }}">
                        {{ $item->keterangan ?? '-' }}
                        <i class="bi bi-pencil ms-1 text-secondary" style="cursor:pointer;"></i>
                      </span>

                      <form action="{{ route('gudang.jual.updateKeterangan', $item->id) }}" method="POST"
                        class="d-none keterangan-form" data-id="{{ $item->id }}">
                        @csrf
                        @method('PUT')
                        <div class="d-flex">
                          <input type="text" name="keterangan" class="form-control form-control-sm"
                            value="{{ $item->keterangan }}">
                          <button type="submit" class="btn btn-sm btn-primary ms-1"><i
                              class="bi bi-check-circle"></i></button>
                          <button type="button" class="btn btn-sm btn-secondary ms-1 cancel-btn"><i
                              class="bi bi-x-circle"></i></button>
                        </div>
                      </form>
                    </td>
                    <td>
                      @if ($item->is_diganti && !$item->diganti_at)
                        <i class="bi bi-check-circle-fill text-warning"></i>
                      @elseif ($item->is_diganti && $item->diganti_at)
                        <i class="bi bi-check-circle-fill text-success"></i>
                      @else
                        <i class="bi bi-dash-circle-fill text-danger"></i>
                      @endif
                    </td>
                    <td>
                      @if ($item->is_diganti && !$item->diganti_at)
                        <a href="{{ route('gudang.jual.retur-done', ['id' => $item->id]) }}"
                          class="btn btn-sm btn-success"
                          onclick="return confirm('Apakah barang diganti sudah keluar gudang?');">
                          Done
                        </a>
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
      document.querySelectorAll('.keterangan-text').forEach(span => {
        span.addEventListener('click', function() {
          const id = this.dataset.id;
          this.classList.add('d-none');
          document.querySelector(`.keterangan-form[data-id="${id}"]`).classList.remove('d-none');
        });
      });

      document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
          const form = this.closest('.keterangan-form');
          const id = form.dataset.id;
          form.classList.add('d-none');
          document.querySelector(`.keterangan-text[data-id="${id}"]`).classList.remove('d-none');
        });
      });
    });
  </script>
@endpush
