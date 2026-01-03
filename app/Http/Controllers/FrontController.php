<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\table;

class FrontController extends Controller
{
  public function index(){

  //  prx(getTopNavCat());
   $result['home_categories']=DB::table('categories')
    ->where(['status'=>1,'is_home'=>1])
    ->get();

    foreach($result['home_categories'] as $list){
      $product_data = DB::table('products')->where(['category_id' => $list->id, 'status' => 1])->get();        
      if(count($product_data)){
      $result['home_categories_product'][$list->id]=$product_data;
      
      foreach($product_data as $list2){
          $product_attr_data = DB::table('variants')->where('product_id',$list2->id)->get();

          if(count($product_attr_data)){
          $result['home_product_attr'][$list2->id] = $product_attr_data;
          }
      }
    }
 }
#section 2 


#get is_tranding_product;
 $result['home_tranding_product'][$list->id]=DB::table('products')->where(['status'=>1,'is_tranding'=>1])->get();
 foreach($result['home_tranding_product'] as $list1){
  $tranding_product_query = DB::table('variants')->where(['product_id' => $list->id,'status' => 1])->get();
  if(count($tranding_product_query)){
   $result['home_tranding_product_attr'][$list1->id]= $tranding_product_query;
  }
 }

#get is_discounted product;
$result['home_discounted_product'][$list->id] = DB::table('products')->where(['status' => 1, 'is_discounted' => 1])->get();
foreach($result['home_discounted_product'] as $list1){
  $home_discounted_variant = DB::table('variants')->where(['status'=>1,'product_id'=>$list->id])->get();
  if(count($home_discounted_variant)){
     $result['home_discounted_product_attr'][$list1->id]= $home_discounted_variant;
  }
}

#get is_feature product;
$result['home_featured_product'][$list->id] = DB::table('products')->where(['status' => 1, 'is_featured' => 1])->get();
foreach($result['home_featured_product'] as $list1){
  $home_feature_product_attr = DB::table('variants')->where(['status'=>1,'product_id'=>$list->id])->get();
  if(count($home_discounted_variant)){
     $result['home_featured_product_attr'][$list1->id]= $home_feature_product_attr;
  }
}

    $result['home_brand'] = DB::table('brands')
      ->where(['status' => 1])
      // ->where(['is_home' => 1])
      ->get();



      $result['home_banner']=DB::table('banners')->where(['status'=>1])->get();

    return view('front.index',$result);
  }


  public function product(Request $request, $slug)
  {

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
    if(count($product_attr_query)){
      $result['product_attr'][$list1->id] = $product_attr_query;
    }
    }

    #get product gallary image
    foreach($result['product'] as $list1){
     $gallary_images = DB::table('product_images')->where('product_id',$list1->id )->get();
     if(count($gallary_images)){
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
      if(count($related_product_query)){
        $result['related_product_attr'][$list1->id] = $related_product_query;
       }
         
    }


    // prx($result);
    return view('front.product', $result);
  }

  public function AddToCart(Request $request){

    
    if($request->session()->has('FRONT_USER_LOGIN')){
        $uid=$request->session()->get('FRONT_USER_LOGIN');
        $user_type = "Reg";
    }else{
      $uid = getUserTempId();
      $user_type = "Not-Reg";
    }

  


    $pqty = $request->pqty;
    $wattage = $request->wattage;
    $product_id = $request->product_id;
    $user_id = Auth::user()->id;

    $result = DB::table('variants')->where(['product_id'=>$product_id,'wattage'=>$wattage])->get();
    $product_attr_id = $result[0]->id;

    #update if get this value just update quantity;
     $check = DB::table('carts')
     ->where(['user_id'=> $user_id, 'product_id'=>$product_id,'product_attr_id'=>$product_attr_id])
     ->get();
     if(isset($check[0])){
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


        //  DB::table('carts')
        // ->where(['id' => $update_id])
        // ->update(['qty'=> $pqty]);
        // $msg = "updated"; 
     }else{

      $id=DB::table('carts')->insertGetId([
          'user_id'=> $user_id,
          'product_id'=> $product_id,
          'product_attr_id'=> $product_attr_id,
          'qty'=> $pqty
      ]);
      $msg = "added";


     }

       return response()->json(['msg'=>$msg]);

    // Cart::create();
    // return $request->all();
  }

  public function cart(Request $request)
  {
    // if ($request->session()->has('FRONT_USER_LOGIN')) {
    //   $uid = $request->session()->get('FRONT_USER_LOGIN');
    //   $user_type = "Reg";
    // } else {
    //   $uid = getUserTempId();
    //   $user_type = "Not-Reg";
    // }

    $user_id = Auth::user()->id;
     $result['list'] = DB::table('carts')
      ->leftJoin('products', 'products.id', '=', 'carts.product_id')
      ->leftJoin('variants', 'variants.id', '=', 'carts.product_attr_id')

      // ->leftJoin('sizes', 'sizes.id', '=', 'products_attr.size_id')//no need
      // ->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id')  //no need
      ->where(['user_id' => $user_id])
      // ->where(['user_type' => "registerd"])
      // ->select('carts.qty', 'products.name', 'products.image', '', 'colors.color', 'products_attr.price', 'products.slug', 'products.id as pid', 'products_attr.id as attr_id')
      ->select('carts.qty', 'products.title', 'products.image','variants.price', 'products.slug', 'variants.id as attr_id', 'products.id as pid','variants.wattage')
      ->get();

      // return $result;
    return view('front.cart', $result);
  }
  

 
}
