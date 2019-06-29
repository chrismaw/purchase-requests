@extends('layouts.app')
@section('title','Suppliers')
@section('styles')
    <style>
        #DTE_Field_active, #DTE_Field_created_by {
            padding: 5px 4px;
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="title m-b-md">
            Suppliers
        </div>
        <table id="suppliers-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Active</th>
                <th>Added By</th>
            </tr>
            </thead>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var suppliersEditor, suppliersTable;

        $(document).ready(function() {
            // Projects Table
            suppliersEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('suppliers-update') }}",
                table: "#suppliers-table",
                fields: [
                    { label: "Name:", name: "name" },
                    { label: "Active:", name: "active", type: 'select',
                        options: ['Yes','No']
                    },
                    { label: "Added by:", name: "created_by", type: 'select',
                        options: [
                            @foreach ($users as $user)
                                { label: '{{ $user->name }}', value: '{{ $user->id }}' },
                            @endforeach
                        ]
                    }
                ],
                i18n: {
                    create: {
                        title:  "Add a new Supplier",
                    },
                    edit: {
                        title:  "Edit Supplier",
                    }
                }
            } );

            @if (Auth::user()->isAdmin())
            $('#suppliers-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                suppliersEditor.inline( this );
            } );
            @endif

            suppliersTable = $('#suppliers-table').DataTable( {
                @if (Auth::user()->isAdmin())
                dom: "Bfrtip",
                @else
                dom: "frtip",
                @endif
                ajax: "{{ route('suppliers-data') }}",
                order: [[ 1, 'asc' ]],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false,
                        width: '1%'
                    },
                    { data: "name" },
                    { data: "active" },
                    { data: "created_by" }
                ],
                @if (Auth::user()->isAdmin())
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                @else
                columnDefs: [
                    {visible: false, targets: 0},
                ],
                @endif
                buttons: [
                    { extend: "create", editor: suppliersEditor, text: "Add" },
                    { extend: "edit",   editor: suppliersEditor },
                    { extend: "remove", editor: suppliersEditor }
                ]
            } );
        } );
    </script>
    @endsection
