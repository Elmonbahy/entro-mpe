<form wire:submit.prevent>

  <div class="row">
    <div class="col-md-4 mb-3" wire:ignore>
      <x-form.label value="Brand" />
      <div>
        <x-form.select name="brand_id" placeholder="Cari atau pilih brand" :options="$brands" valueKey="id"
          labelKey="nama" />
      </div>
    </div>

    <div class="col-md-4 mb-3" wire:ignore>
      <x-form.label value="Barang" />
      <div>
        <select name="barang_id" id="barang_id" class="form-select" placeholder="Cari atau pilih barang"></select>
      </div>
    </div>

    <div class="col-md-2 mb-3">
      <x-form.label value="Harga terakhir" />
      <div class="input-group">
        <span class="input-group-text bg-light border-end-0">Rp</span>
        <x-form.input name="harga_beli_terakhir" :readonly="true" wire:model="harga_beli_terakhir" />
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4 mb-3">
      <x-form.label value="Batch" optional />
      <x-form.input name="batch" placeholder="Input batch..." wire:model="batch" />
    </div>
    <div class="col-md-4 mb-3">
      <x-form.label value="Expired" optional />
      <x-form.input name="tgl_expired" wire:model="tgl_expired" type="date" placeholder="Input expired..." />
    </div>
    <div class="col-md-4 mb-3 ">
      <x-form.label value="Keterangan" optional />
      <x-form.input name="keterangan" wire:model="keterangan" />
    </div>
  </div>

  <div class="row border pt-3 mb-3 mx-0 rounded">
    <div class="col-md mb-3">
      <x-form.label value="Harga beli" />
      <div class="input-group">
        <span class="input-group-text">Rp</span>
        <x-form.input name="harga_beli" type="text" placeholder="Input harga beli..."
          wire:model.live.lazy="harga_beli" />
      </div>
    </div>
    <div class="col-md mb-3">
      <x-form.label value="Jumlah" />
      <x-form.input name="jumlah_barang_dipesan" type="number" placeholder="Input jumlah beli..."
        wire:model.live.debounce.300ms="jumlah_barang_dipesan" />
    </div>
    <div class="col-md mb-3">
      <x-form.label value="Satuan" />
      <x-form.input name="satuan" :readonly="true" wire:model="satuan" />
    </div>
  </div>

  <button type="button" class="btn btn-primary" wire:click="save">
    Tambah pembelian

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
        Livewire.dispatch('Beli.TambahPembelianBarang:onBrandChange', {
          id: value
        });
      }
    });

    let barangSelectEl = new TomSelect('#barang_id', {
      valueField: 'value',
      labelField: 'text',
      searchField: ['value', 'text'], // <--- Kuncinya di sini (mencari berdasarkan ID dan Nama)
      onChange(value) {
        Livewire.dispatch('Beli.TambahPembelianBarang:onBarangChange', {
          id: value
        });
      },
      // Opsional: Custom rendering agar tampilan list menunjukkan ID [Nama]
      render: {
        option: function(data, escape) {
          return '<div>' +
            '<span class="fw-bold">' + escape(data.value) + '</span>' +
            ' - ' +
            '<span>' + escape(data.text) + '</span>' +
            '</div>';
        },
        item: function(data, escape) {
          return '<div>' + escape(data.value) + ' - ' + escape(data.text) + '</div>';
        }
      }
    });

    Livewire.on('Beli.TambahPembelianBarang:created', () => {
      brandSelectEl.clear()
      barangSelectEl.clear()
      barangSelectEl.clearOptions()
    })

    Livewire.on('Beli.TambahPembelianBarang:brandChanged', ([res]) => {
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
