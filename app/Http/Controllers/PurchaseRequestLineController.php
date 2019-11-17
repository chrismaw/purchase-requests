<?php

namespace App\Http\Controllers;

use App\PurchaseRequest;
use App\PurchaseRequestLine;
use App\Supplier;
use App\Task;
use App\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestLineController extends Controller
{
    public function data(Request $request)
    {
        $prl_ids = $request->get('prl') ? explode(',',$request->get('prl')) : null;
        if ($request->get('prl')) {
            return collect(['data' => PurchaseRequestLine::with(
                'purchaseRequest:id',
                'task:id,number,description',
                'supplier:id,name',
                'uom:id,name',
                'approverUser:id,name',
                'buyerUser:id,name')
                ->where('is_deleted', '=', false)
                ->when($prl_ids, function ($q) use ($prl_ids) {
                    return $q->whereIn('purchase_request_id', $prl_ids);
                })
                ->get()->map(function ($prl) {
                    $uom_qty_required = ceil(number_format($prl->qty_required / $prl->qty_per_uom,2));
                    return [
                        'DT_RowId' => 'row_' . $prl->id,
                        'purchase_request' => $prl->purchaseRequest->id,
                        'item_number' => $prl->item_number,
                        'item_revision' => $prl->item_revision,
                        'item_description' => $prl->item_description,
                        'qty_required' => $prl->qty_required,
                        'uom' => ['name' => $prl->uom->name, 'id' => $prl->uom_id],
                        'qty_per_uom' => $prl->qty_per_uom,
                        'uom_qty_required' => $uom_qty_required,
                        'cost_per_uom' => '$' . number_format($prl->cost_per_uom, 2),
                        'total_line_cost' => '$' . number_format($prl->cost_per_uom * $uom_qty_required, 2),
                        'task' => ['number' => $prl->task->number, 'id' => $prl->task_id],
                        'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                        'supplier' => $prl->supplier
                            ? ['name' => $prl->supplier->name, 'id' => $prl->supplier_id]
                            : ['name' => '', 'id' => ''],
                        'notes' => $prl->notes,
                        'approver' => $prl->approver
                            ? ['name' => $prl->approverUser->name, 'id' => $prl->approver]
                            : ['name' => '', 'id' => ''],
                        'buyer' => $prl->buyer
                            ? ['name' => $prl->buyerUser->name, 'id' => $prl->buyer]
                            : ['name' => '', 'id' => ''],
                        'prl_status' => $prl->status,
                        'next_assembly' => $prl->next_assembly,
                        'work_order' => $prl->work_order,
                        'po_number' => $prl->po_number,
                    ];
                })])->toJson();
        } else {
            $output = array();
            $output['data'] = array();
            return json_encode($output);
        }
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
            // if action is 'create', data will be an array with a single key of 0
            if (array_key_exists(0,$request->data)){
                $prl = new PurchaseRequestLine();
                $prl->purchase_request_id = $request->data[0]['purchase_request_ID'] ?: $request->data[0]['purchase_request'];
                $prl->item_number = $request->data[0]['item_number'];
                $prl->item_revision = $request->data[0]['item_revision'];
                $prl->item_description = strtoupper($request->data[0]['item_description']);
                $prl->qty_required = $request->data[0]['qty_required'];
                $prl->qty_per_uom = $request->data[0]['qty_per_uom'];
                $prl->uom_id = $request->data[0]['uom']['id'];
                $prl->cost_per_uom = $request->data[0]['cost_per_uom'] ?: '0.00';
                $prl->task_id = $request->data[0]['task']['id'];
                $prl->supplier_id = $request->data[0]['supplier']['id'];
                $prl->notes = $request->data[0]['notes'];
                $prl->need_date = date('Y-m-d H:i:s', strtotime($request->data[0]['need_date']));
                $prl->status = $request->data[0]['prl_status'];
                $prl->next_assembly = $request->data[0]['next_assembly'];
                $prl->work_order = $request->data[0]['work_order'];
                $prl->po_number = $request->data[0]['po_number'];
                $prl->buyers_notes = '';
                $prl->save();

                $uom_qty_required = ceil(number_format($prl->qty_required / $prl->qty_per_uom,2));

                $output['data'][] = [
                    'DT_RowId' => 'row_' . $prl->id,
                    'purchase_request' => $prl->purchaseRequest->id,
                    'item_number' => $prl->item_number,
                    'item_revision' => $prl->item_revision,
                    'item_description' => $prl->item_description,
                    'qty_required' => $prl->qty_required,
                    'uom' => ['name' => $prl->uom->name, 'id' => $prl->uom_id],
                    'qty_per_uom' => $prl->qty_per_uom,
                    'uom_qty_required' => $uom_qty_required,
                    'cost_per_uom' => '$' . number_format($prl->cost_per_uom, 2),
                    'total_line_cost' => '$' . number_format($prl->cost_per_uom * $uom_qty_required, 2),
                    'task' => ['number' => $prl->task->number, 'id' => $prl->task_id],
                    'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                    'supplier' => $prl->supplier
                        ? ['name' => $prl->supplier->name, 'id' => $prl->supplier_id]
                        : ['name' => '', 'id' => ''],
                    'notes' => $prl->notes,
                    'approver' => $prl->approver
                        ? ['name' => $prl->approverUser->name, 'id' => $prl->approver]
                        : ['name' => '', 'id' => ''],
                    'buyer' => $prl->buyer
                        ? ['name' => $prl->buyerUser->name, 'id' => $prl->buyer]
                        : ['name' => '', 'id' => ''],
                    'prl_status' => $prl->status,
                    'next_assembly' => $prl->next_assembly,
                    'work_order' => $prl->work_order,
                    'po_number' => $prl->po_number,
                ];
                return response()->json(
                    $output
                );
            } else {
                // if action is 'duplicate', data will be an array with the row_## as key
                $output = array();
                foreach ($request->data as $row_id => $data){
                    $prl = new PurchaseRequestLine();
                    if (array_key_exists('purchase_request',$data)){
                        $prl->purchase_request_id = $data['purchase_request_ID'] ?: $data['purchase_request'];
                    }
                    if (array_key_exists('item_number',$data)){
                        $prl->item_number = $data['item_number'];
                    }
                    if (array_key_exists('item_revision',$data)){
                        $prl->item_revision = $data['item_revision'];
                    }
                    if (array_key_exists('item_description',$data)){
                        $prl->item_description = $data['item_description'];
                    }
                    if (array_key_exists('qty_required',$data)){
                        $prl->qty_required = $data['qty_required'];
                    }
                    if (array_key_exists('uom',$data)){
                        $prl->uom_id = $data['uom']['id'];
                    }
                    if (array_key_exists('qty_per_uom',$data)){
                        $prl->qty_per_uom = $data['qty_per_uom'];
                    }
                    if (array_key_exists('cost_per_uom',$data)){
                        $prl->cost_per_uom = ltrim($data['cost_per_uom'],'$') ?: '0.00';
                    }
                    if (array_key_exists('task',$data)){
                        $prl->task_id = $data['task']['id'];
                    }
                    if (array_key_exists('need_date',$data)){
                        $prl->need_date = date('Y-d-m H:i:s', strtotime($data['need_date']));
                    }
                    if (array_key_exists('supplier',$data)){
                        $prl->supplier_id = $data['supplier']['id'];
                    }
                    if (array_key_exists('notes',$data)){
                        $prl->notes = $data['notes'];
                    }
                    if (array_key_exists('approver',$data)){
                        $prl->approver = $data['approver']['id'];
                    }
                    if (array_key_exists('buyer',$data)){
                        $prl->buyer = $data['buyer']['id'];
                    }
                    if (array_key_exists('prl_status',$data)){
                        $prl->status = $data['prl_status'];
                    }
                    if (array_key_exists('next_assembly',$data)){
                        $prl->next_assembly = $data['next_assembly'];
                    }
                    if (array_key_exists('work_order',$data)){
                        $prl->work_order = $data['work_order'];
                    }
                    if (array_key_exists('po_number',$data)){
                        $prl->po_number = $data['po_number'];
                    }
                    $prl->buyers_notes = '';
                    $prl->save();

                    $uom_qty_required = ceil(number_format($prl->qty_required / $prl->qty_per_uom,2));

                    $output['data'][] = [
                        'DT_RowId' => 'row_' . $prl->id,
                        'purchase_request' => $prl->purchaseRequest->id,
                        'item_number' => $prl->item_number,
                        'item_revision' => $prl->item_revision,
                        'item_description' => $prl->item_description,
                        'qty_required' => $prl->qty_required,
                        'uom' => ['name' => $prl->uom->name, 'id' => $prl->uom_id],
                        'qty_per_uom' => $prl->qty_per_uom,
                        'uom_qty_required' => $uom_qty_required,
                        'cost_per_uom' => '$' . number_format($prl->cost_per_uom, 2),
                        'total_line_cost' => '$' . number_format($prl->cost_per_uom * $uom_qty_required, 2),
                        'task' => ['number' => $prl->task->number, 'id' => $prl->task_id],
                        'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                        'supplier' => $prl->supplier
                            ? ['name' => $prl->supplier->name, 'id' => $prl->supplier_id]
                            : ['name' => '', 'id' => ''],
                        'notes' => $prl->notes,
                        'approver' => $prl->approver
                            ? ['name' => $prl->approverUser->name, 'id' => $prl->approver]
                            : ['name' => '', 'id' => ''],
                        'buyer' => $prl->buyer
                            ? ['name' => $prl->buyerUser->name, 'id' => $prl->buyer]
                            : ['name' => '', 'id' => ''],
                        'prl_status' => $prl->status,
                        'next_assembly' => $prl->next_assembly,
                        'work_order' => $prl->work_order,
                        'po_number' => $prl->po_number,
                    ];
                }
                return response()->json(
                    $output
                );
            }
        } elseif ($request->action == 'edit'){
            $output = array();
            foreach ($request->data as $row_id => $data){
                $prl = PurchaseRequestLine::find(substr($row_id,4));
                if ($prl instanceof PurchaseRequestLine){
                    if (array_key_exists('purchase_request',$data)){
                        $prl->purchase_request_id = $data['purchase_request']
                            ? $data['purchase_request'] : $prl->purchase_request_id;
                    }
                    if (array_key_exists('item_number',$data)){
                        $prl->item_number = $data['item_number']
                            ? $data['item_number'] : $prl->item_number;
                    }
                    if (array_key_exists('item_revision',$data)){
                        $prl->item_revision = $data['item_revision']
                            ? $data['item_revision'] : $prl->item_revision;
                    }
                    if (array_key_exists('item_description',$data)){
                        $prl->item_description = $data['item_description']
                            ? strtoupper($data['item_description']) : $prl->item_description;
                    }
                    if (array_key_exists('qty_required',$data)){
                        $prl->qty_required = $data['qty_required']
                            ? $data['qty_required'] : $prl->qty_required;
                    }
                    if (array_key_exists('uom',$data)){
                        $prl->uom_id = preg_match('/^\d+$/',$data['uom']['id'])
                            ? $data['uom']['id'] : $prl->uom_id;
                    }
                    if (array_key_exists('qty_per_uom',$data)){
                        $prl->qty_per_uom = $data['qty_per_uom']
                            ? $data['qty_per_uom'] : $prl->qty_per_uom;
                    }
                    if (array_key_exists('cost_per_uom',$data)){
                        $prl->cost_per_uom = $data['cost_per_uom']
                            ? ltrim($data['cost_per_uom'],'$') : $prl->cost_per_uom;
                    }
                    if (array_key_exists('task',$data)){
                        $prl->task_id = preg_match('/^\d+$/',$data['task']['id'])
                            ? $data['task']['id'] : $prl->task_id;
                    }
                    if (array_key_exists('need_date',$data)){
                        $prl->need_date = ($data['need_date'] && ($data['need_date'] != date('m-d-Y',strtotime($prl->need_date))))
                            ? date('Y-m-d H:i:s',strtotime($data['need_date']))
                            : $prl->need_date;
                    }
                    if (array_key_exists('supplier',$data)){
                        $prl->supplier_id = preg_match('/^\d+$/',$data['supplier']['id'])
                            ? $data['supplier']['id'] : $prl->supplier_id;
                    }
                    if (array_key_exists('notes',$data)){
                        $prl->notes = $data['notes']
                            ? $data['notes'] : $prl->notes;
                    }
                    if (array_key_exists('approver',$data)){
                        $prl->approver = preg_match('/^\d+$/',$data['approver']['id'])
                            ? $data['approver']['id'] : $prl->approver;
                    }
                    if (array_key_exists('buyer',$data)){
                        $prl->buyer = preg_match('/^\d+$/',$data['buyer']['id'])
                            ? $data['buyer']['id'] : $prl->buyer;
                    }
                    if (array_key_exists('prl_status',$data)){
                        $prl->status = $data['prl_status']
                            ? $data['prl_status'] : $prl->prl_status;
                    }
                    if (array_key_exists('next_assembly',$data)){
                        $prl->next_assembly = $data['next_assembly']
                            ? $data['next_assembly'] : $prl->next_assembly;
                    }
                    if (array_key_exists('work_order',$data)){
                        $prl->work_order = $data['work_order']
                            ? $data['work_order'] : $prl->work_order;
                    }
                    if (array_key_exists('po_number',$data)){
                        $prl->po_number = $data['po_number']
                            ? $data['po_number'] : $prl->po_number;
                    }
                    $prl->save();

                    $uom_qty_required = ceil(($prl->qty_required / $prl->qty_per_uom));
                    $output['data'][] = [
                        'DT_RowId' => 'row_' . $prl->id,
                        'purchase_request' => $prl->purchaseRequest->id,
                        'item_number' => $prl->item_number,
                        'item_revision' => $prl->item_revision,
                        'item_description' => $prl->item_description,
                        'qty_required' => $prl->qty_required,
                        'uom' => ['name' => $prl->uom->name, 'id' => $prl->uom_id],
                        'qty_per_uom' => $prl->qty_per_uom,
                        'uom_qty_required' => $uom_qty_required,
                        'cost_per_uom' => '$' . number_format($prl->cost_per_uom, 2),
                        'total_line_cost' => '$' . number_format($prl->cost_per_uom * $uom_qty_required, 2),
                        'task' => ['number' => $prl->task->number, 'id' => $prl->task_id],
                        'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                        'supplier' => $prl->supplier
                            ? ['name' => $prl->supplier->name, 'id' => $prl->supplier_id]
                            : ['name' => '', 'id' => ''],
                        'notes' => $prl->notes,
                        'approver' => $prl->approver
                            ? ['name' => $prl->approverUser->name, 'id' => $prl->approver]
                            : ['name' => '', 'id' => ''],
                        'buyer' => $prl->buyer
                            ? ['name' => $prl->buyerUser->name, 'id' => $prl->buyer]
                            : ['name' => '', 'id' => ''],
                        'prl_status' => $prl->status,
                        'next_assembly' => $prl->next_assembly,
                        'work_order' => $prl->work_order,
                        'po_number' => $prl->po_number,
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
    /**
     * Display a listing of all purchase request lines.
     *
     * @return \Illuminate\Http\Response
     */
    public function allIndex()
    {
        $users = DB::table('users')->select('id','name')->orderBy('name')->get();
        $projects = DB::table('projects')->select('id','description')->orderBy('description')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $tasks = Task::all()->sortBy('number');
        $uoms = Uom::orderBy('name')->orderBy('sort_order')->get();
        $purchase_requests = PurchaseRequest::orderBy('id')->get();
        return view('purchase-request-lines-all',[
            'users' => $users,
            'projects' => $projects,
            'suppliers' => $suppliers,
            'tasks' => $tasks,
            'uoms' => $uoms,
            'purchase_requests' => $purchase_requests,
            'prStatuses' => PurchaseRequest::PR_STATUSES,
            'prlStatuses' => PurchaseRequestLine::PRL_STATUSES
        ]);
    }

    public function allData()
    {
        return collect(['data' => PurchaseRequestLine::with(
            'purchaseRequest',
            'task:id,number,description',
            'supplier:id,name',
            'uom:id,name',
            'approverUser:id,name',
            'buyerUser:id,name')
            ->where('is_deleted', '=', false)
            ->get()->map(function ($prl) {
                $uom_qty_required = ceil(number_format($prl->qty_required / $prl->qty_per_uom,2));
                return [
                    'DT_RowId' => 'row_' . $prl->id,
                    'details_control' => '',
                    'purchase_request' => $prl->purchaseRequest->id,
                    'pr_project' => $prl->purchaseRequest->project->description,
                    'pr_requester' => $prl->purchaseRequest->requestedByUser->name,
                    'pr_request_date' => date('m-d-Y', strtotime($prl->purchaseRequest->created_at)),
                    'pr_status' => $prl->purchaseRequest->status,
                    'item_number' => $prl->item_number,
                    'item_revision' => $prl->item_revision,
                    'item_description' => $prl->item_description,
                    'qty_required' => $prl->qty_required,
                    'uom' => ['name' => $prl->uom->name, 'id' => $prl->uom_id],
                    'qty_per_uom' => $prl->qty_per_uom,
                    'uom_qty_required' => $uom_qty_required,
                    'cost_per_uom' => '$' . number_format($prl->cost_per_uom, 2),
                    'total_line_cost' => '$' . number_format($prl->cost_per_uom * $uom_qty_required, 2),
                    'task' => ['number' => $prl->task->number, 'id' => $prl->task_id],
                    'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                    'supplier' => $prl->supplier
                        ? ['name' => $prl->supplier->name, 'id' => $prl->supplier_id]
                        : ['name' => '', 'id' => ''],
                    'notes' => $prl->notes,
                    'approver' => $prl->approver
                        ? ['name' => $prl->approverUser->name, 'id' => $prl->approver]
                        : ['name' => '', 'id' => ''],
                    'buyer' => $prl->buyer
                        ? ['name' => $prl->buyerUser->name, 'id' => $prl->buyer]
                        : ['name' => '', 'id' => ''],
                    'prl_status' => $prl->status,
                    'next_assembly' => $prl->next_assembly,
                    'work_order' => $prl->work_order,
                    'po_number' => $prl->po_number,
                    'buyers_notes' => $prl->buyers_notes,
                    'id' => $prl->id
                ];
            })])->toJson();
    }

    public function allUpdate(Request $request, PurchaseRequestLine $purchaseRequestLine)
    {
        if ($request->action == 'create'){
            // if action is 'create', data will be an array with a single key of 0
            if (array_key_exists(0,$request->data)){
                $prl = new PurchaseRequestLine();
                $prl->purchase_request_id = $request->data[0]['purchase_request'];
                $prl->item_number = $request->data[0]['item_number'];
                $prl->item_revision = $request->data[0]['item_revision'];
                $prl->item_description = strtoupper($request->data[0]['item_description']);
                $prl->qty_required = $request->data[0]['qty_required'];
                $prl->qty_per_uom = $request->data[0]['qty_per_uom'];
                $prl->uom_id = $request->data[0]['uom']['id'];
                $prl->cost_per_uom = $request->data[0]['cost_per_uom'] ?: '0.00';
                $prl->task_id = $request->data[0]['task']['id'];
                $prl->supplier_id = $request->data[0]['supplier']['id'];
                $prl->notes = $request->data[0]['notes'];
                $prl->approver = $request->data[0]['approver']['id'];
                $prl->buyer = $request->data[0]['buyer']['id'];
                $prl->need_date = date('Y-m-d H:i:s', strtotime($request->data[0]['need_date']));
                $prl->status = $request->data[0]['prl_status'];
                $prl->next_assembly = $request->data[0]['next_assembly'];
                $prl->work_order = $request->data[0]['work_order'];
                $prl->po_number = $request->data[0]['po_number'];
                $prl->save();

                $uom_qty_required = ceil(number_format($prl->qty_required / $prl->qty_per_uom,2));

                $output['data'][] = [
                    'DT_RowId' => 'row_' . $prl->id,
                    'details_control' => '',
                    'purchase_request' => $prl->purchaseRequest->id,
                    'pr_project' => $prl->purchaseRequest->project->description,
                    'pr_requester' => $prl->purchaseRequest->requestedByUser->name,
                    'pr_request_date' => date('m-d-Y', strtotime($prl->purchaseRequest->created_at)),
                    'pr_status' => $prl->purchaseRequest->status,
                    'item_number' => $prl->item_number,
                    'item_revision' => $prl->item_revision,
                    'item_description' => $prl->item_description,
                    'qty_required' => $prl->qty_required,
                    'uom' => ['name' => $prl->uom->name, 'id' => $prl->uom_id],
                    'qty_per_uom' => $prl->qty_per_uom,
                    'uom_qty_required' => $uom_qty_required,
                    'cost_per_uom' => '$' . number_format($prl->cost_per_uom, 2),
                    'total_line_cost' => '$' . number_format($prl->cost_per_uom * $uom_qty_required, 2),
                    'task' => ['number' => $prl->task->number, 'id' => $prl->task_id],
                    'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                    'supplier' => $prl->supplier
                        ? ['name' => $prl->supplier->name, 'id' => $prl->supplier_id]
                        : ['name' => '', 'id' => ''],
                    'notes' => $prl->notes,
                    'approver' => $prl->approver
                        ? ['name' => $prl->approverUser->name, 'id' => $prl->approver]
                        : ['name' => '', 'id' => ''],
                    'buyer' => $prl->buyer
                        ? ['name' => $prl->buyerUser->name, 'id' => $prl->buyer]
                        : ['name' => '', 'id' => ''],
                    'prl_status' => $prl->status,
                    'next_assembly' => $prl->next_assembly,
                    'work_order' => $prl->work_order,
                    'po_number' => $prl->po_number,
                    'buyers_notes' => $prl->buyers_notes
                ];
                return response()->json(
                    $output
                );
            } else {
                // if action is 'duplicate', data will be an array with the row_## as key
                $output = array();
                foreach ($request->data as $row_id => $data){
                    $prl = new PurchaseRequestLine();
                    if (array_key_exists('purchase_request',$data)){
                        $prl->purchase_request_id = $data['purchase_request']
                            ? $data['purchase_request'] : $prl->purchase_request_id;
                    }
                    if (array_key_exists('item_number',$data)){
                        $prl->item_number = $data['item_number']
                            ? $data['item_number'] : $prl->item_number;
                    }
                    if (array_key_exists('item_revision',$data)){
                        $prl->item_revision = $data['item_revision']
                            ? $data['item_revision'] : $prl->item_revision;
                    }
                    if (array_key_exists('item_description',$data)){
                        $prl->item_description = $data['item_description']
                            ? strtoupper($data['item_description']) : $prl->item_description;
                    }
                    if (array_key_exists('qty_required',$data)){
                        $prl->qty_required = $data['qty_required']
                            ? $data['qty_required'] : $prl->qty_required;
                    }
                    if (array_key_exists('uom',$data)){
                        $prl->uom_id = preg_match('/^\d+$/',$data['uom']['id'])
                            ? $data['uom']['id'] : $prl->uom_id;
                    }
                    if (array_key_exists('qty_per_uom',$data)){
                        $prl->qty_per_uom = $data['qty_per_uom']
                            ? $data['qty_per_uom'] : $prl->qty_per_uom;
                    }
                    if (array_key_exists('cost_per_uom',$data)){
                        $prl->cost_per_uom = $data['cost_per_uom']
                            ? ltrim($data['cost_per_uom'],'$') : $prl->cost_per_uom;
                    }
                    if (array_key_exists('task',$data)){
                        $prl->task_id = preg_match('/^\d+$/',$data['task']['id'])
                            ? $data['task']['id'] : $prl->task_id;
                    }
                    if (array_key_exists('need_date',$data)){
                        $prl->need_date = date('Y-d-m H:i:s', strtotime($data['need_date']));
                    }
                    if (array_key_exists('supplier',$data)){
                        $prl->supplier_id = preg_match('/^\d+$/',$data['supplier']['id'])
                            ? $data['supplier']['id'] : $prl->supplier_id;
                    }
                    if (array_key_exists('notes',$data)){
                        $prl->notes = $data['notes']
                            ? $data['notes'] : $prl->notes;
                    }
                    if (array_key_exists('approver',$data)){
                        $prl->approver = preg_match('/^\d+$/',$data['approver']['id'])
                            ? $data['approver']['id'] : $prl->approver;
                    }
                    if (array_key_exists('buyer',$data)){
                        $prl->buyer = preg_match('/^\d+$/',$data['buyer']['id'])
                            ? $data['buyer']['id'] : $prl->buyer;
                    }
                    if (array_key_exists('prl_status',$data)){
                        $prl->status = $data['prl_status']
                            ? $data['prl_status'] : $prl->prl_status;
                    }
                    if (array_key_exists('next_assembly',$data)){
                        $prl->next_assembly = $data['next_assembly']
                            ? $data['next_assembly'] : $prl->next_assembly;
                    }
                    if (array_key_exists('work_order',$data)){
                        $prl->work_order = $data['work_order']
                            ? $data['work_order'] : $prl->work_order;
                    }
                    if (array_key_exists('po_number',$data)){
                        $prl->po_number = $data['po_number']
                            ? $data['po_number'] : $prl->po_number;
                    }
                    $prl->save();

                    $uom_qty_required = ceil(number_format($prl->qty_required / $prl->qty_per_uom,2));

                    $output['data'][] = [
                        'DT_RowId' => 'row_' . $prl->id,
                        'details_control' => '',
                        'purchase_request' => $prl->purchaseRequest->id,
                        'pr_project' => $prl->purchaseRequest->project->description,
                        'pr_requester' => $prl->purchaseRequest->requestedByUser->name,
                        'pr_request_date' => date('m-d-Y', strtotime($prl->purchaseRequest->created_at)),
                        'pr_status' => $prl->purchaseRequest->status,
                        'item_number' => $prl->item_number,
                        'item_revision' => $prl->item_revision,
                        'item_description' => $prl->item_description,
                        'qty_required' => $prl->qty_required,
                        'uom' => ['name' => $prl->uom->name, 'id' => $prl->uom_id],
                        'qty_per_uom' => $prl->qty_per_uom,
                        'uom_qty_required' => $uom_qty_required,
                        'cost_per_uom' => '$' . number_format($prl->cost_per_uom, 2),
                        'total_line_cost' => '$' . number_format($prl->cost_per_uom * $uom_qty_required, 2),
                        'task' => ['number' => $prl->task->number, 'id' => $prl->task_id],
                        'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                        'supplier' => $prl->supplier
                            ? ['name' => $prl->supplier->name, 'id' => $prl->supplier_id]
                            : ['name' => '', 'id' => ''],
                        'notes' => $prl->notes,
                        'approver' => $prl->approver
                            ? ['name' => $prl->approverUser->name, 'id' => $prl->approver]
                            : ['name' => '', 'id' => ''],
                        'buyer' => $prl->buyer
                            ? ['name' => $prl->buyerUser->name, 'id' => $prl->buyer]
                            : ['name' => '', 'id' => ''],
                        'prl_status' => $prl->status,
                        'next_assembly' => $prl->next_assembly,
                        'work_order' => $prl->work_order,
                        'po_number' => $prl->po_number,
                        'buyers_notes' => $prl->buyers_notes
                    ];
                }
                return response()->json(
                    $output
                );
            }
        } elseif ($request->action == 'edit'){
            $output = array();
            foreach ($request->data as $row_id => $data){
                $prl = PurchaseRequestLine::find(substr($row_id,4));
                if ($prl instanceof PurchaseRequestLine){
                    if (array_key_exists('purchase_request',$data)){
                        $prl->purchase_request_id = $data['purchase_request']
                            ? $data['purchase_request'] : $prl->purchase_request_id;
                    }
                    if (array_key_exists('item_number',$data)){
                        $prl->item_number = $data['item_number']
                            ? $data['item_number'] : $prl->item_number;
                    }
                    if (array_key_exists('item_revision',$data)){
                        $prl->item_revision = $data['item_revision']
                            ? $data['item_revision'] : $prl->item_revision;
                    }
                    if (array_key_exists('item_description',$data)){
                        $prl->item_description = $data['item_description']
                            ? strtoupper($data['item_description']) : $prl->item_description;
                    }
                    if (array_key_exists('qty_required',$data)){
                        $prl->qty_required = $data['qty_required']
                            ? $data['qty_required'] : $prl->qty_required;
                    }
                    if (array_key_exists('uom',$data)){
                        $prl->uom_id = preg_match('/^\d+$/',$data['uom']['id'])
                            ? $data['uom']['id'] : $prl->uom_id;
                    }
                    if (array_key_exists('qty_per_uom',$data)){
                        $prl->qty_per_uom = $data['qty_per_uom']
                            ? $data['qty_per_uom'] : $prl->qty_per_uom;
                    }
                    if (array_key_exists('cost_per_uom',$data)){
                        $prl->cost_per_uom = $data['cost_per_uom']
                            ? ltrim($data['cost_per_uom'],'$') : $prl->cost_per_uom;
                    }
                    if (array_key_exists('task',$data)){
                        $prl->task_id = preg_match('/^\d+$/',$data['task']['id'])
                            ? $data['task']['id'] : $prl->task_id;
                    }
                    if (array_key_exists('need_date',$data)){
                        $prl->need_date = ($data['need_date'] && ($data['need_date'] != date('m-d-Y',strtotime($prl->need_date))))
                            ? date('Y-m-d H:i:s',strtotime($data['need_date']))
                            : $prl->need_date;
                    }
                    if (array_key_exists('supplier',$data)){
                        $prl->supplier_id = preg_match('/^\d+$/',$data['supplier']['id'])
                            ? $data['supplier']['id'] : $prl->supplier_id;
                    }
                    if (array_key_exists('notes',$data)){
                        $prl->notes = $data['notes']
                            ? $data['notes'] : $prl->notes;
                    }
                    if (array_key_exists('approver',$data)){
                        $prl->approver = preg_match('/^\d+$/',$data['approver']['id'])
                            ? $data['approver']['id'] : $prl->approver;
                    }
                    if (array_key_exists('buyer',$data)){
                        $prl->buyer = preg_match('/^\d+$/',$data['buyer']['id'])
                            ? $data['buyer']['id'] : $prl->buyer;
                    }
                    if (array_key_exists('prl_status',$data)){
                        $prl->status = $data['prl_status']
                            ? $data['prl_status'] : $prl->prl_status;
                    }
                    if (array_key_exists('next_assembly',$data)){
                        $prl->next_assembly = $data['next_assembly']
                            ? $data['next_assembly'] : $prl->next_assembly;
                    }
                    if (array_key_exists('work_order',$data)){
                        $prl->work_order = $data['work_order']
                            ? $data['work_order'] : $prl->work_order;
                    }
                    if (array_key_exists('po_number',$data)){
                        $prl->po_number = $data['po_number']
                            ? $data['po_number'] : $prl->po_number;
                    }
                    $prl->save();

                    $uom_qty_required = ceil(number_format($prl->qty_required / $prl->qty_per_uom,2));

                    $output['data'][] = [
                        'DT_RowId' => 'row_' . $prl->id,
                        'details_control' => '',
                        'purchase_request' => $prl->purchaseRequest->id,
                        'pr_project' => $prl->purchaseRequest->project->description,
                        'pr_requester' => $prl->purchaseRequest->requestedByUser->name,
                        'pr_request_date' => date('m-d-Y', strtotime($prl->purchaseRequest->created_at)),
                        'pr_status' => $prl->purchaseRequest->status,
                        'item_number' => $prl->item_number,
                        'item_revision' => $prl->item_revision,
                        'item_description' => $prl->item_description,
                        'qty_required' => $prl->qty_required,
                        'uom' => ['name' => $prl->uom->name, 'id' => $prl->uom_id],
                        'qty_per_uom' => $prl->qty_per_uom,
                        'uom_qty_required' => $uom_qty_required,
                        'cost_per_uom' => '$' . number_format($prl->cost_per_uom, 2),
                        'total_line_cost' => '$' . number_format($prl->cost_per_uom * $uom_qty_required, 2),
                        'task' => ['number' => $prl->task->number, 'id' => $prl->task_id],
                        'need_date' => date('m-d-Y', strtotime($prl->need_date)),
                        'supplier' => $prl->supplier
                            ? ['name' => $prl->supplier->name, 'id' => $prl->supplier_id]
                            : ['name' => '', 'id' => ''],
                        'notes' => $prl->notes,
                        'approver' => $prl->approver
                            ? ['name' => $prl->approverUser->name, 'id' => $prl->approver]
                            : ['name' => '', 'id' => ''],
                        'buyer' => $prl->buyer
                            ? ['name' => $prl->buyerUser->name, 'id' => $prl->buyer]
                            : ['name' => '', 'id' => ''],
                        'prl_status' => $prl->status,
                        'next_assembly' => $prl->next_assembly,
                        'work_order' => $prl->work_order,
                        'po_number' => $prl->po_number,
                        'buyers_notes' => $prl->buyers_notes
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

    public function updateBuyersNotes(Request $request, $id){
        $prl = PurchaseRequestLine::find($id);
        $success = false;
        if ($prl instanceof PurchaseRequestLine){
            $prl->buyers_notes = $request->note;
            $prl->save();
            $success = true;
        }
        return response()->json([
            'success' => $success
        ]);
    }
    public function approve(Request $request){
        $success = false;
        $message = '';
        foreach ($request->IDs as $id){
            $id = substr($id,4);
            $prl = PurchaseRequestLine::find($id);
            if ($prl instanceof PurchaseRequestLine){
                $prl->status = 'Approved for Purchasing';
                $prl->approver = Auth::user()->id;
                try {
                    $prl->save();
                    $success = true;
                } catch (\Exception $e){
                    if ($success){
                        $message = 'Some of the Purchase Request Lines failed to be approved.';
                    } else {
                        $message = 'None of the Purchase Request Lines were successfully approved.';
                    }
                }
            }
        }
        return response()->json([
            'success' => $success, 'message' => $message
        ]);
    }
}
