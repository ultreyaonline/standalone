@extends ('layouts.app')

@section('title')
    {{ $scope_title ?: config('site.community_acronym') . ' Community Directory' }}
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">{{ $scope_prefix }}Community Members as
                        of {{ Illuminate\Support\Carbon::today()->toFormattedDateString() }}</div>

                    <div class="card-body">

                        <table id="members-table" data-page-length="25"
                               class="col-12 compact order-column table table-striped table-bordered table-hover"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th scope="col" class="max-tablet-p">Name</th>
                                <th scope="col" class="min-tablet-l">Name</th>
                                <th scope="col" class="min-tablet-l">Surname</th>
                                <th scope="col">Email</th>
                                <th scope="col">Weekend</th>
                                <th scope="col">Home Community</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <td class="non_searchable"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('extra_css')
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css" rel="stylesheet">
@endsection
{{-- Version reference: https://datatables.net/download/packages and CDN https://cdn.datatables.net --}}
{{--Future @TODO https://datatables.net/blog/2017-07-24 --}}
@section('page-js')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>

    <script>
        $('#members-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            stateSave: true,
            stateDuration: -1, // session
            // sPaginationType: "full_numbers",
            ajax: '{{ route('api.members_search') }}',
            columns: [
                {data: 'membername', name: 'membername'},
                {data: 'first', name: 'first'},
                {data: 'last', name: 'last'},
                {data: 'email', name: 'email'},
                {data: 'weekend', name: 'weekend'},
                {data: 'community', name: 'community'}
            ],
            order: [[2, 'asc']],
        @if (Request::input('q'))
        "search": {
            "search": "{{ Request::input('q') }}"
        },
        @endif
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;

                    // add search-field to footer of columns (except class="non_searchable")
                    if (column.footer().className !== 'non_searchable') {
                        var input = document.createElement("input");
                        $(input).appendTo($(column.footer()).empty())
                            .keyup(function () {
                                column.search($(this).val(), false, false, true).draw();
                            });
                    }
                });
            }
        });
    </script>
@endsection
