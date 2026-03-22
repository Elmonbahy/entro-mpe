@extends('layouts.main-layout')

@section('title')
  Dashboard
@endsection

@section('content')
  <div class="container-fluid px-4">
    <div class="row">
      <div class="col">
        <div class="card callout callout-primary">
          <p class="mb-0 h5">Work Progress
          </p>
        </div>
      </div>
    </div>

    @roles(['as'])
    <div class="row g-3">
      <div class="col-md-6">
        @livewire('work-progress.monthly-purchase-table')
      </div>
      <div class="col-md-6">
        @livewire('work-progress.monthly-sales-table')
      </div>
    </div>
    @endroles

  </div>
@endsection
