<div class="card">
  <div class="card-header p-3 d-flex justify-content-between align-items-center">
    <p class="mb-0 fw-semibold">Daftar sampel masuk barang</p>

    @if (
        $sample_in->status_sample === \App\Enums\StatusSample::PROCESS_SAMPLE ||
            $sample_in->status_sample === \App\Enums\StatusSample::NEW)
      <button class="btn btn-primary" wire:click="sendToGudang()"
        wire:confirm="Sampel masuk akan dikirim untuk diproses oleh gudang?">
        Selesai
      </button>
    @endif
  </div>

  <div class="card-body">
    @if ($sample_in_details->isEmpty())
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
            <th>Batch</th>
            <th>Tgl Expired</th>
            <th>Action</th>
          </thead>
          <tbody>
            @foreach ($sample_in_details as $item)
              <tr wire:key="{{ $item->id }}">
                <td>{{ $item->sampleBarang->barang->brand->nama }}</td>
                <td>{{ $item->sampleBarang->barang->kode }}</td>
                <td>{{ $item->sampleBarang->barang->nama }}</td>
                <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                <td>{{ $item->jumlah_barang_masuk ? number_format($item->jumlah_barang_masuk, 0, ',', '.') : '-' }}</td>
                <td>{{ $item->sampleBarang->satuan ?? '-' }}</td>
                <td>{{ $item->batch ?? '-' }}</td>
                <td>
                  @if ($item->tgl_expired)
                    {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') }}
                  @else
                    -
                  @endif
                </td>
                <td>
                  <button class="btn btn-danger mb-1" wire:click="delete({{ $item->id }})"
                    wire:confirm="Hapus barang dari daftar sampel masuk?">
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
