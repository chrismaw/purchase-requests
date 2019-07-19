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
                'approver' => $u->approver ? 'Yes' : 'No',
                'buyer' => $u->buyer ? 'Yes' : 'No',
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
        if ($request->action == 'create') {
            $u = new User();
            $u->name = $request->data[0]['name'];
            $u->email = $request->data[0]['email'];
            $u->password = Hash::make($request->data[0]['password']);
            $u->is_admin = $request->data[0]['admin'] == 'Yes' ? true : false;
            $u->approver = $request->data[0]['approver'] == 'Yes' ? true : false;
            $u->buyer = $request->data[0]['buyer'] == 'Yes' ? true : false;
            $u->created_at = date('Y-m-d H:i:s');
            $u->save();
            $output['data'][] = [
                'DT_RowId' => 'row_' . $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'admin' => $u->is_admin ? 'Yes' : 'No',
                'approver' => $u->approver ? 'Yes' : 'No',
                'buyer' => $u->buyer ? 'Yes' : 'No',
                'added_on' => date('m-d-Y', strtotime($u->created_at))
            ];
            return response()->json(
                $output
            );
        } elseif ($request->action == 'edit') {
            $output = array();
            $output['data'] = array();
            foreach ($request->data as $row_id => $data) {
                $u = User::find(substr($row_id,4));
                if ($u instanceof User) {
                    if (array_key_exists('name', $data)) {
                        $u->name = $data['name'];
                    }
                    if (array_key_exists('email', $data)) {
                        $u->email = $data['email'];
                    }
                    if (array_key_exists('password', $data)) {
                        $u->password = $data['password']
                            ? Hash::make($data['password'])
                            : $u->password;
                    }
                    if (array_key_exists('admin', $data)) {
                        $u->is_admin = $data['admin'] == 'Yes' ? true : false;
                    }
                    if (array_key_exists('approver', $data)) {
                        $u->approver = $data['approver'] == 'Yes' ? true : false;
                    }
                    if (array_key_exists('buyer', $data)) {
                        $u->buyer = $data['buyer'] == 'Yes' ? true : false;
                    }
                    $u->updated_at = date('Y-m-d H:i:s');
                    $u->save();
                    $output['data'][] = [
                        'DT_RowId' => 'row_' . $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'admin' => $u->is_admin ? 'Yes' : 'No',
                        'approver' => $u->approver ? 'Yes' : 'No',
                        'buyer' => $u->buyer ? 'Yes' : 'No',
                        'added_on' => date('m-d-Y', strtotime($u->created_at))
                    ];
                }
            }
            return response()->json(
                $output
            );
        } elseif ($request->action == 'remove') {

            $u = User::find(substr(array_key_first($request->data), 4));
            if ($u instanceof User) {
                $u->delete();

                return response()->json();
            }

        };
    }
}
