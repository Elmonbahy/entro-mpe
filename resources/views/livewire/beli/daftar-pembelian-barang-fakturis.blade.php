<div class="card">
  <div class="card-header p-3 d-flex justify-content-between align-items-center">
    <p class="mb-0 fw-semibold">Daftar pembelian barang</p>

    @if (
        $beli->status_faktur === \App\Enums\StatusFaktur::PROCESS_FAKTUR ||
            $beli->status_faktur === \App\Enums\StatusFaktur::NEW)
      <button class="btn btn-primary" wire:click="sendToGudang()"
        wire:confirm="Faktur beli akan dikirim untuk diproses oleh gudang?">
        Selesai
      </button>
    @endif
  </div>

  <div class="card-body">
    @if ($beli_details->isEmpty())
      <p colspan="10" class="text-center fs-5 mb-0">Tidak ada data. </p>
    @else
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead>
            <th>Brand</th>
            <th>Kode Barang</th>
            <th>Nama</th>
            <th>Pesanan</th>
            <th>Masuk</th>
            <th>Satuan</th>
            <th>Diskon1</th>
            <th>Diskon2</th>
            <th>Batch</th>
            <th>Tgl Expired</th>
            <th>Harga Beli</th>
            <th>Total</th>
            <th>Action</th>
          </thead>
          <tbody>
            @foreach ($beli_details as $item)
              <tr wire:key="{{ $item->id }}">
                <td>{{ $item->barang->brand->nama }}</td>
                <td>{{ $item->barang->kode }}</td>
                <td>{{ $item->barang->nama }}</td>
                <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                <td>{{ $item->jumlah_barang_masuk ? number_format($item->jumlah_barang_masuk, 0, ',', '.') : '-' }}</td>
                <td>{{ $item->barang->satuan }}</td>
                <td>
                  @if ($editingId === $item->id)
                    <input type="number" class="form-control" wire:model.defer="editDiskon1">
                  @else
                    {{ $item->diskon1 > 0 ? $item->diskon1 : '-' }}
                  @endif
                </td>
                <td>
                  @if ($editingId === $item->id)
                    <input type="number" class="form-control" wire:model.defer="editDiskon2">
                  @else
                    {{ $item->diskon2 > 0 ? $item->diskon2 : '-' }}
                  @endif
                </td>
                <td>{{ $item->batch ?? '-' }}</td>
                <td>
                  @if ($item->tgl_expired)
                    {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') }}
                  @else
                    -
                  @endif
                </td>
                <td>
                  @if ($editingId === $item->id)
                    <input type="text" class="form-control" wire:model.defer="editHargaBeliFormatted"
                      oninput="formatRupiah(this)" />
                  @else
                    {{ formatCurrencyDinamis($item->harga_beli) }}
                  @endif
                </td>
                <td>{{ Number::currency($item->total_tagihan, in: 'IDR', locale: 'id_ID') }}</td>
                <td>
                  <button class="btn btn-danger mb-1" wire:click="delete({{ $item->id }})"
                    wire:confirm="Hapus barang dari daftar pembelian?">
                    <i class="bi-trash text-white"></i>
                  </button>
                  <button class="btn btn-warning mb-1" wire:click="edit({{ $item->id }})">
                    <i class="bi-pencil text-white"></i>
                  </button>

                  @if ($editingId === $item->id)
                    <button class="btn btn-primary mb-1" wire:click="updateHargaBeli"
                      wire:confirm="edit dari daftar pembelian?"><i class="bi bi-check-circle"></i></button>
                    <button class="btn btn-secondary mb-1" wire:click="$set('editingId', null)"><i
                        class="bi bi-x-circle"></i></button>
                  @endif

                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="11" class="fw-bold text-end">
                Total Faktur
              </td>
              <td class="fw-bold" colspan="3">
                {{ Number::currency(round($beli_details->sum('total_tagihan')), in: 'IDR', locale: 'id_ID') }}</td>
            </tr>
          </tfoot>
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
