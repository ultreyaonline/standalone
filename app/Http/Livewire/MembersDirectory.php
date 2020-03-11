<?php

namespace App\Http\Livewire;

use App\User;
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
    public $sortField = 'first'; // initial sort field
    public $sortAsc = true; // used for query and icons

    protected $updatesQueryString = ['q', 'perPage'];

    public function __construct($id)
    {
        parent::__construct($id);

        abort_unless(Auth::check() && Auth::user()->can('view members'), '403', 'Unauthorized.');
    }

    public function mount($initialSearch = ''): void
    {
        $this->q = request('q', $initialSearch);

        $this->perPage = request('perPage', 10);
    }

    public function render()
    {
        return view('livewire.members-directory', [
            'users' => User::datatableSearch($this->q)
                ->active()
                //->onlyLocal()
                ->select($this->getColumns())
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ]);
    }

    public function searchClear()
    {
        $this->reset('q');
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
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
