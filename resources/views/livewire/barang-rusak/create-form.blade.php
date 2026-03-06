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
      <div class="col-md-4 mb-3" wire:ignore>
        <x-form.label value="Barang" />
        <select name="barang" id="barang" placeholder="Pilih Barang" class="form-select"></select>
      </div>

      <div class="col-md-4 mb-3">
        <x-form.label value="Satuan" />
        <input type="text" class="form-control" name="satuan" id="satuan" readonly>
      </div>

      <div class="col-md-4 mb-3">
        <x-form.label value="Brand" />
        <input type="text" class="form-control" name="brand" id="brand" readonly>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-md-4 mb-3">
      <x-form.label value="Jumlah" />
      <x-form.input name="jumlah_barang_rusak" type="number" placeholder="Input jumlah barang rusak..."
        wire:model="jumlah_barang_rusak" />
    </div>
    <div class="col-md-4 mb-3">
      <x-form.label value="Batch" optional />
      <x-form.input name="batch" placeholder="Input batch..." wire:model="batch" />
    </div>
    <div class="col-md-4 mb-3">
      <x-form.label value="Expired" optional />
      <x-form.input name="tgl_expired" wire:model="tgl_expired" type="date" placeholder="Input expired..." />
    </div>
  </div>

  <div class="row">
    {{-- <div class="col-md-4 mb-3">
      <x-form.label value="Tanggal" />
      <x-form.input name="tgl_rusak" wire:model="tgl_rusak" type="date" placeholder="Input tanggal..." />
    </div> --}}
    <div class="col-md-4 mb-3">
      <div wire:ignore>
        <x-form.label value="Penyebab" />
        <x-form.select name="penyebab" placeholder="Pilih Penyebab" :options="$penyebabs" valueKey="key" labelKey="value"
          wire:model="penyebab" />
      </div>

      @error('penyebab')
        <div class="text-danger mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="col-md-4 mb-3">
      <div wire:ignore>
        <x-form.label value="Tindakan" />
        <x-form.select name="tindakan" placeholder="Pilih Tindakan" :options="$tindakans" valueKey="key" labelKey="value"
          wire:model="tindakan" />
      </div>

      @error('tindakan')
        <div class="text-danger mt-1">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="mb-3">
    <x-form.label value="Keterangan" optional />
    <x-form.input name="keterangan" placeholder="Input keterangan..." wire:model="keterangan" />
  </div>

  <button type="submit" class="btn btn-primary">
    Simpan

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

    new TomSelect('#penyebab');
    new TomSelect('#tindakan');
    new TomSelect('#brand_id', {
      onChange(value) {
        Livewire.dispatch('BarangRusak.CreateForm:onBrandChange', {
          id: value
        });
      }
    });

    let barangSelectEl = new TomSelect('#barang', {
      onChange(value) {
        Livewire.dispatch('BarangRusak.CreateForm:onBarangChange', {
          id: value
        });
      }
    });

    Livewire.on('BarangRusak.CreateForm:barangChanged', ([res]) => {
      const barang = res?.data || {};
      setBarangRelatedInput(barang.satuan, barang?.brand?.nama);
    });

    Livewire.on('BarangRusak.CreateForm:brandChanged', ([res]) => {
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
