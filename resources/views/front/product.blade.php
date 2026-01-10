@extends('front/layout')
@section('title', $product->title)

<style>
    <style>.qty-wrapper {
        display: inline-flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
    }

    .qty-btn {
        width: 35px;
        height: 35px;
        border: none;
        background: #f5f5f5;
        font-size: 20px;
        cursor: pointer;
    }

    .qty-btn:hover {
        background: #ddd;
    }

    #qty {
        width: 50px;
        height: 35px;
        text-align: center;
        border: none;
        font-size: 16px;
    }

    .aa-prod-view-size a {
        padding: 6px 12px;
        border: 1px solid #ddd;
        margin: 4px;
        display: inline-block;
        cursor: pointer;
    }

    .aa-prod-view-size a.active {
        background: #000;
        color: #fff;
    }

    .aa-color-wrapper {
        display: flex;
        gap: 10px;
    }

    .color-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #ccc;
        cursor: pointer;
    }

    .color-circle.active {
        border-color: #000;
    }

    .disabled {
        pointer-events: none;
        opacity: .4;
        text-decoration: line-through;
    }

    .color-grey {
        background: #9e9e9e
    }

    .color-brown {
        background: #795548
    }

    .color-yellow {
        background: #fbc02d
    }

    .color-black {
        background: #000
    }

    .color-white {
        background: #fff
    }
</style>
@section('container')
    <!-- product category -->
    <section id="aa-product-details">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="aa-product-details-area">
                        <div class="aa-product-details-content">
                            <div class="row">

                                <!-- product left content start -->
                                <div class="col-md-5 col-sm-5 col-xs-12">
                                    <div class="aa-product-view-slider">
                                        <div id="demo-1" class="simpleLens-gallery-container">
                                            <div class="simpleLens-container">
                                                <div class="simpleLens-big-image-container"><a
                                                        data-lens-image="{{ asset('storage/' . $product->image) }}"
                                                        class="simpleLens-lens-image"><img
                                                            src="{{ asset('storage/' . $product->image) }}"
                                                            class="simpleLens-big-image"></a></div>
                                            </div>
                                            <div class="simpleLens-thumbnails-container">
                                                <!-- Gallary Image Loop Start -->
                                                @if (isset($product->gallary_images[0]))
                                                    @foreach ($product->gallary_images as $list)
                                                        <a data-big-image="{{ asset('storage/' . $list->product_images) }}"
                                                            data-lens-image="{{ asset('storage/' . $list->product_images) }}"
                                                            class="simpleLens-thumbnail-wrapper" href="#">
                                                            <img src="{{ asset('storage/' . $list->product_images) }}"
                                                                width="100px">
                                                        </a>
                                                    @endforeach
                                                @endif
                                                <!-- Gallary Image loop End -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= PREPARE VARIANT DATA ================= --}}
                                @php
                                    $variantsForJs = [];
                                    $arrWattage = [];
                                    $arrCCT = [];
                                    $arrColor = [];

                                    foreach ($product->variants as $v) {
                                        $variantsForJs[] = [
                                            'wattage' => $v->wattage,
                                            'cct' => $v->cct,
                                            'color' => $v->bodyColor ? $v->bodyColor->slug : null,
                                            'price' => $v->price,
                                            'mrp' => $v->mrp,
                                            'stock' => $v->stock,
                                            'image' => $v->image ? asset('storage/' . $v->image) : null, // <-- add this
                                        ];

                                        $arrWattage[] = $v->wattage;
                                        $arrCCT[] = $v->cct;

                                        if ($v->bodyColor) {
                                            $arrColor[] = $v->bodyColor->slug;
                                        }
                                    }

                                    $arrWattage = array_unique($arrWattage);
                                    $arrCCT = array_unique($arrCCT);
                                    $arrColor = array_unique($arrColor);
                                @endphp

                                {{-- ================= PASS DATA TO JS ================= --}}
                                <script>
                                    window.VARIANTS = {!! json_encode($variantsForJs, JSON_UNESCAPED_UNICODE) !!};
                                </script>

                                <div class="container">
                                    <div class="row">

                                        <div class="col-md-7 col-sm-7 col-xs-12">
                                            <div class="aa-product-view-content">

                                                <h3>{{ $product->title }}</h3>

                                                <div class="aa-price-block">
                                                    <span class="aa-product-view-price">Rs
                                                        {{ $product->variants[0]->price }}</span>
                                                    <span class="aa-product-view-price">
                                                        <del>Rs {{ $product->variants[0]->mrp }}</del>
                                                    </span>
                                                    <p class="aa-product-avilability">
                                                        Availability: <span>{{ $product->variants[0]->stock }}</span>
                                                    </p>
                                                </div>

                                                <p>{!! $product->short_description !!}</p>

                                                {{-- ================= WATTAGE ================= --}}
                                                @if (count($arrWattage))
                                                    <h4>Wattage</h4>
                                                    <div class="aa-prod-view-size">
                                                        @foreach ($arrWattage as $w)
                                                            <a href="javascript:void(0)" class="wattage_link"
                                                                data-wattage="{{ $w }}">
                                                                {{ $w }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                {{-- ================= CCT ================= --}}
                                                @if (count($arrCCT))
                                                    <h4>CCT</h4>
                                                    <div class="aa-prod-view-size">
                                                        @foreach ($arrCCT as $c)
                                                            <a href="javascript:void(0)" class="cct_link"
                                                                data-cct="{{ $c }}">
                                                                {{ $c }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                {{-- ================= BODY COLOR ================= --}}
                                                @if (count($arrColor))
                                                    <h4>Body Color</h4>
                                                    <div class="aa-color-wrapper">
                                                        @foreach ($arrColor as $color)
                                                            <a href="javascript:void(0)"
                                                                class="color-circle color-{{ $color }}"
                                                                data-color="{{ $color }}">
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif


                                                <div class="aa-prod-quantity">

                                                    <div class="qty-wrapper">
                                                        <button type="button" class="qty-btn"
                                                            onclick="changeQty(-1)">−</button>

                                                        <input type="number" id="qty" name="qty" value="1"
                                                            min="1" max="10" oninput="qtyChanged()">

                                                        <button type="button" class="qty-btn"
                                                            onclick="changeQty(1)">+</button>
                                                    </div>

                                                    <p class="aa-prod-category">
                                                        Model: <a href="#">Category Name Here</a>
                                                    </p>

                                                </div>

                                                <div class="aa-prod-view-bottom">
                                                    <a onclick="add_to_cart('{{ $product->id }}',1)"
                                                        class="aa-add-to-cart-btn" href="javascript:void(0)">
                                                        Add To Cart
                                                    </a>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <form id="frmAddToCart">
        <input type="hidden" id="wattage" name="wattage" />
        <input type="hidden" id="cct" name="cct" />
        <input type="hidden" id="color" name="color" />
        <input type="hidden" id="pqty" name="pqty" />
        <input type="hidden" id="product_id" name="product_id" />
        @csrf
    </form>
@endsection
