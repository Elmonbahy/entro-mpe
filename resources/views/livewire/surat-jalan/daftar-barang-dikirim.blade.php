<div class="card mt-3">
  <div class="card-header p-3 d-flex justify-content-between align-items-center">
    <p class="mb-0 fw-semibold">Daftar Barang Dikirim</p>
    @if ($surat_jalan_details && $surat_jalan_details->isNotEmpty())
      <a href="{{ route($role == 'logistik' ? 'logistik.surat-jalan.show' : 'gudang.surat-jalan.show', ['id' => $surat_jalan_id]) }}"
        class="btn btn-primary">Selesai</a>
    @endif
  </div>

  <div class="card-body">
    @if ($surat_jalan_details && $surat_jalan_details->isNotEmpty())
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead>
            <th>Nomor Faktur</th>
            <th>Nama Barang</th>
            <th>Jumlah Barang</th>
            <th>Satuan</th>
            <th>Action</th>
          </thead>
          <tbody>
            @foreach ($surat_jalan_details as $item)
              <tr wire:key="{{ $item->id }}">
                <td>
                  {{ $item->jual->nomor_faktur }}
                </td>
                <td>
                  {{ $item->barang->nama }}
                </td>
                <td>
                  {{ number_format($item->jumlah_barang_dikirim, 0, ',', '.') }}
                </td>
                <td>
                  {{ $item->barang->satuan }}
                </td>
                <td>
                  <button class="btn btn-danger" wire:click="deleteItem({{ $item->id }})"
                    wire:confirm="Hapus barang?">
                    <span class="text-white">
                      <i class="bi-trash text-white"></i>
                    </span>
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <p class="mb-0 text-center">Tidak ada barang dikirim.</p>
    @endif
  </div>
</div>
