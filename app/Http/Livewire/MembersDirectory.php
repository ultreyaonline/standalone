<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MembersDirectory extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public $perPage;
    public $q; // query string for search
    public $sortBy = 'first'; // initial sort field
    public $sortAsc = true; // used for query and icons

    protected $paginationTheme = 'bootstrap';

    protected $queryString = ['q', 'perPage', 'sortBy', 'sortAsc'];

    public function render()
    {
        abort_unless(Auth::check() && Auth::user()->can('view members'), '403', 'Unauthorized.');

        return view('livewire.members-directory', [
            'users' => User::datatableSearch($this->q)
                ->active()
                //->onlyLocal()
                ->select($this->getColumns())
                ->orderBy($this->sortBy, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ]);
    }

    public function searchClear(): void
    {
        $this->reset('q');
    }

    public function sortBy($field): void
    {
        if ($this->sortBy === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortBy = $field;
    }

    /**
     * Get the columns which should be allowed to be returned to the ajax query.
     * (This is to avoid exposing unnecessary information.)
     */
    protected function getColumns(): array
    {
        return [
            'id',
            'first',
            'last',
            'email',
            'weekend',
            'community',

            // include more columns below if needed
            'active',
        ];
    }
}
