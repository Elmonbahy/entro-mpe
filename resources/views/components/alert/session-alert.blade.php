 @if (session('success'))
   <div class="alert alert-success alert-dismissible fade show" role="alert">
     {{ session('success') }}
     <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
   </div>
 @endif

 @if (session('error'))
   <div class="alert alert-danger alert-dismissible fade show" role="alert">
     {{ session('error') }}
     <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
   </div>
 @endif

 <script>
   document.addEventListener('DOMContentLoaded', function() {
     const alertList = document.querySelectorAll('.alert.alert-dismissible')

     if (alertList.length > 0) {
       const alerts = [...alertList].map(element => new coreui.Alert(element))
       // Set timeout to auto-hide only success alerts after a few seconds
       setTimeout(function() {
         alerts.forEach(function(alert) {
           // if the alert has manually close, alert._element will be null
           if (alert._element?.classList.contains('alert-success')) {
             alert.close()
           }
         });
       }, 3000); // 3000ms = 3 seconds
     }
   });
 </script>
