<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\sub_sub_catagory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class subSubCatagoryController extends Controller
{

    public function index()
    {

        $data = sub_sub_catagory::where([["user_id", "=", Auth::user()->id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])
            ->with('catagory')
            ->with('user')
            ->with('subCatagory')
            ->latest()
            ->get();
        return response()->json($data);
    }




    public function store(Request $request)
    {
        try {
            $subSubCatagory = new sub_sub_catagory();
            $request->validate([
                'name' => 'required',
                'catagory_id' => 'required',
                'sub_catagory_id' => 'required',
                'description' => 'required',
                'image' => 'image|mimes:jpg,png,jpeg,gif,svg',
            ]);

            $filename = "";
            if ($image = $request->file('image')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);
            } else {
                $filename = Null;
            }
            $subSubCatagory->name = $request->name;
            $subSubCatagory->user_id = Auth::user()->id;
            $subSubCatagory->catagory_id = $request->catagory_id;
            $subSubCatagory->sub_catagory_id = $request->sub_catagory_id;
            $subSubCatagory->description = $request->description;
            $subSubCatagory->company_id = Auth::user()->company_id;
            $subSubCatagory->image = $filename;
            $result = $subSubCatagory->save();

            $data = [
                'status' => true,
                'message' => 'Sub Sub CatEgory created successfully.',
                'status code' => 200,
            ];
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $data = sub_sub_catagory::with('catagory')->with('user')->with('subCatagory')->where([["id", "=", $id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->first();
        return response()->json($data);
    }


    public function edit($id)
    {
        $data = sub_sub_catagory::where([["id", "=", $id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->first();
        return response()->json($data);
    }


    public function update(Request $request, $id)
    {
        try {
            $subSubCatagory = sub_sub_catagory::where([["id", "=", $id], [
                "company_id", "=",
                Auth::user()->company_id
            ]])->first();

            $imageName = "";
            if ($image = $request->file('image')) {
                if ($subSubCatagory->image) {
                    unlink(public_path("images/" . $subSubCatagory->image));
                }

                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
            } else {
                $imageName = $subSubCatagory->image;
            }




            $subSubCatagory->name = $request->name;
            $subSubCatagory->description = $request->description;
            $subSubCatagory->status = $request->status;
            $subSubCatagory->image = $imageName;
            $data = $subSubCatagory->save();


            $data = [
                'status' => true,
                'message' => 'Sub Sub Category Update Successfully.',
                'status code' => 200,
                'data' => $subSubCatagory,
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $subCategory   = sub_sub_catagory::where([["id", "=", $id], [
                "company_id", "=",
                Auth::user()->company_id
            ]])->first();
            if ($subCategory->image) {
                unlink(public_path("images/" . $subCategory->image));
            }
            $subCategory->delete();
            $data = [
                'status' => true,
                'message' => 'Sub Sub Category Delate Successfully.',
                'status code' => 200,
            ];
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getSubSubCatagoryBySubCatagoryId($id)
    {
        $data = sub_sub_catagory::where([["sub_catagory_id", "=", $id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->get();
        return response()->json($data);
    }
}
