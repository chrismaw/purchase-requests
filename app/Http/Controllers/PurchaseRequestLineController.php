<?php

namespace App\Http\Controllers;

use App\PurchaseRequestLine;
use Illuminate\Http\Request;

class PurchaseRequestLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function data()
    {
        return collect(['data' => PurchaseRequestLine::with(
            'purchaseRequest:id',
            'task:id,number,description',
            'supplier:id,name',
            'uom:id,name',
            'approverUser:id,name',
            'buyerUser:id,name'
            )->get()->map(function ($prl){
            return [
                'DT_RowId' => 'row_' . $prl->id,
                'purchase_request' => $prl->purchaseRequest->id,
                'item_number' => $prl->item_number,
                'item_revision' => $prl->item_revision,
                'item_description' => $prl->item_description,
                'qty_required' => $prl->qty_required,
                'uom' => $prl->uom->name,
                'qty_per_uom' => $prl->qty_per_uom,
                'uom_qty_required' => $prl->uom_qty_required,
                'cost_per_uom' => $prl->cost_per_uom,
                'total_line_cost' => $prl->total_line_cost,
                'task' => $prl->task->number . ' - ' . $prl->task->description,
                'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                'supplier' => $prl->supplier->name,
                'notes' => $prl->notes,
                'approver' => $prl->approverUser->name,
                'buyer' => $prl->buyerUser->name,
                'status' => $prl->status
            ];
        })])->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PurchaseRequestLine  $purchaseRequestLine
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseRequestLine $purchaseRequestLine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PurchaseRequestLine  $purchaseRequestLine
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseRequestLine $purchaseRequestLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseRequestLine  $purchaseRequestLine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseRequestLine $purchaseRequestLine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PurchaseRequestLine  $purchaseRequestLine
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseRequestLine $purchaseRequestLine)
    {
        //
    }
}
