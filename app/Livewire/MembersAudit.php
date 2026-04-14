<?php

namespace App\Livewire;

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
    public $sort_key = 'last';
    public $sortAsc = true; // used for query and icons

    protected $paginationTheme = 'bootstrap';

    protected $queryString = ['q', 'perPage', 'sortBy', 'sortAsc'];

    private array $allowedSorts = ['first', 'last', 'email', 'weekend', 'community', 'active', 'cellphone', 'homephone', 'church'];

    public function mount(): void
    {
        $this->perPage = request('perPage', config('site.pagination_threshold', 25));
    }

    public function render()
    {
        abort_unless(Auth::check() && Auth::user()->can('edit members'), '403', 'Unauthorized.');

        if (!in_array($this->sort_key, $this->allowedSorts, true)) {
            $this->sort_key = 'last';
        }
        return view('livewire.members-audit', [
            'users' => User::datatableSearch($this->q)
                ->select($this->getColumns())
                ->orderBy($this->sort_key, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ]);
    }

    public function searchClear(): void
    {
        $this->reset('q');
    }

    public function sortBy($field): void
    {
        if (!in_array($field, $this->allowedSorts, true)) {
            return; // silently reject invalid sort fields
        }

        if ($this->sort_key === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sort_key = $field;
    }

    /**
     * Get the columns which should be allowed to be returned to the page.
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
