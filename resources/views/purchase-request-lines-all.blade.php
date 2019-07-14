@extends('layouts.app')
@section('title','Purchase Request Lines | All')
@section('styles')
    <style>
        #DTE_Field_active, #DTE_Field_created_by-id {
            padding: 5px 4px;
            width: 100%;
        }
        #purchase-request-lines-table {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
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
        td.details-control {
            background: url('../resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control {
            background: url('../resources/details_close.png') no-repeat center center;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid" style="width: unset;">
        <div class="title m-b-md">
            Purchase Request Lines | All
        </div>
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
                <th>Next Assembly</th>
                <th>Work Order</th>
                <th>PO Number</th>
                <th>Purchase Request ID</th>
                <th>Purchase Request Project</th>
                <th>Purchase Request Requester</th>
                <th>Purchase Request Request Date</th>
                <th>Purchase Request Status</th>
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
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="purchase-request-lines-uom-filter" class="filter-input" multiple>
                        @foreach ($uoms as $uom)
                            <option value="{{ $uom->name }}">{{ $uom->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="purchase-request-lines-task-filter" class="filter-input" multiple>
                        @foreach ($tasks as $task)
                            <option value="{{ $task->number }}">{{ $task->number }} - {{ $task->description }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="searchable"></td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="purchase-request-lines-supplier-filter" class="filter-input"  multiple>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="searchable"></td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="purchase-request-lines-approver-filter" class="filter-input" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="purchase-request-lines-buyer-filter" class="filter-input" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="purchase-request-lines-status-filter" class="filter-input" multiple>
                        @foreach ($prlStatuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td class="searchable"></td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="purchase-request-requester-filter" class="filter-input" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="searchable"></td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="purchase-request-status-filter" class="filter-input" multiple>
                        <option value="Open">Open</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Closed">Closed</option>
                    </select>
                </td>
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
        var prlEditor, prlTable;

        $(document).ready(function() {
            // Purchase Request Lines Editor
            prlEditor = new $.fn.dataTable.Editor( {
                ajax: {
                    url: "{{ route('purchase-request-lines-all-update') }}",
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
                            { label: 'ID: {{ $request->id }} | {{ $request->project->description }}', value: '{{ $request->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Item Number:", name: "item_number" },
                    { label: "Item Revision:", name: "item_revision" },
                    { label: "Item Description:", name: "item_description" },
                    { label: "Qty Required:", name: "qty_required" },
                    { label: "Qty Per UOM:", name: "qty_per_uom", def: '1' },
                    { label: "Uom:", name: "uom.id", type: 'select',
                        options: [
                                @foreach ($uoms as $uom)
                            { label: "{{ $uom->name }}", value: "{{ $uom->id }}" },
                            @endforeach
                        ]
                    },
                    { label: "UOM Cost:", name: "cost_per_uom" },
                    { label: "Task:", name: "task.id", type: 'select',
                        options: [
                                @foreach ($tasks as $task)
                            { label: "{{ $task->number }} - {{ $task->description }}", value: "{{ $task->id }}" },
                            @endforeach
                        ]
                    },
                    { label: "Need Date:", name: "need_date", type: 'datetime' },
                    { label: "Supplier:", name: "supplier.id", type: 'select',
                        options: [
                                @foreach ($suppliers as $supplier)
                            { label: '{!! addslashes($supplier->name) !!}', value: "{{ $supplier->id }}" },
                            @endforeach
                        ]
                    },
                    { label: "Notes:", name: "notes" },
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
                    { label: "Status:", name: "prl_status", type: 'select', def: 'Pending Approval',
                        options: [
                                @foreach ($prlStatuses as $status)
                            { label: "{{ addslashes($status) }}", value: "{{ $status }}"},
                            @endforeach
                        ]
                    },
                    { label: "Next Assembly:", name: "next_assembly" },
                    { label: "Work Order:", name: "work_order" },
                    { label: "PO Number:", name: "po_number" }
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

            function format ( d ) {
                // `d` is the original data object for the row
                return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                    '<tr>'+
                    '<td>Buyer\'s Notes:</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<td>'+d.buyers_notes+'</td>'+
                    '</tr>'+
                    '</table>';
            }
            // Purchase Request Lines Datatable
            prlTable = $('#purchase-request-lines-table').DataTable( {
                dom: "Bfrtip",
                ajax: "{{ route('purchase-request-lines-all-data') }}",
                order: [[ 2, 'asc' ]],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false,
                        width: '1%'
                    },
                    {
                        className: 'details-control',
                        orderable: false,
                        data: "id",
                        defaultContent: ''
                    },
                    { data: "item_number" },
                    { data: "item_revision" },
                    { data: "item_description" },
                    { data: "qty_required" },
                    { data: "uom.name", editField: "uom.id" },
                    { data: "qty_per_uom" },
                    { data: "uom_qty_required" },
                    { data: "cost_per_uom" },
                    { data: "total_line_cost" },
                    { data: "task.number", editField: "task.id" },
                    { data: "need_date" },
                    { data: "supplier.name", editField: "supplier.id" },
                    { data: "notes" },
                    { data: "approver" },
                    { data: "buyer" },
                    { data: "prl_status" },
                    { data: "next_assembly" },
                    { data: "work_order" },
                    { data: "po_number" },
                    { data: "pr_id" },
                    { data: "pr_project" },
                    { data: "pr_requester" },
                    { data: "pr_request_date" },
                    { data: "pr_status" },
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                columnDefs: [
                    { className: "text-nowrap", "targets": [4,6,12,13,15,16,17] }
                ],
                paging: false,
                buttons: [
                    { extend: "create", editor: prlEditor, text: "Add" },
                    { extend: "edit",   editor: prlEditor },
                    {
                        extend: "selected",
                        text: 'Duplicate',
                        action: function ( e, dt, node, config ) {
                            // Start in edit mode, and then change to create
                            prlEditor
                                .edit( prlTable.rows( {selected: true} ).indexes(), {
                                    title: 'Duplicate record',
                                    buttons: 'Create from existing'
                                } )
                                .mode( 'create' );
                        }
                    },
                    { extend: "remove", editor: prlEditor }
                ]
            } );

            // Add event listener for opening and closing details
            $('#purchase-request-lines-table tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = prlTable.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child( format(row.data()) ).show();
                    tr.addClass('shown');
                }
            } );

            // add input for each column for Purchase Request Lines Table
            $('#purchase-request-lines-table tfoot td.searchable').each(function(){
                $(this).html('<input class="filter-input" type="text"/>')
            });
            // add search function for Purchase Request Lines Table
            prlTable.columns().every(function(){
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                });
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

            prlEditor.on( 'open', function ( e, mode, action ) {
                {{--var optionsA = [];--}}
                {{--$.getJSON('{{ route('get-select-purchase-requests') }}',--}}
                {{--    function (data) {--}}
                {{--        var option = {};--}}
                {{--        $.each(data, function (i,e) {--}}
                {{--            option.label = e.text;--}}
                {{--            option.value = e.id;--}}
                {{--            optionsA.push(option);--}}
                {{--            option = {};--}}
                {{--        });--}}
                {{--    }--}}
                {{--).done(function() {--}}
                {{--    prlEditor.field('purchase_request').update(optionsA);--}}
                {{--});--}}

                $('#DTE_Field_purchase_request').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
                $('#DTE_Field_uom-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
                $('#DTE_Field_task-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
                $('#DTE_Field_supplier-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
            } );

            // column filters w/ select2
            $('#purchase-request-lines-uom-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-uom-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(6).search(search, true, false).draw();
            });
            $('#purchase-request-lines-task-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-task-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(11).search(search, true, false).draw();
            });
            $('#purchase-request-lines-supplier-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-supplier-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(13).search(search, true, false).draw();
            });
            $('#purchase-request-lines-approver-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-approver-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(15).search(search, true, false).draw();
            });
            $('#purchase-request-lines-buyer-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-buyer-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(16).search(search, true, false).draw();
            });
            $('#purchase-request-lines-status-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-status-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(17).search(search, true, false).draw();
            });
            $('#purchase-request-requester-filter').select2().on('change', function(){
                var search = [];
                $.each($('#purchase-request-requester-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prTable.column(20).search(search, true, false).draw();
            });
            $('#purchase-request-status-filter').select2().on('change', function(){
                var search = [];
                $.each($('#purchase-request-status-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prTable.column(22).search(search, true, false).draw();
            });
        } );
    </script>
    @endsection
