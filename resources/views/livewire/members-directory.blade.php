<div>
    <div class="card-header row mb-4 justify-content-between">
        <div class="col h4"><strong>Community Directory</strong></div>

        <div class="col btn-group">
            <input wire:model="q" class="form-control" type="text" placeholder="Search Members..." autofocus>
            <span wire:click="searchClear()" id="searchClear"><i class="fa fa-times-circle"></i></span>
        </div>

        <div class="col form-inline justify-content-end">
            Per Page: &nbsp;
            <select wire:model="perPage" class="form-control">
                <option>10</option>
                <option>15</option>
                <option>25</option>
                <option>50</option>
            </select>
        </div>
    </div>

    <div class="row" wire:loading.class="is-loading">
        <table class="col-12 compact order-column table table-striped table-bordered table-hover" style="width:100%">
            <thead>
            <tr>
                <th scope="col" class="d-md-none">
                    <a wire:click.prevent="sortBy('first')" role="button" href="#">
                        Name
                        @include('partials._sort-icon', ['field' => 'first'])
                    </a>
                </th>
                {{-- first column, above, only visible on small devices --}}


                <th scope="col" class="d-none d-md-table-cell">
                    <a wire:click.prevent="sortBy('first')" role="button" href="#">
                        Name
                        @include('partials._sort-icon', ['field' => 'first'])
                    </a>
                </th>
                <th scope="col" class="d-none d-md-table-cell">
                    <a wire:click.prevent="sortBy('last')" role="button" href="#">
                        Surname
                        @include('partials._sort-icon', ['field' => 'last'])
                    </a>
                </th>
                <th scope="col">
                    <a wire:click.prevent="sortBy('email')" role="button" href="#">
                        Email
                        @include('partials._sort-icon', ['field' => 'email'])
                    </a>
                </th>
                <th scope="col">
                    <a wire:click.prevent="sortBy('weekend')" role="button" href="#">
                        Weekend
                        @include('partials._sort-icon', ['field' => 'weekend'])
                    </a>
                </th>
                <th scope="col" class="small">
                    <a wire:click.prevent="sortBy('community')" role="button" href="#">
                        Home Community
                        @include('partials._sort-icon', ['field' => 'community'])
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $person)
                <tr @if(!$person->active)class="table-dark"@endif>
                    <td class="d-md-none"><a href="{{url('/members/' . $person->id)}}">{{$person->name}}</a></td>
                    <td class="d-none d-md-table-cell"><a href="{{url('/members/' . $person->id)}}">{{$person->first}}</a></td>
                    <td class="d-none d-md-table-cell"><a href="{{url('/members/' . $person->id)}}">{{$person->last}}</a></td>
                    <td class="nowrap">{{$person->email}}</td>
                    <td>{{$person->weekend}}</td>
                    <td>{{$person->community}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>

    <div class="row">
        <div class="col">
            {{ $users->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
        </div>
    </div>
</div>
