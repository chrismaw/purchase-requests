<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\Auth0IndexController;
use App\Project;
use App\PurchaseRequest;
use App\PurchaseRequestLine;
use App\Supplier;
use App\Task;
use App\Uom;
use App\User;
use Auth0\SDK\Auth0;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd(\Auth::user());
        $users = DB::table('users')->select('id','name')->orderBy('name')->get();
        $projects = DB::table('projects')->select('id','description')->orderBy('description')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $tasks = Task::all()->sortBy('number');
        $uoms = Uom::orderBy('name')->orderBy('sort_order')->get();
        $purchase_requests = PurchaseRequest::all()
            ->where('is_deleted', '=', false)->sortBy('number');
        return view('purchase-requests',[
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

    public function data()
    {
        return collect(['data' => PurchaseRequest::with('requestedByUser:id,name','project')
            ->where('is_deleted', '=', false)->get()->map(function ($pr){
            return [
                'DT_RowId' => 'row_' . $pr->id,
                'id' => $pr->id,
                'project' => ['name' => $pr->project->description, 'id' => $pr->project->id],
                'requester' => ['name' => $pr->requestedByUser->name, 'id' => $pr->requester],
                'request_date' => date('m-d-Y', strtotime($pr->created_at)),
                'purchase_request_status' => $pr->status
            ];
        })])->toJson();
    }

    public function selectData()
    {
        return collect(PurchaseRequest::with('project')
            ->where('is_deleted', '=', false)->get()->map(function ($pr){
            return [
                'id' => $pr->id,
                'text' => $pr->id . ' | ' . $pr->project->description
            ];
        }))->toJson();
    }

    public function select($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        $resp = [
            'id' => $pr->id,
            'text' => $pr->id . ' | ' . $pr->project->description
        ];
        return json_encode($resp);
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
            $p->project_id = $request->data[0]['project']['id'];
            $p->requester = $request->data[0]['requester']['id'];
            $p->created_at = date('Y-m-d H:i:s');
            $p->status = $request->data[0]['purchase_request_status'];
            $p->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $p->id,
                'id' => $p->id,
                'project' => ['name' => $p->project->description, 'id' => $p->project->id],
                'requester' => ['name' => $p->requestedByUser->name, 'id' => $p->requester],
                'request_date' => date('m-d-Y', strtotime($p->created_at)),
                'purchase_request_status' => $p->status
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit'){
//            dd($request->all());
            $output = array();
            foreach ($request->data as $row_id => $data){
                $p = PurchaseRequest::find(substr($row_id,4));
                if ($p instanceof PurchaseRequest){
                    if (array_key_exists('project',$data)){
                        $p->project_id = preg_match('/^\d+$/',$data['project']['id'])
                            ? $data['project']['id']
                            : $p->project_id;
                    }
                    if (array_key_exists('requester',$data)){
                        $p->requester = preg_match('/^\d+$/',$data['requester']['id'])
                            ? $data['requester']['id']
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
                    $p->updated_at = date('Y-m-d H:i:s');
                    $p->save();
                    $output['data'][] = [
                        'DT_RowId' => 'row_' . $p->id,
                        'id' => $p->id,
                        'project' => ['name' => $p->project->description, 'id' => $p->project->id],
                        'requester' => ['name' => $p->requestedByUser->name, 'id' => $p->requester],
                        'request_date' => date('m-d-Y', strtotime($p->created_at)),
                        'purchase_request_status' => $p->status
                    ];
                }
            }
            return response()->json(
                $output
            );

        } elseif ($request->action == 'remove'){

            $p = PurchaseRequest::find(substr(array_key_first($request->data),4));
            if ($p instanceof PurchaseRequest){
                //delete prlines first
// commented out because decision was made to keep prlines active in case parent pr is reactivated
//                $prls = PurchaseRequestLine::where('purchase_request_id','=',$p->id)->get();
//                if ($prls){
//                    foreach ($prls as $prl){
//                        if ($prl instanceof PurchaseRequestLine){
//                            $prl->is_deleted = true;
//                            $prl->save();
//                        }
//                    }
//                }
                $p->is_deleted = true;
                $p->save();

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
