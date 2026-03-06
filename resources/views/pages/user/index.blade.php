@extends('layouts.main-layout')

@section('title')
  Data user
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data user" class="mb-3">
      <a href="{{ route('register') }}" class="btn btn-primary">
        Tambah data
      </a>
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data user</p>
      </div>

      <div class="p-3">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Nama</th>
                <th scope="col">Email</th>
                <th scope="col">Username</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($users as $user)
                <tr>
                  <th scope="row">{{ $user->id }}</th>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td>
                    @if (Auth::user()->id !== $user->id)
                      <span class="badge text-bg-secondary">
                        {{ $user->username }}
                      </span>
                    @else
                      <span class="badge text-bg-primary">
                        {{ $user->username }}
                      </span>
                    @endif
                  </td>

                  <td>
                    @if (Auth::user()->id !== $user->id)
                      <form action="{{ route('user.destroy', ['id' => $user->id]) }}" method="post"
                        id="remove{{ $user->id }}"
                        onsubmit="return confirm('Hapus user? Aksi ini tidak dapat dibatalkan!')">
                        @csrf
                        @method('DELETE')
                      </form>
                      <button class="btn btn-danger" form="remove{{ $user->id }}">
                        <i class="bi-trash text-white"></i>
                      </button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
