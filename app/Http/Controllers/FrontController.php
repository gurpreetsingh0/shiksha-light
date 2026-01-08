<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\table;

class FrontController extends Controller
{
  public function index()
  {
    //  prx(getTopNavCat());
    $result['home_categories'] = DB::table('categories')
      ->where(['status' => 1, 'is_home' => 1])
      ->get();

    foreach ($result['home_categories'] as $list) {
      $product_data = DB::table('products')->where(['category_id' => $list->id, 'status' => 1])->get();
      if (count($product_data)) {
        $result['home_categories_product'][$list->id] = $product_data;

        foreach ($product_data as $list2) {
          $product_attr_data = DB::table('variants')->where('product_id', $list2->id)->get();

          if (count($product_attr_data)) {
            $result['home_product_attr'][$list2->id] = $product_attr_data;
          }
        }
      }
    }
    #section 2 


    #get is_tranding_product;
    $result['home_tranding_product'][$list->id] = DB::table('products')->where(['status' => 1, 'is_tranding' => 1])->get();
    foreach ($result['home_tranding_product'] as $list1) {
      $tranding_product_query = DB::table('variants')->where(['product_id' => $list->id, 'status' => 1])->get();
      if (count($tranding_product_query)) {
        $result['home_tranding_product_attr'][$list1->id] = $tranding_product_query;
      }
    }

    #get is_discounted product;
    $result['home_discounted_product'][$list->id] = DB::table('products')->where(['status' => 1, 'is_discounted' => 1])->get();
    foreach ($result['home_discounted_product'] as $list1) {
      $home_discounted_variant = DB::table('variants')->where(['status' => 1, 'product_id' => $list->id])->get();
      if (count($home_discounted_variant)) {
        $result['home_discounted_product_attr'][$list1->id] = $home_discounted_variant;
      }
    }

    #get is_feature product;
    $result['home_featured_product'][$list->id] = DB::table('products')->where(['status' => 1, 'is_featured' => 1])->get();
    foreach ($result['home_featured_product'] as $list1) {
      $home_feature_product_attr = DB::table('variants')->where(['status' => 1, 'product_id' => $list->id])->get();
      if (count($home_discounted_variant)) {
        $result['home_featured_product_attr'][$list1->id] = $home_feature_product_attr;
      }
    }

    $result['home_brand'] = DB::table('brands')
      ->where(['status' => 1])
      // ->where(['is_home' => 1])
      ->get();



    $result['home_banner'] = DB::table('banners')->where(['status' => 1])->get();

    return view('front.index', $result);
  }


  public function product(Request $request, $slug)
  {
 
    $product = Product::where(['slug'=>$slug,'status'=>1])
    ->with(['category', 'gallary_images', 'variants'])
    ->get();


    // return $slug;
    $result['product'] =
      DB::table('products')
      ->where(['status' => 1])
      ->where(['slug' => $slug])
      ->get();

    // prx($result['product']);


    #get product variant
    foreach ($result['product'] as $list1) {
      $product_attr_query = $result['product_attr'][$list1->id] =
        DB::table('variants')
        ->leftJoin('attribute_options', 'attribute_options.id', '=', 'variants.wattage')

        // ->leftJoin('sizes', 'sizes.id', '=', 'products_attr.size_id')
        // ->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id')
        ->where(['variants.product_id' => $list1->id])
        ->get();
      if (count($product_attr_query)) {
        $result['product_attr'][$list1->id] = $product_attr_query;
      }
    }

    #get product gallary image
    foreach ($result['product'] as $list1) {
      $gallary_images = DB::table('product_images')->where('product_id', $list1->id)->get();
      if (count($gallary_images)) {
        $result['product_images'][$list1->id] = $gallary_images;
      }
    }

    // return $result['product'];






    $result['related_product'] =
      DB::table('products')
      ->where(['status' => 1])
      ->where('slug', '!=', $slug)
      ->where(['category_id' => $result['product'][0]->category_id])
      ->get();
    foreach ($result['related_product'] as $list1) {

      $related_product_query = DB::table('variants')
        ->leftJoin('attribute_options', 'attribute_options.id', '=', 'variants.wattage')
        // ->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id')
        ->where(['variants.product_id' => $list1->id])
        ->get();
      if (count($related_product_query)) {
        $result['related_product_attr'][$list1->id] = $related_product_query;
      }
    }


    // prx($result);
    return view('front.product', $result);
    // return view('front.product',compact('product'));
  }

  public function AddToCart(Request $request)
  {
    $pqty = $request->pqty;
    $wattage = $request->wattage;
    $product_id = $request->product_id;
    $user_id = Auth::user()->id;

    $result = DB::table('variants')->where(['product_id' => $product_id, 'wattage' => $wattage])->get();
    $product_attr_id = $result[0]->id;

    #update if get this value just update quantity;
    $check = DB::table('carts')
      ->where(['user_id' => $user_id, 'product_id' => $product_id, 'product_attr_id' => $product_attr_id])
      ->get();
    if (isset($check[0])) {
      $update_id = $check[0]->id;

      if ($pqty == 0) {
        DB::table('carts')
          ->where(['id' => $update_id])
          ->delete();
        $msg = "removed";
      } else {
        DB::table('carts')
          ->where(['id' => $update_id])
          ->update(['qty' => $pqty]);
        $msg = "updated";
      }
    } else {
      $id = DB::table('carts')->insertGetId([
        'user_id' => $user_id,
        'product_id' => $product_id,
        'product_attr_id' => $product_attr_id,
        'qty' => $pqty
      ]);
      $msg = "added";
    }


    $result = DB::table('carts')
      ->leftJoin('products', 'products.id', '=', 'carts.product_id')
      ->leftJoin('variants', 'variants.id', '=', 'carts.product_attr_id')
      ->where(['user_id' => $user_id])
      ->select('carts.qty', 'products.title', 'products.image', 'variants.price', 'products.slug', 'products.id as pid', 'variants.id as attr_id')
      ->get();
    return response()->json(['msg' => $msg, 'data' => $result, 'totalItem' => count($result)]);
    // Cart::create();
    // return $request->all();
  }

  public function cart(Request $request)
  {
    $user_id = Auth::user()->id;
    $result['list'] = DB::table('carts')
      ->leftJoin('products', 'products.id', '=', 'carts.product_id')
      ->leftJoin('variants', 'variants.id', '=', 'carts.product_attr_id')

      // ->leftJoin('sizes', 'sizes.id', '=', 'products_attr.size_id')//no need
      // ->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id')  //no need
      ->where(['user_id' => $user_id])
      // ->where(['user_type' => "registerd"])
      // ->select('carts.qty', 'products.name', 'products.image', '', 'colors.color', 'products_attr.price', 'products.slug', 'products.id as pid', 'products_attr.id as attr_id')
      ->select('carts.qty', 'products.title', 'products.image', 'variants.price', 'products.slug', 'variants.id as attr_id', 'products.id as pid', 'variants.wattage')
      ->get();

    // return $result;
    return view('front.cart', $result);
  }

  public function category(Request $request, $slug)
  {
    $sort = "";
    $sort_txt = "";
    $filter_price_start = "";
    $filter_price_end = "";
    $color_filter = "";
    $colorFilterArr = [];
    if ($request->get('sort') !== null) {
      $sort = $request->get('sort');
    }

    $query = DB::table('products');
    $query = $query->distinct()->select('products.*');
    $query = $query->leftJoin('categories', 'categories.id', '=', 'products.category_id');
    $query = $query->leftJoin('variants', 'products.id', '=', 'variants.product_id');
    $query = $query->where(['products.status' => 1]);
    $query = $query->where(['categories.slug' => $slug]);


    if ($sort == 'name') {
      $query = $query->orderBy('products.title', 'asc');
      $sort_txt = "Product Name";
    }
    if ($sort == 'date') {
      $query = $query->orderBy('products.id', 'desc');
      $sort_txt = "Date";
    }
    if ($sort == 'price_desc') {
      $query = $query->orderBy('variants.price', 'desc');
      $sort_txt = "Price - DESC";
    }
    if ($sort == 'price_asc') {
      $query = $query->orderBy('products_attr.price', 'asc');
      $sort_txt = "Price - ASC";
    }


    if ($request->get('filter_price_start') !== null && $request->get('filter_price_end') !== null) {
      $filter_price_start = $request->get('filter_price_start');
      $filter_price_end = $request->get('filter_price_end');

      if ($filter_price_start > 0 && $filter_price_end > 0) {
        $query = $query->whereBetween('variants.price', [$filter_price_start, $filter_price_end]);
      }
    }

    // if ($request->get('color_filter') !== null) {
    //   $color_filter = $request->get('color_filter');
    //   $colorFilterArr = explode(":", $color_filter);
    //   $colorFilterArr = array_filter($colorFilterArr);

    //   $query = $query->where(['products_attr.color_id' => $request->get('color_filter')]);
    // }

    // $query = $query->distinct()->select('products.*');
    $query = $query->get();
    $result['product'] = $query;
    foreach ($result['product'] as $list1) {

      $query1 = DB::table('variants');
      // $query1 = $query1->leftJoin('sizes', 'sizes.id', '=', 'products_attr.size_id');
      // $query1 = $query1->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id');
      $query1 = $query1->where(['variants.product_id' => $list1->id]);
      $query1 = $query1->get();
      $result['product_attr'][$list1->id] = $query1;
    }

    // $result['product_attr'];

    // $result['colors'] = DB::table('colors')
    //   ->where(['status' => 1])
    //   ->get();


    // $result['categories_left'] = DB::table('categories')
    //   ->where(['status' => 1])
    //   ->get();

    // $result['slug'] = $slug;
    $result['sort'] = $sort;
    $result['sort_txt'] = $sort_txt;
    $result['filter_price_start'] = $filter_price_start;
    $result['filter_price_end'] = $filter_price_end;
    // $result['color_filter'] = $color_filter;
    // $result['colorFilterArr'] = $colorFilterArr;
    return view('front.category', $result);
  }

  public function checkout(Request $request)
  {
    // return "i am her";
    $result['cart_data'] = getAddToCartTotalItem();
    if (isset($result['cart_data'][0])) { #if cart have value than enter inside if condition;
      $user = Auth::user()->name;
      // return $user;
      if (Auth::check()) {
        $customer_info = Auth::user();
        $result['customers']['name'] = $customer_info->name;
        $result['customers']['email'] = $customer_info->email;
        $result['customers']['mobile'] = $customer_info->mobile;
        $result['customers']['address'] = $customer_info->address;
        $result['customers']['city'] = $customer_info->city;
        $result['customers']['state'] = $customer_info->state;
        $result['customers']['zip'] = $customer_info->pin_code;
      } else {
        $result['customers']['name'] = '';
        $result['customers']['email'] = '';
        $result['customers']['mobile'] = '';
        $result['customers']['address'] = '';
        $result['customers']['city'] = '';
        $result['customers']['state'] = '';
        $result['customers']['zip'] = '';
      }

      return view('front.checkout', $result);
    } else {
      return redirect('/');
    }
  }


  public function place_order(Request $request)
  {
    DB::beginTransaction(); // 🔹 start transaction

    try {
      $uid = Auth::id();
      $totalPrice = 0;
      $getAddToCartTotalItem = getAddToCartTotalItem(); #get detail from cart table and get total;

      foreach ($getAddToCartTotalItem as $list) {
        $totalPrice = $totalPrice + ($list->qty * $list->price);
      }

      $orderArray = [
        "user_id" => $uid,
        "name" => $request->name,
        "email" => $request->email,
        "mobile" => $request->mobile,
        "address" => $request->address,
        "city" => $request->city,
        "state" => $request->state,
        "pin_code" => $request->zip,
        "payment_type" => $request->payment_type,
        "payment_status" => "pending",
        "total_amount" => $totalPrice,
        "order_status" => "processing",
        "payment_id"   => 0,
      ];
      $order = Order::create($orderArray);
      $order_id = $order->id;
      if ($order_id > 0) {
        foreach ($getAddToCartTotalItem as $list) {
          $prductDetailArr['product_id'] = $list->pid;
          $prductDetailArr['variant_id'] = $list->attr_id;
          $prductDetailArr['price'] = $list->price;
          $prductDetailArr['qty'] = $list->qty;
          $prductDetailArr['order_id'] = $order_id;
          OrderDetails::create($prductDetailArr);
        }

        //empty cart if order placed
        Cart::where('user_id', $uid)->delete();
        $request->session()->put('ORDER_ID', $order_id);

        DB::commit(); // 🔹 success → save everything

        $status = "success";
        $msg = "Order placed";
      } else {
        DB::rollBack(); // 🔹 failure → undo everything

        $status = "false";
        $msg = "Please try after sometime";
      }
    } catch (\Exception $e) {
      DB::rollBack(); // 🔹 error → undo everything

      $status = "false";
      $msg = "Please try after sometime";
      // logger($e->getMessage()); // optional
    }

    return response()->json(['status' => $status, 'msg' => $msg,'order_id'=> $order_id]);
  }
  public function order_placed(Request $request)
  {
    if ($request->session()->has('ORDER_ID')) {
      return view('front.order_placed');
    } else {
      return redirect('/');
    }
  }

  public function order(Request $request)
  {
    $logged_user_id = Auth::id();
    $result['orders'] = Order::where('user_id',$logged_user_id)->get();
    return view('front.order', $result);
  }
  public function order_detail(Request $request, $id)
  {
    $logged_user_id = Auth::id();

    $orders_details = OrderDetails::with(['order', 'product', 'variant'])
      ->where('order_id', $id)
      ->whereHas('order', function ($q) use ($logged_user_id) {
        $q->where('user_id', $logged_user_id);
      })
      ->get();

    if ($orders_details->isEmpty()) {
      return redirect('/');
    }

    return view('front.order_detail', compact('orders_details'));
  }
}
