<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\roleHasPermission;
use Illuminate\Http\Request;

class roleHasPermissionController extends Controller
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
        //role has permission

        try {


            foreach ($request->permission_id as $key => $permissionId) {
                $userHasPer[] = [
                    'role_id' => $request->role_id,
                    'permission_id' => $permissionId,
                ];
            }

            roleHasPermission::insert($userHasPer);

            return response()->json(['message' => 'Permission Assigned Successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $userHasPerDel = roleHasPermission::where('role_id', $id)->get();
            foreach ($userHasPerDel as $key => $value) {
                $value->delete();
            }

         
            foreach ($request->permission_id as $key => $permissionId) {
                $userHasPer[] = [
                    'role_id' => $request->role_id,
                    'permission_id' => $permissionId,
                ];
            }
            
            roleHasPermission::insert($userHasPer);
            return response()->json(['message' => 'Permission Assigned Successfully']);


        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
