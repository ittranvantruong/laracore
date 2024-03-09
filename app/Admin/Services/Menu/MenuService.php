<?php

namespace App\Admin\Services\Menu;

use App\Admin\Repositories\Menu\MenuItemRepositoryInterface;
use App\Admin\Repositories\Menu\MenuLocationRepositoryInterface;
use App\Admin\Services\Menu\MenuServiceInterface;
use  App\Admin\Repositories\Menu\MenuRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MenuService implements MenuServiceInterface
{
    /**
     * Current Object instance
     *
     * @var array
     */
    protected $data;
    
    protected $repository;
    protected $repoMenuItem;
    protected $repoMenuLocation;

    public function __construct(
        MenuRepositoryInterface $repository,
        MenuItemRepositoryInterface $repoMenuItem,
        MenuLocationRepositoryInterface $repoMenuLocation
    ){
        $this->repository = $repository;
        $this->repoMenuItem = $repoMenuItem;
        $this->repoMenuLocation = $repoMenuLocation;
    }
    
    public function store(Request $request){

        $this->data = $request->validated();

        return $this->repository->create($this->data);
    }

    public function update(Request $request){
        
        
        $data = $request->validated();
        $data['locations'] = $data['locations'] ?? [];
        $collect_menu_items = collect(json_decode($data['json_menu_items'], true));

        DB::beginTransaction();
        try {

            $menu = $this->repository->update($data['menu']['id'], $data['menu'])->load(['locations']);

            $this->handleMenuLocation($menu, $data);

            $newIds = $this->handleAddAndRemoveMenuItem($menu, $data);

            $this->handleUpdateMenuItem($collect_menu_items, $newIds, $data);
            
            DB::commit();
            
            // Cache::forget(Menu::CACHE_KEY_GET_ALL);
            
            return $menu;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return false;
        }
    }

    public function delete($id){
        return $this->repository->delete($id);

    }

    private function handleUpdateMenuItem($collect_menu_items, $newIds, $data)
    {
        $collect_menu_items = $collect_menu_items->map(function ($item, $key) use ($newIds, $data) {

            if (in_array($item['id'], array_keys($data['reference_id'])) || gettype($item['id']) == 'string') {

                $dataMenuItem['id'] = $newIds[$item['id']] ?? $item['id'];
                $dataMenuItem['parent_id'] = $newIds[$item['parent_id']] ?? $item['parent_id'];
                $dataMenuItem['position'] = $key;
                $dataMenuItem['_lft'] = $item['lft'];
                $dataMenuItem['_rgt'] = $item['rgt'];

                if (gettype($item['id']) == 'integer') {

                    $dataMenuItem['title'] = $data['title'][$item['id']];

                    if ($data['reference_type'][$item['id']] == null) {

                        $dataMenuItem['url'] = $data['url'][$item['id']];
                    }
                }
                $this->repoMenuItem->update($dataMenuItem['id'], $dataMenuItem);
            }
        });
    }

    private function handleAddAndRemoveMenuItem($menu, $data)
    {
        $newIds = [];
        if (isset($data['reference_id']) && !empty($data['reference_id'])) {

            $this->repoMenuItem->deleteBy(['menu_id' => $menu->id, ['id', 'not_in', array_keys($data['reference_id'])]]);

            foreach ($data['reference_id'] as $key => $value) {
                if (gettype($key) == 'string') {
                    $newItems = $menu->items()->create([
                        'reference_id' => $value,
                        'reference_type' => $data['reference_type'][$key],
                        'url' => $data['url'][$key] ?? null,
                        'title' => $data['title'][$key]
                    ]);
                    $newIds[$key] = $newItems->id;
                }
            }
        }
        return $newIds;
    }

    private function handleMenuLocation($menu, $data){

        if ($menu->locations) {

            foreach ($menu->locations as $key => $item) {

                if (in_array($item->location, $data['locations'])) {

                    unset($data['locations'][$key]);
                } else {
                    $item->delete();
                }
            }
        }

        if (!empty($data['locations'])) {

            foreach ($data['locations'] as $location) {

                $this->repoMenuLocation->updateOrCreate(['location' => $location], [
                    'menu_id' => $menu->id,
                    'name' => config('custom.menu.locations.' . $location)
                ]);
            }
        }
    }
}