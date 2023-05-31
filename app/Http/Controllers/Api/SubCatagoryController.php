<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Sub_catagory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class subCatagoryController extends Controller
{

    public function index()
    {

        $data = Sub_catagory::where([["user_id", "=", Auth::user()->id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])
            ->with('catagory')->with('user')
            ->latest()
            ->get();
        return response()->json($data);
    }




    public function store(Request $request)
    {



        try {
            $subCatagory = new Sub_catagory();
            $request->validate([
                'name' => 'required',
                'catagory_id' => 'required',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg |max:2048',
            ]);

            $filename = "";
            if ($image = $request->file('image')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);
            } else {
                $filename = Null;
            }

            $subCatagory->name = $request->name;
            $subCatagory->user_id = auth()->user()->id;
            $subCatagory->catagory_id = $request->catagory_id;
            $subCatagory->description = $request->description;
            $subCatagory->company_id = auth()->user()->company_id;
            $subCatagory->image = $filename;
            $subCatagory->save();

            $data = [
                'status' => true,
                'message' => 'Sub Category created successfully.',
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $data = Sub_catagory::with('catagory')->with('user')->where([["id", "=", $id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->first();
        return response()->json($data);
    }

    public function showSubSubCatagory($id)
    {
        $data = Sub_catagory::with('SubSubCatagory')->with('user')->where([["id", "=", $id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->first();

        return response()->json($data);
    }



    public function edit($id)
    {
        $data  = Sub_catagory::where([["id", "=", $id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->first();
        return response()->json($data);
    }


    public function update(Request $request, $id)
    {
        try {

            $subCategory = Sub_catagory::where([["id", "=", $id], [
                "company_id", "=",
                Auth::user()->company_id
            ]])->first();;


            $imageName = "";
            if ($image = $request->file('image')) {
                if ($subCategory->image) {
                    unlink(public_path("images/" . $subCategory->image));
                }

                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
            } else {
                $imageName = $subCategory->image;
            }

            $subCategory->name = $request->name;
            $subCategory->description = $request->description;
            $subCategory->status = $request->status;
            $subCategory->image = $imageName;
            $data = $subCategory->save();


            $data = [
                'status' => true,
                'message' => 'Sub Category Update Successfully.',
                'status code' => 200,
                'data' => $subCategory,
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
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
        try {
            $subCategory = Sub_catagory::where([["id", "=", $id], [
                "company_id", "=",
                Auth::user()->company_id
            ]])->first();
            if ($subCategory->image) {
                unlink(public_path("images/" . $subCategory->image));
            }
            $subCategory->delete();
            $data = [
                'status' => true,
                'message' => 'Sub Category Delate Successfully.',
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


    public function subCategoryByCatagory($id)
    {
        $data = Sub_catagory::where([['catagory_id', $id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->get();
        return response()->json($data);
    }
}
