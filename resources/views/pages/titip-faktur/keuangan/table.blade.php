<table class="table table-bordered m-0">
  <thead class="text-center">
    @if (!$isPdf)
      <th><input type="checkbox" id="checkAll"></th>
    @endif
    <th>Tanggal Faktur</th>
    <th>Nomor Faktur</th>
    <th>Tanggal Jatuh Tempo</th>
    <th>Status Bayar</th>
    <th>Tagihan</th>
    @if (!$isPdf)
      <th>Tanggal Cetak</th>
    @endif
  </thead>
  <tbody>
    @foreach ($juals as $item)
      <tr>
        @if (!$isPdf)
          <td class="text-center">
            <input type="checkbox" name="selected_ids[]" value="{{ $item->id }}">
          </td>
        @endif
        <td class="text-center">
          {{ \Carbon\Carbon::parse($item->tgl_faktur)->format('d/m/Y') }}
        </td>
        <td class="text-center">{{ $item->nomor_faktur }}</td>
        <td class="text-center">
          {{ $item->tgl_faktur
              ? \Carbon\Carbon::parse($item->tgl_faktur)->addDays((int) $item->kredit)->format('d/m/Y')
              : '-' }}
        </td>
        <td class="text-center">
          @if ($item->bayar)
            Cicil
          @else
            Belum bayar
          @endif
        </td>
        <td class="text-end">
          {{ Number::currency($item->total_tagihan - $item->total_terbayar, in: 'IDR', locale: 'id_ID') }}</td>
        @if (!$isPdf)
          <td class="text-center">
            {{ $item->cetak_titip_faktur_at ? \Carbon\Carbon::parse($item->cetak_titip_faktur_at)->format('d/m/Y') : '-' }}
          </td>
        @endif
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      @if (!$isPdf)
        <td></td>
      @endif
      <td></td>
      <td class="text-center">
        <strong>{{ $juals->count() }}</strong>
      </td>
      <td></td>
      <td class="text-end">
        <strong> Total Tagihan</strong>
      </td>
      <td class="text-end">
        <strong>
          {{ Number::currency(
              $juals->sum(fn($item) => $item->total_tagihan - $item->total_terbayar),
              in: 'IDR',
              locale: 'id_ID',
          ) }}
        </strong>
      </td>
      @if (!$isPdf)
        <td></td>
      @endif
    </tr>
  </tfoot>

</table>

@push('scripts')
  <script>
    document.getElementById('checkAll')?.addEventListener('change', function() {
      document.querySelectorAll('input[name="selected_ids[]"]').forEach(cb => {
        cb.checked = this.checked;
      });
    });
  </script>
@endpush
