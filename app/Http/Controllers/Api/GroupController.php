<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Group_member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class groupController extends Controller
{

    public function userWiseGroupView()
    {
        try {
            $member = Group_member::where('user_id', Auth::user()->id)
                ->where('company_id', Auth::user()->company_id)
                ->with('group', 'group.groupCreator')
                ->latest()
                ->get();
            $data = [
                'status' => true,
                'message' => 'Group list.',
                'status code' => 200,
                'data' => $member,
            ];

            // $member=DB::table('groups')
            // ->join('group_members','groups.id','=','group_members.group_id')
            // ->join('users','groups.user_id','=','users.id')
            // ->select('groups.*','users.name as group_creator_name')
            // ->where('group_members.user_id',$authId)
            // ->get();

            $data = [
                'status' => true,
                'message' => 'Group list.',
                'status code' => 200,
                'data' => $member,
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }





    public function createGroup(Request $request)
    {
        
      DB::beginTransaction();
        try {
            $authId = Auth::user()->id;
            $companyId = Auth::user()->company_id;
            $group = new Group();
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'member' => 'required',
                'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg',


            ]);

            $filename = "";
            if ($image = $request->file('image')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);
            } else {
                $filename = Null;
            }


            $group->name = $request->name;
            $group->user_id = $authId;
            $group->description = $request->description;
            $group->company_id = $companyId;
            $group->image = $filename;
            $group->save();

            $groupArr = json_decode($request->member);

            if ($groupArr) {
                foreach ($groupArr as $key => $userId) {
                    $groupMember[] = [
                        'group_id' => $group->id,
                        'user_id' => $userId,
                        'company_id' => $companyId,
                    ];
                }
                Group_member::insert($groupMember);
                   if (!in_array($authId, $groupArr)) {
                    $memberAdd=new Group_member();
                    $memberAdd->group_id = $group->id;
                    $memberAdd->user_id = $authId;
                    $memberAdd->company_id = $companyId;
                    $memberAdd->save();
                }

            }



            $data = [
                'status' => true,
                'message' => 'Group created successfully.',
                'status code' => 200,
            ];
            DB::commit();
            return response()->json($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function singalGroup($id)
    {
        $group = Group::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->with('user')
            ->first();


        $data = [
            'status' => true,
            'message' => 'Group list.',
            'status code' => 200,
            'data' => $group,
        ];
        return response()->json($data);
    }




    public function updateGroup(Request $request, $id)
    {

      DB::beginTransaction();

        try {
            $group = Group::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();
            $authId = Auth::user()->id;
            $companyId = Auth::user()->company_id;

            $request->validate([
                'name' => 'required',
                'description' => 'required',

            ]);

            $imageName = "";
            if ($image = $request->file('image')) {
                if ($group->image) {
                    unlink(public_path("images/" . $group->image));
                }

                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
            } else {
                $imageName = $group->image;
            }

            $group->name = $request->name;
            $group->description = $request->description;
            $group->image = $imageName;
            $group->save();

            $userHasPerDel = Group_member::where('group_id', $group->id)
            ->where('company_id', $companyId)
            ->get();

            foreach ($userHasPerDel as $key => $value) {
                $value->delete();
            }

            $groupArr = json_decode($request->member);


            if ($groupArr > 0) {

                if (!in_array($authId, $groupArr)) {
                    $memberAdd=new Group_member();
                    $memberAdd->group_id = $group->id;
                    $memberAdd->user_id = $authId;
                    $memberAdd->company_id = $companyId;
                    $memberAdd->save();
                }

                foreach ($groupArr as $key => $userId) {
                    $groupMember[] = [
                        'group_id' => $group->id,
                        'user_id' => $userId,
                        'company_id' => $companyId,
                        
                    ];
                }
                Group_member::insert($groupMember);
            }

        


            $data = [
                'status' => true,
                'message' => 'Group Update Successfully.',
                'status code' => 200,
                'data' => $group,
            ];

            DB::commit();

            return response()->json($data);
        } catch (\Throwable $th) {
            DB::rollBack();
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
    public function destroyGroup($id)
    {
        try {
            $group = Group::where('id', $id)
                ->where('company_id', Auth::user()->company_id)
                ->first();
            if ($group->image) {
                unlink(public_path("images/" . $group->image));
            }
            $group->delete();
            $data = [
                'status' => true,
                'message' => 'Group Delate Successfully.',
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
