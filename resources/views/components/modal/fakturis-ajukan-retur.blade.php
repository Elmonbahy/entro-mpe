@php
  $sudahDiretur = $item->returs->where('status', '!=', \App\Enums\StatusRetur::REJECTED)->sum('jumlah_barang_retur');

  $maxRetur = $item instanceof \App\Models\JualDetail ? $item->jumlah_barang_keluar : $item->jumlah_barang_masuk;

  $sisaKuota = $maxRetur - $sudahDiretur;
@endphp

<button type="button" class="btn btn-warning btn-sm" data-coreui-toggle="modal"
  data-coreui-target="#modalRetur{{ $item->id }}">
  <i class="bi bi-arrow-counterclockwise"></i> Retur
</button>


<div class="modal fade" id="modalRetur{{ $item->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajukan Retur: {{ $item->barang->nama }}</h5>
        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('fakturis.retur.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="returnable_id" value="{{ $item->id }}">
          <input type="hidden" name="returnable_type" value="{{ get_class($item) }}">

          <div class="mb-3">
            <label class="form-label">Batch / Expired</label>
            <input type="text" class="form-control" value="{{ $item->batch }} / {{ $item->tgl_expired ?? '-' }}"
              disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">
              Jumlah Retur
              <span class="badge bg-info">Maks: {{ $sisaKuota }}</span>
            </label>
            <input type="number" name="jumlah_barang_retur" class="form-control" required min="1"
              max="{{ $sisaKuota }}" {{ $sisaKuota <= 0 ? 'disabled' : '' }}>
            @if ($sisaKuota <= 0)
              <small class="text-danger">Tidak ada kuota retur untuk barang ini.</small>
            @endif
          </div>

          <div class="mb-3">
            <label class="form-label">Jenis Retur</label>
            <select name="jenis_retur" class="form-select">
              <option value="0">Tidak Diganti</option>
              <option value="1">Diganti</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Keterangan Retur</label>
            <textarea name="keterangan" class="form-control" placeholder="Contoh: Barang rusak saat pengiriman..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
          @if ($sisaKuota > 0)
            <button type="submit" class="btn btn-warning">Ajukan ke
              Gudang</button>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>
