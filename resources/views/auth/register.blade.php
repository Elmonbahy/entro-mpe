@extends('layouts.auth-layout')

@section('content')
  <div class="card p-2">
    <div class="card-body">
      <h1 class="mb-3"><strong>APM</strong> Register</h1>

      <x-alert.session-alert />

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
          <x-form.label value="Nama" />
          <x-form.input placeholder="Input name..." name="name" required />
        </div>

        <div class="mb-3">
          <x-form.label value="Username" />
          <x-form.input placeholder="Input username..." name="username" required />
        </div>

        <div class="mb-3">
          <x-form.label value="Email" />
          <x-form.input placeholder="Input email..." name="email" required />
        </div>

        <div class="mb-3">
          <x-form.label value="Password" />
          <x-form.input placeholder="Password" name="password" type="password" required />
        </div>

        <div class="mb-3">
          <x-form.label value="Password Confirmation" />
          <x-form.input placeholder="Password" name="password_confirmation" type="password" required />
        </div>

        <div class="mb-3">
          <label for="role" class="form-label">Role</label>
          <x-form.select name="role" :options="$roles" :selected="old('role')" valueKey="id" labelKey="name"
            placeholder="Cari atau pilih role" />
        </div>

        <button class="btn btn-primary btn-lg px-4 me-0" type="submit">Login</button>
      </form>
    </div>
  </div>
@endsection
