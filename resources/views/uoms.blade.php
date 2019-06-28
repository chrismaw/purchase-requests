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
{{--                <th>Sort Order</th>--}}
            </tr>
            </thead>
            <tr>
                <td></td>
                <td></td>
<!--                <td></td>-->
            </tr>
        </table>
    </div>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var uomsEditor;

        $(document).ready(function() {
            // Projects Table
            uomsEditor = new $.fn.dataTable.Editor( {
                ajax: "{{ route('uoms-update') }}",
                table: "#uoms-table",
                fields: [
                    { label: "Name:", name: "name" },
                    // { label: "Sort Order:", name: "sort_order" },
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

            $('#uoms-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
                uomsEditor.inline( this );
            } );

            $('#uoms-table').DataTable( {
                dom: "Bfrtip",
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
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                buttons: [
                    { extend: "create", editor: uomsEditor, text: "Add" },
                    { extend: "edit",   editor: uomsEditor },
                    { extend: "remove", editor: uomsEditor }
                ]
            } );
        } );
    </script>
    @endsection
