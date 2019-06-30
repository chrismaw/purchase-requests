@extends('layouts.app')
@section('title','Users')
@section('styles')
    <style>
        #DTE_Field_admin {
            padding: 5px 4px;
            width: 100%;
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
                <th>Date Added</th>
            </tr>
            </thead>
            <tr>
                <td></td>
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
        var usersEditor;

        $(document).ready(function() {
            // Projects Table
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

            $('#users-table').DataTable( {
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
        } );
    </script>
    @endsection
