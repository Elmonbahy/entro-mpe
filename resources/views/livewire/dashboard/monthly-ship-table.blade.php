<div class="card mt-4">
  <div class="card-header p-3 fw-bold d-flex justify-content-between align-items-center">
    <span>Data Pengiriman Bulanan</span>
    <div class="d-flex align-items-center gap-2">
      <select wire:model.live="selectedYear" id="year" class="form-select form-select-sm" style="width: 120px;">
        @foreach ($years as $year)
          <option value="{{ $year }}">{{ $year }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="card-body text-center table-responsive">
    <table class="table table-bordered table-striped table-sm">
      <thead>
        <tr>
          <th>Bulan</th>
          <th>Jumlah Faktur</th>
          <th>Selesai</th>
          <th>Process Gudang</th>
          <th>Process Faktur</th>
          <th>Baru</th>
          <th>Shipped</th>
          <th>Partial</th>
          <th>Pending</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($monthlyData as $row)
          <tr>
            <td class="text-start">{{ $row['month'] }}</td>
            <td>{{ $row['jumlah_faktur'] }}</td>
            <td>{{ $row['done'] }}</td>
            <td>{{ $row['process_gudang'] }}</td>
            <td>{{ $row['process_faktur'] }}</td>
            <td>{{ $row['new'] }}</td>
            <td>{{ $row['shipped'] }}</td>
            <td>{{ $row['partial'] }}</td>
            <td>{{ $row['pending'] }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">Tidak ada data</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
