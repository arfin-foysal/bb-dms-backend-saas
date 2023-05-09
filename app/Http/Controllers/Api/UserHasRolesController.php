<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\userHasRole;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class userHasRolesController extends Controller
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
        //userHasRoles
        try {
            $userHasRoles = new userHasRole();
            $userHasRoles->user_id = $request->user_id;
            $userHasRoles->role_id = $request->role_id;
            $userHasRoles->save();
            return response()->json(['message' => 'userHasRoles Created Successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'userHasRoles Created Failed']);
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
        //
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
