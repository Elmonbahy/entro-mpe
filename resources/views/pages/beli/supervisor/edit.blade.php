@extends('layouts.main-layout')

@section('title')
  Ubah pembelian
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah data pembelian" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah faktur pembelian</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.beli.update', ['id' => $beli->id]) }}" method="post" autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3 col-md">
            <x-form.label value="Supplier" />
            <x-form.select name="supplier" placeholder="Cari atau pilih supplier" :options="$suppliers" :selected="old('supplier') ?? $beli->supplier_id"
              valueKey="id" labelKey="nama" />
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal faktur" />
              <x-form.input name="tgl_faktur" type="date" :value="optional(Carbon\Carbon::parse($beli->tgl_faktur))->format('Y-m-d')" />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Tanggal terima faktur" optional />
              <x-form.input name="tgl_terima_faktur" type="date" :value="$beli->tgl_terima_faktur
                  ? Carbon\Carbon::parse($beli->tgl_terima_faktur)->format('Y-m-d')
                  : ''" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Nomor faktur" />
              <x-form.input name="nomor_faktur" placeholder="Input nomor faktur..." :value="$beli->nomor_faktur" />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Nomor pemesanan" />
              <x-form.input name="nomor_pemesanan" placeholder="Input nomor pemesanan..." :value="$beli->nomor_pemesanan" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Kredit (hari)" optional />
              <x-form.input name="kredit" type="number" placeholder="Input kredit..." :value="$beli->kredit" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Ongkir" optional />
              <x-form.input name="ongkir" type="number" placeholder="Input ongkir..." :value="$beli->ongkir ?? 0"
                :readonly="$beli->status_bayar === \App\Enums\StatusBayar::PAID" />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Materai" optional />
              <x-form.input name="materai" type="number" placeholder="Input materai..." :value="$beli->materai ?? 0"
                :readonly="$beli->status_bayar === \App\Enums\StatusBayar::PAID" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Diskon faktur" optional />
              <x-form.input name="diskon_faktur" type="text" value="{{ old('diskon_faktur', '0') }}"
                placeholder="Input diskon ..." oninput="this.value = this.value.replace(',', '.')" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Biaya Lainya" optional />
              <x-form.input name="biaya_lainnya" type="number" placeholder="Input biaya lainya..." :value="$beli->biaya_lainnya ?? 0"
                :readonly="$beli->status_bayar === \App\Enums\StatusBayar::PAID" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.radio-group name="ppn" label="PPN" :options="[
                '0' => 'Tanpa PPN',
                '11' => '11%',
                '12' => '12%',
            ]" :value="old('ppn') ?? $beli->ppn" />
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan_faktur" placeholder="Input keterangan..." :value="$beli->keterangan_faktur" />
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
