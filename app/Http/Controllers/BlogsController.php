<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Blog;
use Storage;
use Carbon\Carbon;

class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        return view('blog.index');
    }

    public function getJsonData(Request $request)
    {
        $user = Auth::user();
        $role = $user ? $user->role->name : "";
        try {
            $columns = array(
                0 => 'id',
                1 => 'title',
                2 => 'start_date',
                3 => 'end_date',
                4 => 'image',
                5 => 'actions',
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request->input('search.value');
            $draw = $request->input('draw');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = Blog::where('is_active',1)->whereDate('end_date', '>', Carbon::now());
            $totalData = $query->count();
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where('title', 'LIKE', "%{$search}%");
                $totalFiltered = $query->count();
            }

            $blogs = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $data = [];
            if (!empty($blogs)) {
                foreach ($blogs as $value) {
                    $id = $value->id;
                    $nestedData['id'] = $id;
                    $edit = route('blog.edit', $id);
                    $nestedData['title'] = $value->title;
                    $nestedData['image'] = "<img src='".$value->image."' alt='".$value->title."' class='m-1' style='width:100px; height:100px' />";
                    $nestedData['start_date'] = $value->start_date;
                    $nestedData['end_date'] = $value->end_date;
                    if($role == "Admin" || ($user && $user->id == $value->user_id )) {
                        $editButton =  "<a role='button' href='" . $edit . "' title='' data-original-title='Edit' class='btn btn-primary' data-toggle='tooltip'>Edit</a>";
                        $deleteButton = "<a href='javascript:void(0)' role='button' onclick='deleteBlog(" . $id . ")' class='btn btn-danger' data-toggle='tooltip' data-original-title='Delete'>Delete</a>";
                    }else {
                        $editButton = $deleteButton = "";
                    }
                    $nestedData['actions'] = "$editButton $deleteButton";
                    $data[] = $nestedData;
                }
            }
            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            return response()->json([]);
        }
    }  

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blog.form');
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
            DB::beginTransaction();
            $user = Auth::user();
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'image' => 'required|image',
                'is_active' => 'required',
            ]);
            if ($validator->fails()) {
                notify()->error("Validation Error", "Error", "topRight");
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = [
                "title" => $inputs['title'],
                "description" => $inputs['description'],
                "start_date" => $inputs['start_date'],
                "end_date" => $inputs['end_date'],
                "is_active" => $inputs['is_active'],
                'user_id' => $user->id
            ];
            
            if ($request->hasFile('image')) {
                $path = 'public/albums';                
                if (!Storage::exists($path)) {
                    Storage::makeDirectory($path);
                }
                $image = Storage::putFile($path, $request->file('image'));
                $fileName = basename($image);
                $data['image'] = $fileName;
            }            
            $blog = Blog::create($data);

            
            DB::commit();
            notify()->success("Blog created sucessfully.", "Success", "topRight");
            return redirect()->route('blog');
        } catch (\Exception $e) {
            Log::info($e);
            notify()->error("Failed to create blog.", "Error", "topRight");
            return redirect()->route('blog');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        return view('blog.form',compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        try {
            $inputs = $request->all();
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'image' => 'image',
                'is_active' => 'required',
            ]);
            if ($validator->fails()) {
                notify()->error("Validation Error", "Error", "topRight");
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = [
                "title" => $inputs['title'],
                "description" => $inputs['description'],
                "start_date" => $inputs['start_date'],
                "end_date" => $inputs['end_date'],
                "is_active" => $inputs['is_active'],
            ];
            
            if ($request->hasFile('image')) {
                $path = 'public/albums';                
                if (!Storage::exists($path)) {
                    Storage::makeDirectory($path);
                }
                Storage::delete($blog->image);
                $image = Storage::putFile($path, $request->file('image'));
                $fileName = basename($image);
                $data['image'] = $fileName;
            }            
            $blog = Blog::where('id',$blog->id)->update($data);

            
            DB::commit();
            notify()->success("Blog updated sucessfully.", "Success", "topRight");
            return redirect()->route('blog');
        } catch (\Exception $e) {
            Log::info($e);
            notify()->error("Failed to update blog.", "Error", "topRight");
            return redirect()->route('blog');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        return view('blog.delete',compact('id'));
    }
    public function destroy(Blog $blog)
    {
        try {
            DB::beginTransaction();
            if($blog->logo){
                Storage::delete($blog->logo);
            }
            Blog::where('id',$blog->id)->delete();
            DB::commit();
            notify()->success("Blog deleted successfully", "Success", "topRight");
            return redirect()->route('blog');
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info($ex);
            notify()->error("Failed to delete blog", "Error", "topRight");
            return redirect()->route('blog');        
        }
    }
}
