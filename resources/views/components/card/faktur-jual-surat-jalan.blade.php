@props(['jual'])

<div class="accordion" id="accordion-surat-jalan">
  <div class="accordion-item">
    <button class="accordion-button p-3 bg-light-subtle rounded-top text-body" type="button" data-coreui-toggle="collapse"
      data-coreui-target="#collapse-surat-jalan" aria-expanded="true" aria-controls="collapse-surat-jalan">
      <p class="mb-0 fw-semibold">Surat Jalan</p>
    </button>

    <div id="collapse-surat-jalan" class="accordion-collapse collapse" data-coreui-parent="#accordion-surat-jalan">
      <div class="accordion-body p-2">

        @php
          $suratJalans = $jual->jualDetails
              ->flatMap(fn($detail) => $detail->suratJalanDetails)
              ->pluck('suratJalan')
              ->unique('id')
              ->filter();
        @endphp

        @if ($suratJalans->isNotEmpty())
          <table class="table table-bordered mb-0">
            <thead>
              <tr>
                <th>Nomor Surat Jalan</th>
                <th>Nama Kendaraan</th>
                <th>Tanggal Surat Jalan</th>
                <th>Koli</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($suratJalans as $sj)
                <tr>
                  <td>
                    <a href="{{ route(auth()->user()->getRoutePrefix() . '.surat-jalan.show', $sj->id) }}">
                      {{ $sj->nomor_surat_jalan }}
                    </a>
                  </td>
                  <td>{{ $sj->kendaraan->nama ?? '-' }}</td>
                  <td>{{ \Carbon\Carbon::parse($sj->tgl_surat_jalan)->format('d/m/Y') }}</td>
                  <td>{{ $sj->koli }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div>
            <p class="mb-0 text-center">Belum ada data surat jalan</p>
          </div>
        @endif

      </div>
    </div>
  </div>
</div>
