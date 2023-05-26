<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

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
                $company->status = $request->status;
                $company->save();

                $data = [
                    'status' => true,
                    'message' => 'Company created successfully.',
                    'status code' => 200,
                ];
                return response()->json($data);
            } else {

                $company = Company::find($request->id);

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
                $company->status = $request->status;
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
