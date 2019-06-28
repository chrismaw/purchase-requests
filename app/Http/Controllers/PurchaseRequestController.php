<?php

namespace App\Http\Controllers;

use App\PurchaseRequest;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('purchase-requests');
    }

    public function data()
    {
        $output = array();
        $output['data'][] = [
            "DT_RowId" => "row_1",
            "first_name" => "Tiger",
            "last_name" => "Nixon",
            "position" => "System Architect",
            "email" => "t.nixon@datatables.net",
              "office" => "Edinburgh",
              "extn" => "5421",
              "age" => "61",
              "salary" => "320800",
              "start_date" => "2011-04-25"
        ];
        $output['data'][] = [
            "DT_RowId" => "row_2",
            "first_name" => "Chris",
            "last_name" => "Maw",
            "position" => "Developr Architect",
            "email" => "c.maw@datatables.net",
              "office" => "Greenville 1",
              "extn" => "5421",
              "age" => "30",
              "salary" => "2000",
              "start_date" => "2018-04-25"
        ];
        return json_encode($output);
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
        dd($request->all());
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
