<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class userController extends Controller
{

    public function index()
    {
        $data = User::where([
            ['user_type', '!=', 'Systemadmin'],
            ['user_type', '!=', 'Superadmin'],
            ['company_id', '=', Auth::user()->company_id]
        ])
            ->latest()
            ->get();
        return response()->json($data);
    }

    public function allUser()
    {
        $data = User::where([['company_id', '=', Auth::user()->company_id], ['user_type', '!=', 'Systemadmin']])
            ->latest()
            ->get();
        return response()->json($data);
    }
    public function allUserforGroup()
    { {
            $authId = Auth::user()->id;
            $data = User::where([
                ['company_id', '=', Auth::user()->company_id],
                ['user_type', '!=', 'Systemadmin'],
            ])
                ->latest()
                ->get();
            return response()->json($data);
        }
    }




    public function store(Request $request)
    {
        try {
            $user = new User();
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|min:4|unique:users,username',
                'password' => [
                    'required',
                    'min:6',
                    // 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                ],
                'image' => ' nullable|image|mimes:jpg,png,jpeg,gif,svg',
                'gender' => 'required',
                'number' => 'required',

            ]);


            $filename = "";
            if ($image = $request->file('image')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);
            } else {
                $filename = Null;
            }


            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->number = $request->number;
            $user->password = bcrypt($request->password);
            $user->status = $request->status;
            $user->gender = $request->gender;
            $user->company_id = Auth::user()->company_id;
            $user->user_type = $request->user_type;
            $user->image = $filename;

            $user->save();


            $data = [
                'status' => true,
                'message' => 'User created successfully.',
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
        $user = User::where([['users.id', '=', $id], ['company_id', '=', Auth::user()->company_id]])
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->select('users.*', 'companies.name as company_name')
            ->first();
        $data = [
            'status' => true,
            'message' => 'User Update Successfully.',
            'status code' => 200,
            'data' => $user,
        ];

        return response()->json($data);
    }





    public function update(Request $request, $id)
    {


        try {

            $user = User::where([['id', '=', $id], ['company_id', '=', Auth::user()->company_id]])->first();
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'username' => 'required|min:4|unique:users,username,' . $user->id,

            ]);


            $imageName = "";
            if ($image = $request->file('image')) {
                if ($user->image) {
                    unlink(public_path("images/" . $user->image));
                }

                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
            } else {
                $imageName = $user->image;
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->number = $request->number;
            $user->status = $request->status;
            $user->gender = $request->gender;
            $user->user_type = $request->user_type;
            $user->image = $imageName;
            $user->save();




            $data = [
                'status' => true,
                'message' => 'User Update Successfully.',
                'status code' => 200,
                'data' => $user,
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
            $user = User::where([['id', '=', $id], ['company_id', '=', Auth::user()->company_id]])->first();
            if ($user->image) {
                unlink(public_path("images/" . $user->image));
            }
            $user->delete();
            $data = [
                'status' => true,
                'message' => 'User deleted successfully.',
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


    public function superAdminCreateOrUpdateAndCompanyAssign(Request $request)
    {

        try {
            if (empty($request->id)) {

                $user = new User();
                $request->validate([
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'username' => 'required|min:4|unique:users,username',
                    'company_id' => 'required',
                    'password' => [
                        'required',
                        'min:6',
                        // 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                    ],
                    'image' => ' nullable|image|mimes:jpg,png,jpeg,gif,svg',
                    'gender' => 'required',
                    'number' => 'required',

                ]);


                $filename = "";
                if ($image = $request->file('image')) {
                    $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                } else {
                    $filename = Null;
                }


                $user->name = $request->name;
                $user->email = $request->email;
                $user->username = $request->username;
                $user->number = $request->number;
                $user->password = bcrypt($request->password);
                $user->status = $request->status;
                $user->gender = $request->gender;
                $user->company_id = $request->company_id;
                $user->user_type = "Superadmin";
                $user->image = $filename;
                $user->save();


                $data = [
                    'status' => true,
                    'message' => 'User created successfully.',
                    'status code' => 200,
                ];
                return response()->json($data);
            } else {


                $user = User::where('id', '=', $request->id)->first();
                $request->validate([
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $user->id,
                    'username' => 'required|min:4|unique:users,username,' . $user->id,

                ]);


                $imageName = "";
                if ($image = $request->file('image')) {
                    if ($user->image) {
                        unlink(public_path("images/" . $user->image));
                    }

                    $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                } else {
                    $imageName = $user->image;
                }

                $user->name = $request->name;
                $user->email = $request->email;
                $user->username = $request->username;
                $user->number = $request->number;
                $user->status = $request->status;
                $user->gender = $request->gender;
                $user->company_id = $request->company_id;
                $user->image = $imageName;
                $user->save();




                $data = [
                    'status' => true,
                    'message' => 'User Update Successfully.',
                    'status code' => 200,
                    'data' => $user,
                ];

                return response()->json($data);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function superAdminList()
    {
        try {
            $user = User::where('user_type', '=', 'Superadmin')
                ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
                ->select('users.*', 'companies.name as company_name')
                ->get();
            $data = [
                'status' => true,
                'message' => 'Superadmin List.',
                'status code' => 200,
                'data' => $user,
            ];
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function superAdminDelete($id)
    {
        try {
            $user = User::where('id', '=', $id)->first();
            if ($user->image) {
                unlink(public_path("images/" . $user->image));
            }
            $user->delete();
            $data = [
                'status' => true,
                'message' => 'Superadmin deleted successfully.',
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






    public function ChangePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'validations fails',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find(Auth::user()->id);

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);


            return response()->json([
                'message' => ' password successfully updated',
                'errors' => $validator->errors()
            ], 200);
        } else {
            return response()->json([
                'message' => 'old password does not match',
                'errors' => $validator->errors()
            ], 422);
        }
    }
}
