<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
  <div class="sidebar-header border-bottom">
    <div class="sidebar-brand">
      <strong class="sidebar-brand-full">ENTRO-MPE</strong>
      <strong class="sidebar-brand-narrow">ENTRO MPE</strong>
    </div>
    <button class="btn-close d-lg-none close-sidebar" type="button" data-coreui-theme="dark"></button>
  </div>

  <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('dashboard.index') }}">
        <span class="nav-icon">
          <i class="bi bi-house-door-fill"></i>
        </span>
        Dashboard
      </a>
    </li>

    @role('su')
      @include('components/sidebar_superadmin')
    @endrole

    @role('as')
      @include('components/sidebar_supervisor')
    @endrole

    @role('af')
      @include('components/sidebar_fakturis')
    @endrole

    @role('ag')
      @include('components/sidebar_gudang')
    @endrole

    @role('aw')
      @include('components/sidebar_warehouse ')
    @endrole

    @role('ak')
      @include('components/sidebar_keuangan')
    @endrole

    @role('aa')
      @include('components/sidebar_accounting')
    @endrole

    @role('al')
      @include('components/sidebar_logistik')
    @endrole

    @role('ap')
      @include('components/sidebar_pajak')
    @endrole

  </ul>
  <div class="sidebar-footer border-top d-none d-md-flex justify-content-end">
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
  </div>
</div>
