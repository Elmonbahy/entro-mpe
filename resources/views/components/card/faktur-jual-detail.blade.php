@props(['jual'])

<div class="accordion" id="accordion-jual-detail">
  <div class="accordion-item">
    <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button" data-coreui-toggle="collapse"
      data-coreui-target="#collapse-jual-detail" aria-expanded="true" aria-controls="collapse-jual-detail">
      <p class="mb-0 fw-semibold">Detail Penjualan</p>
    </button>

    <div id="collapse-jual-detail" class="accordion-collapse collapse show" data-coreui-parent="#accordion-jual-detail">
      <div class="accordion-body p-2">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="230">Salesman </td>
              <td>{{ $jual->salesman->nama ?? '-' }}</td>
            </tr>
            <tr>
              <td width="230">Pelanggan </td>
              <td>{{ $jual->pelanggan->nama }}</td>
            </tr>
            <tr>
              <td>Nomor Faktur </td>
              <td>{{ $jual->nomor_faktur }}</td>
            </tr>
            <tr>
              <td>Tanggal Faktur </td>
              <td>{{ \Carbon\Carbon::parse($jual->tgl_faktur)->format('d/m/Y') }}</td>
            </tr>
            <tr>
              <td>Nomor Pemesanan </td>
              <td>{{ $jual->nomor_pemesanan ?? '-' }}</td>
            </tr>
            <tr>
              <td>Status Faktur</td>
              <td>
                <x-badge.status-faktur :status="$jual->status_faktur" />
              </td>
            </tr>
            <tr>
              <td>Keterangan </td>
              <td>{{ $jual->keterangan ?? '-' }}</td>
            </tr>

            @if (auth()->check() &&
                    auth()->user()->hasAnyRole(['af', 'ag']))
              <tr>
                <td>Tanggal Dibuat</td>
                <td>{{ \Carbon\Carbon::parse($jual->created_at)->format('d/m/Y H:i') }}</td>
              </tr>
              <tr>
                <td>Terakhir Diperbarui</td>
                <td>{{ \Carbon\Carbon::parse($jual->updated_at)->format('d/m/Y H:i') }}</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
