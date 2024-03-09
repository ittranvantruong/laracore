<?php

namespace App\Admin\Repositories\Menu;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface MenuItemRepositoryInterface extends EloquentRepositoryInterface
{
    public function deleteBy(array $filter);
}