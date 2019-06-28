@extends('layouts.app')
@section('title','Projects')
@section('styles')
@endsection
@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                Projects
            </div>
            <table id="projects-table" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th></th>
                    <th>Project Name</th>
                </tr>
                </thead>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <div class="title m-b-md">
                Tasks
            </div>
            <table id="tasks-table" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th></th>
                    <th>Number</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Created By</th>
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
    </div>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var projectsEditor;
        var tasksEditor;

        $(document).ready(function() {
            // Projects Table
            projectsEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('projects-update') }}",
                table: "#projects-table",
                fields: [{
                    label: "Project name:",
                    name: "display_name"
                }]
            } );

            $('#projects-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                projectsEditor.inline( this );
            } );

            $('#projects-table').DataTable( {
                dom: "Bfrtip",
                ajax: "{{ route('projects-data') }}",
                order: [[ 1, 'asc' ]],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false
                    },
                    { data: "display_name" }
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                buttons: [
                    { extend: "create", editor: projectsEditor },
                    { extend: "edit",   editor: projectsEditor },
                    { extend: "remove", editor: projectsEditor }
                ]
            } );

            // Tasks Table
            tasksEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('tasks-update') }}",
                table: "#tasks-table",
                fields: [{
                    label: "Number:",
                    name: "task_number"
                },{
                    label: "Description:",
                    name: "task_description"
                },{
                    label: "Active:",
                    name: "task_active"
                },{
                    label: "Created by:",
                    name: "task_created_by"
                }
                ]
            } );

            // Activate an inline edit on click of a table cell
            $('#tasks-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                tasksEditor.inline( this );
            } );

            $('#tasks-table').DataTable( {
                dom: "Bfrtip",
                ajax: "{{ route('tasks-data') }}",
                order: [[ 1, 'asc' ]],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false
                    },
                    { data: "task_number" },
                    { data: "task_description" },
                    { data: "task_active" },
                    { data: "task_created_by" }
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                buttons: [
                    { extend: "create", editor: tasksEditor },
                    { extend: "edit",   editor: tasksEditor },
                    { extend: "remove", editor: tasksEditor }
                ]
            } );
        } );
    </script>
    @endsection
