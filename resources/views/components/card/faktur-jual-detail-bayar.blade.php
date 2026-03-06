@props(['jual'])

<div class="accordion" id="accordion-jual-bayar">
  <div class="accordion-item">
    <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button" data-coreui-toggle="collapse"
      data-coreui-target="#collapse-jual-bayar" aria-expanded="true" aria-controls="collapse-jual-bayar">
      <p class="mb-0 fw-semibold">Info Pembayaran</p>
    </button>

    <div id="collapse-jual-bayar" class="accordion-collapse collapse show" data-coreui-parent="#accordion-jual-bayar">
      <div class="accordion-body p-2">
        @php
          $sisa_bayar = $jual->total_tagihan - $jual->total_terbayar;
        @endphp
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="230">Diskon</td>
              <td>
                @if ($jual->diskon_faktur > 0)
                  {{ number_format($jual->diskon_faktur, $jual->diskon_faktur == 0 ? 2 : 2) }}
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <td>Kredit</td>
              <td>
                @if ($jual->kredit > 0)
                  {{ $jual->kredit }} Hari
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <td>PPN</td>
              <td>
                @if ($jual->ppn > 0)
                  {{ number_format($jual->ppn, $jual->ppn == 0 ? 0 : 2) }}%
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <td>Pungut PPN</td>
              <td>{{ $jual->pungut_ppn }}</td>
            </tr>
            <tr>
              <td>Ongkir</td>
              <td>
                {{ Number::currency($jual->ongkir, in: 'IDR', locale: 'id_ID') }}
              </td>
            </tr>
            <tr>
              <td>Total Tagihan </td>
              <td>
                <strong>{{ Number::currency($jual->total_tagihan, in: 'IDR', locale: 'id_ID') }}</strong>
              </td>
            </tr>
            <tr>
              <td>Total Bayar</td>
              <td>
                @if ($jual->total_terbayar > 0)
                  {{ Number::currency($jual->total_terbayar, in: 'IDR', locale: 'id_ID') }}
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
              <td>Status Bayar</td>
              <td>
                <x-badge.status-bayar :status="$jual->status_bayar" />
              </td>
            </tr>
            <tr>
              <td>Keterangan Bayar</td>
              <td>{{ $jual->keterangan_bayar ?? '-' }}</td>
            </tr>
          </tbody>
        </table>

        @if ($jual->status_bayar === \App\Enums\StatusBayar::PAID && $sisa_bayar != 0)
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
