<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderDetails;
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
      ->addIndexColumn()


      // ->editColumn('order_detail', function ($data) {
      //   return '<a href="' . route('admin.order.detail', $data->id) . '">
      //         ' . $data->DT_RowIndex . '
      //       </a>';
      // })

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

      ->editColumn('created_at', function ($data) {
        return dmyHelper($data->created_at);
      })

      ->addColumn('action', function ($data) {

        return '<div class="dropdown d-inline-block">
        <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="ik ik-more-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item edit_btn" data-id="' . $data->id . '" href="javascript:void(0)"><i class="ik ik-edit"></i> Edit </a>
            <a class="dropdown-item" href="#InvoiceModal" data-toggle="modal" data-target="#InvoiceModal">
              <i class="ik ik-file-text"></i> 
              Preveiw Invoice
          </a>
          
          <a class="dropdown-item" href="'.route('admin.order.detail',$data->id).'">
              <i class="ik ik-eye"></i> 
              Order Details
          </a>
          <a class="dropdown-item">
              <i class="ik ik-printer"></i> 
              Invoice POS
          </a>
          <a class="dropdown-item">
              <i class="ik ik-mail"></i> 
              Send on Email
          </a>
            
            <a class="dropdown-item delete_btn" data-id="'.$data->id.'" href="javascript:void(0)">
              <i class="ik ik-trash"></i> Delete </a>
        </div>
    </div>';
      })

      // ->addColumn('action', function ($data) {
      //   return '
      //   <a data-id="' . $data->id . '" href="javascript:void(0)" 
      //   class="edit_btn
      //   title="Edit">
      //       <i class="ik ik-edit f-16 mr-15 text-green"></i>
      //   </a>
      //   <a href="javascript:void(0);" 
      //      class="delete_btn" 
      //      data-id="' . $data->id . '" 
      //      title="Delete">
      //       <i class="ik ik-trash-2 f-16 text-red"></i>
      //   </a>';
      // })
      ->editColumn('status', function ($data) {
        if ($data->status == 1) {
          return "Active";
        } else {
          return "Deactive";
        }
      })
      // ->addColumn('checkbox', function ($data) {
      //   return '<label class="custom-control custom-checkbox">
      //     <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
      //     <span class="custom-control-label">&nbsp;</span>
      //   </label>';
      // })
      ->rawColumns(['order_detail','address_detail', 'customer_detail', 'checkbox', 'action'])
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
    // $formData['slug'] = Str::slug($request->name);
    $update = Order::find($request->edit_id)->update($formData);

    if ($update) {
      MessageFlashHelper('success', 'Order Updated Successfully!');
      return redirect()->back();
    } else {
      MessageFlashHelper('error', 'Something Went Wrong!');
      return redirect()->back();
    }
  }

  public function order_detail($id)
  {
    $order_detail = OrderDetails::with(['product','order','variant'])->where('order_id',$id)->get();
    return view('admin.order.order_detail',compact('order_detail'));
  }

   

  // public function edit($id){
  //   return  Order::find($id);
  //  }
}
