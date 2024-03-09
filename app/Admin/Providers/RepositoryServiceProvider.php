<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected $repositories = [
        'App\Admin\Repositories\Admin\AdminRepositoryInterface' => 'App\Admin\Repositories\Admin\AdminRepository',
        'App\Admin\Repositories\User\UserRepositoryInterface' => 'App\Admin\Repositories\User\UserRepository',
        'App\Admin\Repositories\Setting\SettingRepositoryInterface' => 'App\Admin\Repositories\Setting\SettingRepository',
        'App\Admin\Repositories\Category\CategoryRepositoryInterface' => 'App\Admin\Repositories\Category\CategoryRepository',
        'App\Admin\Repositories\Post\PostRepositoryInterface' => 'App\Admin\Repositories\Post\PostRepository',
        'App\Admin\Repositories\Slider\SliderRepositoryInterface' => 'App\Admin\Repositories\Slider\SliderRepository',
        'App\Admin\Repositories\Slider\SliderItemRepositoryInterface' => 'App\Admin\Repositories\Slider\SliderItemRepository',
        'App\Admin\Repositories\Page\PageRepositoryInterface' => 'App\Admin\Repositories\Page\PageRepository',
        'App\Admin\Repositories\Tag\TagRepositoryInterface' => 'App\Admin\Repositories\Tag\TagRepository',
        'App\Admin\Repositories\Menu\MenuRepositoryInterface' => 'App\Admin\Repositories\Menu\MenuRepository',
        'App\Admin\Repositories\Menu\MenuItemRepositoryInterface' => 'App\Admin\Repositories\Menu\MenuItemRepository',
        'App\Admin\Repositories\Menu\MenuLocationRepositoryInterface' => 'App\Admin\Repositories\Menu\MenuLocationRepository',
        
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        foreach ($this->repositories as $interface => $implement) {
            $this->app->singleton($interface, $implement);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
