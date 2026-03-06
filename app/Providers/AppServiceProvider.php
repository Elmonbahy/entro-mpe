<?php

namespace App\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Route;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // Tambahkan ini paling atas sebelum Livewire dan Blade

    // if (file_exists(storage_path('framework/maintenance.flag'))) {
    //   abort(503, 'Situs sedang dalam pemeliharaan.');
    // }

    Livewire::setUpdateRoute(function ($handle) {
      return Route::post('/apm/livewire/update', $handle)
        ->middleware(['web', 'auth']);
    });

    // Define the @roles directive
    Blade::directive('roles', function ($rolesSlug) {
      return "<?php if(auth()->user() && auth()->user()->hasAnyRole(" . $rolesSlug . ")) : ?>";
    });

    // Define the @endroles directive
    Blade::directive('endroles', function () {
      return "<?php endif; ?>";
    });

    Blade::directive('role', function ($roleSlug) {
      return "<?php if(auth()->user() && auth()->user()->role->slug == {$roleSlug}) : ?>";
    });

    Blade::directive('endrole', function () {
      return "<?php endif; ?>";
    });
  }
}
