@extends('layouts.app')
@section('title','Projects')
@section('styles')
    <style>
        #DTE_Field_task_active, #DTE_Field_task_created_by {
            padding: 5px 4px;
            width: 100%;
        }
        .display-none {
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                Projects
            </div>
            <table id="projects-table" class="display" cellspacing="0" Kwidth="100%">
                <thead>
                <tr>
                    <th></th>
                    <th>Number</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tr>
                    <td></td>
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
                fields: [
                    { label: "Number:", name: "number" },
                    { label: "Description:", name: "description" }
                ],
                i18n: {
                    create: {
                        title:  "Add a new Project",
                        submit: 'Submit Project'
                    },
                    edit: {
                        title:  "Edit Project",
                        submit: 'Submit Edit'
                    }
                }
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
                        orderable: false,
                        width: '1%'
                    },
                    { data: "number" },
                    { data: "description" }
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                buttons: [
                    { extend: "create", editor: projectsEditor, text: "Add" },
                    { extend: "edit",   editor: projectsEditor },
                    { extend: "remove", editor: projectsEditor }
                ]
            } );

            // Tasks Table
            tasksEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('tasks-update') }}",
                table: "#tasks-table",
                fields: [
                    { label: "Number:", name: "task_number" },
                    { label: "Description:", name: "task_description" },
                    { label: "Active:", name: "task_active", type: 'select',
                        options: ['Yes','No']
                    },
                    { label: "Created by:", name: "task_created_by", type: 'select',
                        options: [
                                @foreach ($users as $user)
                            { label: '{{ $user->name }}', value: '{{ $user->id }}' }
                                @endforeach
                        ]
                    }
                ],
                i18n: {
                    create: {
                        title:  "Add a new Task",
                        submit: 'Submit Task'
                    },
                    edit: {
                        title:  "Edit Task",
                        submit: 'Submit Edit'
                    }
                }
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
                        orderable: false,
                        width: '1%'
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
                    { extend: "create", editor: tasksEditor, text: "Add" },
                    { extend: "edit",   editor: tasksEditor },
                    { extend: "remove", editor: tasksEditor }
                ]
            } );
        } );
    </script>
    @endsection
