<?php

namespace VoyagerBlocker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use VoyagerBlocker\Http\Middleware\VoyagerBlockerMiddleware;

class VoyagerBlockerServiceProvider extends ServiceProvider
{
    private $models = [
        'Blocker',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blocker');
        $this->loadModels();

        // Add the redirect middleware that will handle all redirects
        $this->app['Illuminate\Contracts\Http\Kernel']->prependMiddleware(VoyagerBlockerMiddleware::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        define('VOYAGER_BLOCKER_PATH', __DIR__.'/..');

        app(Dispatcher::class)->listen('voyager.admin.routing', [$this, 'addBlockerRoutes']);
        app(Dispatcher::class)->listen('voyager.menu.display', [$this, 'addBlockerMenuItem']);
    }

    public function addBlockerRoutes($router)
    {
        $namespacePrefix = '\\VoyagerBlocker\\Http\\Controllers\\';
        $router->get('blocker', ['uses' => $namespacePrefix.'VoyagerBlockerController@browse', 'as' => 'blocker']);
        $router->put('blocker', ['uses' => $namespacePrefix.'VoyagerBlockerController@update', 'as' => 'blocker.update']);
    }

    public function addBlockerMenuItem(Menu $menu)
    {
        if ($menu->name == 'admin') {
            $url = route('voyager.blocker', [], false);
            $menuItem = $menu->items->where('url', $url)->first();
            if (is_null($menuItem)) {
                $menu->items->add(MenuItem::create([
                    'menu_id'    => $menu->id,
                    'url'        => $url,
                    'title'      => 'Blocker',
                    'target'     => '_self',
                    'icon_class' => 'voyager-lock',
                    'color'      => null,
                    'parent_id'  => null,
                    'order'      => 99,
                ]));
                $this->ensurePermissionExist();
                $this->addBlockerTable();
            }
        }
    }

    private function loadModels()
    {
        foreach ($this->models as $model) {
            $namespacePrefix = 'VoyagerBlocker\\Models\\';
            if (!class_exists($namespacePrefix . $model)) {
                @include(__DIR__.'/Models/' . $model . '.php');
            }
        }
    }

    protected function ensurePermissionExist()
    {
        $permissions = [
            Permission::firstOrNew(['key' => 'browse_blocker', 'table_name' => 'blocker']),
            Permission::firstOrNew(['key' => 'edit_blocker', 'table_name' => 'blocker']),
        ];

        foreach ($permissions as $permission) {
            if (!$permission->exists) {
                $permission->save();
                $role = Role::where('name', 'admin')->first();
                if (!is_null($role)) {
                    $role->permissions()->attach($permission);
                }
            }
        }
    }

    private function addBlockerTable()
    {
        if (!Schema::hasTable('voyager_blocker')) {
            Schema::create('voyager_blocker', function (Blueprint $table) {
                $table->increments('id');
                $table->json('ips');
                $table->timestamps();
            });

        }
    }
}
