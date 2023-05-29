<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientInfo;
use Illuminate\Http\Request;

class ClientInfoController extends Controller
{

    public function allClientInfo()
    {

        try {
            $clientInfo = ClientInfo::all();
            return response()->json([
                'status' => 200,
                'message' => 'Client Info Data',
                'data' => $clientInfo
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Client Info Data Failed',
                'data' => $th->getMessage()
            ]);
        }
       
    }


    public function addClientInfo(Request $request)
    {
        // return $request->all();

        try {

            $filename = "";
            if ($image = $request->file('company_logo')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);
            } else {
                $filename = Null;
            }
            $userImage = "";
            if ($image = $request->file('company_user_image')) {
                $userImage = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $userImage);
            } else {
                $userImage = Null;
            }



            $clientInfo = new ClientInfo();
            $clientInfo->company_name = $request->company_name;
            $clientInfo->company_address = $request->company_address;
            $clientInfo->company_phone = $request->company_phone;
            $clientInfo->company_email = $request->company_email;
            $clientInfo->company_website = $request->company_website;
            $clientInfo->company_logo = $filename;
            $clientInfo->company_country = $request->company_country;
            $clientInfo->company_user_name = $request->company_user_name;
            $clientInfo->company_user_email = $request->company_user_email;
            $clientInfo->company_user_phone = $request->company_user_phone;
            $clientInfo->company_user_gender = $request->company_user_gender;
            $clientInfo->company_user_image = $userImage;
            $clientInfo->save();

     
            return response()->json([
                'status' => 200,
                'message' => 'Client Info Added Successfully !! Please Contact us to Activation your account ',
                'data' => $clientInfo
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Client Info Added Failed',
                'data' => $th->getMessage()
            ]);
        }
    }


    public function show($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
