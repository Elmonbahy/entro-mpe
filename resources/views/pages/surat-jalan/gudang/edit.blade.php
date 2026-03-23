@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah Data" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Ubah Surat Jalan</p>
      </div>

      <div class="p-3">
        <form action="{{ route('gudang.surat-jalan.update', ['id' => $surat_jalan->id]) }}" method="post"
          autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3 col-md">
            <x-form.label value="Pelanggan" />
            <x-form.input name="pelanggan" :value="$surat_jalan->pelanggan->nama" :disabled="true" />
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Kendaraan" />
              <x-form.select name="kendaraan" placeholder="Cari atau pilih kendaraan" :options="$kendaraans" :selected="old('kendaraan', $surat_jalan->kendaraan_id)"
                valueKey="id" labelKey="nama" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Jumlah koli" />
              <x-form.input name="koli" type="text" placeholder="Input koli..." :value="$surat_jalan->koli" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal" />
              <x-form.input name="tgl_surat_jalan" type="date" :value="\Carbon\Carbon::parse($surat_jalan->tgl_surat_jalan)->format('Y-m-d')" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Tanggal terima kembali" />
              <x-form.input name="tgl_kembali_surat_jalan" type="date" :value="$surat_jalan->tgl_kembali_surat_jalan
                  ? \Carbon\Carbon::parse($surat_jalan->tgl_kembali_surat_jalan)->format('Y-m-d')
                  : ''" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Penanggung jawab" />
              <x-form.select name="staf_logistik" placeholder="Cari atau pilih penanggung jawab" :options="$staf_logistiks"
                :selected="old('staf_logistik', $surat_jalan->staf_logistik)" valueKey="id" labelKey="nama" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Keterangan" optional />
              <x-form.input name="keterangan" placeholder="Input keterangan..." :value="$surat_jalan->keterangan" />
            </div>
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
      const selectElements = document.querySelectorAll('.form-select');
      selectElements.forEach(element => {
        new TomSelect(`#${element.id}`);
      });
    });
  </script>
@endpush
