<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class CompanyController extends Controller
{

    public function companyList()
    {
        $data = Company::latest()->get();
        return response()->json($data);
    }


    public function createOrUpdateCompany(Request $request)
    {
        try {

            if (empty($request->id)) {
                

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:companies,email',
                    'unique_id' => 'required|min:4|unique:companies,unique_id',
                    'image' => ' nullable|image|mimes:jpg,png,jpeg,gif,svg',
                    'country' => 'required',
                    'address' => 'required',
                    'number' => 'required',

                ]);
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 409
                    );
                }

                $company = new Company();
                $filename = "";
                if ($image = $request->file('image')) {
                    $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                } else {
                    $filename = Null;
                }


                $company->name = $request->name;
                $company->email = $request->email;
                $company->number = $request->number;
                $company->image = $filename;
                $company->country = $request->country;
                $company->address = $request->address;
                $company->unique_id = $request->unique_id;
                $company->status = $request->status;
                $company->created_by = Auth()->user()->id;
                $company->save();

                $data = [
                    'status' => true,
                    'message' => 'Company created successfully.',
                    'status code' => 200,
                ];
                return response()->json($data);
            } else {

                $company = Company::find($request->id);


                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $company->id,
                    'unique_id' => 'required|min:4|unique:companies,unique_id,' . $company->id,
    
                ]);

                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 409
                    );
                }
                
                $filename = "";
                if ($image = $request->file('image')) {
                    $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                } else {
                    $filename = Null;
                }


                $company->name = $request->name;
                $company->email = $request->email;
                $company->number = $request->number;
                $company->image = $filename;
                $company->country = $request->country;
                $company->address = $request->address;
                $company->unique_id = $request->unique_id;
                $company->status = $request->status;
                $company->updated_by = $request->updated_by;
                $company->save();

                $data = [
                    'status' => true,
                    'message' => 'Company updated successfully.',
                    'status code' => 200,
                ];
                return response()->json($data);
            }
        } catch (\Throwable $th) {

            $data = [
                'status' => false,
                'message' => $th->getMessage(),
                'status code' => 500,
            ];
            return response()->json($data);
        }
    }




    public function companyDelete($id)
    {

        try {
            $company = Company::find($id);
            $company->delete();
            if ($company->image) {
                unlink(public_path("images/" . $company->image));
            }
            $company->delete();
            $data = [
                'status' => true,
                'message' => 'Company deleted successfully.',
                'status code' => 200,
            ];
            return response()->json($data);
        } catch (\Throwable $th) {

            $data = [
                'status' => false,
                'message' => $th->getMessage(),
                'status code' => 500,
            ];
            return response()->json($data);
        }
    }
}
