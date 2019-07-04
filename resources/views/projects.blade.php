@extends('layouts.app')
@section('title','Projects')
@section('styles')
    <style>
        #DTE_Field_task_active, #DTE_Field_task_created_by,#DTE_Field_task_project, #DTE_Field_is_active {
            padding: 5px 4px;
            width: 100%;
        }
        #projects-table_wrapper {
            margin-bottom: 50px;
        }
        .select2-selection__rendered {
            color: #000 !important;
        }
        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single {
            border: 1px solid #aaa; !important;
            border-radius: unset !important;
        }
        .select2-dropdown {
            border-radius: unset !important;
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
{{--                <th>Number</th>--}}
                <th>Description</th>
                <th>Active</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td></td>
{{--                <td class="searchable"></td>--}}
                <td class="searchable"></td>
                <td><input id="projects-active-filter" class="filter-input" type="text"/></td>
            </tr>
            </tfoot>
        </table>
		<hr />
        <div class="title">
            Tasks
        </div>
        <table id="tasks-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
{{--                <th>Project</th>--}}
                <th>Number</th>
                <th>Description</th>
                <th>Active</th>
                <th>Created By</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td></td>
{{--                <td class="searchable"></td>--}}
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td><input id="tasks-active-filter" class="filter-input" type="text"/></td>
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
                    // { label: "Number:", name: "number" },
                    { label: "Description:", name: "description" },
                    { label: "Active:", name: "is_active", type: 'select', def: 'Yes',
                        options: ['Yes','No']
                    }
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

            projectsEditor.on( 'preSubmit', function ( e, o, action ) {
                if ( action !== 'remove' ) {
                    // var number = this.field('number'),
                        description = this.field('description');

                    if (!description.isMultiValue()){
                        if (!description.val()) {
                            description.error('A description must be provided');
                        }
                    }
                    // if (!number.isMultiValue()) {
                    //     if (!/\d/.test(number.val())) {
                    //         number.error('This must be a number');
                    //     }
                    //     if (!number.val()) {
                    //         number.error('A number must be provided');
                    //     }
                    //
                    // }
                    if ( this.inError() ) {
                        return false;
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
                    dom: "B<'p-toolbar'>frtip",
                @else
                    dom: "<'p-toolbar'>frtip",
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
                    // { data: "number" },
                    { data: "description" },
                    { data: "is_active" }
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
                paging: false,
                buttons: [
                    { extend: "create", editor: projectsEditor, text: "Add" },
                    { extend: "edit",   editor: projectsEditor },
                    { extend: "remove", editor: projectsEditor }
                ],
                initComplete: function (settings, json) {
                    document.getElementById('projects-active-filter').value = 'Yes';
                    $('#projects-active-filter').trigger('keyup');
                }
            } );

            // create the Show Active checkbox
            $('div.p-toolbar').html('<input type="checkbox" id="projects-active-checkbox" style="margin: 10px 5px 10px 10px" checked="checked"/><label for="projects-active-checkbox">Show Active</label>');
            $('#projects-active-checkbox').on('change', function(){
                if($(this).is(':checked')){
                    document.getElementById('projects-active-filter').value = 'Yes';
                    $('#projects-active-filter').trigger('keyup');
                } else {
                    document.getElementById('projects-active-filter').value = '';
                    $('#projects-active-filter').trigger('keyup');
                }
            });
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
                    {{--{ label: "Project:", name: "task_project", type: 'select',--}}
                    {{--    options: [--}}
                    {{--        @foreach ($projects as $project)--}}
                    {{--            { label: '{{ $project->description }}', value: '{{ $project->id }}' },--}}
                    {{--        @endforeach--}}
                    {{--    ]--}}
                    {{--},--}}
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
            tasksEditor.on( 'preSubmit', function ( e, o, action ) {
                if ( action !== 'remove' ) {
                    var number = this.field('task_number'),
                        description = this.field('task_description');

                    if (!description.isMultiValue()){
                        if (!description.val()) {
                            description.error('A description must be provided');
                        }
                    }
                    if (!number.isMultiValue()) {
                        if (!/\d/.test(number.val())) {
                            number.error('This must be a number');
                        }
                        if (!number.val()) {
                            number.error('A number must be provided');
                        }

                    }
                    if ( this.inError() ) {
                        return false;
                    }
                }
            } );
            //Tasks Datatable
            tasksTable = $('#tasks-table').DataTable( {
                @if (Auth::user()->isAdmin())
                    dom: "B<'t-toolbar'>frtip",
                @else
                    dom: "<'t-toolbar'>frtip",
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
                    // { data: "task_project" },
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
                paging: false,
                buttons: [
                    { extend: "create", editor: tasksEditor, text: "Add" },
                    { extend: "edit",   editor: tasksEditor },
                    { extend: "remove", editor: tasksEditor }
                ],
                initComplete: function (settings, json) {
                    document.getElementById('tasks-active-filter').value = 'Yes';
                    $('#tasks-active-filter').trigger('keyup');
                }
            } );

            $('div.t-toolbar').html('<input type="checkbox" id="tasks-active-checkbox" style="margin: 10px 5px 10px 10px" checked="checked"/><label for="tasks-active-checkbox">Show Active</label>');
            $('#tasks-active-checkbox').on('change', function(){
                if($(this).is(':checked')){
                    document.getElementById('tasks-active-filter').value = 'Yes';
                    $('#tasks-active-filter').trigger('keyup');
                } else {
                    document.getElementById('tasks-active-filter').value = '';
                    $('#tasks-active-filter').trigger('keyup');
                }
            });
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
            });
			tasksEditor.on( 'open', function ( e, mode, action ) {
				// $('#DTE_Field_task_project').select2();
				$('#DTE_Field_task_created_by').select2();
			} );
        } );
    </script>
    @endsection
