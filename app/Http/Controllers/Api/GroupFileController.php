<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Group_file;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class groupFileController extends Controller
{



    public function createGroupDocumnent(Request $request)
    {
        
        try {
            $authId = Auth::user()->id;

            $document = new Group_file();
            $request->validate([
                'name' => 'required',
                'group_id' => 'required',
                'doc_id' => 'nullable',
                'description' => 'nullable',
                'file' => 'required|mimes:csv,txt,xlx,xls,xlsx,pdf,docx,doc,jpg,png,jpeg,gif,svg',
            ]);

            $filename = "";
            if ($image = $request->file('file')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('file'), $filename);
            } else {
                $filename = Null;
            }
            $document->name = $request->name;
            $document->user_id = $authId;
            $document->group_id = $request->group_id;
            $document->doc_id = $request->doc_id;
            $document->company_id = Auth::user()->company_id;
            $document->description = $request->description;
            $document->file = $filename;
            $document->save();

            $data = [
                'status' => true,
                'message' => 'Document created successfully.',
                'status code' => 200,
                // 'data' => $document,
            ];
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function groupSingalDocumnet($id)
    {
        $data = Group_file::where('id', $id)->where('company_id', Auth::user()->company_id)
            ->with(['user','group'])
            ->first();
        return response()->json($data);
    }


    public function documnetupdate(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);


            $document = Group_file::where('id', $id)->where('company_id', Auth::user()->company_id)->first();
            $imageName = "";
            if ($image = $request->file('file')) {
                if ($document->file && $document->is_shared=='no' ) {
                    unlink(public_path("file/" . $document->file));
                }

                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('file'), $imageName);
            } else {
                $imageName = $document->file;
            }

            $document->name = $request->name;
            $document->description = $request->description;
            $document->file = $imageName;
            $data = $document->save();


            $data = [
                'status' => true,
                'message' => 'Document Update Successfully.',
                'status code' => 200,
                'data' => $document,
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
    public function destroyGroupDocument($id)
    {
   
        try {
            $document = Group_file::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();
            if ($document->file&& $document->is_shared=='no') {
                unlink(public_path("file/" . $document->file));
            }
            $document->delete();
            $data = [
                'status' => true,
                'message' => 'Document Delete Successfully.',
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



    public function getGroupDocument($id)
    {
        try {
            $data = Group_file::where('group_id', $id)
                ->where('company_id', Auth::user()->company_id)
                ->with('user')
                ->with('group')
                ->get();
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function downloadFile($id)
    {
        try {
            $document = Group_file::where([["id", "=", $id], [
                "company_id", "=",
                Auth::user()->company_id
            ]])->first();
            $file = public_path("file/" . $document->file);
            $headers = array(
                'Content-Type: application/pdf',
                
            );
            return response()->download($file, $document->name, $headers); 
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function shareDocument(Request $request)
    {
        try {
            $authId = Auth::user()->id;

            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'file' => 'required',
                'group_id' => 'required',
            ]);



            $existDocument = Group_file::where('company_id', Auth::user()->company_id)->get();

            if(
                $existDocument->where('name',$request->name)
                ->where('group_id',$request->group_id)
                ->first()
            ){
                return response()->json([
                    'status' => false,
                    'message' => 'Document already shared This Group',
                ], 500);
            }

        
            
            $document = new Group_file();
            $document->name = $request->name;
            $document->user_id = $authId;
            $document->doc_id= $request->doc_id;
            $document->group_id = $request->group_id;
            $document->company_id = Auth::user()->company_id;
            $document->description = $request->description;
            $document->file = $request->file;
            $document->is_shared = 'yes';
            $document->save();



            

            $data = [
                'status' => true,
                'message' => 'Document share Successfully.',
                'status code' => 200,
                // 'data' => $document,
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
