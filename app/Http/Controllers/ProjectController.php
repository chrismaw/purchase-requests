<?php

namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->sortBy('name');
        return view('projects', ['users' => $users]);
    }

    public function data()
    {
        return collect(['data' => Project::all()->map(function ($p){
            return [
                'DT_RowId' => 'row_' . $p->id,
                'number' => $p->number,
                'description' => $p->description,
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
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
//        dd($request->all());
        if ($request->action == 'create'){
            $p = new Project();
            $p->number = $request->data[0]['number'];
            $p->description = $request->data[0]['description'];
            $p->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $p->id,
                'number' => $p->number,
                'description' => $p->description
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit'){
            $p = Project::find(substr(array_key_first($request->data),4));
            if ($p instanceof Project){
                if (array_key_exists('number',$request->data[array_key_first($request->data)])){
                    $p->number = $request->data[array_key_first($request->data)]['number'];
                }
                if (array_key_exists('description',$request->data[array_key_first($request->data)])){
                    $p->description = $request->data[array_key_first($request->data)]['description'] ?: $p->description;
                }
                $p->save();
                $output['data'][] = [
                    'DT_RowId' => 'row_' . $p->id,
                    'number' => $p->number,
                    'description' => $p->description
                ];
                return response()->json(
                    $output
                );
            }
        } elseif ($request->action == 'remove'){

            $p = Project::find(substr(array_key_first($request->data),4));
            if ($p instanceof Project){
                $p->delete();

                return response()->json();
            }

        };

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }
}
