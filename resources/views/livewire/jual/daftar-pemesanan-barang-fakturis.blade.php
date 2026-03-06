<div class="card">
  <div class="card-header p-3 d-flex justify-content-between align-items-center">
    <p class="mb-0 fw-semibold">Daftar pemesanan barang</p>
    <button class="btn btn-primary" wire:click="selesai()" wire:confirm="apakah sudah sesuai?">
      Selesai
    </button>
  </div>

  <div class="card-body">
    @if ($spjual_detail->isEmpty())
      <p colspan="10" class="text-center fs-5 mb-0">Tidak ada data. </p>
    @else
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead>
            <th>Brand</th>
            <th>Nama</th>
            <th>Jumlah Barang</th>
            <th>Satuan</th>
            <th>Action</th>
          </thead>
          <tbody>

            @foreach ($spjual_detail as $item)
              <tr wire:key="{{ $item->id }}">
                <td>{{ $item->barang->brand->nama }}</td>
                <td>{{ $item->barang->nama }}</td>
                <td>{{ $item->jumlah_barang_dipesan }}</td>
                <td>{{ $item->barang->satuan }}</td>
                <td>
                  <button class="btn btn-danger" wire:click="delete({{ $item->id }})"
                    wire:confirm="Hapus barang dari daftar pesanan?">
                    <i class="bi-trash text-white"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

  </div>
</div>
