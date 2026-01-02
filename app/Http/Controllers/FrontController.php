<?php

namespace App\Http\Controllers;

 use Illuminate\Http\Request;
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
    $pqty = $request->pqty;
    $wattage = $request->wattage;
    $product_id = $request->product_id;
    // return $request->all();
  }

 
}
