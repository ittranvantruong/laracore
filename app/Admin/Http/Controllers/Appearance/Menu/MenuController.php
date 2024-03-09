<?php

namespace App\Admin\Http\Controllers\Appearance\Menu;

use App\Admin\DataTables\Menu\MenuDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Menu\MenuRequest;
use App\Admin\Http\Resources\Menu\MenuItemToTreeResource;
use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use App\Admin\Repositories\Menu\MenuRepositoryInterface;
use App\Admin\Repositories\Page\PageRepositoryInterface;
use App\Admin\Services\Menu\MenuServiceInterface;
use App\Enums\DefaultStatus;

class MenuController extends Controller
{
    protected $repoCategory;

    protected $repoPage;

    public function __construct(
        MenuRepositoryInterface $repository, 
        CategoryRepositoryInterface $repoCategory, 
        PageRepositoryInterface $repoPage, 
        MenuServiceInterface $service
    )
    {
        parent::__construct();

        $this->repository = $repository;
        $this->repoCategory = $repoCategory;
        $this->repoPage = $repoPage;
        $this->service = $service;
    }

    public function getView()
    {
        return [
            'index' => 'admin.appearances.menus.index',
            'create' => 'admin.appearances.menus.create',
            'edit' => 'admin.appearances.menus.edit',
        ];
    }

    public function getRoute()
    {
        return [
            'index' => 'admin.appearance.menu.index',
            'edit' => 'admin.appearance.menu.edit',
        ];
    }

    public function index(MenuDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrums' => $this->crums->add(__('appearance'))->add(__('menu'))
        ]);
    }

    public function create(){
        return view($this->view['create'], [
            'status' => DefaultStatus::asSelectArray(),
            'breadcrums' => $this->crums->add(__('appearance'))->add(__('menu'), route('admin.appearance.menu.index'))->add(trans('add'))
        ]);
    }

    public function store(MenuRequest $request){

        $response = $this->service->store($request);

        if($response){

            return to_route($this->route['edit'], $response->id);
        }

        return back()->with('error', __('notifyFail'));
    }

    public function edit($id) {

        $menu = $this->repository->findOrFail($id, ['locations', 'items.reference']);
        // $menu->items()->create([
        //     'url' => '/about-us',
        //     'title' => 'About us'
        // ]);
        $menuItems = new MenuItemToTreeResource($menu->items->toTree());

        $categories = $this->repoCategory->getFlatTree();

        $pages = $this->repoPage->getAll();

        $menuLocaltions = config('custom.menu.locations');
        
        return view($this->view['edit'], [
            'menu' => $menu,
            'menu_items' => $menuItems,
            'status' => DefaultStatus::asSelectArray(),
            'categories' => $categories,
            'pages' => $pages,
            'menu_locations' => $menuLocaltions
        ]);
    }

    public function update(MenuRequest $request){

        $response = $this->service->update($request);

        if($response){
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'))->withInput();
    }

    public function delete($id){

        $this->service->delete($id);

        return to_route($this->route['index'])->with('success', __('notifySuccess'));
    }
}