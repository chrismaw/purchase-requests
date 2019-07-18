@extends('layouts.app')
@section('title','Purchase Request Lines | All')
@section('styles')
    <style>
        #DTE_Field_purchase_request,
        #DTE_Field_task-id,
        #DTE_Field_supplier-id,
        #DTE_Field_approver-id,
        #DTE_Field_buyer-id,
        #DTE_Field_prl_status,
        #DTE_Field_uom-id {
            padding: 5px 4px;
            width: 100%;
        }
        #DTE_Field_item_description {
            text-transform: uppercase;
        }
        body > div.DTED.DTED_Lightbox_Wrapper > div > div > div > div.DTE.DTE_Action_Create > div.DTE_Body > div > form > div > div.DTE_Field.DTE_Field_Type_select.DTE_Field_Name_prl_status {
            display: none;
        }
        body {
            overflow-x: scroll;
        }
        #purchase-request-lines-table {
            overflow-x: visible;
        }
        #purchase-request-lines-table > tfoot > tr > td:nth-child(12) > span,
        #purchase-request-lines-table > tfoot > tr > td:nth-child(17) > span,
        #purchase-request-lines-table > tfoot > tr > td:nth-child(19) > span,
        #purchase-request-lines-table > tfoot > tr > td:nth-child(21) > span,
        #purchase-request-lines-table > tfoot > tr > td:nth-child(22) > span,
        #purchase-request-lines-table > tfoot > tr > td:nth-child(23) > span {
            width: 100% !important;
        }

        @if (!Auth::user()->isApprover())
            #purchase-request-lines-table > tbody > tr > td:nth-child(21),
        @endif
        @if (!Auth::user()->isBuyer())
            #purchase-request-lines-table > tbody > tr > td:nth-child(22),
        @endif
        #purchase-request-lines-table > tbody > tr > td:nth-child(3),
        #purchase-request-lines-table > tbody > tr > td:nth-child(4),
        #purchase-request-lines-table > tbody > tr > td:nth-child(5),
        #purchase-request-lines-table > tbody > tr > td:nth-child(6),
        #purchase-request-lines-table > tbody > tr > td:nth-child(7),
        #purchase-request-lines-table > tbody > tr > td:nth-child(14),
        #purchase-request-lines-table > tbody > tr > td:nth-child(16) {
            color: #333;
            font-style: italic;
        }
        #purchase-request-lines-table > tfoot > tr > td.details-control {
            background: none;
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
            max-width: 16px;
            max-height: 16px;
            background: url({{ url('/icons/down-caret.png') }}) no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control {
            max-width: 16px;
            max-height: 16px;
            background: url({{ url('/icons/up-caret.png') }}) no-repeat center center;
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid" style="width: unset;">
        <div class="title m-b-md">
            Purchase Request Lines | All
        </div>
        <table id="purchase-request-lines-table" class="display cell-border" cellspacing="0">
            <thead>
            <tr>
                <th></th>
                <th></th>
                <th>Purchase Request ID</th>
                <th>Project</th>
                <th>Requester</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Item Number</th>
                <th>Item Revision</th>
                <th>Item Description</th>
                <th id="qty_required_th">Qty Required</th>
                <th id="uom_th">UOM</th>
                <th id="qty_per_uom_th">Qty Per UOM</th>
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
                <th id="next_assembly_th">Next Assembly</th>
                <th id="work_order_th">Work Order</th>
                <th>PO Number</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td></td>
                <td></td>
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
                ajax: "{{ route('purchase-request-lines-all-update') }}",
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
                    @if (Auth::user()->isApprover())
                        { label: "Approver:", name: "approver.id", type: 'select',
                            options: [
                                { label: '', value: '' },
                                @foreach ($users as $user)
                                    { label: "{{ addslashes($user->name) }}", value: "{{ $user->id }}" },
                                @endforeach
                            ]
                        },
                    @endif
                    @if (Auth::user()->isBuyer())
                        { label: "Buyer:", name: "buyer.id", type: 'select',
                            options: [
                                { label: '', value: '' },
                                @foreach ($users as $user)
                                    { label: "{{ addslashes($user->name) }}", value: "{{ $user->id }}" },
                                @endforeach
                            ]
                        },
                    @endif
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
            $('#purchase-request-lines-table').on( 'click', 'tbody td:not(:first-child):not(:nth-child(2))', function (e) {
                prlEditor.inline( this, {
                    onBlur: 'submit'
                });
            } );

            // format child row for buyers note
            function format ( d ) {
                // `d` is the original data object for the row
                return '<div style="padding-left:65px;">'+
                    '<label style="font-weight:bold; display:block; margin-bottom: 5px;">Buyer\'s Notes</label>'+
                    '<textarea name="note" rows="4" style="width: 600px;" id="buyers_notes_'+d.id+'">'+d.buyers_notes+'</textarea><br>'+
                    '<input onclick="submitNote('+d.id+')" type="button" value="Submit Note"/>'+
                    '</div>';
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
                        data: "details_control",
                        defaultContent: ''
                    },
                    { data: "purchase_request" },
                    { data: "pr_project" },
                    { data: "pr_requester" },
                    { data: "pr_request_date" },
                    { data: "pr_status" },
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
                    { data: "approver.name", editField: "approver.id" },
                    { data: "buyer.name", editField: "buyer.id" },
                    { data: "prl_status" },
                    { data: "next_assembly" },
                    { data: "work_order" },
                    { data: "po_number" },
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                columnDefs: [
                    { className: "text-nowrap", "targets": [5,7,17,24,25] }
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
                    // reload table to make sure note stays after closing child
                    prlTable.ajax.reload();
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

            // validate form fields on create/edit
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
                        if (!/\d/.test(qtyRequired.val())) {
                            qtyRequired.error('A quantity must be a number');
                        }
                        if (!qtyRequired.val()) {
                            qtyRequired.error('A quantity must be provided');
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
                // initiate tooltips on open since these elements dont exist on page load
                tippy('label[for="DTE_Field_qty_required"]',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                    placement: 'left'
                });
                tippy('label[for="DTE_Field_qty_per_uom"]',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                    placement: 'left'
                });
                tippy('label[for="DTE_Field_uom-id"]',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                    placement: 'left'
                });
                tippy('label[for="DTE_Field_next_assembly"]',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                    placement: 'left'
                });
                tippy('label[for="DTE_Field_work_order"]',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                    placement: 'left'
                });

                // select2 for edit fields on page load since elements do not exist on page load
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
                $('#DTE_Field_approver-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
                $('#DTE_Field_buyer-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
                $('#DTE_Field_prl_status').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });

                // add red border for required fields
                $('#DTE_Field_item_description').addClass('is-invalid');
                $('#DTE_Field_qty_required').addClass('is-invalid');
                $('#DTE_Field_qty_per_uom').addClass('is-invalid');
                $('#DTE_Field_need_date').addClass('is-invalid');
                // remove red border
                $('#DTE_Field_item_description').on('keyup keydown', function () {
                    if ($(this).val() === '' && !$(this).hasClass('is-invalid')){
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                $('#DTE_Field_qty_required').on('keyup keydown', function () {
                    if ($(this).val() === '' && !$(this).hasClass('is-invalid')){
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                $('#DTE_Field_qty_per_uom').on('keyup keydown', function () {
                    if ($(this).val() === '' && !$(this).hasClass('is-invalid')){
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                $('#DTE_Field_need_date').on('keyup keydown', function () {
                    if ($(this).val() === '' && !$(this).hasClass('is-invalid')){
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
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
                prlTable.column(11).search(search, true, false).draw();
            });
            $('#purchase-request-lines-task-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-task-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(16).search(search, true, false).draw();
            });
            $('#purchase-request-lines-supplier-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-supplier-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(18).search(search, true, false).draw();
            });
            $('#purchase-request-lines-approver-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-approver-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(20).search(search, true, false).draw();
            });
            $('#purchase-request-lines-buyer-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-buyer-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(21).search(search, true, false).draw();
            });
            $('#purchase-request-lines-status-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#purchase-request-lines-status-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(22).search(search, true, false).draw();
            });
            $('#purchase-request-requester-filter').select2().on('change', function(){
                console.log('here');
                var search = [];
                $.each($('#purchase-request-requester-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(4).search(search, true, false).draw();
            });
            $('#purchase-request-status-filter').select2().on('change', function(){
                var search = [];
                $.each($('#purchase-request-status-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prlTable.column(6).search(search, true, false).draw();
            });
        } );
        // submit buyers note and alert
        function submitNote(id){
            var note_value = $('#buyers_notes_'+id).val();
            $.post("{{ url('/purchase-request-line/buyers-notes') }}/"+id, { note: note_value }, function (data) {
                if (data.success === true){
                    // prlTable.ajax.reload()
                    alert('Note Saved!');
                } else {
                    alert('There was an issue submitting the note!');
                }
            });
        }

        //initialize tooltips
        tippy('#qty_required_th',{
            content: 'Text TBD',
            duration: 0,
            arrow: true,
            boundary: 'window',
            distance: 1
        });
        tippy('#qty_per_uom_th',{
            content: 'Text TBD',
            duration: 0,
            arrow: true,
            boundary: 'window',
            distance: 1
        });
        tippy('#next_assembly_th',{
            content: 'Text TBD',
            duration: 0,
            arrow: true,
            boundary: 'window',
            distance: 1
        });
        tippy('#work_order_th',{
            content: 'Text TBD',
            duration: 0,
            arrow: true,
            boundary: 'window',
            distance: 1
        });
        // initalized after select2 for id
        tippy('#uom_th',{
            content: 'Text TBD',
            duration: 0,
            arrow: true,
            boundary: 'window',
            distance: 1
        });

    </script>
    @endsection
