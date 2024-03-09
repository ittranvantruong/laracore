<?php

namespace App\Admin\DataTables\Menu;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Menu\MenuRepositoryInterface;
use App\Enums\DefaultStatus;

class MenuDataTable extends BaseDataTable
{

    protected $nameTable = 'menuTable';

    public function __construct(
        MenuRepositoryInterface $repository
    ){
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(){
        $this->view = [
            'action' => 'admin.appearances.menus.datatable.action',
            'edit_link' => 'admin.appearances.menus.datatable.edit-link',
            'status' => 'admin.appearances.menus.datatable.status'
        ];
    }

    public function setColumnSearch(){

        $this->columnAllSearch = [0, 1, 2];

        $this->columnSearchDate = [2];

        $this->columnSearchSelect = [
            [
                'column' => 1,
                'data' => DefaultStatus::asSelectArray()
            ]
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy();
    }

    protected function setCustomColumns(){
        $this->customColumns = config('datatables_columns.menu', []);
    }

    protected function setCustomEditColumns(){
        $this->customEditColumns = [
            'name' => $this->view['edit_link'],
            'status' => $this->view['status'],
            'created_at' => '{{ format_date($created_at) }}'
        ];
    }

    protected function setCustomAddColumns(){
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(){
        $this->customRawColumns = ['name', 'status', 'action'];
    }
}
