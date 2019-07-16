@extends('layouts.app')
@section('title','Users')
@section('styles')
    <style>
        #DTE_Field_approver-id,
        #DTE_Field_buyer-id,
        #DTE_Field_admin {
            padding: 5px 4px;
            width: 100%;
        }
        .select2-selection__rendered {
            color: #000 !important;
        }
        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border: 1px solid #aaa; !important;
            border-radius: unset !important;
        }
        .select2-container .select2-selection--multiple {
            min-height: 29px !important;
        }
        .select2-dropdown {
            border-radius: unset !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            border-radius: unset;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="title">
            Users
        </div>
        <table id="users-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Approver</th>
                <th>Buyer</th>
                <th>Date Added</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="users-approver-filter" class="filter-input" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="users-buyer-filter" class="filter-input" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="searchable"></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var usersEditor, usersTable;

        $(document).ready(function() {
            // Users Editor
            usersEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('users-update') }}",
                table: "#users-table",
                fields: [
                    { label: "Name:", name: "name" },
                    { label: "Email:", name: "email" },
                    { label: "Password:", name: "password", },
                    { label: "Admin:", name: "admin", type: 'select',
                        options: ['Yes','No']
                    },
                    { label: "Approver:", name: "approver.id", type: 'select',
                        options: [
                            { label: '', value: '' },
                            @foreach ($users as $user)
                                { label: "{{ addslashes($user->name) }}", value: "{{ $user->id }}" },
                            @endforeach
                        ]
                    },
                    { label: "Buyer:", name: "buyer.id", type: 'select',
                        options: [
                            { label: '', value: '' },
                            @foreach ($users as $user)
                                { label: "{{ addslashes($user->name) }}", value: "{{ $user->id }}" },
                            @endforeach
                        ]
                    },
                ],
                i18n: {
                    create: {
                        title:  "Add a new User",
                    },
                    edit: {
                        title:  "Edit User",
                    }
                }
            } );
            // Users Datatable
            usersTable = $('#users-table').DataTable( {
                dom: "Bfrtip",
                ajax: "{{ route('users-data') }}",
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
                    { data: "email" },
                    { data: "admin" },
                    { data: "approver.name", editField: "approver.id" },
                    { data: "buyer.name", editField: "buyer.id" },
                    { data: "added_on" }
                ],
                select: {
                    style:    'single',
                    selector: 'td:first-child'
                },
                buttons: [
                    { extend: "create", editor: usersEditor, text: "Add" },
                    { extend: "edit",   editor: usersEditor },
                    { extend: "remove", editor: usersEditor }
                ]
            } );

            // add input for each column for Projects Table
            $('#users-table tfoot td.searchable').each(function(){
                $(this).html('<input class="filter-input" type="text"/>')
            });
            // add search function for Projects Table
            usersTable.columns().every(function(){
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                })
            });
            usersEditor.on( 'open', function ( e, mode, action ) {
                $('#DTE_Field_approver-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
                $('#DTE_Field_buyer-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
            });

            $('#users-approver-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#users-approver-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                usersTable.column(4).search(search, true, false).draw();
            });
            $('#users-buyer-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#users-buyer-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                usersTable.column(5).search(search, true, false).draw();
            });
        } );
    </script>
    @endsection
