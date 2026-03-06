@props(['sample_out'])

<div class="accordion" id="accordion-jual-detail">
  <div class="accordion-item">
    <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button" data-coreui-toggle="collapse"
      data-coreui-target="#collapse-jual-detail" aria-expanded="true" aria-controls="collapse-jual-detail">
      <p class="mb-0 fw-semibold">Detail Sampel Keluar</p>
    </button>

    <div id="collapse-jual-detail" class="accordion-collapse collapse show" data-coreui-parent="#accordion-jual-detail">
      <div class="accordion-body p-2">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="230">Salesman </td>
              <td>{{ $sample_out->salesman->nama ?? '-' }}</td>
            </tr>
            <tr>
              <td width="230">Pelanggan </td>
              <td>{{ $sample_out->pelanggan->nama }}</td>
            </tr>
            <tr>
              <td>Nomor Sampel </td>
              <td>{{ $sample_out->nomor_sample }}</td>
            </tr>
            <tr>
              <td>Tanggal </td>
              <td>{{ \Carbon\Carbon::parse($sample_out->tanggal)->format('d/m/Y') }}</td>
            </tr>
            <tr>
              <td>Status Sampel</td>
              <td>
                <x-badge.status-sample :status="$sample_out->status_sample" />
              </td>
            </tr>
            <tr>
              <td>Keterangan </td>
              <td>{{ $sample_out->keterangan ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
