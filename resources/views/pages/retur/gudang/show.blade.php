@extends('layouts.main-layout')

@section('title', 'Detail Retur Barang')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />

    <div class="d-flex justify-content-between align-items-center mb-3">
      <x-page-header title="Detail Pengajuan Retur" withBackButton />
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card mb-3">
          <div class="card-header p-3 bg-light fw-bold">
            Informasi Barang & Batch
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="text-muted small text-uppercase">Nama Barang</label>
                <div class="fs-5 fw-bold">{{ $retur->barang->nama }}</div>
                <div class="small">{{ $retur->barang->kode }}</div>
              </div>
              <div class="col-md-3 mb-3">
                <label class="text-muted small text-uppercase">Batch</label>
                <div class="fs-5">{{ $retur->returnable->batch ?? '-' }}</div>
              </div>
              <div class="col-md-3 mb-3">
                <label class="text-muted small text-uppercase">Expired Date</label>
                <div
                  class="fs-5 {{ \Carbon\Carbon::parse($retur->returnable->tgl_expired)->isPast() ? 'text-danger' : '' }}">
                  {{ $retur->returnable->tgl_expired ? \Carbon\Carbon::parse($retur->returnable->tgl_expired)->format('d/m/Y') : '-' }}
                </div>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="text-muted small text-uppercase">Asal Transaksi</label>
                @if ($retur->returnable_type === 'App\Models\JualDetail')
                  <div class="d-flex align-items-center mt-1">
                    <span class="badge bg-primary me-2">Penjualan</span>
                    <a href="{{ route('gudang.jual.show', $retur->returnable->jual_id) }}" target="_blank"
                      class="text-decoration-none">
                      {{ $retur->returnable->jual->nomor_faktur }} <i class="bi bi-box-arrow-up-right small"></i>
                    </a>
                  </div>
                  <small class="text-muted">Pelanggan: {{ $retur->returnable->jual->pelanggan->nama ?? '-' }}</small>
                @else
                  <div class="d-flex align-items-center mt-1">
                    <span class="badge bg-danger me-2">Pembelian</span>
                    <a href="{{ route('gudang.beli.show', $retur->returnable->beli_id) }}" target="_blank"
                      class="text-decoration-none">
                      {{ $retur->returnable->beli->nomor_faktur }} <i class="bi bi-box-arrow-up-right small"></i>
                    </a>
                  </div>
                  <small class="text-muted">Supplier: {{ $retur->returnable->beli->supplier->nama ?? '-' }}</small>
                @endif
              </div>
              <div class="col-md-3 mb-3">
                <label class="text-muted small text-uppercase">Jumlah Transaksi Awal</label>
                <div class="fw-bold">
                  @if ($retur->returnable_type === 'App\Models\JualDetail')
                    {{ $retur->returnable->jumlah_barang_keluar }} {{ $retur->barang->satuan }} (Keluar)
                  @else
                    {{ $retur->returnable->jumlah_barang_masuk }} {{ $retur->barang->satuan }} (Masuk)
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header p-3 bg-light fw-bold">
            Detail Pengajuan Retur
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="text-muted small text-uppercase">Jumlah Retur</label>
                <div class="display-6 fw-bold text-danger">
                  {{ $retur->jumlah_barang_retur }} <span class="fs-4">{{ $retur->barang->satuan }}</span>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="text-muted small text-uppercase">Jenis Retur</label>
                <div class="mt-2">
                  @if ($retur->is_diganti)
                    <div class="p-2 bg-warning-subtle text-warning-emphasis border border-warning rounded">
                      <i class="bi bi-arrow-repeat"></i> <strong>Ganti Barang</strong> <br>
                      <small>Stok akan disesuaikan, namun tagihan/hutang tetap sama.</small>
                    </div>
                  @else
                    <div class="p-2 bg-secondary-subtle text-secondary-emphasis border border-secondary rounded">
                      <i class="bi bi-scissors"></i> <strong>Potong Nota</strong> <br>
                      <small>Stok disesuaikan, tagihan/hutang akan dikurangi.</small>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="text-muted small text-uppercase">Alasan / Keterangan (Dari Fakturis)</label>
              <div class="p-3 bg-light rounded border">
                "{{ $retur->keterangan }}"
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card mb-3">
          <div class="card-body text-center">
            <h6 class="text-muted text-uppercase mb-3">Status Saat Ini</h6>

            @if ($retur->status === \App\Enums\StatusRetur::PENDING)
              <i class="bi bi-hourglass-split text-info display-1"></i>
              <h3 class="mt-2 text-info">Menunggu Verifikasi</h3>
              <p class="small text-muted">Diajukan: {{ $retur->created_at->format('d/m/Y') }}</p>

              <hr>

              <div class="d-grid gap-2">
                <form action="{{ route('gudang.retur.verify', $retur->id) }}" method="POST"
                  onsubmit="return confirm('Apakah Anda yakin menyetujui retur ini? Stok akan langsung berubah.');">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="action" value="approve">
                  <button class="btn btn-success btn-lg w-100 text-white">
                    TERIMA / SETUJUI <i class="bi bi-check-circle-fill"></i>
                  </button>
                </form>

                <button class="btn btn-danger text-white btn-lg w-100" data-coreui-toggle="modal"
                  data-coreui-target="#modalReject">
                  TOLAK <i class="bi bi-x-circle-fill"></i>
                </button>
              </div>
            @elseif($retur->status === \App\Enums\StatusRetur::APPROVED)
              <i class="bi bi-check-circle-fill text-success display-1"></i>
              <h3 class="mt-2 text-success">Disetujui</h3>
              <p class="small text-muted">Diverifikasi: {{ \Carbon\Carbon::parse($retur->verified_at)->format('d/m/Y') }}
              </p>
            @elseif($retur->status === \App\Enums\StatusRetur::REJECTED)
              <i class="bi bi-x-circle-fill text-danger display-1"></i>
              <h3 class="mt-2 text-danger">Ditolak</h3>
              <p class="small text-muted">Diverifikasi: {{ \Carbon\Carbon::parse($retur->verified_at)->format('d/m/Y') }}
              </p>
              <div class="alert alert-danger text-start">
                <strong>Alasan Penolakan:</strong><br>
                {{ $retur->keterangan_gudang }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  @if ($retur->status === \App\Enums\StatusRetur::PENDING)
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('gudang.retur.verify', $retur->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="action" value="reject">

            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title">Tolak Pengajuan Retur</h5>
              <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                <textarea name="keterangan_gudang" class="form-control" rows="4" required
                  placeholder="Contoh: Barang fisik tidak ditemukan, Batch tidak sesuai, Kondisi barang bagus..."></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger text-white">Konfirmasi Tolak</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

@endsection
