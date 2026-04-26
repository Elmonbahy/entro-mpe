<div class="card">
  <div class="card-header p-3 d-flex justify-content-between align-items-center">
    <p class="mb-0 fw-semibold">Daftar penjualan barang</p>

    @if ($jual->status_faktur === \App\Enums\StatusFaktur::PROCESS_FAKTUR)
      <button class="btn btn-primary" wire:click="sendToGudang()"
        wire:confirm="Faktur jual akan dikirim untuk diproses oleh gudang?">
        Selesai
      </button>
    @endif
  </div>

  <div class="card-body">
    @if ($jual_detail->isEmpty())
      <p colspan="10" class="text-center fs-5 mb-0">Tidak ada data. </p>
    @else
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead>
            <th>Brand</th>
            <th>Kode Barang</th>
            <th>Nama</th>
            <th>Pesanan</th>
            <th>Keluar</th>
            <th>Satuan</th>
            <th>Batch</th>
            <th>Tgl Expired</th>
            <th>Harga Jual</th>
            <th>Action</th>
          </thead>
          <tbody>

            @foreach ($jual_detail as $item)
              <tr wire:key="{{ $item->id }}">
                <td>{{ $item->barang->brand->nama }}</td>
                <td>{{ $item->barang->kode }}</td>
                <td>{{ $item->barang->nama }}</td>
                <td>
                  {{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}
                </td>
                <td>{{ $item->jumlah_barang_keluar ? number_format($item->jumlah_barang_keluar, 0, ',', '.') : '-' }}
                </td>
                <td>{{ $item->barang->satuan }}</td>
                <td>{{ $item->batch ?? '-' }}</td>
                <td>
                  @if ($item->tgl_expired)
                    {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') }}
                  @else
                    -
                  @endif
                </td>
                <td>
                  {{ formatCurrencyDinamis($item->harga_jual) }}
                </td>
                <td>
                  <button class="btn btn-danger mb-1" wire:click="delete({{ $item->id }})"
                    wire:confirm="Hapus barang dari daftar penjualan?">
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

<script>
  function formatRupiah(el) {
    let value = el.value;

    // Hanya izinkan angka dan koma
    value = value.replace(/[^0-9,]/g, '');

    // Pisahkan bagian ribuan dan desimal
    let parts = value.split(',');
    let integerPart = parts[0];
    let decimalPart = parts[1] !== undefined ? ',' + parts[1].slice(0, 2) : '';

    // Hapus nol depan (kecuali 0 sendiri)
    integerPart = integerPart.replace(/^0+(?!$)/, '');

    // Format ribuan (misal 10000 jadi 10.000)
    let formatted = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    el.value = formatted + decimalPart;
  }
</script>
