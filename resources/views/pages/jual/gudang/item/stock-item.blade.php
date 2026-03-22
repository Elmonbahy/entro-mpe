@extends('layouts.main-layout')

@section('content')
  <div class="container px-4">
    <x-alert.session-alert />
    <x-page-header title="Barang keluar" class="mb-3" withBackButton />

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Data Barang Keluar</p>
      </div>

      <div class="card-body p-2">
        <table class="table mb-0">
          <tr>
            <td width="250">Nomor pemesanan</td>
            <td><strong>{{ $jual_detail->jual->nomor_pemesanan }}</strong></td>
          </tr>
          <tr>
            <td>Nomor Faktur</td>
            <td>
              @if ($jual_detail->jual->nomor_faktur > 0)
                <strong>{{ $jual_detail->jual->nomor_faktur }}</strong>
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Brand</td>
            <td>{{ $jual_detail->barang->brand->nama }}</td>
          </tr>
          <tr>
            <td>Nama barang</td>
            <td>
              <a href="{{ route('gudang.barang.show', $jual_detail->barang->id) }}" target="_blank"
                rel="noopener noreferrer" class="text-blue-600 hover:underline">
                {{ $jual_detail->barang->nama }}
              </a>
            </td>
          </tr>
          <tr>
            <td>Batch</td>
            <td>: {{ $jual_detail->batch ?? '-' }}</td>
          </tr>
          <tr>
            <td>Expired</td>
            <td>:
              {{ $jual_detail->tgl_expired ? \Carbon\Carbon::parse($jual_detail->tgl_expired)->format('d/m/Y') : '-' }}
            </td>
          </tr>
          <tr>
            <td>Jumlah barang</td>
            <td>
              @if ($jual_detail->jumlah_barang_dipesan > 0)
                {{ $jual_detail->jumlah_barang_dipesan }} {{ $jual_detail->barang->satuan }}
              @else
                -
              @endif
            </td>
          </tr>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Form barang keluar</p>
      </div>

      <div class="card-body">

        {{-- TAMBAHAN: INFORMASI BATCH LOCKING --}}
        <div class="alert flex-column alert-warning d-flex align-items-start" role="alert">
          <small>
            <i class="bi bi-exclamation-diamond-fill me-2"></i><strong>PENTING:</strong><br>
            Jika barang yang keluar memiliki <strong>Batch atau Expired yang berbeda-beda</strong>,
            Silahkan klik tombol <strong>Split</strong> untuk memecah penjualan.
          </small>

          <form
            action="{{ route('gudang.jual.split-stock', ['id' => $jual_detail->jual->id, 'jual_detail_id' => $jual_detail->id]) }}"
            method="post">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-warning btn-block fw-bold mt-1"><i
                class="bi bi-copy me-1"></i>Split</button>
          </form>
        </div>
        {{-- END TAMBAHAN --}}

        <form
          action="{{ route('gudang.jual.stock-item-update', ['id' => $jual_detail->jual->id, 'jual_detail_id' => $jual_detail->id]) }}"
          method="post" autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3">
            {{-- Perbaikan: ubah jumlah_barang_masuk menjadi jumlah_barang_keluar agar disable berfungsi --}}
            <select id="batch" name="batch" class="form-select"
              {{ $jual_detail->jumlah_barang_keluar > 0 ? 'disabled' : '' }}>
              <option value="">Pilih atau cari batch</option>
              @foreach ($daftarBatch as $batch)
                <option value="{{ $batch->batch }}"
                  data-expired="{{ $batch->tgl_expired ? \Carbon\Carbon::parse($batch->tgl_expired)->format('Y-m-d') : '' }}"
                  {{ $jual_detail->batch == $batch->batch ? 'selected' : '' }}>
                  {{ $batch->batch }}
                </option>
              @endforeach
            </select>
            @if ($jual_detail->jumlah_barang_keluar > 0)
              <input type="hidden" name="batch" value="{{ $jual_detail->batch }}">
              <small class="text-muted">Batch terkunci karena barang sudah keluar sebagian.</small>
            @endif
          </div>

          <div class="mb-3">
            <x-form.input id="tgl_expired" name="tgl_expired" type="date" :value="$jual_detail->tgl_expired
                ? \Carbon\Carbon::parse($jual_detail->tgl_expired)->format('Y-m-d')
                : null" :readonly="$jual_detail->jumlah_barang_keluar > 0" />
          </div>

          <div class="mb-3">
            <x-form.label value="Jumlah barang keluar" />
            <x-form.input name="jumlah_barang_keluar" type="number" :value="$jual_detail->jumlah_barang_keluar" />
          </div>

          <div class="alert alert-info small">
            INFO: Pastikan jumlah barang keluar sudah sesuai
          </div>

          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const expiredInput = document.getElementById('tgl_expired');
      const tom = new TomSelect('#batch', {});

      tom.on('change', function(value) {
        const option = document.querySelector(`#batch option[value="${value}"]`);
        if (option) {
          const expiredDate = option.getAttribute('data-expired');
          expiredInput.value = expiredDate || '';
        } else {
          expiredInput.value = '';
        }
      });

      const selectedOption = document.querySelector('#batch option:checked');
      if (selectedOption && selectedOption.dataset.expired) {
        expiredInput.value = selectedOption.dataset.expired;
      }
    });
  </script>
@endpush
