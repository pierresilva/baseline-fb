<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

error_reporting(E_ALL & ~E_USER_NOTICE);

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
    if ($this->app->isLocal()) {
      $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
    }
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    Schema::defaultStringLength(255);
    setlocale(LC_ALL, "es_ES");
    \Carbon\Carbon::setLocale(config('app.locale'));

    // Add Pagenate to Collectoin
    if (!Collection::hasMacro('paginate')) {

      Collection::macro(
        'paginate',
        function ($perPage = 15, $page = null, $options = []) {
          $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
          return (new LengthAwarePaginator(
            $this->forPage($page, $perPage),
            $this->count(),
            $perPage,
            $page,
            $options
          ))
            ->withPath('');
        }
      );
    }

    // Recursive array search
    if (!Collection::hasMacro('recursive')) {
      Collection::macro('recursive', function () {
        return $this->map(function ($value) {
          if (is_array($value)) {
            return collect($value)->recursive();
          }
          if (is_object($value)) {
            return collect($value)->recursive();
          }

          return $value;
        });
      });
    }
  }
}
