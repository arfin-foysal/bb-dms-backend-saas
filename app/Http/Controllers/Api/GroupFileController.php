<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Group_file;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
class groupFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            $document->user_id = $authId ;
            $document->group_id = $request->group_id;
            $document->doc_id = $request->doc_id;
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function groupSingalDocumnet($id)
    {
        $data = Group_file::with('user')->with('group')
        ->find($id);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function documnetupdate(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);
            $filename = "";
            $document = Group_file::findOrFail($id);
            $deleteOldImage = public_path("file" . $document->file);


            if ($image = $request->file('file')) {
                if (File::exists($deleteOldImage)) {
                    File::delete($deleteOldImage);
                }

                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('file'), $filename);
            } else {
                $filename = $request->file;
            }

            $document->name = $request->name;
            $document->description = $request->description;
            $document->file = $filename;
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
        // return 'dcc';
        try {
            $document = Group_file::findOrFail($id);
            $deleteImage = public_path("file" . $document->file);
            if (File::exists($deleteImage)) {
                File::delete($deleteImage);
            }
            $result = $document->delete();
            $data = [
                'status' => true,
                'message' => 'Document Delate Successfully.',
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
            
                   $groupDocument = Group_file::find($id);
        $file = public_path('file/'.$groupDocument->file);
        $headers = array(
            'Content-Type: application/pdf',
        );
        return response()->download($file, $groupDocument->file, $headers); 
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

           
            $document = new Group_file();
            $document->name = $request->name;
            $document->user_id = $authId ;
            $document->group_id = $request->group_id;
            $document->description = $request->description;
            $document->file = $request->file;
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
