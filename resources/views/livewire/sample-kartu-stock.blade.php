<div>
  <form action="{{ route('fakturis.sample-mutation.kartu-stock') }}" method="GET" autocomplete="off">

    <div class="row">
      <div class="col-md-4 mb-3" wire:ignore>
        <x-form.label value="Brand" />
        <x-form.select id="brand_id" name="brand_id" placeholder="Cari atau pilih brand" :options="$brands" valueKey="id"
          labelKey="nama" :selected="$brand_id" />
      </div>

      <div class="col-md-4 mb-3" wire:ignore>
        <x-form.label value="Barang" />
        <x-form.select id="barang_id" name="barang_id" placeholder="Cari atau pilih barang" :options="$barangs"
          valueKey="id" labelKey="nama" :selected="$barang_id" />
      </div>

      <div class="col-md-4 mb-3">
        <x-form.label value="Brand Terkait" />
        <x-form.input id="brand" name="brand" readonly :value="$brand" />
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
        <span class="spinner-grow spinner-grow-sm"></span>
        <span class="visually-hidden">Loading...</span>
      </span>
    </button>
  </form>
</div>

@script
  <script>
    const setBrandInput = (namaBrand = '') => {
      const el = document.getElementById('brand');
      if (el) el.value = namaBrand;
    }

    const brandSelect = new TomSelect('#brand_id', {
      onChange(value) {
        Livewire.dispatch('SampleKartuStock:onBrandChange', {
          id: value
        });
      }
    });

    const barangSelect = new TomSelect('#barang_id', {
      onChange(value) {
        Livewire.dispatch('SampleKartuStock:onBarangChange', {
          id: value
        });
      }
    });

    Livewire.on('SampleKartuStock:brandChanged', ([res]) => {
      setBrandInput();
      barangSelect.clear();
      barangSelect.clearOptions();

      let options = res?.data || [];
      options = options.map(item => ({
        value: item.id,
        text: item.nama
      }));
      barangSelect.addOptions(options);
    });

    Livewire.on('SampleKartuStock:barangChanged', ([res]) => {
      const barang = res?.data || {};
      setBrandInput(barang?.barang?.brand?.nama || '');
    });

    // ✅ Tambahkan bagian ini:
    document.addEventListener('DOMContentLoaded', () => {
      const selectedBrand = @json($brand_id);
      const selectedBarang = @json($barang_id);
      const brandList = @json($brands);
      const barangList = @json($barangs);

      // restore daftar options dari server
      brandList.forEach(item => brandSelect.addOption({
        value: item.id,
        text: item.nama
      }));
      barangList.forEach(item => barangSelect.addOption({
        value: item.id,
        text: item.nama
      }));

      // set kembali nilai yang dipilih
      if (selectedBrand) brandSelect.setValue(selectedBrand, true);
      if (selectedBarang) barangSelect.setValue(selectedBarang, true);
    });
  </script>
@endscript
