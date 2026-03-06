@props(['data'])

<div class="accordion" id="accordion-sj-detail">
  <div class="accordion-item">
    <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button" data-coreui-toggle="collapse"
      data-coreui-target="#collapse-sj-detail" aria-expanded="true" aria-controls="collapse-sj-detail">
      <p class="mb-0 fw-semibold">Detail Surat Jalan</p>
    </button>

    <div id="collapse-sj-detail" class="accordion-collapse collapse show" data-coreui-parent="#accordion-sj-detail">
      <div class="accordion-body p-2">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="230">Nomot Surat Jalan </td>
              <td>{{ $data->nomor_surat_jalan }}</td>
            </tr>
            <tr>
              <td width="230">Tanggal </td>
              <td>{{ \Carbon\Carbon::parse($data->tgl_surat_jalan)->format('d/m/Y') }}</td>
            </tr>
            <tr>
              <td>Kendaraan </td>
              <td>{{ $data->kendaraan->nama }}</td>
            </tr>
            <tr>
              <td>Nama Pelanggan </td>
              <td>{{ $data->pelanggan->nama }}</td>
            </tr>
            <tr>
              <td>Nomor HP Pelanggan</td>
              <td>{{ $data->pelanggan->contact_phone }}</td>
            </tr>
            <tr>
              <td>Jumlah Koli</td>
              <td>{{ $data->koli }} Koli</td>
            </tr>
            <tr>
              <td>Penanggung Jawab</td>
              <td>{{ $data->staf_logistik }}</td>
            </tr>
            <tr>
              <td>Keterangan</td>
              <td>{{ $data->keterangan ?? '-' }}</td>
            </tr>
            <tr>
              <td>Tanggal Dibuat</td>
              <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
              <td>Terakhir Diperbarui</td>
              <td>{{ \Carbon\Carbon::parse($data->updated_at)->format('d/m/Y H:i') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
