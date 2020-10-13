<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MembersAudit extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public $perPage;
    public $q = ''; // query string for search
    public $sortBy = 'last';
    public $sortAsc = true; // used for query and icons

    protected $paginationTheme = 'bootstrap';

    protected $queryString = ['q', 'perPage', 'sortBy', 'sortAsc'];

    public function mount(): void
    {
        $this->perPage = request('perPage', config('site.pagination_threshold', 25));
    }

    public function render()
    {
        abort_unless(Auth::check() && Auth::user()->can('edit members'), '403', 'Unauthorized.');

        return view('livewire.members-audit', [
            'users' => User::datatableSearch($this->q)
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
            'cellphone',
            'homephone',
            'church',
            'community',

            // include more columns below if needed
            'active',
        ];
    }
}
