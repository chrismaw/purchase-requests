@extends('layouts.app')
@section('title','Purchase Requests')
@section('styles')
    <style>
        #DTE_Field_project,
        #DTE_Field_requester,
        #DTE_Field_purchase_request_status,
        #DTE_Field_task,
        #DTE_Field_supplier,
        #DTE_Field_approver,
        #DTE_Field_buyer,
        #DTE_Field_prl_status {
            padding: 5px 4px;
            width: 100%;
        }
        body > div.DTED.DTED_Lightbox_Wrapper > div > div > div > div.DTE.DTE_Action_Create > div.DTE_Body > div > form > div > div.DTE_Field.DTE_Field_Type_datetime.DTE_Field_Name_request_date {
            display: none;
        }
        #purchase-requests-table_wrapper {
            margin-bottom: 50px;
        }
        #purchase-request-lines-table {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="title">
            Purchase Requests
        </div>
        <table id="purchase-requests-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Project</th>
                <th>Requester</th>
                <th>Request Date</th>
                <th>Status</th>
            </tr>
            </thead>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <div class="title">
            Purchase Request Lines
        </div>
    </div>
    <div class="container-fluid" style="width: unset;">
        <table id="purchase-request-lines-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th></th>
                <th>Item Number</th>
                <th>Item Revision</th>
                <th>Item Description</th>
                <th>Qty Required</th>
                <th>Qty Per UOM</th>
                <th>UOM Qty Required</th>
                <th>UOM Cost</th>
                <th>Total Line Cost</th>
                <th>Task</th>
                <th>Need Date</th>
                <th>Supplier</th>
                <th>Notes</th>
                <th>Approver</th>
                <th>Buyer</th>
                <th>Status</th>
            </tr>
            </thead>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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
        var prEditor, prTable, prlEditor, prlTable;

        $(document).ready(function() {
            // Projects Table
            prEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('purchase-requests-update') }}",
                table: "#purchase-requests-table",
                fields: [
                    { label: "Project:", name: "project", type: 'select',
                        options: [
                            @foreach ($projects as $project)
                                { label: '{{ $project->number }} - {{ $project->description }}', value: '{{ $project->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Requester:", name: "requester", type: 'select',
                        options: [
                            @foreach ($users as $user)
                                { label: '{{ $user->name }}', value: '{{ $user->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Request Date:", name: "request_date", type:'datetime' },
                    { label: "Status:", name: "purchase_request_status", type: 'select',
                        options: [
                            @foreach ($prStatuses as $status)
                                { label: '{{ $status }}', value: '{{ $status }}' },
                            @endforeach
                        ]
                    }

                ],
                i18n: {
                    create: {
                        title:  "Add a new Purchase Request",
                        submit: 'Submit'
                    },
                    edit: {
                        title:  "Edit Purchase Request",
                        submit: 'Submit'
                    }
                }
            } );

            prTable = $('#purchase-requests-table').DataTable( {
                @if (Auth::user()->isAdmin())
                dom: "Bfrtip",
                @else
                dom: "frtip",
                @endif
                ajax: "{{ route('purchase-requests-data') }}",
                order: [[ 1, 'asc' ]],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false,
                        width: '1%'
                    },
                    { data: "id" },
                    { data: "project" },
                    { data: "requester" },
                    { data: "request_date" },
                    { data: "status" },
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
                    { extend: "create", editor: prEditor, text: "Add" },
                    { extend: "edit",   editor: prEditor },
                    { extend: "remove", editor: prEditor }
                ]
            } );
            // Tasks Table
            prlEditor = new $.fn.dataTable.Editor( {
                ajax: {
                    url: "{{ route('purchase-request-lines-update') }}",
                    data: function (d){
                        var selected = prTable.row({selected:true});
                        if (selected.any()){
                            d.prl = selected.data().id;
                        }
                    }
                },

                table: "#purchase-request-lines-table",
                fields: [
                    { label: "Purchase Request:", name: "purchase_request" },
                    { label: "Item Number:", name: "item_number" },
                    { label: "Item Revision:", name: "item_revision" },
                    { label: "Item Description:", name: "item_description" },
                    { label: "Qty Required:", name: "qty_required" },
                    { label: "Qty Per UOM:", name: "qty_per_uom" },
                    { label: "UOM Cost:", name: "cost_per_uom" },
                    { label: "Task:", name: "task", type: 'select',
                        options: [
                            @foreach ($tasks as $task)
                                { label: '{{ $task->number }} - {{ $task->description }}', value: '{{ $task->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Need Date:", name: "need_date", type: 'date' },
                    { label: "Supplier:", name: "supplier", type: 'select',
                        options: [
                            @foreach ($suppliers as $supplier)
                                { label: '{{ $supplier->name }}', value: '{{ $supplier->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Notes:", name: "notes" },
                    { label: "Approver:", name: "approver", type: 'select',
                        options: [
                            @foreach ($users as $user)
                                { label: '{{ $user->name }}', value: '{{ $user->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Buyer:", name: "buyer", type: 'select',
                        options: [
                            @foreach ($users as $user)
                                { label: '{{ $user->name }}', value: '{{ $user->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Status:", name: "prl_status", type: 'select',
                        options: [
                            @foreach ($prlStatuses as $status)
                                { label: '{{ $status }}', value: '{{ $status }}' },
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


            @if (Auth::user()->isAdmin())
            $('#purchase-request-lines-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                prlEditor.inline( this );
            } );
            @endif

            prlTable = $('#purchase-request-lines-table').DataTable( {
                @if (Auth::user()->isAdmin())
                dom: "Bfrtip",
                @else
                dom: "frtip",
                @endif
                ajax: {
                    url:"{{ route('purchase-request-lines-data') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        var selected = prTable.rows({selected:true});
                        if (selected.any()){
                            d.prl = selected.data().pluck('id').join(',');
                        }
                    }
                },
                order: [[ 1, 'asc' ]],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false,
                        width: '1%'
                    },
                    { data: "purchase_request" },
                    { data: "item_number" },
                    { data: "item_revision" },
                    { data: "item_description" },
                    { data: "qty_required" },
                    { data: "qty_per_uom" },
                    { data: "uom_qty_required" },
                    { data: "cost_per_uom" },
                    { data: "total_line_cost" },
                    { data: "task" },
                    { data: "need_date" },
                    { data: "supplier" },
                    { data: "notes" },
                    { data: "approver" },
                    { data: "buyer" },
                    { data: "status" },
                ],
                @if (Auth::user()->isAdmin())
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                @endif
                columnDefs: [
                    { visible: false, targets: [
                        @if (!Auth::user()->isAdmin())0,@endif 1
                    ] },
                    { "className": "text-nowrap", "targets": '_all' }
                ],
                buttons: [
                    { extend: "create", editor: prlEditor, text: "Add" },
                    { extend: "edit",   editor: prlEditor },
                    { extend: "remove", editor: prlEditor }
                ]
            } );

            prTable.on('select', function () {
                prlTable.ajax.reload();

                console.log(prlEditor);
                prlEditor
                    .field('purchase_request')
                    .def(prTable.rows({selected:true}).data().id);
            });
            prTable.on('deselect',function () {
                prlTable.ajax.reload();
            })
        } );
    </script>
    @endsection
