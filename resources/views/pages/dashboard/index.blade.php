@extends('layouts.main-layout')

@section('title')
  Dashboard
@endsection

@section('content')
  <div class="container-fluid px-4">
    <div class="row">
      <div class="col">
        <div class="card callout callout-primary">
          <p class="mb-0 h5">Welcome to ENTRO-MPE Dashboard
          </p>
        </div>
      </div>
    </div>
    <div class="row mb-3 g-3">
      <div class="col-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="card border">
          <div class="card-body">
            <div class="fs-4 fw-semibold">{{ $counts['total_beli'] }}</div>
            <div class="text-success small text-uppercase fw-semibold text-truncate">Faktur Beli</div>
          </div>
          <div class="border-top">
            <a href="#" class="btn py-2 w-100">
              <span class="small">Lihat Semua</span>
              <i class="bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="card border">
          <div class="card-body">
            <div class="fs-4 fw-semibold">{{ $counts['total_jual'] }}</div>
            <div class="text-success small text-uppercase fw-semibold text-truncate">Faktur Jual</div>
          </div>
          <div class="border-top">
            <a href="#" class="btn py-2 w-100">
              <span class="small">Lihat Semua</span>
              <i class="bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="card border">
          <div class="card-body">
            <div class="fs-4 fw-semibold">{{ $counts['total_pelanggan'] }}</div>
            <div class="text-info small text-uppercase fw-semibold text-truncate">Pelanggan</div>
          </div>
          <div class="border-top">
            <a href="#" class="btn py-2 w-100">
              <span class="small">Lihat Semua</span>
              <i class="bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="card border">
          <div class="card-body">
            <div class="fs-4 fw-semibold">{{ $counts['total_supplier'] }}</div>
            <div class="text-info small text-uppercase fw-semibold text-truncate">Supplier</div>
          </div>
          <div class="border-top">
            <a href="#" class="btn py-2 w-100">
              <span class="small">Lihat Semua</span>
              <i class="bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="card border">
          <div class="card-body">
            <div class="fs-4 fw-semibold">{{ $counts['total_barang'] }}</div>
            <div class="text-warning small text-uppercase fw-semibold text-truncate">Barang</div>
          </div>
          <div class="border-top">
            <a href="#" class="btn py-2 w-100">
              <span class="small">Lihat Semua</span>
              <i class="bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="card border">
          <div class="card-body">
            <div class="fs-4 fw-semibold">{{ $counts['total_rusak'] }}</div>
            <div class="text-danger small text-uppercase fw-semibold text-truncate">Barang Rusak</div>
          </div>
          <div class="border-top">
            <a href="#" class="btn py-2 w-100">
              <span class="small">Lihat Semua</span>
              <i class="bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- /.col-->
    </div>

  </div>
@endsection
