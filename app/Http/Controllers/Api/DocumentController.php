<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\document;
use App\Models\sub_catagory;
use App\Models\sub_sub_catagory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use PhpParser\Node\Stmt\TryCatch;

use function Termwind\render;

class documentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $authId = Auth::user()->id;
        $data = document::where('user_id', '=', $authId)->get();
        return response()->json($data);
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
    public function store(Request $request)
    {
        try {
            $document = new document();
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = document::with('user')->find($id);
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
    public function update(Request $request, $id)
    {


        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);

            $document = document::findOrFail($id);

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
    public function destroy($id)
    {
        try {
            $document = document::findOrFail($id);
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
            $document = document::findOrFail($id);
            $file = public_path("file/" . $document->file);
            $headers = array(
                'Content-Type: application/pdf',
            );
            return response()->download($file, $document->name, $headers); //code...
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }






    public function showCategoryDocument($id)

    {

        $authId = Auth::user()->id;


        $data = document::where('catagory_id', $id)
            ->where('sub_catagory_id', null)
            ->where('sub_sub_catagory_id', null)
            ->where('user_id', '=', $authId)
            ->with('user')
            ->with('catagory')
            ->latest()
            ->get();

        return response()->json($data);
    }

    public function showSubCategory($id)
    {
        $data = sub_catagory::where('catagory_id', $id)
            ->with('document')
            ->get();
        return response()->json($data);
    }


    public function showSubCategoryDocument($id)
    {
        $data = document::where('sub_catagory_id', $id)
            ->where('sub_sub_catagory_id', null)
            ->with('user')
            ->with('catagory')
            ->latest()
            ->get();
        return response()->json($data);
    }
    public function showSubSubCategory($id)
    {
        $data = sub_sub_catagory::where('sub_catagory_id', $id)
            ->with('document')
            ->get();
        return response()->json($data);
    }
    public function showSubSubCategoryDocument($id)
    {
        $data = document::where('sub_sub_catagory_id', $id)

            ->with('user')
            ->with('catagory')
            ->latest()
            ->get();
        return response()->json($data);
    }

    public function documentPublish($id)
    {
        $document = document::findOrFail($id);
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
        $data = document::where('admin_status', 'Pending')
            ->where('status', 'Active')
            ->with('user')
            ->latest()
            ->get();
        return response()->json($data);
    }

    public function AdminPublishDocument($id)
    {
        $data = document::findOrfail($id);
        $data->admin_status = "Active";
        $data->save();
        $data = [
            'status' => true,
            'message' => 'Document Publish Successfully.',
            'status code' => 200,
        ];
        return response()->json($data);
    }

    public function AllPublishDocument(Request $request)
    {

        if ($request->search) {
            $data = document::where('admin_status', 'Active')
                ->where('status', 'Active')
                ->where('name', 'like', '%' . $request->search . '%')
                ->with('user')
                ->get();
            return response()->json($data);
        }


        $data = document::where('admin_status', 'Active')
            ->where('status', 'Active')
            ->with('user')
            ->latest()
            ->get();
        return response()->json($data);
    }


    public function dashboardDetails()
    {
        $authId = Auth::user()->id;
        $mydoc = document::where('user_id', $authId)->count();
        $publish = document::where('admin_status', 'Active')
            ->where('status', 'Active')

            ->count();
        $data = [
            'myDoc' => $mydoc, 'publishDoc' => $publish
        ];
        return response()->json($data,);
    }



    public function yourDocument()
    {
        $authId = Auth::user()->id;
        $data = document::where('user_id', $authId)
            ->get();
        return response()->json($data);
    }

    public function dashboardPublishDocument(Request $request)
    {
        $authId = Auth::user()->id;
        $dashboardPublishDoc = document::where('admin_status', 'Active')
            ->where('status', 'Active')
            ->with('user')
            ->latest()
            ->paginate(10);

        $mydoc = document::where('user_id', $authId)->count();
        $publish = document::where('admin_status', 'Active')
            ->where('status', 'Active')
            ->count();
        $data = [
            'myDoc' => $mydoc, 'publishDoc' => $publish
        ];
        $data = [
            'dashboardPublishDoc' => $dashboardPublishDoc,
            'myDoc' => $mydoc,
            'publishDoc' => $publish
        ];
        return response()->json($data);
    }


    public function documentView(Request $request, $id)
    {
        $allData = document::where('id', $id)
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
