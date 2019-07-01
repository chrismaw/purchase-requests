<?php

namespace App\Http\Controllers;

use App\Project;
use App\PurchaseRequest;
use App\Supplier;
use App\Task;
use App\Uom;
use App\User;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    const PR_STATUSES = [
        'Open', 'On Hold', 'Closed'
    ];

    const PRL_STATUSES = [
        'Pending Approval', 'Unreleased Drawing', 'Approved for Purchasing',
        'PO in Progress', 'PO Revision', 'Order Complete', 'Request Cancelled'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->sortBy('name');
        $projects = Project::all()->sortBy('number');
        $suppliers = Supplier::all()->sortBy('name');
        $tasks = Task::all()->sortBy('number');
        $uoms = Uom::all()->sortBy('name');
        $purchase_requests = PurchaseRequest::all()->sortBy('number');
        return view('purchase-requests',[
            'users' => $users,
            'projects' => $projects,
            'suppliers' => $suppliers,
            'tasks' => $tasks,
            'uoms' => $uoms,
            'purchase_requests' => $purchase_requests,
            'prStatuses' => self::PR_STATUSES,
            'prlStatuses' => self::PRL_STATUSES
        ]);
    }

    public function data()
    {
        return collect(['data' => PurchaseRequest::with('requestedByUser:id,name','project')->get()->map(function ($pr){
            return [
                'DT_RowId' => 'row_' . $pr->id,
                'id' => $pr->id,
                'project' => $pr->project->number . ' - ' . $pr->project->description,
                'requester' => $pr->requestedByUser ? $pr->requestedByUser->name : '',
                'request_date' => date('m-d-Y', strtotime($pr->created_at)),
                'status' => $pr->status
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
     * @param  \App\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, PurchaseRequest $purchaseRequest)
    public function update(Request $request)
    {
        if ($request->action == 'create'){
            $p = new PurchaseRequest();
            $p->project_id = $request->data[0]['project'];
            $p->requester = $request->data[0]['requester'];
            $p->created_at = date('Y-m-d H:i:s');
            $p->status = $request->data[0]['purchase_request_status'];
            $p->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $p->id,
                'id' => $p->id,
                'project' => $p->project->number . ' - ' . $p->project->description,
                'requester' => $p->requestedByUser ? $p->requestedByUser->name : '',
                'request_date' => date('m-d-Y', strtotime($p->created_at)),
                'status' => $p->status
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit'){
            $output = array();
            foreach ($request->data as $row_id => $data){
                $p = PurchaseRequest::find(substr($row_id,4));
                if ($p instanceof PurchaseRequest){
                    if (array_key_exists('project',$data)){
                        $p->project_id = is_int($data['project'])
                            ? $data['project']
                            : $p->project_id;
                    }
                    if (array_key_exists('requester',$data)){
                        $p->requester = preg_match('/^\d+$/',$data['requester'])
                            ? $data['requester']
                            : $p->requester;
                    }
                    if (array_key_exists('request_date',$data)){
                        $p->created_at = ($data['request_date'] && ($data['request_date'] != date('m-d-Y',strtotime($p->created_at))))
                            ? date('Y-m-d H:i:s',strtotime($data['request_date']))
                            : $p->created_at;
                    }
                    if (array_key_exists('purchase_request_status',$data)){
                        $p->status = $data['purchase_request_status'];
                    }
                    $p->updated_at = date('Y-m-d H:i:s');
                    $p->save();
                    $output['data'][] = [
                        'DT_RowId' => 'row_' . $p->id,
                        'id' => $p->id,
                        'project' => $p->project->number . ' - ' . $p->project->description,
                        'requester' => $p->requestedByUser ? $p->requestedByUser->name : '',
                        'request_date' => date('m-d-Y', strtotime($p->created_at)),
                        'status' => $p->status
                    ];
                }
            }
            return response()->json(
                $output
            );

        } elseif ($request->action == 'remove'){

            $p = PurchaseRequest::find(substr(array_key_first($request->data),4));
            if ($p instanceof PurchaseRequest){
                $p->delete();

                return response()->json();
            }

        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        //
    }
}
