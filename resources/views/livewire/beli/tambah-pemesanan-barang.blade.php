<form wire:submit="save" autocomplete="off">
  <div class="row">
    <div class="col-md-3 mb-3" wire:ignore>
      <x-form.label value="Brand" />
      <div>
        <x-form.select name="brand_id" placeholder="Cari atau pilih brand" :options="$brands" valueKey="id"
          labelKey="nama" />
      </div>
    </div>

    <div class="col-md-3 mb-3" wire:ignore>
      <x-form.label value="Barang" />
      <div>
        <select name="barang_id" id="barang_id" class="form-select" placeholder="Cari atau pilih barang"></select>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <x-form.label value="Satuan" />
      <x-form.input name="satuan" :readonly="true" wire:model="satuan" />
    </div>

    <div class="col-md-3 mb-3">
      <x-form.label value="Harga Beli Terahkir" />
      <x-form.input name="harga_beli_formatted" :readonly="true" wire:model="harga_beli_formatted" />
    </div>
  </div>

  <div class="row border pt-3 mb-3 mx-0 rounded">
    <div class="col-md mb-3">
      <x-form.label value="Harga beli" />
      <x-form.input name="harga_beli" type="number" placeholder="Input harga beli..."
        wire:model.live.debounce.300ms="harga_beli" />
    </div>
    <div class="col-md mb-3">
      <x-form.label value="Jumlah" />
      <x-form.input name="jumlah_barang_dipesan" type="number" placeholder="Input jumlah beli..."
        wire:model.live.debounce.300ms="jumlah_barang_dipesan" />
    </div>
    <div class="col-md mb-3 ">
      <x-form.label value="Keterangan" optional />
      <x-form.input name="keterangan" wire:model="keterangan" />
    </div>
  </div>

  <button type="submit" class="btn btn-primary">
    Tambah pemesanan

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
        Livewire.dispatch('Beli.TambahPemesananBarang:onBrandChange', {
          id: value
        });
      }
    });

    let barangSelectEl = new TomSelect('#barang_id', {
      onChange(value) {
        Livewire.dispatch('Beli.TambahPemesananBarang:onBarangChange', {
          id: value
        });
      }
    });

    Livewire.on('Beli.TambahPemesananBarang:created', () => {
      brandSelectEl.clear()
      barangSelectEl.clear()
      barangSelectEl.clearOptions()
    })

    Livewire.on('Beli.TambahPemesananBarang:brandChanged', ([res]) => {
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
