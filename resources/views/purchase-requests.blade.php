@extends('layouts.app')
@section('title','Purchase Requests')
@section('styles')
    <style>
        #DTE_Field_project,
        #DTE_Field_requester,
        #DTE_Field_purchase_request_status,
        #DTE_Field_uom,
        #DTE_Field_task,
        #DTE_Field_supplier,
        #DTE_Field_approver,
        #DTE_Field_buyer,
        #DTE_Field_prl_status,
        #DTE_Field_purchase_request {
            padding: 5px 4px;
            width: 100%;
        }
        body > div.DTED.DTED_Lightbox_Wrapper > div > div > div > div.DTE.DTE_Action_Create > div.DTE_Body > div > form > div > div.DTE_Field.DTE_Field_Type_datetime.DTE_Field_Name_request_date,
        body > div.DTED.DTED_Lightbox_Wrapper > div > div > div > div.DTE.DTE_Action_Create > div.DTE_Body > div > form > div > div.DTE_Field.DTE_Field_Type_select.DTE_Field_Name_purchase_request {
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
            <tfoot>
            <tr>
                <td></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td><input id="purchase-request-status-filter" class="filter-input" type="text"/></td>
            </tr>
            </tfoot>
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
                <th>UOM</th>
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
            <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
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
        var prEditor, prTable, prlEditor, prlTable, prID;

        $(document).ready(function() {
            // Purchase Requests Editor
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
                            { label: '{{ Auth::user()->name }}', value: '{{ Auth::user()->id }}' },

                            @foreach ($users as $user)
                                @if ($user->id == Auth::user()->id)
                                @else
                                    { label: '{{ $user->name }}', value: '{{ $user->id }}' },
                                @endif
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
            // Reload Child Table - Purchase Request Lines on removal of Purchase Request
            prEditor.on('postRemove', function (e, json, data) {
                prlTable.ajax.reload();
            });
            // Purchase request Datatable
            prTable = $('#purchase-requests-table').DataTable( {
                dom: "B<'pr-toolbar'>frtip",
                ajax: {
                    url: "{{ route('purchase-requests-data') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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
                    { data: "id" },
                    { data: "project" },
                    { data: "requester" },
                    { data: "request_date" },
                    { data: "status" },
                ],
                select: {
                    style:    'single'
                },
                columnDefs: [
                    { className: "text-nowrap", targets: '_all' }
                ],
                buttons: [
                    { extend: "create", editor: prEditor, text: "Add" },
                    { extend: "edit",   editor: prEditor },
                    { extend: "remove", editor: prEditor }
                ],
                initComplete: function (settings, json) {
                    document.getElementById('purchase-request-status-filter').value = 'Open';
                    $('#purchase-request-status-filter').trigger('keyup');
                }
            } );
            // create the Show Open Request checkbox
            $('div.pr-toolbar').html('<input type="checkbox" id="status-filter-checkbox" style="margin: 10px 5px 10px 10px" checked="checked"/><label for="status-filter-checkbox">Show Open Requests</label>')
            $('#status-filter-checkbox').on('change', function(){
                if($(this).is(':checked')){
                    document.getElementById('purchase-request-status-filter').value = 'Open';
                    $('#purchase-request-status-filter').trigger('keyup');
                } else {
                    document.getElementById('purchase-request-status-filter').value = '';
                    $('#purchase-request-status-filter').trigger('keyup');
                }
            });
            // add input for each column for Purchase Requests Table
            $('#purchase-requests-table tfoot td.searchable').each(function(){
                $(this).html('<input class="filter-input" type="text"/>')
            });
            // add search function for Purchase Requests Table
            prTable.columns().every(function(){
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                })
            });

            // Purchase Request Lines Editor
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
                    { label: "Purchase Request:", name: "purchase_request", type: 'select',
                        options: [
                            @foreach ($purchase_requests as $request)
                                { label: 'ID: {{ $request->id }} | {{ $request->project->number }} - {{ $request->project->description }}', value: '{{ $request->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Item Number:", name: "item_number" },
                    { label: "Item Revision:", name: "item_revision" },
                    { label: "Item Description:", name: "item_description" },
                    { label: "Qty Required:", name: "qty_required" },
                    { label: "Qty Per UOM:", name: "qty_per_uom", def: '1' },
                    { label: "Uom:", name: "uom", type: 'select',
                        options: [
                            @foreach ($uoms as $uom)
                                { label: '{{ $uom->name }}', value: '{{ $uom->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "UOM Cost:", name: "cost_per_uom" },
                    { label: "Task:", name: "task", type: 'select',
                        options: [
                            @foreach ($tasks as $task)
                                { label: '{{ $task->number }} - {{ $task->description }}', value: '{{ $task->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Need Date:", name: "need_date", type: 'datetime' },
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
                            { label: '', value: '' },
                            @foreach ($users as $user)
                                { label: '{{ $user->name }}', value: '{{ $user->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Buyer:", name: "buyer", type: 'select',
                        options: [
                            { label: '', value: '' },
                            @foreach ($users as $user)
                                { label: '{{ $user->name }}', value: '{{ $user->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Status:", name: "prl_status", type: 'select', def: 'Pending Approval',
                        options: [
                            @foreach ($prlStatuses as $status)
                                { label: '{{ $status }}', value: '{{ $status }}'},
                            @endforeach
                        ]
                    }
                ],
                i18n: {
                    create: {
                        title:  "Add a new Purchase Request Line",
                    },
                    edit: {
                        title:  "Edit Line",
                    }
                }
            } );
            // Inline Edit Functionality
            $('#purchase-request-lines-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                prlEditor.inline( this, {
                    onBlur: 'submit'
                });
            } );
            // Purchase Request Lines Datatable
            prlTable = $('#purchase-request-lines-table').DataTable( {
                dom: "Bfrtip",
                ajax: {
                    url:"{{ route('purchase-request-lines-data') }}",
                    type: "post",
                    // headers: {
                    //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    // },
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
                    { data: "uom" },
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
                    { data: "prl_status" },
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                columnDefs: [
                    { visible: false, targets: 1 },
                    { className: "text-nowrap", "targets": [4,12,13,15,16,17] }
                ],
                paging: false,
                buttons: [
                    { extend: "create", editor: prlEditor, text: "Add" },
                    { extend: "edit",   editor: prlEditor },
                    { extend: "remove", editor: prlEditor }
                ]
            } );
            // add input for each column for Purchase Request Lines Table
            $('#purchase-request-lines-table tfoot td.searchable').each(function(){
                $(this).html('<input class="filter-input" type="text"/>')
            });
            prlTable.buttons().disable();
            // add search function for Purchase Request Lines Table
            prlTable.columns().every(function(){
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                })
            });
            prTable.on('select', function (e, dt, type, indexes) {
                prlTable.ajax.reload();
                prlEditor
                    .field('purchase_request')
                    .def(prTable.rows({selected:true}).data().id);
                prID = prTable.rows(indexes).data()[0]['id'];
                setTimeout(function () {
                    prlEditor.set('purchase_request',prID);
                }, 2000);
                prlTable.buttons().enable();
            });
            prTable.on('deselect',function () {
                prlTable.ajax.reload();
                prlTable.buttons().disable();
            });
            prlEditor.on( 'preSubmit', function ( e, o, action ) {
                if ( action !== 'remove' ) {
                    var itemDescription = this.field('item_description'),
                        qtyRequired = this.field('qty_required'),
                        qtyPerUom = this.field('qty_per_uom'),
                        needDate = this.field('need_date');

                    if (!itemDescription.isMultiValue()){
                        if (!itemDescription.val()) {
                            itemDescription.error('A description must be provided');
                        }
                    }
                    if (!qtyRequired.isMultiValue()) {
                        if (!qtyRequired.val()) {
                            qtyRequired.error('A quantity must be provided');
                        }
                        if (!/\d/.test(qtyRequired.val())) {
                            qtyRequired.error('A quantity must be a number');
                        }

                    }
                    if (!qtyPerUom.isMultiValue()) {
                        if (!/\d/.test(qtyPerUom.val())) {
                            qtyPerUom.error('A quantity must be a number');
                        }
                    }
                    if (!needDate.isMultiValue()){
                        if (!needDate.val()){
                            needDate.error('A date must be provided');
                        }
                    }
                    if ( this.inError() ) {
                        return false;
                    }
                }
            } );
            // prlEditor.on( 'onInitCreate', function () {
            //     prlEditor.disable('purchase_request');
            // } );
            // prlEditor.on( 'onInitEdit', function () {
            //     prlEditor.enable('purchase_request');
            // } );
        } );
    </script>
    @endsection
