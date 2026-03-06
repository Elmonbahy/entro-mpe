@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Sampel Keluar" class="mb-3">
      <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-coreui-toggle="dropdown" aria-expanded="false"> <i
            class="bi bi-file-pdf-fill"></i>
          Ekspor PDF
        </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="{{ route('supervisor.sample-out.surat-sample', ['id' => $sample_out->id]) }}"
              target="_blank">
              <i class="bi bi-file-earmark-arrow-down"></i> Surat
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('supervisor.sample-out.spkb', ['id' => $sample_out->id]) }}"
              target="_blank">
              <i class="bi bi-file-earmark-arrow-down"></i> SPKB
            </a>
          </li>
        </ul>
      </div>
    </x-page-header>

    <div class="mb-3">
      <x-card.sample-keluar-detail :sample_out="$sample_out" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Barang Sampel Keluar</p>

        <a class="btn btn-primary" href="{{ route('supervisor.sample-out.add-item', ['id' => $sample_out->id]) }}">
          Sesuaikan
        </a>

      </div>

      <div class="card-body">
        @if ($sample_out_details->isEmpty())
          <p class="mb-0 text-center">Belum ada barang, klik sesuaikan untuk menambahkan barang.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead>
                <th>Brand</th>
                <th>Kode Barang</th>
                <th>Nama</th>
                <th>Pesanan</th>
                <th>Keluar</th>
                <th>Satuan</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
              </thead>
              <tbody>
                @foreach ($sample_out_details as $item)
                  <tr>
                    <td>{{ $item->sampleBarang->barang->brand->nama }}</td>
                    <td>{{ $item->sampleBarang->barang->kode }}</td>
                    <td>{{ $item->sampleBarang->barang->nama }}</td>
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_keluar ? number_format($item->jumlah_barang_keluar, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->sampleBarang->satuan }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>
                      @if ($item->tgl_expired)
                        {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') }}
                      @else
                        -
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


    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar barang sampel retur</p>
      </div>

      <div class="card-body">
        @if ($returs->isEmpty())
          <p class="mb-0 text-center">Tidak ada data</p>
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
              </thead>
              <tbody>
                @foreach ($returs as $item)
                  <tr>
                    <td>{{ $item->sampleBarang->barang->brand->nama }}</td>
                    <td>{{ $item->sampleBarang->barang->kode }}</td>
                    <td>{{ $item->sampleBarang->barang->nama }}</td>
                    <td>{{ $item->jumlah_barang_retur }}</td>
                    <td>{{ $item->sampleBarang->satuan }}</td>
                    <td>{{ $item->returnable->batch ?: '-' }}</td>
                    <td>
                      {{ $item->returnable->tgl_expired ? \Carbon\Carbon::parse($item->returnable->tgl_expired)->format('d/m/Y') : '-' }}
                    </td>
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
