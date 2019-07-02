<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('users');
    }

    public function data()
    {
        return collect(['data' => User::all()->map(function ($u){
            return [
                'DT_RowId' => 'row_' . $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'admin' => $u->is_admin ? 'Yes' : 'No',
                'added_on' => date('m-d-Y', strtotime($u->created_at))
            ];
        })])->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
//        dd($request->all());
        if ($request->action == 'create') {
            $u = new User();
            $u->name = $request->data[0]['name'];
            $u->email = $request->data[0]['email'];
            $u->password = Hash::make($request->data[0]['password']);
            $u->is_admin = $request->data[0]['admin'] == 'Yes' ? true : false;
            $u->created_at = date('Y-m-d H:i:s');
            $u->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'admin' => $u->is_admin ? 'Yes' : 'No',
                'added_on' => date('m-d-Y', strtotime($u->created_at))
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit') {
            $u = User::find(substr(array_key_first($request->data), 4));
            if ($u instanceof User) {
                if (array_key_exists('name', $request->data[array_key_first($request->data)])) {
                    $u->name = $request->data[array_key_first($request->data)]['name'];
                }
                if (array_key_exists('email',$request->data[array_key_first($request->data)])){
                    $u->email = $request->data[array_key_first($request->data)]['email'];
                }
                if (array_key_exists('password',$request->data[array_key_first($request->data)])){
                    $u->password = $request->data[array_key_first($request->data)]['password']
                        ? Hash::make($request->data[array_key_first($request->data)]['password'])
                        : $u->password;
                }
                if (array_key_exists('admin',$request->data[array_key_first($request->data)])){
                    $u->is_admin = $request->data[array_key_first($request->data)]['admin'] == 'Yes' ? true : false;
                }
                $u->updated_at = date('Y-m-d H:i:s');
                $u->save();
                $output['data'][] = [
                    'DT_RowId' => 'row_' . $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'admin' => $u->is_admin ? 'Yes' : 'No',
                    'added_on' => date('m-d-Y', strtotime($u->created_at))
                ];
                return response()->json(
                    $output
                );
            }
        } elseif ($request->action == 'remove') {

            $u = User::find(substr(array_key_first($request->data), 4));
            if ($u instanceof User) {
                $u->delete();

                return response()->json();
            }

        };
    }
}
