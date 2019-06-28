<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
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
        return collect(['data' => Task::with('createdByUser:id,name','project')->get()->map(function ($t){
            return [
                'DT_RowId' => 'row_' . $t->id,
                'task_project' => $t->project->number . ' - ' . $t->project->description,
                'task_number' => $t->number,
                'task_description' => $t->description,
                'task_active' => $t->is_active ? 'Yes' : 'No',
                'task_created_by' => $t->createdByUser->name
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
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
//        dd($request->all());
        if ($request->action == 'create'){
            $t = new Task();
            $t->project_id = $request->data[0]['task_project'];
            $t->number = $request->data[0]['task_number'];
            $t->description = $request->data[0]['task_description'];
            $t->is_active = $request->data[0]['task_active'] == 'Yes' ? true : false;
            $t->created_by = $request->data[0]['task_created_by'];
            $t->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $t->id,
                'task_project' => $t->project->number . ' - ' . $t->project->description,
                'task_number' => $t->number,
                'task_description' => $t->description,
                'task_active' => $t->is_active ? 'Yes' : 'No',
                'task_created_by' => $t->createdByUser->name
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit'){
            $t = Task::find(substr(array_key_first($request->data),4));
            if ($t instanceof Task){
                if (array_key_exists('task_project',$request->data[array_key_first($request->data)])){
                    $t->project_id = $request->data[array_key_first($request->data)]['task_project'];
                }
                if (array_key_exists('task_number',$request->data[array_key_first($request->data)])){
                    $t->number = $request->data[array_key_first($request->data)]['task_number'];
                }
                if (array_key_exists('task_description',$request->data[array_key_first($request->data)])){
                    $t->description = $request->data[array_key_first($request->data)]['task_description'];
                }
                if (array_key_exists('task_active',$request->data[array_key_first($request->data)])){
                    $t->is_active = $request->data[array_key_first($request->data)]['task_active'] == 'Yes' ? true : false;
                }
                if (array_key_exists('task_created_by',$request->data[array_key_first($request->data)])){
                    $t->created_by = $request->data[array_key_first($request->data)]['task_created_by'];
                }
                $t->save();
                $output['data'][] = [
                    'DT_RowId' => 'row_' . $t->id,
                    'task_project' => $t->project->number . ' - ' . $t->project->description,
                    'task_number' => $t->number,
                    'task_description' => $t->description,
                    'task_active' => $t->is_active ? 'Yes' : 'No',
                    'task_created_by' => $t->createdByUser->name
                ];
                return response()->json(
                    $output
                );
            }
        } elseif ($request->action == 'remove'){

            $t = Task::find(substr(array_key_first($request->data),4));
            if ($t instanceof Task){
                $t->delete();

                return response()->json();
            }

        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }
}
