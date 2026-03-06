@props(['beli'])

<div class="accordion" id="accordion-beli-bayar">
  <div class="accordion-item">
    <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button" data-coreui-toggle="collapse"
      data-coreui-target="#collapse-beli-bayar" aria-expanded="true" aria-controls="collapse-beli-bayar">
      <p class="mb-0 fw-semibold">Info Pembayaran</p>
    </button>

    <div id="collapse-beli-bayar" class="accordion-collapse collapse show" data-coreui-parent="#accordion-beli-bayar">
      <div class="accordion-body p-2">
        @php
          $sisa_bayar = $beli->total_tagihan - $beli->total_terbayar;
        @endphp
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="230">Diskon </td>
              <td>
                @if ($beli->diskon_faktur > 0)
                  {{ number_format($beli->diskon_faktur, $beli->diskon_faktur == 0 ? 2 : 2) }}
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <td>Kredit </td>
              <td>
                @if ($beli->kredit > 0)
                  {{ $beli->kredit }} Hari
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <td>PPN </td>
              <td>
                @if ($beli->ppn > 0)
                  {{ number_format($beli->ppn, $beli->ppn == 0 ? 0 : 2) }}%
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <td>Ongkir </td>
              <td>
                {{ Number::currency($beli->ongkir, in: 'IDR', locale: 'id_ID') }}
              </td>
            </tr>
            <tr>
              <td>Materai </td>
              <td>
                {{ Number::currency($beli->materai, in: 'IDR', locale: 'id_ID') }}
              </td>
            </tr>
            <tr>
              <td>Biaya Lainya </td>
              <td>
                {{ Number::currency($beli->biaya_lainnya, in: 'IDR', locale: 'id_ID') }}
              </td>
            </tr>
            <tr>
              <td>Total Tagihan </td>
              <td>
                <strong>{{ Number::currency($beli->total_tagihan, in: 'IDR', locale: 'id_ID') }}</strong>
              </td>
            </tr>
            <tr>
              <td>Total Bayar </td>
              <td>
                @if ($beli->total_terbayar > 0)
                  {{ Number::currency($beli->total_terbayar, in: 'IDR', locale: 'id_ID') }}
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <td>Sisa Bayar </td>
              <td>
                @if ($sisa_bayar == 0)
                  -
                @else
                  {{ Number::currency($sisa_bayar, in: 'IDR', locale: 'id_ID') }}
                @endif
              </td>
            </tr>
            <tr>
              <td>Status Bayar </td>
              <td>
                <x-badge.status-bayar :status="$beli->status_bayar" />
              </td>
            </tr>
            <tr>
              <td>Keterangan </td>
              <td>{{ $beli->keterangan_bayar ?: '-' }}</td>
            </tr>
          </tbody>
        </table>
        @if ($beli->status_bayar === \App\Enums\StatusBayar::PAID && $sisa_bayar != 0)
          <div class="alert alert-danger mt-3 mb-0">
            <span>
              <i class="bi bi-info-circle-fill"></i>
              <span class="fw-bold">Perhatian!</span>
            </span>
            <span class="mb-0">
              Terdapat perubahan faktur saat faktur sudah lunas dari <strong>Admin Fakturis</strong>, harap hubungi
              superadmin untuk menyesuaikan
              data pembayaran jika
              dibutuhkan.
            </span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
