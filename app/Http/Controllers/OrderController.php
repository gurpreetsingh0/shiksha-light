<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Color;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
  public function index()
  {
     return view('admin.order.index');
  }

  public function getList()
  {
    $query = Order::get();
    return DataTables::of($query)
      ->editColumn('customer_detail', function ($data) {
        return '
        <strong>' . $data->name . '</strong><br>
        <small>
            ' . $data->email . ' | ' . $data->mobile . '
        </small>
    ';
      })

      ->editColumn('address_detail', function ($data) {
        $short = Str::limit($data->address, 40);

        return '
        <span title="' . $data->address . '">
            ' . $short . '
        </span><br>
        <small>' . $data->city . ', ' . $data->state . ' - ' . $data->pin_code . '</small>
    ';
      })

      ->editColumn('created_at',function($data){
        return dmyHelper($data->created_at);
      })
 

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
      ->rawColumns(['address_detail','customer_detail','checkbox', 'action'])
      ->make(true);
  }

  public function store(Request $request)
  {

    $request->all();
    // $formData = $request->except(['_token']);
    // $formData['slug'] = Str::slug($request->name);
    // $create = Color::create($formData);
    // if ($create) {
    //   MessageFlashHelper('success', 'Attribute Store Successfully');
    // } else {
    //   MessageFlashHelper('error', 'Something went wrong!');
    // }
    // return redirect()->back();
  }
  public function delete($id)
  {
    $delete = Order::find($id)->delete();
    if ($delete) {
      return response()->json(['message' => 'Order Deleted Successfully!', 'success' => true], 200);
    } else {
      return response()->json(['message' => 'something went wrong!', 'success' => false], 500);
    }
  }

  public function edit($id)
  {
    $data = Order::find($id);
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
    $update = Order::find($request->edit_id)->update($formData);

    if ($update) {
      MessageFlashHelper('success', 'Order Updated Successfully!');
      return redirect()->back();
    } else {
      MessageFlashHelper('error', 'Something Went Wrong!');
      return redirect()->back();
    }
  }
}
