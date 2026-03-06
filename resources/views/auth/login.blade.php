@extends('layouts.auth-layout')

@section('content')
  <div class="text-center mb-4">
    <img src="{{ asset('logo.png') }}" alt="apm" style="width: 140px;">
  </div>

  <div class="card p-2">
    <div class="card-body">
      <h1 class="mb-3">Login</h1>

      <x-alert.session-alert />

      <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf
        <div class="mb-3">
          <x-form.label value="Email atau Username" />
          <x-form.input placeholder="Input email atau username..." name="login" required />
        </div>

        <div class="mb-3">
          <x-form.label value="Password" />
          <x-form.input placeholder="Password" name="password" type="password" required />
        </div>

        <button class="btn btn-primary btn-lg px-4 me-0" type="submit">Login</button>
      </form>
    </div>
  </div>
@endsection
