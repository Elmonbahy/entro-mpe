@props(['data'])

<div class="card">
  <div class="p-3 card-header">
    <p class="mb-0 fw-semibold">Detail data pelanggan</p>
  </div>

  <div class="p-3">
    <table class="table mb-0">
      <tbody>
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
          <td>Person</td>
          <td>{{ $data->contact_person }}</td>
        </tr>
        <tr>
          <td>Telepon</td>
          <td>{{ $data->contact_phone }}</td>
        </tr>
        <tr>
          <td>Tipe pelanggan</td>
          <td>{{ $data->tipe }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
