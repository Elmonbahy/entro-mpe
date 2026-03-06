<div class="card">
  <div class="card-header p-3 d-flex justify-content-between align-items-center">
    <p class="mb-0 fw-semibold">Daftar pemesanan barang</p>
    <button class="btn btn-primary" wire:click="selesai()" wire:confirm="surat pesanan susdah sesuai?">
      Selesai
    </button>
  </div>

  <div class="card-body">
    @if ($spbeli_details->isEmpty())
      <p colspan="10" class="text-center fs-5 mb-0">Tidak ada data. </p>
    @else
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead>
            <th>Brand</th>
            <th>Nama</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Harga Beli</th>
            <th>Tagihan</th>
            <th>Action</th>
          </thead>
          <tbody>
            @foreach ($spbeli_details as $item)
              <tr wire:key="{{ $item->id }}">
                <td>{{ $item->barang->brand->nama }}</td>
                <td>{{ $item->barang->nama }}</td>
                <td>{{ $item->jumlah_barang_dipesan }}</td>
                <td>{{ $item->barang->satuan }}</td>
                <td>{{ Number::currency($item->harga_beli, in: 'IDR', locale: 'id_ID') }}</td>
                <td>{{ Number::currency($item->total_tagihan, in: 'IDR', locale: 'id_ID') }}</td>
                <td>
                  <button class="btn btn-danger" wire:click="delete({{ $item->id }})"
                    wire:confirm="Hapus barang dari daftar pemesanan?">
                    <i class="bi-trash text-white"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="fw-bold text-end">
                Total Pemesanan
              </td>
              <td class="fw-bold" colspan="2">
                {{ Number::currency($spbeli_details->sum('total_tagihan'), in: 'IDR', locale: 'id_ID') }}</td>
            </tr>
          </tfoot>
        </table>

      </div>
    @endif
  </div>
</div>
