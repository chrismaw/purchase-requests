@extends('layouts.app')
@section('title','Purchase Requests')
@section('styles')
    <style>
        #DTE_Field_project,
        #DTE_Field_requester-id,
        #DTE_Field_purchase_request_status,
        #DTE_Field_uom-id,
        #DTE_Field_task-id,
        #DTE_Field_supplier-id,
        #DTE_Field_prl_status,
        #DTE_Field_purchase_request {
            padding: 5px 4px;
            width: 100%;
        }
        #DTE_Field_item_description {
            text-transform: uppercase;
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
        #purchase-request-lines-table > tbody > tr > td:nth-child(8),
        #purchase-request-lines-table > tbody > tr > td:nth-child(10),
        #purchase-request-lines-table > tbody > tr > td:nth-child(15),
        #purchase-request-lines-table > tbody > tr > td:nth-child(16)
        {
            color: #333;
            font-style: italic;
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
        #purchase-requests-table > tfoot > tr > td:nth-child(4) > span {
            width: 100% !important;
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
                        <option selected value="Open">Open</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Closed">Closed</option>
                    </select>
                </td>
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
                <th>Next Assembly</th>
                <th>Work Order</th>
                <th>PO Number</th>
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
                                { label: '{{ $project->description }}', value: '{{ $project->id }}' },
                            @endforeach
                        ]
                    },
                    { label: "Requester:", name: "requester.id", type: 'select',
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
                                "{{ $status }}",
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
                    {
                        data: "id",
                        width: '1%'
                    },
                    { data: "project" },
                    { data: "requester.name", editField: "requester.id" },
                    {
                        data: "request_date",
                        width: '10%'
                    },
                    {
                        data: "purchase_request_status",
                        width: '10%'
                    },
                ],
                select: {
                    style: 'single'
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
                    $('#purchase-request-status-filter').trigger('change');
                }
            } );
            // create the Show Open Request checkbox
            $('div.pr-toolbar').html('<input type="checkbox" id="status-filter-checkbox" style="margin: 10px 5px 10px 10px" checked="checked"/><label for="status-filter-checkbox">Show Open Requests</label>');
            $('#status-filter-checkbox').on('change', function(){
                if($(this).is(':checked')){
                    document.getElementById('purchase-request-status-filter').value = 'Open';
                    $('#purchase-request-status-filter').trigger('change');
                } else {
                    document.getElementById('purchase-request-status-filter').value = '';
                    $('#purchase-request-status-filter').trigger('change');
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
                });
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
                    { label: "", name: "purchase_request_ID", type: 'hidden'},
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
                    {{--{ label: "Approver:", name: "approver.id", type: 'select',--}}
                    {{--    options: [--}}
                    {{--        { label: '', value: '' },--}}
                    {{--        @foreach ($users as $user)--}}
                    {{--            { label: "{{ addslashes($user->name) }}", value: "{{ $user->id }}" },--}}
                    {{--        @endforeach--}}
                    {{--    ]--}}
                    {{--},--}}
                    {{--{ label: "Buyer:", name: "buyer.id", type: 'select',--}}
                    {{--    options: [--}}
                    {{--        { label: '', value: '' },--}}
                    {{--        @foreach ($users as $user)--}}
                    {{--            { label: "{{ addslashes($user->name) }}", value: "{{ $user->id }}" },--}}
                    {{--        @endforeach--}}
                    {{--    ]--}}
                    {{--},--}}
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
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                columnDefs: [
                    { visible: false, targets: 1 },
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
                });
            });
            prTable.on('select', function (e, dt, type, indexes) {
                prlTable.ajax.reload();
                // prlEditor
                //     .field('purchase_request')
                //     .def(prTable.rows({selected:true}).data().id);
                prID = prTable.rows(indexes).data()[0]['id'];
                setTimeout(function () {
                    prlEditor.set('purchase_request',prID);
                    prlEditor.set('purchase_request_ID',prID);
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

            prEditor.on( 'open', function ( e, mode, action ) {
                $('#DTE_Field_project').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
                $('#DTE_Field_requester-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
            } );
            prlEditor.on( 'open', function ( e, mode, action ) {

                var optionsA = [];
                $.getJSON('{{ route('get-select-purchase-requests') }}',
                    function (data) {
                        var option = {};
                        $.each(data, function (i,e) {
                            option.label = e.text;
                            option.value = e.id;
                            optionsA.push(option);
                            option = {};
                        });
                    }
                ).done(function() {
                    prlEditor.field('purchase_request').update(optionsA);
                });

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
                // $('#DTE_Field_approver-id').select2({
                //     selectOnClose: true,
                //     dropdownAutoWidth : true
                // });
                // $('#DTE_Field_buyer-id').select2({
                //     selectOnClose: true,
                //     dropdownAutoWidth : true
                // });

                tippy('#DTE_Field_qty_required',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                });
                tippy('#DTE_Field_qty_per_uom',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                });
                tippy('#DTE_Field_next_assembly',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                });
                tippy('#DTE_Field_work_order',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                });
                // initalized after select2 for id
                tippy('#select2-DTE_Field_uom-id-container',{
                    content: 'Text TBD',
                    duration: 0,
                    arrow: true,
                });
            } );

            // purchase request table column filters
            $('#purchase-request-requester-filter').select2().on('change', function(){
                var search = [];
                $.each($('#purchase-request-requester-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prTable.column(3).search(search, true, false).draw();
            });
            $('#purchase-request-status-filter').select2().on('change', function(){
                var search = [];
                $.each($('#purchase-request-status-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                prTable.column(5).search(search, true, false).draw();
            });
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

            // remove prl once the purchase request is changed... currently causes errors if the end user clicks into another field to submitting edit onblur
            // prlEditor.on( 'submitComplete', function (e, json, data, action) {
            //     if (action === 'edit'){
            //         prlTable.ajax.reload();
            //     }
            // })
        } );

    </script>
    @endsection
