<?php

namespace App\Http\Livewire;

use App\User;
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
    public $sortField = '';
    public $sortAsc = true; // used for query and icons

    protected $updatesQueryString = ['q', 'perPage', 'sortField', 'sortAsc'];

    public function __construct($id)
    {
        parent::__construct($id);

        abort_unless(Auth::check() && Auth::user()->can('edit members'), '403', 'Unauthorized.');
    }

    public function mount($initialSearch = ''): void
    {
        $this->q = request('q', $initialSearch);

        $this->perPage = request('perPage', config('site.pagination_threshold', 25));

        $this->sortField = request('sortField', 'last');

        $this->sortAsc = request('sortAsc', true);
    }

    public function render()
    {
        return view('livewire.members-audit', [
            'users' => User::datatableSearch($this->q)
                ->select($this->getColumns())
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ]);
    }

    public function searchClear(): void
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
            'cellphone',
            'homephone',
            'church',
            'community',

            // include more columns below if needed
            'active',
        ];
    }
}
