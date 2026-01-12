<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Session;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    protected $namespace_admin = 'App\Http\Controllers\Admin';
    protected $locale = '';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */

    const HOME = '/home';
    const ADMIN = '/dashboard';
    const MEMBER = '/member';

    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(Route $route, Request $request)
    {
        $locale = $request->segment(1);
        $current_locale = App::currentLocale();
        $list_locale = config('app.locales');
        $fallback_locale = config('app.fallback_locale');

        if (is_null($locale) || !in_array($locale, $list_locale)) {
            $locale = $current_locale ?: $fallback_locale;
        }
        App::setLocale($locale);

        if ($locale == $fallback_locale) {
            $locale = '';
        }
        $this->locale = $locale;

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //$this->mapAdminRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::prefix($this->locale)
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapAdminRoutes()
    {
        Route::prefix($this->locale . '/' . config('cms.prefix_admin'))
            ->middleware(['web', 'auth'])
            ->namespace($this->namespace_admin)
            ->group(base_path('routes/admin.php'));
    }
}
