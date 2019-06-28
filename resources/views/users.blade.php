@extends('layouts.app')
@section('title','Users')
@section('styles')
@endsection
@section('content')
    <div class="container">
        <div class="title m-b-md">
            Users
        </div>
        <table id="users-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Email</th>
                <th>Added On</th>
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
                    { data: "added_on" }
                ],
                select: {
                    style:    'os',
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
