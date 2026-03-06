   <header class="header header-sticky p-0 mb-3">
     <div class="container-fluid border-bottom px-4">
       <button class="header-toggler" type="button"
         style="margin-inline-start: -14px;">
         <i class="bi bi-list"></i>
       </button>

       <ul class="header-nav ms-auto">
         <li class="nav-item dropdown">
           <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button" aria-expanded="false"
             data-coreui-toggle="dropdown">
             <svg class="icon icon-xl theme-icon-active">

               <use xlink:href="{{ asset('vendor/coreui/icon/free.svg#cil-contrast') }}"></use>
             </svg>
           </button>

           <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
             <li>
               <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="light">
                 <svg class="icon icon-xl me-3 theme-icon">
                   <use xlink:href="{{ asset('vendor/coreui/icon/free.svg#cil-sun') }}"></use>
                 </svg>Light
               </button>
             </li>
             <li>
               <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="dark">
                 <svg class="icon icon-xl me-3 theme-icon">
                   <use xlink:href="{{ asset('vendor/coreui/icon/free.svg#cil-moon') }}"></use>
                 </svg>Dark
               </button>
             </li>
             <li>
               <button class="dropdown-item d-flex align-items-center active" type="button"
                 data-coreui-theme-value="auto">
                 <svg class="icon icon-xl me-3 theme-icon">
                   <use xlink:href="{{ asset('vendor/coreui/icon/free.svg#cil-contrast') }}"></use>
                 </svg>Auto
               </button>
             </li>
           </ul>
         </li>

         <li class="nav-item py-1">
           <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
         </li>

         <li class="nav-item dropdown">
           <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button"
             aria-haspopup="true" aria-expanded="false">
             <div class="d-flex align-items-center gap-2 py-2">
               <i class="bi bi-person-circle"></i>
               <span>{{ Auth::user()->name }}</span>
             </div>
           </a>

           <div class="dropdown-menu dropdown-menu-end pt-2">
             <div class="px-3">
               <p class="mb-0 fw-semibold">&commat;{{ Auth::user()->username }}</p>
               <p class="mb-0 small">{{ Auth::user()->email }}</p>
               <p class="mb-0 small badge text-bg-primary">{{ Auth::user()->role->name }}</p>
             </div>

             {{-- <div class="dropdown-divider"></div> --}}
             {{-- <a class="dropdown-item" href="#">
               <svg class="icon me-2">
                 <use xlink:href="{{ asset('vendor/coreui/icon/free.svg#cil-user') }}"></use>
               </svg> Profile
             </a> --}}
             {{-- <a class="dropdown-item" href="#">
               <svg class="icon me-2">
                 <use xlink:href="{{ asset('vendor/coreui/icon/free.svg#cil-settings') }}"></use>
               </svg> Settings
             </a> --}}

             <div class="dropdown-divider"></div>
             <div>
               <form action="{{ route('logout') }}" method="post" onsubmit="return confirm('Logout dari sistem?')">
                 @csrf
                 <button class="dropdown-item" type="submit">
                   <svg class="icon me-2">
                     <use xlink:href="{{ asset('vendor/coreui/icon/free.svg#cil-account-logout') }}"></use>
                   </svg> Logout
                 </button>
               </form>
             </div>
           </div>
         </li>
       </ul>
     </div>

   </header>

@push('scripts')
<script>
   // Function to toggle the sidebar visibility and backdrop
    function toggleSidebar() {
      const sidebar = document.querySelector('#sidebar');
      const isMobile = window.innerWidth <= 768; // Check if it's mobile screen (adjust as needed)
      const body = document.querySelector('body');
    
      if (isMobile) {
        // On mobile, toggle the sidebar and backdrop
        if (sidebar.classList.contains('show')) {
          // If the sidebar is already shown, hide it and remove the backdrop
          sidebar.classList.remove('show');
          sidebar.classList.add('hide');
          const backdrop = document.querySelector('.sidebar-backdrop');
          if (backdrop) {
            backdrop.remove(); // Remove the backdrop
          }
        } else {
          // If the sidebar is hidden, show it and add the backdrop
          sidebar.classList.add('show');
          sidebar.classList.remove('hide');
          
          // Create the backdrop element
          const backdrop = document.createElement('div');
          backdrop.classList.add('sidebar-backdrop', 'fade', 'show');
          body.appendChild(backdrop);
    
          // Close the sidebar if the backdrop is clicked
          backdrop.addEventListener('click', closeSidebar);
        }
      } else {
        // On desktop, toggle the sidebar normally (without backdrop)
        sidebar.classList.toggle('hide');
        sidebar.classList.toggle('show');
      }
    }
    
    // Function to close the sidebar and remove the backdrop
    function closeSidebar() {
      const sidebar = document.querySelector('#sidebar');
      const backdrop = document.querySelector('.sidebar-backdrop');
      
      // Hide the sidebar
      sidebar.classList.remove('show');
      sidebar.classList.add('hide');
    
      // Remove the backdrop if it exists
      if (backdrop) {
        backdrop.remove();
      }
    }
    
    // Attach event listener to the toggle button
    document.querySelector('.header-toggler').addEventListener('click', toggleSidebar);
    document.querySelector('.close-sidebar').addEventListener('click', closeSidebar);
</script>
@endpush