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
        $users = DB::table('users')->select('id','name')->orderBy('name')->get();
        return view('users',[
            'users' => $users
        ]);
    }

    public function data()
    {
        return collect(['data' => User::all()->map(function ($u){
            return [
                'DT_RowId' => 'row_' . $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'admin' => $u->is_admin ? 'Yes' : 'No',
                'approver' => $u->approver
                    ? ['name' => $u->approverUser->name, 'id' => $u->approver]
                    : ['name' => '', 'id' => ''],
                'buyer' => $u->buyer
                    ? ['name' => $u->buyerUser->name, 'id' => $u->buyer]
                    : ['name' => '', 'id' => ''],
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
            $u->approver = $request->data[0]['approver']['id'];
            $u->buyer = $request->data[0]['buyer']['id'];
            $u->created_at = date('Y-m-d H:i:s');
            $u->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'admin' => $u->is_admin ? 'Yes' : 'No',
                'approver' => $u->approver
                    ? ['name' => $u->approverUser->name, 'id' => $u->approver]
                    : ['name' => '', 'id' => ''],
                'buyer' => $u->buyer
                    ? ['name' => $u->buyerUser->name, 'id' => $u->buyer]
                    : ['name' => '', 'id' => ''],
                'added_on' => date('m-d-Y', strtotime($u->created_at))
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit') {
//            dd($request->all());
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
                if (array_key_exists('approver',$request->data[array_key_first($request->data)])){
                    $u->approver = preg_match('/^\d+$/',$request->data[array_key_first($request->data)]['approver']['id'])
                        ? $request->data[array_key_first($request->data)]['approver']['id']
                        : $u->approver_id;
                }
                if (array_key_exists('buyer',$request->data[array_key_first($request->data)])){
                    $u->buyer = preg_match('/^\d+$/',$request->data[array_key_first($request->data)]['buyer']['id'])
                        ? $request->data[array_key_first($request->data)]['buyer']['id']
                        : $u->buyer;
                }
                $u->updated_at = date('Y-m-d H:i:s');
                $u->save();
                $output['data'][] = [
                    'DT_RowId' => 'row_' . $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'admin' => $u->is_admin ? 'Yes' : 'No',
                    'approver' => $u->approver
                        ? ['name' => $u->approverUser->name, 'id' => $u->approver]
                        : ['name' => '', 'id' => ''],
                    'buyer' => $u->buyer
                        ? ['name' => $u->buyerUser->name, 'id' => $u->buyer]
                        : ['name' => '', 'id' => ''],
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
