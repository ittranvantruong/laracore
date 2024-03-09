<?php

namespace App\Admin\Repositories\Menu;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Menu\MenuLocationRepositoryInterface;
use App\Models\MenuLocation;

class MenuLocationRepository extends EloquentRepository implements MenuLocationRepositoryInterface
{
    public function getModel(){
        return MenuLocation::class;
    }
    
    public function updateOrCreate(array $compare, array $data){
        $this->instance = $this->model->updateOrCreate($compare, $data);
        return $this->instance;
    }
}