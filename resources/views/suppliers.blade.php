@extends('layouts.app')
@section('title','Suppliers')
@section('styles')
    <style>
        #DTE_Field_active, #DTE_Field_created_by-id {
            padding: 5px 4px;
            width: 100%;
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
        #suppliers-table > tfoot > tr > td:nth-child(4) > span {
            width: 100% !important;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="title m-b-md">
            Suppliers
        </div>
        <table id="suppliers-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Active</th>
                <th>Added By</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td></td>
                <td class="searchable"></td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="suppliers-active-filter" class="filter-input"><option value="Yes">Yes</option><option value="No">No</option></select>
                </td>
                <td style="padding: 10px 6px 6px 6px;">
                    <select id="suppliers-added-by-filter" class="filter-input" style="width: 100px" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
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
        var suppliersEditor, suppliersTable;

        $(document).ready(function() {
            // Suppliers Editor
            suppliersEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('suppliers-update') }}",
                table: "#suppliers-table",
                fields: [
                    { label: "Name:", name: "name" },
                    { label: "Active:", name: "active", type: 'select',
                        options: ['Yes','No']
                    },
                    { label: "Added by:", name: "created_by.id", type: 'select',
                        options: [
                            { label: "{{ Auth::user()->name }}", value: "{{ Auth::user()->id }}" },
                            @foreach ($users as $user)
                                @if ($user->id == Auth::user()->id)
                                @else
                                    { label: "{{ addslashes($user->name) }}", value: "{{ $user->id }}" },
                                @endif
                            @endforeach
                        ]
                    }
                ],
                i18n: {
                    create: {
                        title:  "Add a new Supplier",
                    },
                    edit: {
                        title:  "Edit Supplier",
                    }
                }
            } );

            suppliersEditor.on( 'preSubmit', function ( e, o, action ) {
                if ( action !== 'remove' ) {
                    var name = this.field('name');

                    if (!name.isMultiValue()){
                        if (!name.val()) {
                            name.error('A name must be provided');
                        }
                    }
                    if ( this.inError() ) {
                        return false;
                    }
                }
            } );
            // Inline edit functionality
            @if (Auth::user()->isAdmin())
                $('#suppliers-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                    suppliersEditor.inline( this, {
                        onBlur: 'submit'
                    });
                } );
            @endif
            //Suppliers Datatable
            suppliersTable = $('#suppliers-table').DataTable( {
                @if (Auth::user()->isAdmin())
                    dom: "Bfrtip",
                @else
                    dom: "frtip",
                @endif
                ajax: "{{ route('suppliers-data') }}",
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
                    {
                        data: "active",
                        width: '10%'
                    },
                    {
                        data: "created_by.name",
                        editField: "created_by.id",
                        width: '20%'
                    }
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
                    { extend: "create", editor: suppliersEditor, text: "Add" },
                    { extend: "edit",   editor: suppliersEditor },
                    { extend: "remove", editor: suppliersEditor }
                ]
            } );

            // add input for each column for Projects Table
            $('#suppliers-table tfoot td.searchable').each(function(){
                $(this).html('<input class="filter-input" type="text"/>')
            });
            // add search function for Projects Table
            suppliersTable.columns().every(function(){
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                });
                $('select', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                })
            });
            suppliersEditor.on( 'open', function ( e, mode, action ) {
                $('#DTE_Field_created_by-id').select2({
                    selectOnClose: true,
                    dropdownAutoWidth : true
                });
            } );


            $('#suppliers-added-by-filter').select2({
                dropdownAutoWidth : true
            }).on('change', function(){
                var search = [];
                $.each($('#suppliers-added-by-filter option:selected'), function(){
                    search.push($(this).val());
                });
                search = search.join('|');
                suppliersTable.column(3).search(search, true, false).draw();
            });
        } );
    </script>
    @endsection
