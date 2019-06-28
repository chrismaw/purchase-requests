<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\User;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->sortBy('name');
        return view('suppliers', ['users' => $users]);
    }

    public function data()
    {
        return collect(['data' => Supplier::with('createdByUser:id,name')->get()->map(function ($s){
            return [
                'DT_RowId' => 'row_' . $s->id,
                'name' => $s->name,
                'created_by' => $s->createdByUser->name
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
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
//        dd($request->all());
        if ($request->action == 'create'){
            $t = new Supplier();
            $t->name = $request->data[0]['name'];
            $t->is_active = $request->data[0]['active'] == 'Yes' ? true : false;
            $t->created_by = $request->data[0]['created_by'];
            $t->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $t->id,
                'name' => $t->name,
                'active' => $t->is_active ? 'Yes' : 'No',
                'created_by' => $t->createdByUser->name
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit'){
            $t = Supplier::find(substr(array_key_first($request->data),4));
            if ($t instanceof Supplier){
                if (array_key_exists('name',$request->data[array_key_first($request->data)])){
                    $t->name = $request->data[array_key_first($request->data)]['name'];
                }
                if (array_key_exists('active',$request->data[array_key_first($request->data)])){
                    $t->is_active = $request->data[array_key_first($request->data)]['active'] == 'Yes' ? true : false;
                }
                if (array_key_exists('created_by',$request->data[array_key_first($request->data)])){
                    $t->created_by = $request->data[array_key_first($request->data)]['created_by'];
                }
                $t->save();
                $output['data'][] = [
                    'DT_RowId' => 'row_' . $t->id,
                    'name' => $t->name,
                    'active' => $t->is_active ? 'Yes' : 'No',
                    'created_by' => $t->createdByUser->name
                ];
                return response()->json(
                    $output
                );
            }
        } elseif ($request->action == 'remove'){

            $t = Supplier::find(substr(array_key_first($request->data),4));
            if ($t instanceof Supplier){
                $t->delete();

                return response()->json();
            }

        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
