@extends('layouts.main-layout')


@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Penjualan" class="mb-3" withBackButton />

    <x-card.faktur-jual-detail :jual="$jual" />

    <div class="row my-3">
      <div class="col-md-12">
        <div class="accordion" id="accordion2">
          <div class="accordion-item">
            <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button"
              data-coreui-toggle="collapse" data-coreui-target="#collapse2" aria-expanded="true"
              aria-controls="collapse2">
              <p class="mb-0 fw-semibold">Form Tambah Penjualan</p>
            </button>

            <div id="collapse2" class="accordion-collapse collapse show" data-coreui-parent="#accordion2">
              <div class="accordion-body p-3">
                @livewire('jual.tambah-penjualan-barang', ['jual_id' => $jual->id])
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @livewire('jual.daftar-penjualan-barang-fakturis', ['jual_id' => $jual->id])
  </div>
@endsection
