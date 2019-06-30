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

    public function data(Request $request)
    {
        $prl_ids = $request->get('prl') ? explode(',',$request->get('prl')) : null;

        return collect(['data' => PurchaseRequestLine::with(
            'purchaseRequest:id',
            'task:id,number,description',
            'supplier:id,name',
            'uom:id,name',
            'approverUser:id,name',
            'buyerUser:id,name')
            ->when($prl_ids, function ($q) use ($prl_ids) {
                return $q->whereIn('purchase_request_id',$prl_ids);
            })
            ->get()->map(function ($prl){
            $uom_qty_required = number_format($prl->qty_required / $prl->qty_per_uom,2);
            return [
                'DT_RowId' => 'row_' . $prl->id,
                'purchase_request' => $prl->purchaseRequest->id,
                'item_number' => $prl->item_number,
                'item_revision' => $prl->item_revision,
                'item_description' => $prl->item_description,
                'qty_required' => $prl->qty_required,
                'uom' => $prl->uom->name,
                'qty_per_uom' => $prl->qty_per_uom,
                'uom_qty_required' => $uom_qty_required,
                'cost_per_uom' => number_format($prl->cost_per_uom,2),
                'total_line_cost' => number_format($prl->cost_per_uom * $uom_qty_required,2),
                'task' => $prl->task->number . ' - ' . $prl->task->description,
                'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                'supplier' => $prl->supplier ? $prl->supplier->name : '',
                'notes' => $prl->notes,
                'approver' => $prl->approverUser ? $prl->approverUser->name : '',
                'buyer' => $prl->buyerUser ? $prl->buyerUser->name : '',
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
        if ($request->action == 'create'){
            $prl = new PurchaseRequestLine();
            $prl->purchase_request_id = $request->data[0]['purchase_request'];
            $prl->item_number = $request->data[0]['item_number'];
            $prl->item_revision = $request->data[0]['item_revision'];
            $prl->item_description = $request->data[0]['item_description'];
            $prl->qty_required = $request->data[0]['qty_required'];
            $prl->qty_per_uom = $request->data[0]['qty_per_uom'];
            $prl->uom_id = $request->data[0]['uom'];
            $prl->cost_per_uom = $request->data[0]['cost_per_uom'];
            $prl->task_id = $request->data[0]['task'];
            $prl->supplier_id = $request->data[0]['supplier'];
            $prl->notes = $request->data[0]['notes'];
            $prl->approver = $request->data[0]['approver'];
            $prl->buyer = $request->data[0]['buyer'];
            $prl->need_date = date('Y-m-d H:i:s', strtotime($request->data[0]['need_date']));
            $prl->status = $request->data[0]['prl_status'];
            $prl->save();

            $uom_qty_required = number_format($prl->qty_required / $prl->qty_per_uom,2);

            $output['data'][] = [
                'DT_RowId' => 'row_' . $prl->id,
                'purchase_request' => $prl->purchaseRequest->id,
                'item_number' => $prl->item_number,
                'item_revision' => $prl->item_revision,
                'item_description' => $prl->item_description,
                'qty_required' => $prl->qty_required,
                'uom' => $prl->uom->name,
                'qty_per_uom' => $prl->qty_per_uom,
                'uom_qty_required' => $uom_qty_required,
                'cost_per_uom' => number_format($prl->cost_per_uom,2),
                'total_line_cost' => number_format($prl->cost_per_uom * $uom_qty_required,2),
                'task' => $prl->task->number . ' - ' . $prl->task->description,
                'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                'supplier' => $prl->supplier ? $prl->supplier->name : '',
                'notes' => $prl->notes,
                'approver' => $prl->approverUser ? $prl->approverUser->name : '',
                'buyer' => $prl->buyerUser ? $prl->buyerUser->name : '',
                'status' => $prl->status
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit'){
            $output = array();
            foreach ($request->data as $row_id => $data){
                $prl = PurchaseRequestLine::find(substr($row_id,4));
                if ($prl instanceof PurchaseRequestLine){
                    if (array_key_exists('purchase_request',$request->data[array_key_first($request->data)])){
                        $prl->purchase_request_id = $request->data[array_key_first($request->data)]['purchase_request'];
                    }
                    if (array_key_exists('item_number',$request->data[array_key_first($request->data)])){
                        $prl->item_number = $request->data[array_key_first($request->data)]['item_number'];
                    }
                    if (array_key_exists('item_revision',$request->data[array_key_first($request->data)])){
                        $prl->item_revision = $request->data[array_key_first($request->data)]['item_revision'];
                    }
                    if (array_key_exists('item_description',$request->data[array_key_first($request->data)])){
                        $prl->item_description = $request->data[array_key_first($request->data)]['item_description'];
                    }
                    if (array_key_exists('qty_required',$request->data[array_key_first($request->data)])){
                        $prl->qty_required = $request->data[array_key_first($request->data)]['qty_required'];
                    }
                    if (array_key_exists('uom',$request->data[array_key_first($request->data)])){
                        $prl->uom_id = $request->data[array_key_first($request->data)]['uom'];
                    }
                    if (array_key_exists('qty_per_uom',$request->data[array_key_first($request->data)])){
                        $prl->qty_per_uom = $request->data[array_key_first($request->data)]['qty_per_uom'];
                    }
                    if (array_key_exists('cost_per_uom',$request->data[array_key_first($request->data)])){
                        $prl->cost_per_uom = $request->data[array_key_first($request->data)]['cost_per_uom'];
                    }
                    if (array_key_exists('task',$request->data[array_key_first($request->data)])){
                        $prl->task_id = $request->data[array_key_first($request->data)]['task'];
                    }
                    if (array_key_exists('need_date',$request->data[array_key_first($request->data)])){
                        $prl->need_date = date('Y-m-d H:i:s', strtotime($request->data[array_key_first($request->data)]['need_date']));
                    }
                    if (array_key_exists('supplier',$request->data[array_key_first($request->data)])){
                        $prl->supplier_id = $request->data[array_key_first($request->data)]['supplier'];
                    }
                    if (array_key_exists('notes',$request->data[array_key_first($request->data)])){
                        $prl->notes = $request->data[array_key_first($request->data)]['notes'];
                    }
                    if (array_key_exists('approver',$request->data[array_key_first($request->data)])){
                        $prl->approver = $request->data[array_key_first($request->data)]['approver'];
                    }
                    if (array_key_exists('buyer',$request->data[array_key_first($request->data)])){
                        $prl->buyer = $request->data[array_key_first($request->data)]['buyer'];
                    }
                    if (array_key_exists('prl_status',$request->data[array_key_first($request->data)])){
                        $prl->status = $request->data[array_key_first($request->data)]['prl_status'];
                    }
                    $prl->save();

                    $uom_qty_required = number_format($prl->qty_required / $prl->qty_per_uom,2);

                    $output['data'][] = [
                        'DT_RowId' => 'row_' . $prl->id,
                        'purchase_request' => $prl->purchaseRequest->id,
                        'item_number' => $prl->item_number,
                        'item_revision' => $prl->item_revision,
                        'item_description' => $prl->item_description,
                        'qty_required' => $prl->qty_required,
                        'uom' => $prl->uom->name,
                        'qty_per_uom' => $prl->qty_per_uom,
                        'uom_qty_required' => $uom_qty_required,
                        'cost_per_uom' => number_format($prl->cost_per_uom,2),
                        'total_line_cost' => number_format($prl->cost_per_uom * $uom_qty_required,2),
                        'task' => $prl->task->number . ' - ' . $prl->task->description,
                        'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                        'supplier' => $prl->supplier ? $prl->supplier->name : '',
                        'notes' => $prl->notes,
                        'approver' => $prl->approverUser ? $prl->approverUser->name : '',
                        'buyer' => $prl->buyerUser ? $prl->buyerUser->name : '',
                        'status' => $prl->status
                    ];
                }
            }
            return response()->json(
                $output
            );

        } elseif ($request->action == 'remove'){

            $p = PurchaseRequestLine::find(substr(array_key_first($request->data),4));
            if ($p instanceof PurchaseRequestLine){
                $p->delete();

                return response()->json();
            }

        };
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
