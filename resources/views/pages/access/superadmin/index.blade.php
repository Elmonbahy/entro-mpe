@extends('layouts.main-layout')

@section('title')
  Data access
@endsection

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header">
            <strong>Manajemen Akses Role</strong>
            <span class="small ms-1 text-muted">Kontrol siapa yang bisa login ke sistem</span>
          </div>
          <div class="card-body">
            {{-- Alert Success --}}
            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button class="btn-close" type="button" data-coreui-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            {{-- Alert Error --}}
            @if (session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button class="btn-close" type="button" data-coreui-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            <div class="table-responsive">
              <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th width="50">No</th>
                    <th>Nama Role</th>
                    <th>Slug</th>
                    <th class="text-center">Status Akses</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($roles as $index => $role)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td><strong>{{ $role->name }}</strong></td>
                      <td><code>{{ $role->slug }}</code></td>
                      <td class="text-center">
                        @if ($role->is_active)
                          <span class="badge bg-success-light text-success border border-success">
                            Dapat Diakses
                          </span>
                        @else
                          <span class="badge bg-danger-light text-danger border border-danger">
                            Terkunci
                          </span>
                        @endif
                      </td>
                      <td class="text-center">
                        @if ($role->slug === 'sa')
                          <button class="btn btn-sm btn-secondary disabled" title="Superadmin tidak bisa dinonaktifkan">
                            Always Active
                          </button>
                        @else
                          <form action="{{ route('superadmin.manage-access.toggle', $role->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                              class="btn btn-sm {{ $role->is_active ? 'btn-danger' : 'btn-success' }} text-white shadow-sm"
                              onclick="return confirm('Apakah Anda yakin ingin mengubah akses role ini?')">
                              @if ($role->is_active)
                                <i class="cil-lock-locked"></i> Kunci Akses
                              @else
                                <i class="cil-lock-unlocked"></i> Buka Akses
                              @endif
                            </button>
                          </form>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer text-muted small">
            <i class="cil-info"></i> <strong>Catatan:</strong> Jika akses dikunci, user dengan role tersebut akan otomatis
            logout (Kick) dan tidak bisa masuk kembali sampai akses dibuka.
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
