@props(['data'])

<div class="card">
  <div class="p-3 card-header">
    <p class="mb-0 fw-semibold">Detail data pelanggan</p>
  </div>

  <div class="p-3">
    <table class="table mb-0">
      <tbody>
        <tr>
          <td width="150">Kode pelanggan </td>
          <td>{{ $data->kode }}</td>
        </tr>
        <tr>
          <td width="200">Nama pelanggan </td>
          <td>{{ $data->nama }}</td>
        </tr>
        <tr>
          <td>Kota </td>
          <td>{{ $data->kota }}</td>
        </tr>
        <tr>
          <td>Alamat</td>
          <td>{{ $data->alamat }}</td>
        </tr>
        <tr>
          <td>NPWP</td>
          <td>{{ $data->npwp }}</td>
        </tr>
        <tr>
          <td>Person</td>
          <td>{{ $data->contact_person }}</td>
        </tr>
        <tr>
          <td>Telepon</td>
          <td>{{ $data->contact_phone }}</td>
        </tr>

        @roles(['af', 'ak', 'as'])
        <tr>
          <td>Tipe pelanggan</td>
          <td>{{ $data->tipe }}</td>
        </tr>
        <tr>
          <td>Tipe harga</td>
          <td>{{ $data->tipe_harga ?: '-' }}</td>
        </tr>
        <tr>
          <td>Area</td>
          <td>{{ $data->area }}</td>
        </tr>
        <tr>
          <td>Limit hari</td>
          <td>{{ $data->limit_hari }} Hari</td>
        </tr>
        <tr>
          <td>Plafon hutang</td>
          <td>{{ \Number::currency($data->plafon_hutang, 'IDR', 'id_ID') }}</td>
        </tr>
        @endroles
      </tbody>
    </table>
  </div>
</div>
