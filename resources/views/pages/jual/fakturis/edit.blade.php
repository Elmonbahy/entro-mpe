@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah Data" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Ubah Faktur Jual</p>
      </div>

      <div class="p-3">
        <form action="{{ route('fakturis.jual.update', ['id' => $jual->id]) }}" method="post" autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3 col-md">
            <x-form.label value="Pelanggan" />
            <x-form.select name="pelanggan" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" :selected="old('pelanggan') ?? $jual->pelanggan_id"
              valueKey="id" labelKey="nama" />
            <input type="hidden" name="pelanggan" value="{{ $jual->pelanggan_id }}">
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Salesman" />
              <x-form.select name="salesman" placeholder="Cari atau pilih salesman" :options="$salesmans" :selected="old('salesman', $jual->salesman_id)"
                valueKey="id" labelKey="nama" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal faktur" />
              <x-form.input name="tgl_faktur" type="date" :value="\Carbon\Carbon::parse($jual->tgl_faktur)->format('Y-m-d')" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Nomor pemesanan" optional />
              <x-form.input name="nomor_pemesanan" placeholder="Input nomor pemesanan..." :value="$jual->nomor_pemesanan" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan_faktur" placeholder="Input keterangan..." :value="$jual->keterangan_faktur" />
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
