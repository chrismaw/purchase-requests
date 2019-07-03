@extends('layouts.app')
@section('title','Unit of Measures')
@section('styles')@endsection
@section('content')
    <div class="container">
        <div class="title m-b-md">
            Unit of Measures
        </div>
        <table id="uoms-table" class="display" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <!--<th>Sort Order</th>-->
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td></td>
                <td class="searchable"></td>
                <!--<td class="searchable"></td>-->
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
        var uomsEditor, uomsTable;

        $(document).ready(function() {
            // UOMS Editor
            uomsEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('uoms-update') }}",
                table: "#uoms-table",
                fields: [
                    { label: "Name:", name: "name" },
                    { label: "Sort Order:", name: "sort_order", def: '99'},
                ],
                i18n: {
                    create: {
                        title:  "Add a new Unit of Measure",
                    },
                    edit: {
                        title:  "Edit Unit of Measure",
                    }
                }
            } );
            // Inline edit functionality
            @if (Auth::user()->isAdmin())
                $('#uoms-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                    uomsEditor.inline( this, {
                        onBlur: 'submit'
                    });
                } );
            @endif
            // UOMS datatable
            uomsTable = $('#uoms-table').DataTable( {
                @if (Auth::user()->isAdmin())
                    dom: "Bfrtip",
                @else
                    dom: "frtip",
                @endif
                ajax: "{{ route('uoms-data') }}",
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
                    // { data: "sort_order" }
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
                    { extend: "create", editor: uomsEditor, text: "Add" },
                    { extend: "edit",   editor: uomsEditor },
                    { extend: "remove", editor: uomsEditor }
                ]
            } );

            // add input for each column for Projects Table
            $('#uoms-table tfoot td.searchable').each(function(){
                $(this).html('<input class="filter-input" type="text"/>')
            });
            // add search function for Projects Table
            uomsTable.columns().every(function(){
                let that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if(that.search !== this.value){
                        that.search(this.value).draw();
                    }
                })
            });
        } );
    </script>
    @endsection
