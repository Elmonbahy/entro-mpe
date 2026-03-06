@extends('layouts.main-layout')

@section('content')
  <div class="container-full px-4">
    <x-page-header title="Cetak" class="mb-3" />
    <x-alert.session-alert />

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form titip faktur</p>
      </div>

      <div class="card-body">
        <form action="#" method="get" autocomplete="off" onsubmit="updateFormAction(event)" id="cetakForm">
          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal awal" />
              <x-form.input name="tgl_awal" type="date" placeholder="Pilih tanggan awal..." :value="$tgl_awal ?? null" />
            </div>

            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal akhir" />
              <x-form.input name="tgl_akhir" type="date" placeholder="Pilih tanggan akhir..." :value="$tgl_akhir ?? null" />
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Pelanggan" />
              <x-form.select name="pelanggan" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" :selected="old('pelanggan', $pelanggan ?? null)"
                valueKey="id" labelKey="nama" />
            </div>

            <div class="col-md-4 mb-3">
              <x-form.label value="Status Cetak" optional />
              <select name="status_cetak" class="form-control">
                <option value="" {{ request('status_cetak') == '' ? 'selected' : '' }}>Semua</option>
                <option value="belum" {{ request('status_cetak') == 'belum' ? 'selected' : '' }}>Belum dicetak</option>
                <option value="sudah" {{ request('status_cetak') == 'sudah' ? 'selected' : '' }}>Sudah dicetak</option>
              </select>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            Lihat
          </button>

        </form>
      </div>
    </div>

    @isset($juals)
      <div class="card">
        <form action="{{ route('keuangan.titip-faktur.pdf') }}" method="GET" id="exportForm">
          <div class="card-header p-3 d-flex justify-content-between align-items-center">
            <p class="mb-0 fw-semibold">Data Titip faktur</p>

            @if ($juals->isNotEmpty())
              {{-- Filter tetap dikirim --}}
              <input type="hidden" name="tgl_awal" value="{{ $tgl_awal }}">
              <input type="hidden" name="tgl_akhir" value="{{ $tgl_akhir }}">
              <input type="hidden" name="pelanggan" value="{{ $pelanggan }}">
              <input type="hidden" name="status_cetak" value="{{ request('status_cetak') }}">

              <button type="submit" class="btn btn-primary mt-2">
                <i class="bi bi-file-pdf-fill"></i> Ekspor PDF
              </button>
            @endif
          </div>

          <div class="card-body">
            @if ($juals->isNotEmpty())
              {{-- Table akan ikut di sini karena form melingkupi tabel --}}
              @include('pages.titip-faktur.keuangan.table', ['juals' => $juals])
            @else
              <p class="text-center m-0">Tidak ada data tersedia.</p>
            @endif

          </div>
        </form>
      </div>
    @endisset
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const selectElements = document.querySelectorAll('.form-select');
      selectElements.forEach(element => {
        new TomSelect(`#${element.id}`);
      });
    });
  </script>

  <script>
    function updateFormAction(event) {
      event.preventDefault();

      // Get the selected pelanggan value
      const selectElement = document.querySelector('select[name="pelanggan"]');
      const selectedPelanggan = selectElement.value;
      if (!selectedPelanggan) {
        alert('Pelanggan wajib dipilih!')
        return;
      }

      // Update the form action URL
      const form = document.getElementById('cetakForm');
      const baseUrl = "{{ route('keuangan.titip-faktur.index') }}";
      form.action = `${baseUrl}/${selectedPelanggan}`;

      // Submit the form
      form.submit();
    }
  </script>
@endpush
