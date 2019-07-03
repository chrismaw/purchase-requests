@extends('layouts.app')
@section('title','Projects')
@section('styles')
    <style>
        #DTE_Field_task_active, #DTE_Field_task_created_by,#DTE_Field_task_project {
            padding: 5px 4px;
            width: 100%;
        }
        #projects-table_wrapper {
            margin-bottom: 50px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="title">
            Projects
        </div>
        <table id="projects-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th>Number</th>
                <th>Description</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
            </tr>
            </tfoot>
        </table>
        <div class="title">
            Tasks
        </div>
        <table id="tasks-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th>Project</th>
                <th>Number</th>
                <th>Description</th>
                <th>Active</th>
                <th>Created By</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
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
        var projectsEditor, projectsTable, tasksEditor, tasksTable;

        $(document).ready(function() {
            // Projects Editor
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
                        submit: 'Submit'
                    },
                    edit: {
                        title:  "Edit Project",
                        submit: 'Submit'
                    }
                }
            } );
            // Edit inline Functionality
            @if (Auth::user()->isAdmin())
            $('#projects-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                projectsEditor.inline( this, {
                    onBlur: 'submit'
                });
            } );
            @endif
            // Projects Datatable
            projectsTable = $('#projects-table').DataTable( {
                @if (Auth::user()->isAdmin())
                dom: "Bfrtip",
                @else
                dom: "frtip",
                @endif
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
                @if (Auth::user()->isAdmin())
                select: {
                    style:    'single',
                    selector: 'td:first-child'
                },
                @else
                columnDefs: [
                    {visible: false, targets: 0},
                ],
                @endif
                buttons: [
                    { extend: "create", editor: projectsEditor, text: "Add" },
                    { extend: "edit",   editor: projectsEditor },
                    { extend: "remove", editor: projectsEditor }
                ]
            } );

            // add input for each column for Projects Table
            $('#projects-table tfoot td.searchable').each(function(){
                $(this).html('<input class="filter-input" type="text"/>')
            });
            // add search function for Projects Table
            projectsTable.columns().every(function(){
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                })
            });

            // Tasks Editor
            tasksEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('tasks-update') }}",
                table: "#tasks-table",
                fields: [
                    { label: "Project:", name: "task_project", type: 'select',
                        options: [
                            @foreach ($projects as $project)
                                { label: '{{ $project->number }} - {{ $project->description }}', value: '{{ $project->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Number:", name: "task_number" },
                    { label: "Description:", name: "task_description" },
                    { label: "Active:", name: "task_active", type: 'select',
                        options: ['Yes','No']
                    },
                    { label: "Created by:", name: "task_created_by", type: 'select',
                        options: [
                            { label: '{{ Auth::user()->name }}', value: '{{ Auth::user()->id }}' },
                            @foreach ($users as $user)
                                @if ($user->id == Auth::user()->id)
                                @else
                                    { label: '{{ $user->name }}', value: '{{ $user->id }}' },
                                @endif
                            @endforeach
                        ]
                    }
                ],
                i18n: {
                    create: {
                        title:  "Add a new Task",
                    },
                    edit: {
                        title:  "Edit Task",
                    }
                }
            } );

            // Inline Edit Functionality
            @if (Auth::user()->isAdmin())
                $('#tasks-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                    tasksEditor.inline( this, {
                        onBlur: 'submit'
                    });
                } );
            @endif

            //Tasks Datatable
            tasksTable = $('#tasks-table').DataTable( {
                @if (Auth::user()->isAdmin())
                    dom: "Bfrtip",
                @else
                    dom: "frtip",
                @endif
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
                    { data: "task_project" },
                    { data: "task_number" },
                    { data: "task_description" },
                    { data: "task_active" },
                    { data: "task_created_by" }
                ],
                @if (Auth::user()->isAdmin())
                    select: {
                        style:    'single',
                        selector: 'td:first-child'
                    },
                @else
                    columnDefs: [
                        {visible: false, targets: 0},
                    ],
                @endif
                buttons: [
                    { extend: "create", editor: tasksEditor, text: "Add" },
                    { extend: "edit",   editor: tasksEditor },
                    { extend: "remove", editor: tasksEditor }
                ]
            } );

            // add input for each column for Tasks Table
            $('#tasks-table tfoot td.searchable').each(function(){
                $(this).html('<input class="filter-input" type="text"/>')
            });
            // add search function for Tasks Table
            tasksTable.columns().every(function(){
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                })
            })
        } );
    </script>
    @endsection
