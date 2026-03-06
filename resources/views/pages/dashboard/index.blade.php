@extends('layouts.main-layout')

@section('title')
  Dashboard
@endsection

@push('styles')
  <style>
    .pulse {
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.2);
      }

      100% {
        transform: scale(1);
      }
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid px-4">
    <div class="row">
      <div class="col">
        <div class="card callout callout-primary">
          <p class="mb-0 h5">Welcome to APM Dashboard
          </p>
        </div>
      </div>
    </div>
    <div class="row mb-3 g-3">
      <div class="col-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="card border">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fs-4 fw-semibold">{{ $counts['total_beli'] }}</div>
              <div class="fs-4 fw-semibold text-success d-flex align-items-center pulse">
                <i class="bi bi-plus me-1"></i> {{ $counts['today_beli'] }}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <div class="text-success small text-uppercase fw-semibold text-truncate">Faktur Beli</div>
            </div>
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
            <div class="d-flex justify-content-between align-items-center">
              <div class="fs-4 fw-semibold">{{ $counts['total_jual'] }}</div>
              <div class="fs-4 fw-semibold text-success d-flex align-items-center pulse">
                <i class="bi bi-plus me-1"></i> {{ $counts['today_jual'] }}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <div class="text-success small text-uppercase fw-semibold text-truncate">Faktur Jual</div>
            </div>
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
            <div class="d-flex justify-content-between align-items-center">
              <div class="fs-4 fw-semibold">{{ $counts['total_pelanggan'] }}</div>
              <div class="fs-4 fw-semibold text-info d-flex align-items-center pulse">
                <i class="bi bi-plus me-1"></i> {{ $counts['today_pelanggan'] }}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <div class="text-info small text-uppercase fw-semibold text-truncate">Pelanggan</div>
            </div>
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
            <div class="d-flex justify-content-between align-items-center">
              <div class="fs-4 fw-semibold">{{ $counts['total_supplier'] }}</div>
              <div class="fs-4 fw-semibold text-info d-flex align-items-center pulse">
                <i class="bi bi-plus me-1"></i> {{ $counts['today_supplier'] }}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <div class="text-info small text-uppercase fw-semibold text-truncate">Supplier</div>
            </div>
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
            <div class="d-flex justify-content-between align-items-center">
              <div class="fs-4 fw-semibold">{{ $counts['total_barang'] }}</div>
              <div class="fs-4 fw-semibold text-warning d-flex align-items-center pulse">
                <i class="bi bi-plus me-1"></i> {{ $counts['today_barang'] }}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <div class="text-warning small text-uppercase fw-semibold text-truncate">Barang</div>
            </div>
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
            <div class="d-flex justify-content-between align-items-center">
              <div class="fs-4 fw-semibold">{{ $counts['total_rusak'] }}</div>
              <div class="fs-4 fw-semibold text-danger d-flex align-items-center pulse">
                <i class="bi bi-plus me-1"></i> {{ $counts['today_rusak'] }}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <div class="text-danger small text-uppercase fw-semibold text-truncate">Barang Rusak</div>
            </div>
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

    @roles(['af', 'ak'])
    <div class="row g-3">
      <div class="col-md-6 ">
        @livewire('dashboard.weekly-purchase-chart')
      </div>
      <div class="col-md-6">
        @livewire('dashboard.weekly-sales-chart')
      </div>
    </div>
    @endroles

    @roles(['af', 'ak'])
    <div class="row g-3">
      <div class="col-md-6">
        @livewire('dashboard.monthly-purchase-table')
      </div>
      <div class="col-md-6">
        @livewire('dashboard.monthly-sales-table')
      </div>
    </div>
    @endroles

    @roles(['af', 'ag'])
    <div class="row ">
      <div class="col-md-6">
        @livewire('dashboard.monthly-receive-table')
      </div>
      <div class="col-md-6">
        @livewire('dashboard.monthly-ship-table')
      </div>
    </div>
    @endroles


  </div>
@endsection
