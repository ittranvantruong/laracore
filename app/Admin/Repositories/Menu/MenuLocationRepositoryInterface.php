<?php

namespace App\Admin\Repositories\Menu;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface MenuLocationRepositoryInterface extends EloquentRepositoryInterface
{
    public function updateOrCreate(array $compare, array $data);
}