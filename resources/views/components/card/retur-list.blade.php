@props(['returs', 'jual'])

<div class="card mt-3 mb-3">
  <div class="card-header p-3 d-flex justify-content-between align-items-center">
    <p class="mb-0 fw-semibold">Riwayat Retur Barang</p>
  </div>

  <div class="card-body">
    @if ($returs->isEmpty())
      <p class="mb-0 text-center text-muted">Tidak ada data retur untuk faktur ini.</p>
    @else
      <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0 align-middle">
          <thead class="text-nowrap table-light">
            <tr>
              <th>Barang</th>
              <th>Batch / Exp</th>
              <th>Jumlah</th>
              <th>Jenis</th>
              <th>Status</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($returs as $item)
              <tr>
                <td>
                  @if (Auth::check() && Auth::user()->role->slug === 'ag')
                    <a href="{{ route('gudang.retur.show', $item->id) }}" class="fw-bold text-decoration-none d-block">
                      {{ $item->barang->nama }} <i class="bi bi-box-arrow-up-right small ms-1"></i>
                    </a>
                  @else
                    <div class="fw-bold d-block">{{ $item->barang->nama }}</div>
                  @endif
                  <small class="text-muted">BRAND: {{ $item->barang->brand->nama }}</small>
                </td>

                <td>
                  <div>{{ $item->returnable->batch ?: '-' }}</div>
                  <small class="text-muted">
                    {{ $item->returnable->tgl_expired ? \Carbon\Carbon::parse($item->returnable->tgl_expired)->format('d/m/Y') : '-' }}
                  </small>
                </td>

                <td class="fw-bold text-danger">
                  {{ $item->jumlah_barang_retur }} {{ $item->barang->satuan }}
                </td>

                <td>
                  @if ($item->is_diganti)
                    <span class="badge bg-warning text-dark">Ganti Barang</span>
                  @else
                    <span class="badge bg-secondary">Tidak Diganti</span>
                  @endif
                </td>

                <td>
                  <span class="badge bg-{{ $item->status->color() }}">
                    {{ $item->status->label() }}
                  </span>
                  @if ($item->status === \App\Enums\StatusRetur::APPROVED)
                    <br><small class="text-success"
                      style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($item->verified_at)->format('d/m/Y') }}</small>
                  @endif
                </td>

                <td>
                  <div class="mb-1">
                    <small class="text-muted fw-bold">Ket. Fakturis:</small><br>
                    {{ $item->keterangan ?? '-' }}
                  </div>

                  @if ($item->status === \App\Enums\StatusRetur::REJECTED)
                    <div class="p-1 bg-danger-subtle border border-danger rounded mt-1">
                      <small class="text-danger fw-bold">Ditolak Gudang:</small><br>
                      <small class="text-danger">{{ $item->keterangan_gudang }}</small>
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
