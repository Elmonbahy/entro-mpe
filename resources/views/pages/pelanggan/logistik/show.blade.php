@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Data pelanggan" class="mb-3" withBackButton />

    <x-card.pelanggan-detail :data="$pelanggan" />
  </div>
@endsection
