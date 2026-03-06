@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah Data" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Ubah Faktur Jual</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.jual.update', ['id' => $jual->id]) }}" method="post" autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3 col-md">
            <x-form.label value="Pelanggan" />
            <x-form.select name="pelanggan" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" :selected="old('pelanggan') ?? $jual->pelanggan_id"
              valueKey="id" labelKey="nama" :disabled="$jual->status_kirim !== \App\Enums\StatusKirim::PENDING" />
            @if ($jual->status_kirim !== \App\Enums\StatusKirim::PENDING)
              <input type="hidden" name="pelanggan" value="{{ $jual->pelanggan_id }}">
              <small class="text-muted">Pelanggan tidak dapat diubah karena status kirim sudah
                <strong>{{ $jual->status_kirim }}</strong>.</small>
            @endif
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tipe penjualan" />
              <x-form.select name="tipe_penjualan" placeholder="Cari atau pilih tipe penjualan" :options="$tipe_penjualans"
                :selected="old('tipe_penjualan', $jual->tipe_penjualan)" valueKey="id" labelKey="nama" />
            </div>

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

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Kredit (hari)" optional />
              <x-form.input name="kredit" type="number" placeholder="Input kredit..." :value="$jual->kredit" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Ongkir" optional />
              <x-form.input name="ongkir" type="number" placeholder="Input ongkir..." :value="$jual->ongkir ?? 0"
                :readonly="$jual->status_bayar === \App\Enums\StatusBayar::PAID" />
            </div>


            <div class="col-md mb-3">
              <x-form.label value="Diskon faktur" optional />
              <x-form.input name="diskon_faktur" type="text" :value="$jual->diskon_faktur" placeholder="Input diskon ..."
                oninput="this.value = this.value.replace(',', '.')" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.radio-group name="ppn" label="PPN" :options="[
                '0' => 'Tanpa PPN',
                '11' => '11%',
                '12' => '12%',
            ]" :value="old('ppn', $jual->ppn)" />
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
