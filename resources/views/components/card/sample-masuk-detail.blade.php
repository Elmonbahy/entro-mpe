@props(['sample_in'])

<div class="accordion" id="accordion-beli-detail">
  <div class="accordion-item">
    <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button" data-coreui-toggle="collapse"
      data-coreui-target="#collapse-beli-detail" aria-expanded="true" aria-controls="collapse-beli-detail">
      <p class="mb-0 fw-semibold">Detail Sampel Masuk</p>
    </button>

    <div id="collapse-beli-detail" class="accordion-collapse collapse show" data-coreui-parent="#accordion-beli-detail">
      <div class="accordion-body p-2">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="230">Supplier </td>
              <td>{{ $sample_in->supplier->nama }}</td>
            </tr>
            <tr>
              <td>Nomor Sampel </td>
              <td>{{ $sample_in->nomor_sample }}</td>
            </tr>
            <tr>
              <td>Tanggal </td>
              <td>{{ \Carbon\Carbon::parse($sample_in->tanggal)->format('d/m/Y') }}</td>
            </tr>
            <tr>
              <td>Status Sampel </td>
              <td>
                <x-badge.status-sample :status="$sample_in->status_sample" />
              </td>
            </tr>
            <tr>
              <td>Keterangan </td>
              <td>{{ $sample_in->keterangan ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
