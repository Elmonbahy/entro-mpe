@extends('layouts.main-layout')

@section('content')
  <div class="container px-4">
    <x-alert.session-alert />
    <x-page-header title="Samppel Barang keluar" class="mb-3" withBackButton />

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Data Sampel Barang Keluar</p>
      </div>

      <div class="card-body p-2">
        <table class="table mb-0">
          <tr>
            <td width="250">Nomor Sampel</td>
            <td>
              @if ($sample_out_detail->sampleout->nomor_sample > 0)
                <strong>
                  {{ $sample_out_detail->sampleout->nomor_sample }}
                </strong>
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Brand</td>
            <td>
              {{ $sample_out_detail->sampleBarang->barang->brand->nama }}
            </td>
          </tr>
          <tr>
            <td>Nama barang</td>
            <td>
              {{ $sample_out_detail->sampleBarang->barang->nama }}
            </td>
          </tr>
          <tr>
            <td>
              Batch
            </td>
            <td>
              : {{ $sample_out_detail->batch ?? '-' }}
            </td>
          </tr>
          <tr>
            <td>
              Expired
            </td>
            <td>
              :
              {{ $sample_out_detail->tgl_expired ? \Carbon\Carbon::parse($sample_out_detail->tgl_expired)->format('d/m/Y') : '-' }}
            </td>
          </tr>
          <tr>
            <td>Jumlah barang</td>
            <td>
              @if ($sample_out_detail->jumlah_barang_dipesan > 0)
                {{ $sample_out_detail->jumlah_barang_dipesan }} {{ $sample_out_detail->sampleBarang->satuan }}
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
        <p class="mb-0 fw-semibold text-capitalize">Form sampel barang keluar</p>
      </div>

      <div class="card-body">
        <form
          action="{{ route('gudang.sample-out.stock-item-update', ['id' => $sample_out_detail->sampleout->id, 'sample_out_detail_id' => $sample_out_detail->id]) }}"
          method="post" autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3">
            <select id="batch" name="batch" class="form-select"
              {{ $sample_out_detail->jumlah_barang_masuk > 0 ? 'disabled' : '' }}>
              <option value="">Pilih atau cari batch</option>
              @foreach ($daftarBatch as $batch)
                <option value="{{ $batch->batch }}"
                  data-expired="{{ $batch->tgl_expired ? \Carbon\Carbon::parse($batch->tgl_expired)->format('Y-m-d') : '' }}"
                  {{ $sample_out_detail->batch == $batch->batch ? 'selected' : '' }}>
                  {{ $batch->batch }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <x-form.input id="tgl_expired" name="tgl_expired" type="date" :value="$sample_out_detail->tgl_expired
                ? \Carbon\Carbon::parse($sample_out_detail->tgl_expired)->format('Y-m-d')
                : null" />
          </div>

          <div class="mb-3">
            <x-form.label value="Jumlah barang keluar" />
            <x-form.input name="jumlah_barang_keluar" type="number" :value="$sample_out_detail->jumlah_barang_keluar" />
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

      // Inisialisasi TomSelect tanpa onChange di opsi
      const tom = new TomSelect('#batch', {});

      // Pasang event listener change dengan TomSelect API
      tom.on('change', function(value) {
        const option = document.querySelector(`#batch option[value="${value}"]`);
        if (option) {
          const expiredDate = option.getAttribute('data-expired');
          expiredInput.value = expiredDate || '';
        } else {
          expiredInput.value = '';
        }
      });


      // Set tanggal expired saat halaman load berdasarkan batch yang sudah terpilih
      const selectedOption = document.querySelector('#batch option:checked');
      if (selectedOption && selectedOption.dataset.expired) {
        expiredInput.value = selectedOption.dataset.expired;
      }

    });
  </script>
@endpush
