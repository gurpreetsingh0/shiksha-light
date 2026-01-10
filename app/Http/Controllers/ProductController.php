<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Color;
use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
  public function index()
  {
    return view('admin.product.index');
  }
  public function getProductList()
  {
    $search['value'] = 'jo';
    $query = Product::get();
    return DataTables::of($query)
      ->addColumn('category', function ($data) {
        return ($data->category) ? $data->category->name : "--";
      })
      // ->addColumn('action', function ($data) {
      // //  <a href="#productView" data-toggle="modal" data-target="#productView"><i class="ik ik-eye f-16 mr-15"></i></a>
      //   return '
      //    <a href="'.route('admin.product.edit',$data->id). '"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>
      //   <a  href="javascript:void(0)" data-id="' . $data->id . '" class="delete_btn"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
      // })


      ->addColumn('action', function ($data) {
         return '<div class="dropdown d-inline-block">
        <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="ik ik-more-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
 
          
          <a class="dropdown-item" href="'.route('admin.product.edit',$data->id). '">
              <i class="ik ik-edit"></i> 
              Edit
          </a>
 
   
            
            <a class="dropdown-item delete_btn" data-id="'.$data->id.'" href="javascript:void(0)">
              <i class="ik ik-trash"></i> Delete </a>
        </div>
    </div>';
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


  public function create()
  {
    $data['color'] = Color::select(['id','name','slug'])->where('status',1)->get();
    $data['categories'] = Category::where('status', 1)->get();
    return view('admin.product.create', compact('data'));
  }




  public function store(Request $request)
  {
    // return $request->all();
    DB::beginTransaction();
    $product_image_path = null;
    try {
      if ($request->hasFile('images')) {
        $product_image_path = $request->file('images')
          ->store('products', 'public');
      }

      $product = Product::create([
        'title'             => $request->title,
        'slug'              => Str::slug($request->title),
        'category_id'       => $request->category_id,
        'short_description' => $request->short_description,
        'description'       => $request->description,
        'price'             => $request->price ?? 0,
        'sale_price'        => $request->sale_price,
        'status'            => 1,
        'image'             => $product_image_path, // null or path
      ]);

      // 2. Store Product Images (multiple)
      if ($request->hasFile('product_images')) {
        $images = $request->file('product_images'); // array of UploadedFile
        foreach ($images as $image) {
          if ($image->isValid()) {
            $path = $image->store('products', 'public');

            ProductImage::create([
              'product_id'    => $product->id,
              'product_images' => $path,
            ]);
          }    
        }
      }

      // 3. Store Variants (one image per variant)
      if ($request->has('variants')) {
        foreach ($request->variants as $index =>$variant) {
          $variantImage = null;
          // Handle variant images (array input)
          if (isset($variant['images']) && is_array($variant['images'])) {
             $firstImage = $variant['images'][0] ?? null;
            if ($firstImage instanceof \Illuminate\Http\UploadedFile) {
              $variantImage = $firstImage->store('variants', 'public');
            }
          }

          // return "outer image;";

          Variant::create([
            'product_id'     => $product->id,
            'catalog_number' => $variant['catalog_no'] ?? null,
            'sku'            => $variant['sku'] ?? null,

            #attribute data
            'wattage'        => $variant['wattage'] ?? null,
            'body_color'     => $variant['body_color'] ?? null,
            'cct'            => $variant['cct'] ?? null,


            'voltage'        => $variant['voltage'] ?? null,
            'dimension'      => $variant['dimension'] ?? null,
            'material'       => $variant['material'] ?? null,
            'color'          => $variant['color'] ?? null,
            'weight'         => $variant['weight'] ?? null,

            'outer_diameter' =>$variant['outer_diameter'] ?? null,
            'inner_diameter' => $variant['inner_diameter'] ?? null,
            'height'         => $variant['height'] ?? null,

            'mrp'            => $variant['mrp'] ?? null,
            'price'          => $variant['price'] ?? null,
             'stock'          => $variant['stock'] ?? 0,

            'image'          => $variantImage,
            'status'         => 1,
          ]);
        }
      }

      DB::commit();

      return redirect()
        ->route('admin.product')
        ->with('success', 'Product created successfully');
    } catch (\Exception $e) {
      DB::rollBack();
      return $e->getMessage();
      // You can log $e->getMessage() if needed
      return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
  }

  public function edit($id){
    $data = Product::with(['category', 'gallary_images','variants'])->find($id);
    $color = Color::select(['id','name','slug'])->where('status',1)->get();

    $data['categories'] = Category::where('status', 1)->get();
    return view('admin.product.edit',compact('data','color'));
  }

  public function update(Request $request, $id)
  {
    // return $request->all();
    DB::beginTransaction();
    try {
      $product = Product::findOrFail($id);
      /* ---------- MAIN PRODUCT IMAGE ---------- */
      $product_image_path = $product->image;

      if ($request->hasFile('images')) {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
          Storage::disk('public')->delete($product->image);
        }

        $product_image_path = $request->file('images')
          ->store('products', 'public');
      }

      /* ---------- UPDATE PRODUCT ---------- */
      $product->update([
        'title'             => $request->title,
        'slug'              => Str::slug($request->title),
        'category_id'       => $request->category_id,
        'short_description' => $request->short_description,
        'description'       => $request->description,
        'price'             => $request->price ?? 0,
        'sale_price'        => $request->sale_price,
        'is_featured'       => $request->is_featured,
        'is_discounted'     => $request->is_discounted,
        'is_tranding'       => $request->is_tranding,
        'image'             => $product_image_path,
      ]);

      /* ---------- PRODUCT GALLERY IMAGES ---------- */
      if ($request->hasFile('product_images')) {
        foreach ($request->file('product_images') as $image) {
          if ($image->isValid()) {
            ProductImage::create([
              'product_id'     => $product->id,
              'product_images' => $image->store('products', 'public'),
            ]);
          }
        }
      }

      /* ---------- VARIANTS ---------- */
      // SIMPLE & SAFE: remove old variants and re-insert
      $product->variants()->delete();

      if ($request->has('variants')) {
        foreach ($request->variants as $index => $variant) {

          $variantImage = null;

          // Handle variant images (first image only — same as store)
          if (isset($variant['images']) && is_array($variant['images'])) {
            $firstImage = $variant['images'][0] ?? null;
            if ($firstImage instanceof \Illuminate\Http\UploadedFile) {
              $variantImage = $firstImage->store('variants', 'public');
            }
          }

          Variant::create([
            'product_id'     => $product->id,
            'catalog_number' => $variant['catalog_no'] ?? null,
            'sku'            => $variant['sku'] ?? null,

            'wattage'        => $variant['wattage'] ?? null,
            'cct'            => $variant['cct'] ?? null,
            'body_color'     => $variant['body_color'] ?? null,

            'voltage'        => $variant['voltage'] ?? null,
            'dimension'      => $variant['dimension'] ?? null,
            'material'       => $variant['material'] ?? null,
            'color'          => $variant['color'] ?? null,
            'weight'         => $variant['weight'] ?? null,

            'outer_diameter' => $variant['outer_diameter'] ?? null,
            'inner_diameter' => $variant['inner_diameter'] ?? null,
            'height'         => $variant['height'] ?? null,

            'mrp'            => $variant['mrp'] ?? null,
            'price'          => $variant['price'] ?? null,
            'stock'          => $variant['stock'] ?? 0,

            'image'          => $variantImage,
            'status'         => 1,
          ]);
        }
      }

      DB::commit();

      return redirect()
        ->route('admin.product')
        ->with('success', 'Product updated successfully');
    } catch (\Exception $e) {
        return $e;
      DB::rollBack();
      return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
  }

  public function delete($id){
    $delete = Product::find($id)->delete($id);
    if($delete){
      return response()->json(['success'=>true,'message'=>'Product Deleted Successfully!'],200);
    }else{
      return response()->json(['success'=>false,'message'=>'something went wrong!'],500);
    }
  }
}
