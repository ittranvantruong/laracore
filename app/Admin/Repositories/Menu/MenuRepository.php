<?php

namespace App\Admin\Repositories\Menu;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Menu\MenuRepositoryInterface;
use App\Models\Menu;

class MenuRepository extends EloquentRepository implements MenuRepositoryInterface
{
    public function getModel(){
        return Menu::class;
    }
}