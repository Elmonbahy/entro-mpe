@extends('layouts.main-layout')

@section('title')
  Ubah group
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data group" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data group</p>
      </div>

      <div class="p-3">
        <form action="{{ route('superadmin.group.update', ['group' => $group->id]) }}" method="post" autocomplete="off">
          @csrf
          @method('PATCH')
          <div class="mb-3">
            <x-form.label value="Nama group" />
            <x-form.input name="nama" placeholder="Input nama group..." :value="$group->nama" />
          </div>
          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
