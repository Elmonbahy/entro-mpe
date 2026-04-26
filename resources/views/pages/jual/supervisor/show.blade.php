@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Penjualan" class="mb-3">
      <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-coreui-toggle="dropdown" aria-expanded="false"> <i
            class="bi bi-file-pdf-fill"></i>
          Ekspor PDF
        </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="{{ route('supervisor.jual.faktur', ['id' => $jual->id]) }}" target="_blank">
              <i class="bi bi-file-earmark-arrow-down"></i> Faktur
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('supervisor.jual.spkb', ['id' => $jual->id]) }}" target="_blank">
              <i class="bi bi-file-earmark-arrow-down"></i> SPKB
            </a>
          </li>
        </ul>
      </div>
    </x-page-header>

    <div class="mb-3">
      <x-card.faktur-jual-detail :jual="$jual" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Penjualan Barang</p>
        <a class="btn btn-primary" href="{{ route('supervisor.jual.add-item', ['id' => $jual->id]) }}">
          Sesuaikan
        </a>
      </div>

      <div class="card-body">
        @if ($jual_details->isEmpty())
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
                <th>Diskon1</th>
                <th>Diskon2</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Harga Jual</th>
                <th>Total</th>
              </thead>
              <tbody>
                @foreach ($jual_details as $item)
                  <tr>
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->kode }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_keluar ? number_format($item->jumlah_barang_keluar, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>{{ $item->diskon1 }}</td>
                    <td>{{ $item->diskon2 }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>
                      @if ($item->tgl_expired)
                        {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') }}
                      @else
                        -
                      @endif
                    </td>
                    <td>{{ Number::currency($item->harga_jual, in: 'IDR', locale: 'id_ID') }}</td>
                    <td>{{ Number::currency($item->total_tagihan, in: 'IDR', locale: 'id_ID') }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="10" class="fw-bold text-end">
                    Total Faktur
                  </td>
                  <td class="fw-bold" colspan="3">
                    {{ Number::currency(round($jual_details->sum('total_tagihan')), in: 'IDR', locale: 'id_ID') }}</td>
                </tr>
              </tfoot>
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
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->kode }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ $item->jumlah_barang_retur }}</td>
                    <td>{{ $item->barang->satuan }}</td>
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
