@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah Data" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Ubah Sampel Keluar</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.sample-out.update', ['id' => $sample_out->id]) }}" method="post"
          autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3 col-md">
            <x-form.label value="Pelanggan" />
            <x-form.select name="pelanggan" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" :selected="old('pelanggan') ?? $sample_out->pelanggan_id"
              valueKey="id" labelKey="nama" />
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Salesman" />
              <x-form.select name="salesman" placeholder="Cari atau pilih salesman" :options="$salesmans" :selected="old('salesman', $sample_out->salesman_id)"
                valueKey="id" labelKey="nama" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal" />
              <x-form.input name="tanggal" type="date" :value="\Carbon\Carbon::parse($sample_out->tanggal)->format('Y-m-d')" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan" placeholder="Input keterangan..." :value="$sample_out->keterangan" />
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
