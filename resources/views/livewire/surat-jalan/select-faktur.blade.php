<div class="d-flex gap-2">
  <div class="flex-grow-1" wire:ignore>
    <x-form.label value="Faktur" />
    <x-form.select name="faktur" placeholder="Cari atau pilih faktur" :options="$juals" valueKey="id"
      labelKey="nomor_faktur" />
  </div>

  <div class="align-content-end">
    <button class="btn btn-primary" wire:loading.attr="disabled" wire:click="resetFaktur()" type="button">
      Reset
      <span wire:loading>
        <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
        <span class="visually-hidden" role="status">Loading...</span>
      </span>
    </button>
  </div>
</div>

@script
  <script>
    const fakturSelectEl = new TomSelect('#faktur', {
      onChange(value) {
        if (value) {
          Livewire.dispatch('SuratJalan.SelectFaktur:onJualChange', {
            id: value
          });
        }
      }
    });

    Livewire.on('reset-jual-id', () => {
      fakturSelectEl.clear();
    });
  </script>
@endscript
