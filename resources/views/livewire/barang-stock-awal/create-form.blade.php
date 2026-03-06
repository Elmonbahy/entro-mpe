<form wire:submit="save" autocomplete="off">
  <div class="border-bottom mb-4 pb-2" wire:ignore>

    <div class="row">
      <div class="col-md-4 mb-3">
        <x-form.label value="Brand" />
        <x-form.select name="brand_id" placeholder="Cari atau pilih brand" :options="$brands" valueKey="id"
          labelKey="nama" />
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 mb-3">
        <x-form.label value="Barang" />
        <select name="barang" id="barang" placeholder="Pilih Barang" class="form-select"></select>
      </div>

      <div class="col-md-2 mb-3">
        <x-form.label value="Satuan" />
        <input type="text" class="form-control" name="satuan" id="satuan" readonly>
      </div>

      <div class="col-md-2 mb-3">
        <x-form.label value="Brand" />
        <input type="text" class="form-control" name="brand" id="brand" readonly>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-md-4 mb-3">
      <x-form.label value="Jumlah" />
      <x-form.input name="jumlah_stock" type="number" placeholder="Input jumlah stock..." wire:model="jumlah_stock" />
    </div>
    <div class="col-md-4 mb-3">
      <x-form.label value="Batch" :required="$jenis_perubahan === \App\Enums\JenisPerubahan::AWAL->value" />
      <x-form.input name="batch" placeholder="Input batch..." wire:model="batch" />
    </div>
    <div class="col-md-4 mb-3">
      <x-form.label value="Expired" optional />
      <x-form.input name="tgl_expired" wire:model="tgl_expired" type="date" placeholder="Input expired..." />
    </div>
  </div>

  <div class="row">
    <div class="col-md-4 mb-3">
      <x-form.label value="Jenis Perubahan" />
      <x-form.select name="jenis_perubahan" placeholder="pilih jenis perubahan" :options="$jenisPerubahans" valueKey="key"
        labelKey="value" wire:model.live="jenis_perubahan" />
    </div>
    {{-- <div class="col-md-4 mb-3">
      <x-form.label value="Tanggal" />
      <x-form.input name="tgl_stock" type="date" placeholder="Input tangaal..." wire:model="tgl_stock" />
    </div> --}}
    <div class="col-md-4 mb-3">
      <x-form.label value="Keterangan" />
      <x-form.input name="keterangan" placeholder="Input keterangan..." wire:model="keterangan" />
    </div>
  </div>

  <div class="alert alert-info small">
    INFO: semua penyesuaian tercatat di kartu stock.
  </div>

  <button type="submit" class="btn btn-primary">
    Tambah

    <span wire:loading>
      <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
      <span class="visually-hidden" role="status">Loading...</span>
    </span>
  </button>

</form>


@script
  <script>
    const setBarangRelatedInput = (satuan, brand) => {
      document.getElementById('satuan').value = satuan || '';
      document.getElementById('brand').value = brand || '';
    }

    new TomSelect('#jenis_perubahan');
    new TomSelect('#brand_id', {
      onChange(value) {
        Livewire.dispatch('BarangStockAwal.CreateForm:onBrandChange', {
          id: value
        });
      }
    });

    let barangSelectEl = new TomSelect('#barang', {
      onChange(value) {
        Livewire.dispatch('BarangStockAwal.CreateForm:onBarangChange', {
          id: value
        });
      }
    });

    Livewire.on('BarangStockAwal.CreateForm:barangChanged', ([res]) => {
      const barang = res?.data || {};
      setBarangRelatedInput(barang.satuan, barang?.brand?.nama);
    });

    Livewire.on('BarangStockAwal.CreateForm:brandChanged', ([res]) => {
      setBarangRelatedInput();

      barangSelectEl.clear();
      barangSelectEl.clearOptions();

      let options = res?.data || [];
      options = options.map(item => ({
        value: item.id,
        text: item.nama
      }));

      barangSelectEl.addOptions(options);
    })
  </script>
@endscript
