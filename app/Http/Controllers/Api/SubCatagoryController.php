<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\sub_catagory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class subCatagoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $authId = Auth::user()->id;
        $data = sub_catagory::where("user_id", "=", $authId)
            ->with('catagory')->with('user')
            ->latest()
            ->get();
        return response()->json($data);
    }




    public function store(Request $request)
    {



        try {
            $subCatagory = new sub_catagory();
            $request->validate([
                'name' => 'required',
                'catagory_id' => 'required',
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

            $subCatagory->name = $request->name;
            $subCatagory->user_id = auth()->user()->id;
            $subCatagory->catagory_id = $request->catagory_id;
            $subCatagory->description = $request->description;
            // $subCatagory->status = $request->status;
            $subCatagory->image = $filename;
            $result = $subCatagory->save();

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

        $data = sub_catagory::with('catagory')->with('user')->find($id);
        return response()->json($data);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSubSubCatagory($id)
    {

        $data = sub_catagory::with('SubSubCatagory')->with('user')->find($id);
        return response()->json($data);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data  = sub_catagory::findOrFail($id);
        return response()->json($data);
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

            $subCategory = sub_catagory::findOrFail($id);
           

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
            $subCategory = sub_catagory::findOrFail($id);
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
        $data = sub_catagory::where('catagory_id', $id)->get();
        return response()->json($data);
    }


}
