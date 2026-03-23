@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Surat Jalan" class="mb-3" />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Buat Surat Jalan</p>
      </div>

      <div class="p-3">
        <form action="{{ route('gudang.surat-jalan.store') }}" method="post" autocomplete="off">
          @csrf


          <div class="col-md mb-3">
            <x-form.label value="Pelanggan" />
            <x-form.select name="pelanggan" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" :selected="old('pelanggan')"
              valueKey="id" labelKey="nama" />
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Alamat Pengiriman" />
              <x-form.input name="alamat_kirim" id="alamat_kirim"
                placeholder="Alamat akan terisi otomatis, tapi bisa diubah..." />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Kota" />
              <x-form.input name="kota" id="kota" placeholder="Kota akan terisi otomatis, tapi bisa diubah..." />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Contact Phone" />
              <x-form.input name="contact_phone" id="contact_phone"
                placeholder="Contact Phone akan terisi otomatis, tapi bisa diubah..." />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Contact Person" />
              <x-form.input name="contact_person" id="contact_person"
                placeholder="Contact Person akan terisi otomatis, tapi bisa diubah..." />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Kendaraan" />
              <x-form.select name="kendaraan" placeholder="Cari atau pilih kendaraan" :options="$kendaraans" :selected="old('kendaraan')"
                valueKey="id" labelKey="nama" />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Jumlah koli" />
              <x-form.input name="koli" type="text" placeholder="Input jumlah koli..." />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal" />
              <x-form.input name="tgl_surat_jalan" type="date" />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Penanggung jawab" />
              <x-form.select name="staf_logistik" placeholder="Cari atau pilih penanggung jawab" :options="$staf_logistiks"
                :selected="old('staf_logistik')" valueKey="id" labelKey="nama" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan" placeholder="Input keterangan..." />
          </div>

          <button type="submit" class="btn btn-primary">
            Buat surat jalan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Simpan data pelanggan ke variable JS
      const dataPelanggan = @json($pelanggans);

      // Inisialisasi TomSelect untuk Pelanggan
      const selectPelanggan = new TomSelect('#pelanggan', {
        onChange: function(value) {
          const pelanggan = dataPelanggan.find(item => item.id == value);
          if (pelanggan) {
            document.getElementById('alamat_kirim').value = pelanggan.alamat || '';
            document.getElementById('kota').value = pelanggan.kota || '';
            document.getElementById('contact_person').value = pelanggan.contact_person || '';
            document.getElementById('contact_phone').value = pelanggan.contact_phone || '';
          }
        }
      });

      // Inisialisasi TomSelect lainnya (Kendaraan)
      new TomSelect('#kendaraan');
    });
  </script>
@endpush
