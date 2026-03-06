<div class="table-responsive">
  @if ($jualDetails)
    <div class="mb-2 d-flex justify-content-end">
      <button class="btn btn-secondary btn-sm" wire:click="submitSemuaBarang"
        wire:confirm="Apakah Anda yakin ingin menambahkan semua barang ke surat jalan?">
        <i class="bi bi-plus-circle me-1"></i> Tambah Semua Barang
      </button>
    </div>
    <table class="table table-bordered mb-0">
      <thead>
        <th>Nama Barang</th>
        <th>Barang Keluar</th>
        <th>Barang Terkirim</th>
        <th>Barang Dikirim</th>
        <th>Satuan</th>
      </thead>
      <tbody>
        @foreach ($jualDetails as $item)
          <tr wire:key="{{ $item->id }}">
            <td>{{ $item->barang->nama }}</td>
            <td>{{ number_format($item->jumlah_barang_keluar, 0, ',', '.') }}</td>
            <td>
              {{ $item->surat_jalan_details_sum_jumlah_barang_dikirim ? number_format($item->surat_jalan_details_sum_jumlah_barang_dikirim, 0, ',', '.') : '-' }}
            </td>
            <td>
              <div class="d-flex input-group" wire:ignore x-data="{
                  'qnt': '',
                  'id': '{{ $item->id }}',
                  submitBarangDikirim() {
                      if (confirm('Tambah barang ke surat jalan?')) {
                          $wire.submitBarangDikirim(this.qnt, this.id)
                              .then(() => this.qnt = '')
                              .catch(e => console.log(e));
                      }
                  }
              }">
                <input type="number" class="form-control form-control-sm" style="max-width: 100px" x-model="qnt"
                  min="1">
                <button class="btn btn-secondary btn-sm" :disabled="!qnt" @click="submitBarangDikirim()">
                  <i class="bi bi-plus"></i>
                </button>
              </div>

            </td>
            <td>{{ $item->barang->satuan }}</td>
          </tr>
        @endforeach
      </tbody>

    </table>
  @endif
</div>
