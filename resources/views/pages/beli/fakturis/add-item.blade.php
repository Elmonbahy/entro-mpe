@extends('layouts.main-layout')

@section('title')
  Detail faktur beli
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Pembelian" class="mb-3" withBackButton />

    <x-card.faktur-beli-detail :beli="$beli" />

    <div class="row my-3">
      <div class="col-md-12">
        <div class="accordion" id="accordion-beli">
          <div class="accordion-item">
            <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button"
              data-coreui-toggle="collapse" data-coreui-target="#collapse-tambah-beli" aria-expanded="true"
              aria-controls="collapse-tambah-beli">
              <p class="mb-0 fw-semibold">Form Tambah Pembelian</p>
            </button>

            <div id="collapse-tambah-beli" class="accordion-collapse collapse show" data-coreui-parent="#accordion-beli">
              <div class="accordion-body p-3">
                @livewire('beli.tambah-pembelian-barang', ['beli_id' => $beli->id])
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @livewire('beli.daftar-pembelian-barang-fakturis', ['beli_id' => $beli->id])
  </div>
@endsection
