@extends ('layouts.app')

{{-- ALERT: To actually use the Datatables 'Editor' tool as an inline editor, a license will need to be purchased and uploaded to your public directory --}}

@section('title')
    {{ $scope_title ?? config('site.community_acronym') . ' Community Directory' }}
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <table id="members-table" data-page-length="25" class="table table-striped table-bordered table-hover ">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Surname</th>
                        <th scope="col">Email</th>
                        <th scope="col">Weekend</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Home Phone</th>
                        <th scope="col">Church</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
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

@stop

@section('extra_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.0/css/select.bootstrap4.min.css">
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">--}}
    <link rel="stylesheet" href="/css/editor.dataTables.css">
    <link rel="stylesheet" href="/css/editor.bootstrap.css">
@endsection

@section('page-js')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    {{--<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>--}}
    {{--<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap.min.js"></script>--}}
    {{--<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>--}}
    <script src="/js/dataTables.editor.min.js"></script>
    <script src="/js/editor.bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            var editor; // use a global for the submit and return data rendering
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });
            // Activate an inline edit on click of a table cell
            $('#members-table').on('click', 'tbody td:not(:first-child)', function (e) {
                editor.inline(this, {
                    onBlur: 'submit'
                });
            }).DataTable({
                processing: true,
                serverSide: true,
                // dom: "Bfrtip",
                ajax: '{{ route('api.members_search') }}',
                columns: [
                    {data: 'first', name: 'first'},
                    {data: 'last', name: 'last'},
                    {data: 'email', name: 'email'},
                    {data: 'weekend', name: 'weekend'},
                    {data: 'cellphone', name: 'cellphone'},
                    {data: 'homephone', name: 'homephone'},
                    {data: 'church', name: 'church'}
                ],
                order: [[1, 'asc']],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                },
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
            editor = new $.fn.dataTable.Editor({
                ajax: {
                    url: "{{ route('api.members_edit') }}",
                    type: 'POST'
                },
                table: "#members-table",
                display: "envelope",
                fields: [{
                    label: "First name:",
                    name: "first"
                }, {
                    label: "Last name:",
                    name: "last"
                }, {
                    label: "Email:",
                    name: "email"
                }, {
                    label: "Weekend:",
                    name: "weekend"
                }, {
                    label: "Mobile:",
                    name: "cellphone"
                }, {
                    label: "Home Phone:",
                    name: "homephone"
                }, {
                    label: "Church:",
                    name: "church"
                    // }, {
                    //     label: "Attended:",
                    //     name: "attend_date",
                    //     format: "YYYY-MM-DD hh:mm:ss",
                    //     type: "datetime"
                }
                ]
            });
        });
    </script>
@endsection
