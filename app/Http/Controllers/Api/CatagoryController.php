<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\catagory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class catagoryController extends Controller
{

    public function index()
    {
        $auth = Auth::user();
        $data = catagory::where("user_id", "=", $auth->id)
            ->where("company_id", "=", $auth->company_id)
            ->with('user')
            ->latest()
            ->get();
        return response()->json($data);
    }





    public function allCategory()
    {
        $auth = Auth::user();
        $data = catagory::where("user_id", "=", $auth->id)
            ->where("company_id", "=", $auth->company_id)
            ->get();
        return response()->json($data);
    }



    public function store(Request $request)
    {

        try {

            $catagory = new catagory();
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg',
            ]);




            $filename = "";
            if ($image = $request->file('image')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);
            } else {
                $filename = Null;
            }

            $catagory->name = $request->name;
            $catagory->user_id = Auth::user()->id;
            $catagory->description = $request->description;
            $catagory->company_id = Auth::user()->company_id;
            $catagory->image = $filename;
            $catagory->save();

            $data = [
                'status' => true,
                'message' => 'Category created successfully.',
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
        $data = catagory::with('user')
            ->where("company_id", "=", Auth::user()->company_id)
            ->find($id);

        return response()->json($data);
    }


    public function categoryList(Request $request)
    {
        $data = catagory::where("company_id", "=", Auth::user()->company_id)
            ->all();
        return response()->json($data);
    }

    public function showSubCatagory($id)
    {
        $authId = Auth::user()->id;

        $data = catagory::where("user_id", "=", $authId)
        ->where("company_id", "=", Auth::user()->company_id)
            ->with('subCatagory')
            ->with('subSubCatagory')
            ->find($id);

        return response()->json($data);
    }


    public function edit($id)
    {
        $data  = catagory:: where([["id", "=", $id], ["company_id", "=",
        Auth::user()->company_id]])->first();

        return response()->json($data);
    }


    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',

            ]);


            $catagory = 
            catagory::where([["id", "=", $id], ["company_id", "=",
            Auth::user()->company_id]])->first();
            $imageName = "";
            if ($image = $request->file('image')) {
                if ($catagory->image) {
                    unlink(public_path("images/" . $catagory->image));
                }

                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
            } else {
                $imageName = $catagory->image;
            }

            $catagory->name = $request->name;
            $catagory->description = $request->description;
            $catagory->status = $request->status;
            $catagory->image = $imageName;
            $catagory->save();


            $data = [
                'status' => true,
                'message' => 'Category Update Successfully.',
                'status code' => 200,
                // 'data' => $catagory,
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
            $catagory =
                catagory::
                where([["id", "=", $id], ["company_id", "=",
                 Auth::user()->company_id]])->first();

            if ($catagory->image) {
                unlink(public_path("images/" . $catagory->image));
            }
            $catagory->delete();
            $data = [
                'status' => true,
                'message' => 'Category Delate Successfully.',
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
}
