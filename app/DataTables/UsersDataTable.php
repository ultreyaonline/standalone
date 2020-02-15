<?php

namespace App\DataTables;

use App\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build DataTable class for ajax response
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        abort_unless(Auth::user()->can('view members'), '403', 'Unauthorized.');

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
        return $model->newQuery()
            ->select('id', 'first', 'last', 'email', 'weekend', 'community', 'cellphone', 'homephone', 'church')
            ->active();
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
