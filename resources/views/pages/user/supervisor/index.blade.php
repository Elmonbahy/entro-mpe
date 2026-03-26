@extends('layouts.main-layout')

@section('title', 'Monitor Aktivitas User')

@section('content')
  <div class="container-fluid">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h5 class="mb-0">Daftar Pengguna Aktif</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>User</th>
                <th>Role / Cabang</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($users as $user)
                <tr>
                  <td>
                    <div class="fw-bold">{{ $user->name }}</div>
                    <small class="text-muted">{{ $user->username }}</small>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">{{ $user->role->slug ?? 'N/A' }}</span>
                  </td>
                  <td>
                    {{-- Cek apakah last_interaction melampaui threshold --}}
                    @if ($user->last_interaction >= $threshold)
                      <span class="badge bg-success">
                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Online
                      </span>
                    @else
                      <span class="badge bg-secondary">Offline</span>
                      @if ($user->last_interaction)
                        <div class="text-muted mt-1" style="font-size: 11px;">
                          Terakhir: {{ \Carbon\Carbon::createFromTimestamp($user->last_interaction)->diffForHumans() }}
                        </div>
                      @endif
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
