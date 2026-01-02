<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AttributeController extends Controller
{
  public function index()
  {
    return view('admin.attribute.index');
  }

  public function getList()
  {
    $query = Attribute::get();
    return DataTables::of($query)
      ->addColumn('action', function ($data) {
        return '
        <a data-id="' . $data->id . '" href="javascript:void(0)" 
        class="edit_btn
        title="Edit">
            <i class="ik ik-edit f-16 mr-15 text-green"></i>
        </a>
        <a href="javascript:void(0);" 
           class="delete_btn" 
           data-id="' . $data->id . '" 
           title="Delete">
            <i class="ik ik-trash-2 f-16 text-red"></i>
        </a>';
      })
      ->editColumn('status', function ($data) {
        if ($data->status == 1) {
          return "Active";
        } else {
          return "Deactive";
        }
      })
      ->addColumn('checkbox', function ($data) {
        return '<label class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
          <span class="custom-control-label">&nbsp;</span>
        </label>';
      })
      ->rawColumns(['checkbox', 'action'])
      ->make(true);
  }

  public function store(Request $request)
  {
    $formData = $request->except(['_token']);
    $formData['slug'] = Str::slug($request->name);
    $create = Attribute::create($formData);
    if ($create) {
      MessageFlashHelper('success', 'Attribute Store Successfully');
    } else {
      MessageFlashHelper('error', 'Something went wrong!');
    }
    return redirect()->back();
  }
  public function delete($id)
  {
    $delete = Attribute::find($id)->delete();
    if ($delete) {
      return response()->json(['message' => 'Attribute Deleted Successfully!', 'success' => true], 200);
    } else {
      return response()->json(['message' => 'something went wrong!', 'success' => false], 500);
    }
  }

  public function edit($id)
  {

    $data = Attribute::find($id);

    if ($data) {
      return response()->json(['data' => $data, 'success' => true], 200);
    } else {
      return response()->json(['success' => false], 500);
    }
  }

  public function update(Request $request)
  {
    $formData = $request->except(['_token', '_method']);
    $formData['slug'] = Str::slug($request->name);
    $update = Attribute::find($request->edit_id)->update($formData);

    if ($update) {
      MessageFlashHelper('success', 'Attribute Updated Successfully!');
      return redirect()->back();
    } else {
      MessageFlashHelper('error', 'Something Went Wrong!');
      return redirect()->back();
    }
  }
}
