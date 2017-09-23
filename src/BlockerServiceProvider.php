<?php

namespace VoyagerBlocker;

use Illuminate\Events\Dispatcher;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class BlockerServiceProvider extends \Illuminate\Support\ServiceProvider
{
	private $models = [
		'Poll',
		'PollAnswer',
		'PollQuestion'
	];

	public function register()
	{
		define('VOYAGER_BLOCKER_PATH', __DIR__.'/..');
		
		app(Dispatcher::class)->listen('voyager.admin.routing', [$this, 'addBlockerRoutes']);
		app(Dispatcher::class)->listen('voyager.menu.display', [$this, 'addBlockerMenuItem']);
	}

	public function boot(\Illuminate\Routing\Router $router, Dispatcher $events)
	{
		$this->pollRoutesAPI($router);
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'polls');
		$this->loadModels();
	}

	public function addBlockerRoutes($router)
    {
        $namespacePrefix = '\\VoyagerBlocker\\Http\\Controllers\\';
        $router->get('blocker', ['uses' => $namespacePrefix.'BlockerController@browse', 'as' => 'blocker']);
    	$router->put('blocker', ['uses' => $namespacePrefix.'BlockerController@update', 'as' => 'blocker.update']);
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
	                'icon_class' => 'voyager-bar-chart',
	                'color'      => null,
	                'parent_id'  => null,
	                'order'      => 99,
	            ]));
	            $this->ensurePermissionExist();
	            $this->addPollsTable();
	        }
	    }
	}

	private function loadModels(){
		foreach($this->models as $model){
			$namespacePrefix = 'VoyagerBlocker\\Models\\';
			if(!class_exists($namespacePrefix . $model)){
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

        foreach($permissions as $permission){
	        if (!$permission->exists) {
	            $permission->save();
	            $role = Role::where('name', 'admin')->first();
	            if (!is_null($role)) {
	                $role->permissions()->attach($permission);
	            }
	        }
	    }
    }

    private function addPollsTable(){
    	if(!Schema::hasTable('voyager_polls')){

    		Schema::create('voyager_blocker', function (Blueprint $table) {
	            $table->increments('id');
				$table->json('ips');
				$table->timestamps();
	        });

	    }
    }
}
