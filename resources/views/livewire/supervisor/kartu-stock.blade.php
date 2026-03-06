<div>
  <form action="{{ route('supervisor.mutation.kartu-stock') }}" method="GET" autocomplete="off">

    <div class="row">
      <div class="col-md-4 mb-3" wire:ignore>
        <x-form.label value="Brand" />
        <x-form.select name="brand_id" placeholder="Cari atau pilih brand" :options="$brands" valueKey="id" labelKey="nama"
          :selected="$brand_id" />
      </div>

      <div class="col-md-4 mb-3" wire:ignore>
        <x-form.label value="Barang" />
        <x-form.select name="barang_id" placeholder="Cari atau pilih barang" :options="$barangs" valueKey="id"
          labelKey="nama" :selected="$barang_id" />
      </div>

      <div class="col-md-4 mb-3">
        <x-form.label value="Brand" />
        <x-form.input name="brand" readonly :value="$brand" />
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 mb-3">
        <x-form.label value="Tanggal awal" />
        <x-form.input name="tgl_awal" type="date" :value="$tgl_awal" />
      </div>
      <div class="col-md-4 mb-3">
        <x-form.label value="Tanggal akhir" />
        <x-form.input name="tgl_akhir" type="date" :value="$tgl_akhir" />
      </div>
    </div>

    <button type="submit" class="btn btn-primary">
      Lihat Kartu Stock

      <span wire:loading>
        <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
        <span class="visually-hidden" role="status">Loading...</span>
      </span>
    </button>
  </form>
</div>


@script
  <script>
    const setBarangRelatedInput = (brand) => {
      document.getElementById('brand').value = brand || '';
    }

    new TomSelect('#brand_id', {
      onChange(value) {
        Livewire.dispatch('KartuStock:onBrandChange', {
          id: value
        });
      }
    });

    let barangSelectEl = new TomSelect('#barang_id', {
      onChange(value) {
        Livewire.dispatch('KartuStock:onBarangChange', {
          id: value
        });
      }
    });

    Livewire.on('KartuStock:barangChanged', ([res]) => {
      const barang = res?.data || {};
      console.log(barang);
      setBarangRelatedInput(barang?.brand?.nama);
    });

    Livewire.on('KartuStock:brandChanged', ([res]) => {
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
