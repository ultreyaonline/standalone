<?php

namespace App\DataTables;

use App\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        // if not allowed to see other members, abort
        if (!Auth::user()->can('view members')) {
            abort(403, 'Unauthorized');
        }

        return datatables($query)
            ->editColumn('id', '{{ $id }}')
            ->setRowId('id')
            ->orderColumn('last', 'last $1, first')
            ->orderColumn('first', 'first $1, last')
            ->editColumn('first', function ($member) {
                return '<a href="'. url('members/' . $member->id) .'">' . $member->first . '</a>';
            })
            ->addColumn('membername', function ($member) {
                return '<a href="'. url('members/' . $member->id) .'">' . $member->name . '</a>';
            })
//            ->addColumn('action', 'members.action')
            ->rawColumns(['first', 'membername']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery()->select('id', 'first', 'last', 'email', 'weekend', 'community', 'cellphone', 'homephone', 'church')
            ->active();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction(['width' => '80px'])
                    ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id',
            'first',
            'last',
            'email',
            'weekend',
            'community',
            'cellphone',
            'homephone',
            'church',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Users_' . date('YmdHis');
    }
}
