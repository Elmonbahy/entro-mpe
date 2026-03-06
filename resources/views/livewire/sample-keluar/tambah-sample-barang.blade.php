<form wire:submit.prevent>
  <div class="row">
    <div class="col-md-3 mb-3" wire:ignore>
      <x-form.label value="Brand" />
      <div>
        <x-form.select name="brand_id" placeholder="Cari atau pilih brand" :options="$brands" valueKey="id"
          labelKey="nama" />
      </div>
    </div>

    <div class="col-md-6 mb-3">
      <x-form.label value="Barang" />
      <div wire:ignore>
        <select name="barang_id" id="barang_id" class="form-select" placeholder="Cari atau pilih barang"></select>
      </div>

      @error('barang_stock')
        <div class="text-danger mb-3">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="row border pt-3 mb-3 mx-0 rounded">
    <div class="col-md mb-3">
      <x-form.label value="Jumlah" />
      <x-form.input name="jumlah_barang_dipesan" type="number" placeholder="Input jumlah barang..."
        wire:model.live.debounce.300ms="jumlah_barang_dipesan" />
    </div>

    <div class="col-md mb-3">
      <x-form.label value="Satuan" />
      <x-form.input name="satuan" :readonly="true" wire:model="satuan" />
    </div>

    <div class="col-md-3 mb-3">
      <x-form.label value="Batch" />
      <x-form.input name="batch" :readonly="true" wire:model="batch" />
    </div>

    <div class="col-md-3 mb-3">
      <x-form.label value="Expired" />
      <x-form.input name="tgl_expired_formatter" :readonly="true" wire:model="tgl_expired_formatter" />
    </div>

    <div class="col-md mb-3">
      <x-form.label value="Stock" />
      <x-form.input name="stock" :readonly="true" wire:model="stock" />
    </div>

    <div class="col-md mb-3">
      <x-form.label value="Stock All" />
      <x-form.input name="stock_all" :readonly="true" wire:model="stock_all" />
    </div>
  </div>


  <button type="button" class="btn btn-primary" wire:click="save">
    Tambah penjualan

    <span wire:loading>
      <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
      <span class="visually-hidden" role="status">Loading...</span>
    </span>
  </button>


</form>


@script
  <script>
    let brandSelectEl = new TomSelect('#brand_id', {
      onChange(value) {
        Livewire.dispatch('SampleKeluar.TambahSampleBarang:onBrandChange', {
          id: value
        });
      }
    });

    let barangSelectEl = new TomSelect('#barang_id', {
      onChange(value) {
        Livewire.dispatch('SampleKeluar.TambahSampleBarang:onBarangChange', {
          id: value
        });
      }
    });

    Livewire.on('SampleKeluar.TambahSampleBarang:created', () => {
      brandSelectEl.clear()
      barangSelectEl.clear()
      barangSelectEl.clearOptions()
    })

    Livewire.on('SampleKeluar.TambahSampleBarang:brandChanged', ([res]) => {
      barangSelectEl.clear();
      barangSelectEl.clearOptions();

      let options = res?.data || [];
      options = options.map(item => ({
        value: item.id,
        text: item.nama
      }));

      barangSelectEl.addOptions(options);
    });
  </script>
@endscript
