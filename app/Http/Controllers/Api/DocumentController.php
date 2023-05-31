<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Group;
use App\Models\Group_file;
use App\Models\Sub_catagory;
use App\Models\Sub_sub_catagory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;


class documentController extends Controller
{

    public function allDocument()
    {

        $data = Document::where([["user_id", "=", Auth::user()->id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->get();
        return response()->json($data);
    }


    public function uploadeDocument(Request $request)
    {
        try {
            $document = new Document();
            $request->validate([
                'name' => 'required',
                // 'user_id' => 'required',
                'catagory_id' => 'required',
                'sub_catagory_id' => 'nullable',
                'sub_sub_catagory_id' => 'nullable',
                'description' => 'nullable',
                'file' => 'required|mimes:csv,txt,xlx,xls,xlsx,pdf,docx,doc,jpg,png,pptx,ppt,jpeg,gif,svg',
            ]);

            $filename = "";
            if ($image = $request->file('file')) {
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('file'), $filename);
            } else {
                $filename = Null;
            }
            $document->name = $request->name;
            $document->user_id = Auth::user()->id;
            $document->catagory_id = $request->catagory_id;
            $document->sub_catagory_id = $request->sub_catagory_id;
            $document->company_id = Auth::user()->company_id;
            $document->sub_sub_catagory_id = $request->sub_sub_catagory_id;
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


    public function documnetByCategory($id)
    {
        $data = Document::where([["catagory_id", "=", $id], [
            "company_id", "=",
            Auth::user()->company_id
        ]])->with('user')->get();
        return response()->json($data);
    }



    public function update(Request $request, $id)
    {


        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);

            $document = Document::where([["id", "=", $id], [
                "company_id", "=",
                Auth::user()->company_id
            ]])->first();

            $imageName = "";
            if ($image = $request->file('file')) {
                if ($document->file) {
                    unlink(public_path("file/" . $document->file));
                }

                $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('file'), $imageName);
            } else {
                $imageName = $document->file;
            }


            $document->name = $request->name;
            $document->description = $request->description;
            $document->status = $request->status;
            $document->file = $imageName;
            $data = $document->save();

            Group_file::where('doc_id', $id)
                ->update(
                    [
                        'name' => $request->name,
                        'description' => $request->description,
                        'file' => $imageName
                    ]
                );


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

    public function destroy($id)
    {
        try {
            $document = Document::where([["id", "=", $id], [
                "company_id", "=",
                Auth::user()->company_id
            ]])->first();
            if ($document->file) {
                unlink(public_path("file/" . $document->file));
            }
            $document->delete();
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


    public function download($id)
    {
        try {
            $document = Document::where([["id", "=", $id], [
                "company_id", "=",
                Auth::user()->company_id
            ]])->first();
            $file = public_path("file/" . $document->file);

            $headers = array(
                'Content-Type: application/zip',
            );

            return response()->download($file, $document->name, $headers);






            // return response()->download($file, $document->name, $headers); 

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }






    public function showCategoryDocument($id)

    {
        $data = Document::where('catagory_id', $id)
            ->where('sub_catagory_id', null)
            ->where('sub_sub_catagory_id', null)
            ->where('user_id', '=', Auth::user()->id)
            ->where('company_id', '=', Auth::user()->company_id)
            ->with('user')
            ->with('catagory')
            ->latest()
            ->get();

        return response()->json($data);
    }

    public function showSubCategory($id)
    {
        $data = Sub_catagory::where('catagory_id', $id)
            ->where('company_id', '=', Auth::user()->company_id)
            ->with('document')
            ->get();
        return response()->json($data);
    }


    public function showSubCategoryDocument($id)
    {
        $data = Document::where('sub_catagory_id', $id)
            ->where('sub_sub_catagory_id', null)
            ->where('company_id', '=', Auth::user()->company_id)
            ->with('user')
            ->with('catagory')
            ->latest()
            ->get();
        return response()->json($data);
    }
    public function showSubSubCategory($id)
    {
        $data = Sub_sub_catagory::where('sub_catagory_id', $id)
            ->where('company_id', '=', Auth::user()->company_id)
            ->with('document')
            ->get();
        return response()->json($data);
    }
    public function showSubSubCategoryDocument($id)
    {
        $data = Document::where('sub_sub_catagory_id', $id)
            ->where('company_id', '=', Auth::user()->company_id)
            ->with('user')
            ->with('catagory')
            ->latest()
            ->get();
        return response()->json($data);
    }

    public function documentPublish($id)
    {
        $document = Document::where([['id', '=', $id], ['company_id', '=', Auth::user()->company_id]])->first();
        $document->status = "Active";
        $document->save();
        $data = [
            'status' => true,
            'message' => 'Document Publish Successfully.',
            'status code' => 200,
        ];
        return response()->json($data);
    }

    public function AdminUnpubishDocumentList()
    {
        $data = Document::where('admin_status', 'Pending')
            ->where('company_id', '=', Auth::user()->company_id)
            ->where('status', 'Active')
            ->with('user')
            ->latest()
            ->get();
        return response()->json($data);
    }

    public function AdminPublishDocument($id)
    {
        $data = Document::where([['id', '=', $id], ['company_id', '=', Auth::user()->company_id]])->first();
        $data->admin_status = "Active";
        $data->save();
        $data = [
            'status' => true,
            'message' => 'Document Publish Successfully.',
            'status code' => 200,
        ];
        return response()->json($data);
    }


    public function AdminCancelPublishDocument($id)
    {
        $data = Document::where([['id', '=', $id], ['company_id', '=', Auth::user()->company_id]])->first();
        $data->admin_status = "Cancel";
        $data->save();
        $data = [
            'status' => true,
            'message' => 'Document Cancel Successfully.',
            'status code' => 200,
        ];
        return response()->json($data);
    }



    public function AllPublishDocument(Request $request)
    {

        if ($request->search) {
            $data = Document::where('admin_status', 'Active')
                ->where('company_id', '=', Auth::user()->company_id)
                ->where('status', 'Active')
                ->where('name', 'like', '%' . $request->search . '%')
                ->with('user')
                ->get();
            return response()->json($data);
        }


        $data = Document::where('admin_status', 'Active')
            ->where('company_id', '=', Auth::user()->company_id)
            ->where('status', 'Active')
            ->with('user')
            ->latest()
            ->get();
        return response()->json($data);
    }


    public function dashboardDetails()
    {

        $mydoc = Document::where([
            ['user_id', '=', Auth::user()->id],
            ['company_id', '=', Auth::user()->company_id]
        ])->count();
        $publish = Document::where('admin_status', 'Active')
            ->where('status', 'Active')
            ->where('company_id', '=', Auth::user()->company_id)

            ->count();

        $myGroup = Group::where('user_id', Auth::user()->id)
            ->where('company_id', '=', Auth::user()->company_id)
            ->count();

        $data = [
            'myDoc' => $mydoc, 'publishDoc' => $publish, 'myGroup' => $myGroup

        ];
        
        return response()->json($data,);
    }



    public function yourDocument()
    {

        $data = Document::where([['user_id', '=', Auth::user()->id], ['company_id', '=', Auth::user()->company_id]])
            ->get();
        return response()->json($data);
    }

    public function dashboardPublishDocument(Request $request)
    {
        $authId = Auth::user()->id;
        $dashboardPublishDoc = Document::where('admin_status', 'Active')
            ->where('company_id', '=', Auth::user()->company_id)
            ->where('status', 'Active')
            ->with('user')
            ->latest()
            ->paginate(10);

        $mydoc = Document::where([['user_id', '=', $authId], ['company_id', '=', Auth::user()->company_id]])->count();
        $publish = Document::where('admin_status', 'Active')
            ->where('status', 'Active')
            ->where('company_id', '=', Auth::user()->company_id)
            ->count();
            $myGroup = Group::where('user_id', Auth::user()->id)
            ->where('company_id', '=', Auth::user()->company_id)
            ->count();


        // $data = [
        //     'myDoc' => $mydoc, 'publishDoc' => $publish
        // ];
        $data = [
            'dashboardPublishDoc' => $dashboardPublishDoc,
            'myDoc' => $mydoc,
            'publishDoc' => $publish,
            'myGroup' => $myGroup
        ];
        return response()->json($data);
    }



    public function documentView(Request $request, $id)
    {
        $allData = Document::where('id', $id)
            ->where('company_id', '=', Auth::user()->company_id)
            ->with(['user', 'catagory', 'subcatagory', 'subsubcatagory'])
            ->get();

        // 

        $data = [
            'status' => true,
            'message' => 'Document View Successfully.',
            'status code' => 200,
            'data' => $allData[0]

        ];
        return response()->json($data);
    }
}
