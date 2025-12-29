<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
class ProductController extends Controller
{
    public function index(){
      return view('admin.product.index');
    }
    public function getProductList(){
      $search['value']='jo';
      $query = Product::get();
      return DataTables::of($query)
      ->addColumn('category',function($data){
        return ($data->category)?$data->category->name:"--";
      })
      ->addColumn('action',function($data){
        return '<a href="#productView" data-toggle="modal" data-target="#productView"><i class="ik ik-eye f-16 mr-15"></i></a>
		                    			<a href="/products/9/edit"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>
		                    			<a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
      })
      ->addColumn('checkbox',function($data){
        return '<label class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
          <span class="custom-control-label">&nbsp;</span>
        </label>';
      })
      ->rawColumns(['checkbox','action'])
      ->make(true);
    }


    public function create(){
      $data['categories']=Category::where('status',1)->get();
      return view('admin.product.create',compact('data'));
    }




  public function store(Request $request)
  {
    DB::beginTransaction();

    try {
      // 1. Store Product
      $product = Product::create([
        'title'             => $request->title,
        'slug'              => Str::slug($request->title),
        'category_id'       => $request->category_id,
        'short_description' => $request->short_description,
        'description'       => $request->description,
        'price'             => $request->price ?? 0,
        'sale_price'        => $request->sale_price,
        'status'            => 1,
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
        foreach ($request->variants as $variant) {
          $variantImage = null;

          // Handle variant images (array input)
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
            'voltage'        => $variant['voltage'] ?? null,
            'dimension'      => $variant['dimension'] ?? null,
            'material'       => $variant['material'] ?? null,
            'color'          => $variant['color'] ?? null,
            'weight'         => $variant['weight'] ?? null,

            'outer_dia'      => $variant['outer_dia'] ?? null,
            'inner_cut'      => $variant['inner_cut'] ?? null,

            'mrp'            => $variant['mrp'] ?? null,
            'price'          => $variant['price'] ?? null,
            'stock'          => $variant['stock'] ?? 0,

            'image'          => $variantImage,
            'status'         => $variant['status'] ?? 1,
          ]);
        }
      }

      DB::commit();

      return redirect()
        ->route('admin.product')
        ->with('success', 'Product created successfully');
    } catch (\Exception $e) {
      DB::rollBack();
      // You can log $e->getMessage() if needed
      return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
  }
}
