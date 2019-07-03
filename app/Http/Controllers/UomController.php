<?php

namespace App\Http\Controllers;

use App\uom;
use Illuminate\Http\Request;

class UomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('uoms');
    }

    public function data()
    {
        return collect(['data' => Uom::orderBy('name')->orderBy('sort_order')->get()->map(function ($u){
            return [
                'DT_RowId' => 'row_' . $u->id,
                'name' => $u->name,
//                'sort_order' => $u->sort_order
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
     * @param  \App\uom  $uom
     * @return \Illuminate\Http\Response
     */
    public function show(uom $uom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\uom  $uom
     * @return \Illuminate\Http\Response
     */
    public function edit(uom $uom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\uom  $uom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, uom $uom)
    {
//        dd($request->all());
        if ($request->action == 'create'){
            $u = new Uom();
            $u->name = $request->data[0]['name'];
            $u->sort_order = $request->data[0]['sort_order'] ?: 99;
            $u->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $u->id,
                'name' => $u->name,
//                'sort_order' => $u->sort_order
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit'){
            $u = Uom::find(substr(array_key_first($request->data),4));
            if ($u instanceof Uom){
                if (array_key_exists('name',$request->data[array_key_first($request->data)])){
                    $u->name = $request->data[array_key_first($request->data)]['name'];
                }
                if (array_key_exists('sort_order',$request->data[array_key_first($request->data)])){
                    $u->sort_order = $request->data[array_key_first($request->data)]['sort_order'];
                }
                $u->save();
                $output['data'][] = [
                    'DT_RowId' => 'row_' . $u->id,
                    'name' => $u->name,
//                    'sort_order' => $u->sort_order
                ];
                return response()->json(
                    $output
                );
            }
        } elseif ($request->action == 'remove'){

            $u = Uom::find(substr(array_key_first($request->data),4));
            if ($u instanceof Uom){
                $u->delete();

                return response()->json();
            }

        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\uom  $uom
     * @return \Illuminate\Http\Response
     */
    public function destroy(uom $uom)
    {
        //
    }
}
