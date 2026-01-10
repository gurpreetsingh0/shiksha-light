@extends('admin.layouts.main')
@section('title', 'Edit Product')
<style>
  .img-thumbnail {
    margin-left: 20px;
  }
</style>
 


@section('content')
  <div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="row align-items-end">
        <div class="col-lg-8">
          <div class="page-header-title d-flex align-items-center gap-3">
            <i class="ik ik-box bg-primary text-white p-3 rounded-circle"></i>
            <div>
              <h4 class="mb-0">Add Update</h4>
              <small class="text-muted">Create product with variants</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PRODUCT FORM -->
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <form action="{{ route('admin.product.update',$data->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method("PATCH")
          <!-- PRODUCT INFORMATION -->
          <h5 class="text-primary mb-3"><i class="ik ik-info"></i> Product Information</h5>
          <div class="row mb-3">
            <div class="col-md-6 mb-3">
              <label class="fw-bold">Product Title *</label>
              <input value="{{$data->title}}" name="title" type="text" class="form-control shadow-sm" placeholder="Enter product name">
            </div>
            <div class="col-md-6 mb-3">
              <label class="fw-bold">Category *</label>
              <select name="category_id" class="form-control shadow-sm">
                <option value="">Select Category</option>
                @foreach ($data['categories'] as $item)
                  <option @if($data->category->id == $item->id) selected @endif value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group mb-4">
            <label class="fw-bold">Description</label>
            <textarea name="description" class="form-control shadow-sm" rows="4" placeholder="Product description">{{$data->description}}</textarea>
          </div>


         <div class="row"> 

                    <div class="form-group col-6">
                 <label class="fw-bold">Main Image(250x300)</label>
            <input name="images" type="file" class="form-control shadow-sm">
            <div id="product-preview" class="d-flex flex-wrap mt-2 gap-2"></div>
          </div>

          
          <div class="form-group col-6">
            <label class="fw-bold">Short Description*</label>
            <input value="{{$data->short_description}}" name="short_description" type="text" class="form-control shadow-sm" placeholder="Enter Short Description Here..">
          </div>
          
 
         </div>


          <div class="row mb-3"> <!-- Is Feautred, Is Tranding, Is Discounted -->
            <div class="col-md-4 mb-3">
              <label class="fw-bold">Is Feautred </label>

              <select class="form-control form-select-lg" name="is_featured" id="">
                <option @if($data->is_featured=="1") selected @endif  value="1">Active</option>
                <option @if($data->is_featured=="0") selected @endif value="0">Deactive</option>
              </select>

            </div>
            <div class="col-md-4 mb-3">
              <label class="fw-bold">Is Tranding</label>
              <select class="form-control form-select-lg" name="is_tranding" id="">
                <option @if($data->is_tranding=="1") selected @endif value="1">Active</option>
                <option @if($data->is_tranding=="0") selected @endif value="0">Deactive</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="fw-bold">Is Discounted</label>
              <select class="form-control form-select-lg" name="is_discounted" id="">
                <option @if($data->is_discounted=="1") selected @endif value="1">Active</option>
                <option @if($data->is_discounted=="1") selected @endif value="0">Deactive</option>
              </select>
            </div>

          </div>

 

          {{-- for single image --}}
          {{-- <div class="form-group mb-4">
            <label class="fw-bold">Main Image(250x300)</label>
            <input name="images" type="file" class="form-control shadow-sm">
            <div id="product-preview" class="d-flex flex-wrap mt-2 gap-2"></div>
          </div> --}}


         <!-- Gallary Images -->
        <div class="form-group mb-4">
          <label class="fw-bold">Gallary Images(250x300)</label>
          <input name="product_images[]" type="file" class="form-control shadow-sm" multiple accept="image/*">
          <div id="product-preview" class="d-flex flex-wrap mt-2 gap-2"></div>
        </div>

















        
 <hr class="my-4">
<h5 class="text-primary mb-3">
  <i class="ik ik-layers"></i> Product Variants
</h5>

<!-- ONE WRAPPER ONLY -->
<div id="variants-wrapper">

@foreach ($data->variants as $index => $item)
<div class="variant-card border rounded p-3 mb-4 shadow-sm bg-light">

  <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0 text-dark">
      Variant <span class="variant-index">{{ $index + 1 }}</span>
    </h6>
    <button type="button"
      class="btn btn-sm btn-danger remove-variant {{ $index == 0 ? 'd-none' : '' }}">
      <i class="ik ik-trash-2"></i> Remove
    </button>
  </div>

  <!-- Row -->
  <div class="row mb-2">
    <div class="col-md-6 mb-2">
      <label>Cat No *</label>
      <input type="text" class="form-control shadow-sm"
        name="variants[{{ $index }}][catalog_no]"
        value="{{ $item->catalog_number }}">
    </div>

    <div class="col-md-6 mb-2">
      <label>Variant Images</label>
      <input type="file"
        class="form-control variant-image shadow-sm"
        name="variants[{{ $index }}][images][]"
        multiple>
      <div class="variant-preview d-flex flex-wrap mt-2 gap-2"></div>
    </div>
  </div>


  <div class="row mb-2">
    <div class="col-md-4">
      <label>Outer Diameter</label>
      <input value="{{$item->outer_diameter }}" type="number" step="0.01" class="form-control" name="variants[{{ $index }}][outer_diameter]">
    </div>
    <div class="col-md-4">
      <label>Inner Diameter/Cut</label>
      <input value="{{$item->inner_diameter }}" type="number" step="0.01" class="form-control" name="variants[{{ $index }}][inner_diameter]">
    </div>
    <div class="col-md-4">
      <label>Height/Depth</label>
      <input value="{{$item->height }}" type="number" step="0.01" class="form-control" name="variants[{{ $index }}][height]">
    </div>
  </div>




  <div class="row mb-2">
    <!--Attribute Start -->
    <div class="col-md-2">
      <label>Wattage</label>
      <input class="form-control shadow-sm" placeholder="Enter Wattage"
        name="variants[{{ $index }}][wattage]"
        value="{{ $item->wattage }}">
    </div>

     <div class="col-md-2">
      <label>CCT</label>
      <input class="form-control shadow-sm"
        name="variants[{{ $index }}][cct]"
        value="{{ $item->cct }}" placeholder="3K,4K,6k">
    </div>

 
     <div class="col-md-2 mb-2">
     <label>Body Color</label>
      <select name="variants[{{ $index }}][body_color]" class="form-control shadow-sm">
      <option value="">Select Body Color</option>
      @foreach ($color as $col)
        <option @if($item->body_color == $col->id) selected @endif  value="{{ $col->id }}">{{ $col->name }}</option>
      @endforeach
    </select>
    </div>

    <!-- Attribute End -->

  
    <div class="col-md-2">
      <label>Voltage</label>
      <input class="form-control shadow-sm" placeholder="Enter Voltage"
        name="variants[{{ $index }}][voltage]"
        value="{{ $item->voltage }}">
    </div>
    <div class="col-md-4">
      <label>Material</label>
      <input class="form-control shadow-sm"
        name="variants[{{ $index }}][material]" placeholder="Enter Material"
        value="{{ $item->material }}">
    </div>
    
  </div>

  
    <div class="row mb-2">
    <div class="col-md-4 mb-2">
      <label>Regular Price</label>
      <input value="{{ $item->mrp }}" type="number" class="form-control shadow-sm" name="variants[{{ $index }}][mrp]" placeholder="₹">
    </div>
    
    <div class="col-md-4 mb-2"> <!-- mrp -->
      <label>Sale Price</label> 
      <input value="{{ $item->price }}" type="number" class="form-control shadow-sm" name="variants[{{ $index }}][price]" placeholder="Enter mrp product">
    </div>

    <div class="col-md-4 mb-2">
      <label>Stock</label>
      <input value="{{ $item->stock }}" type="number" class="form-control shadow-sm" name="variants[{{ $index }}][stock]" placeholder="Enter Stock">
    </div>
  </div>

</div>
@endforeach

</div>





          

           <!-- Edit VARIANT -->
          <div class="mb-4 d-flex justify-content-between align-items-center">
            <button type="button" id="add-variant" class="btn btn-outline-primary shadow-sm">
              <i class="ik ik-plus"></i> Add Variant
            </button>
 
            <!-- SUBMIT -->
            <button type="submit" class="btn btn-primary shadow-sm">
              <i class="ik ik-save"></i> Save Product
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
@endsection

@push('script')
  <script>
    $(document).ready(function() {
     let variantIndex = $('#variants-wrapper .variant-card').length;
      // ----- Product Images -----
      let productFiles = [];

      $('input[name="product_images[]"]').on('change', function() {
        productFiles = Array.from(this.files);
        renderProductPreview();
      });

      function renderProductPreview() {
        $('#product-preview').html('');
        productFiles.forEach((file, idx) => {
          const reader = new FileReader();
          reader.onload = e => {
            $('#product-preview').append(`
          <div class="position-relative m-1">
            <img src="${e.target.result}" class="img-thumbnail" style="width:100px;height:100px;">
            <span class="position-absolute top-0 end-0 badge bg-danger cursor-pointer remove-img" data-index="${idx}">X</span>
          </div>
        `);
          };
          reader.readAsDataURL(file);
        });
      }

      $(document).on('click', '#product-preview .remove-img', function() {
        const idx = $(this).data('index');
        productFiles.splice(idx, 1);
        renderProductPreview();
      });

      // ----- Add Variant -----
      $('#add-variant').click(function() {
        let $clone = $('.variant-card:first').clone();
        $clone.find('input, select').each(function() {
          let name = $(this).attr('name');
          if (name) name = name.replace(/\[\d+\]/, '[' + variantIndex + ']');
          $(this).attr('name', name).val('');
        });
        $clone.find('.variant-index').text(variantIndex + 1);
        $clone.find('.variant-preview').html('');
        $clone.find('.remove-variant').removeClass('d-none');
        $clone.find('.variant-image').data('files', []);
        $('#variants-wrapper').append($clone);
        variantIndex++;
      });

      $(document).on('click', '.remove-variant', function() {
        $(this).closest('.variant-card').remove();
        $('.variant-card').each(function(i) {
          $(this).find('.variant-index').text(i + 1);
        });
      });

      // ----- Variant Images -----
      $(document).on('change', '.variant-image', function() {
        const files = Array.from(this.files);
        $(this).data('files', files);
        renderVariantPreview($(this));
      });

      function renderVariantPreview(input) {
        const preview = input.siblings('.variant-preview');
        preview.html('');
        const files = input.data('files') || [];
        files.forEach((file, idx) => {
          const reader = new FileReader();
          reader.onload = e => {
            preview.append(`
          <div class="position-relative m-1">
            <img src="${e.target.result}" class="img-thumbnail" style="width:100px;height:100px;">
            <span class="position-absolute top-0 end-0 badge bg-danger cursor-pointer remove-variant-img" data-index="${idx}">X</span>
          </div>
        `);
          };
          reader.readAsDataURL(file);
        });
      }

      $(document).on('click', '.variant-preview .remove-variant-img', function() {
        const idx = $(this).data('index');
        const input = $(this).closest('.variant-preview').siblings('.variant-image');
        let files = input.data('files') || [];
        files.splice(idx, 1);
        input.data('files', files);
        renderVariantPreview(input);
      });

      // ----- On Form Submit -----
      $('form').on('submit', function(e) {
        const dtProduct = new DataTransfer();
        productFiles.forEach(file => dtProduct.items.add(file));
        $('input[name="product_images[]"]')[0].files = dtProduct.files;

        $('.variant-image').each(function() {
          const dtVariant = new DataTransfer();
          const files = $(this).data('files') || [];
          files.forEach(f => dtVariant.items.add(f));
          this.files = dtVariant.files;
        });
      });

    });
  </script>
@endpush
