@props(['beli'])

<div class="accordion" id="accordion-beli-detail">
  <div class="accordion-item">
    <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button" data-coreui-toggle="collapse"
      data-coreui-target="#collapse-beli-detail" aria-expanded="true" aria-controls="collapse-beli-detail">
      <p class="mb-0 fw-semibold">Detail Pembelian</p>
    </button>

    <div id="collapse-beli-detail" class="accordion-collapse collapse show" data-coreui-parent="#accordion-beli-detail">
      <div class="accordion-body p-2">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="230">Supplier </td>
              <td>{{ $beli->supplier->nama }}</td>
            </tr>
            <tr>
              <td>Nomor Pemesanan </td>
              <td>{{ $beli->nomor_pemesanan }}</td>
            </tr>
            <tr>
              <td>Tanggal Faktur </td>
              <td>{{ \Carbon\Carbon::parse($beli->tgl_faktur)->format('d/m/Y') }}</td>
            </tr>
            <tr>
              <td>Nomor Faktur </td>
              <td>{{ $beli->nomor_faktur ?? '-' }}</td>
            </tr>
            <tr>
              <td>Tanggal Terima Faktur </td>
              <td>
                {{ $beli->tgl_terima_faktur ? \Carbon\Carbon::parse($beli->tgl_terima_faktur)->format('d/m/Y') : '-' }}
              </td>
            </tr>
            <tr>
              <td>Status Faktur</td>
              <td>
                <x-badge.status-faktur :status="$beli->status_faktur" />
              </td>
            </tr>
            <tr>
              <td>Keterangan </td>
              <td>{{ $beli->keterangan_faktur ?? '-' }}</td>
            </tr>
            @if (auth()->check() &&
                    auth()->user()->hasAnyRole(['af', 'ag', 'as']))
              <tr>
                <td>Tanggal Dibuat</td>
                <td>{{ \Carbon\Carbon::parse($beli->created_at)->format('d/m/Y H:i') }}</td>
              </tr>
              <tr>
                <td>Terakhir Diperbarui</td>
                <td>{{ \Carbon\Carbon::parse($beli->updated_at)->format('d/m/Y H:i') }}</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
