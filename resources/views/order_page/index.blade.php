@extends('layouts.order_page')

@section('header')
    <div class="loader d-flex justify-content-center align-items-center transition-veryfast">
        <div class="logo">
            <?xml version="1.0" encoding="utf-8"?>
            <!-- Generator: Adobe Illustrator 21.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">
            <style type="text/css">
            .st0{clip-path:url(#SVGID_2_);}
            .st1{fill:#FFFFFF;}
            .st2{fill:#00B7B9;}
            </style>
            <g>
            <g>
            <path class="st2" d="M73.9,43.8l-4-9.4h-0.1l-4,9.4h-5.2l7.2-14.2h4.1l7.2,14.2H73.9z"/>
            </g>
            <g>
            <path class="st1" d="M22.9,53.5c0,2-0.6,3.4-1.9,4.5c-1.3,1-3.2,1.5-5.7,1.5h-3.2v5.8H5.7V47.2h9.6C20.4,47.2,22.9,49.3,22.9,53.5
            z M16,54.7c0.4-0.3,0.5-0.7,0.5-1.3c0-0.6-0.2-1-0.5-1.3c-0.4-0.3-0.9-0.4-1.7-0.4h-2.2v3.4h2.2C15,55.1,15.6,55,16,54.7z"/>
            <path class="st1" d="M36.1,61.8h-4.9l-0.9,3.5H24l5.9-18.1h7.7l5.9,18.1h-6.5L36.1,61.8z M35.1,57.8l-1.5-6h-0.1l-0.7,3.1l-0.7,3
            H35.1z"/>
            <path class="st1" d="M61.9,60.6v4.7H47.3V47.2h6.4v13.4H61.9z"/>
            <path class="st1" d="M73,65.3h-6.4V47.2H73V65.3z"/>
            <path class="st1" d="M95.4,51.9h-5.7v13.4h-6.4V51.9h-5.7v-4.7h17.7V51.9z"/>
            </g>
            </g>
            </svg>

        </div>
    </div>
    <div class="header">
        <h1 class="transition-instant">{{$company->name}}</h1>
        <h5 class="text-grey">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </h5>
    </div>
@endsection
    
@section('content')
    <div class="container catalogue">
        <!-- collections -->
        @if($company->collections->count() > 0)
            <div class="row">
                <h2>COLLECTIONS</h2>
            </div>
            <div class="row" style="margin-bottom:70px;">
            @foreach($company->collections->chunk(3) as $collections)
                <div class="card-deck">
                    @foreach($collections as $collection)
                        <div class="card block">
                            @if(!empty($collection->image_url))
                            <img class="card-img-top" src="/uploads/{{$collection['image_url']}}" alt="Card image cap">
                            @endif
                            <div class="card-body">
                                <h4 class="with-underline">{{$collection->name}}</h4>
                                <p class="card-text">{{$collection->description}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
            </div>
        @endif

        <!-- products -->
        @if($products->count() > 0)
            <div class="row">
                <h2>PRODUCTS</h2>
            </div>
            <div class="row">
            @foreach($products->chunk(3) as $products)
                <div class="card-deck">
                    @foreach($products as $product)
                        <div class="card block" data-toggle="modal" data-target="#productModal{{$product->slug}}">
                            @if(!empty($product->image_url))
                            <img class="card-img-top" src="/uploads/{{$product['image_url']}}" alt="Card image cap">
                            @endif
                            <div class="card-body">
                                <h4 class="with-underline">{{$product->name}}</h4>
                                <h5>{{ $product->hasSameVariantPrices() ? $product->view_price() : $product->variants()->sortBy('price')->pluck('view_price')->unique()->implode(', ') }}</h5>
                            </div>
                        </div>
                        @if($product->variants->count() > 0)
                            <div class="modal product-modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="productModal{{$product->slug}}" aria-labelledby="product Modal" aria-hidden="true">
                                <!-- with variants modal-->
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" style="margin-top:-30px;padding-left:20px;padding-right:20px">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-6 img-div">
                                                    @if(!empty($product->image_url))
                                                        <img class="img-responsive" src="/uploads/{{$product['image_url']}}" alt="Image">
                                                    @endif
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <h2 class="with-underline">{{$product->name}}</h2>
                                                    <h4>{{ $product->hasSameVariantPrices() ? $product->view_price() : $product->variants->sortBy('price')->pluck('view_price')->unique()->implode(', ') }}</h4>
                                                    <h6>{{$product->description}}</h6>
                                                    <form action="/{{$company->slug}}/addToCart" method="POST">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <div class="product-variants-block">
                                                            <p class="caption" style="color:black">SELECT VARIANTS TO ADD TO YOUR CART:</p>
                                                            <table class="table with-checkbox" style="margin-bottom:40px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:25px;"></th>
                                                                        @foreach($company->settings->where('name', 'variant_' . $product->id) as $column)
                                                                            <th>{{$column->value}}</th>
                                                                        @endforeach
                                                                        <th class="text-right">Price</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody data-entityname="variants">
                                                                    @foreach($product->variants->where('is_available', true)->sortBy('position') as $variant)
                                                                        @if($variant->available_inventory > 0)
                                                                            <tr class="variant_{{$variant->id}}" data-id="{{$variant->id}}" data-inventory="{{$variant->inventory}}" data-price="{{$variant->price}}" data-itemId="{{{ $variant->id }}}">
                                                                                <td>
                                                                                    <div class="form-check">
                                                                                        <input class="form-check-input" type="checkbox" name="{{$product->id}}[{{$variant->id}}]" value="true">
                                                                                    </div>
                                                                                </td>
                                                                                @foreach($company->settings->where('name', 'variant_' . $product->id) as $column)
                                                                                    <td class="{{$column->value_2}}">{{ $variant->{$column->value_2} }}</td>
                                                                                @endforeach
                                                                                <td class="text-right">{{$product->currency}} {{$variant->view_price}}</td>
                                                                            </tr>
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                            <button type="submit" class="button add-to-cart" disabled >Add to cart</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="modal product-modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="productModal{{$product->slug}}" aria-labelledby="product Modal" aria-hidden="true">
                                <!-- without variants modal-->
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" style="">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-6 img-div">
                                                    @if(!empty($product->image_url))
                                                        <img class="img-responsive" src="/uploads/{{$product['image_url']}}" alt="Image">
                                                    @endif
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <h2 class="with-underline">{{$product->name}}</h2>
                                                    <h4>{{$product->view_price()}}</h4>
                                                    <h6>{{$product->description}}</h6>
                                                    <form action="/{{$company->slug}}/addToCart" method="POST">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="{{$product->id}}" value="no-v">
                                                        <div class="product-variants-block">
                                                            <br>
                                                            <button type="submit" class="button add-to-cart" >Add to cart</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
            </div>
        @endif
    </div>

    <!-- cart -->
    @if($cart_itemcount > 0)
        <div class="view-cart" data-toggle="modal" data-target="#cart">
            <h4 class="text-center">View cart / checkout</h4>
        </div>
        <div class="modal cart-modal product-modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="cart" aria-labelledby="product Modal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="">
                        <div class="row status">
                            <div class="col text-center active">
                                <i class="fas fa-shopping-cart"></i>
                                <h6>Step 1: Shop</h6>
                            </div>
                            <div class="col text-center">
                                <i class="fas fa-truck"></i>
                                <h6>Step 2: Select shipping</h6>
                            </div>
                            <div class="col text-center">
                                <i class="fas fa-money-check"></i>
                                <h6>Step 3: Checkout</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <h1 class="with-underline">CART</h1>    
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <form action="/{{$company->slug}}/shipping" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <table class="table">
                                        <thead>
                                            <th>Product</th>
                                            <th class="text-right" >Price</th>
                                            <th class="text-right" style="width:240px;">Quantity</th>
                                            <th class="text-right">Total</th>
                                            <th ></th>
                                        </thead>
                                        <tbody>
                                            @foreach($cart->where('name', 'Product') as $item)
                                                <tr class="cart-item {{$item->rowId}}">
                                                    <td class="align-middle">
                                                        @if(session('invalid_cart_items'))
                                                            @if(array_key_exists($item->rowId,session('invalid_cart_items')))
                                                                @if(session('invalid_cart_items')[$item->rowId] == 0)
                                                                    <b class="text-red">No stocks available</b><br>
                                                                @elseif(session('invalid_cart_items')[$item->rowId] == 1)
                                                                    <b class="text-red">Only 1 stock available</b><br>
                                                                @elseif(session('invalid_cart_items')[$item->rowId] > 1)
                                                                    <b class="text-red">Only {{session('invalid_cart_items')[$item->rowId]}} stocks available</b><br>
                                                                @endif
                                                            @endif
                                                        @endif
                                                        {{$item->id->name}}<br><span class="text-grey">{{$item->options->description}}</span>
                                                    </td>
                                                    <td class="align-middle text-right">{{$item->options->currency . " " . number_format($item->price, 2, '.', ',')}}</td>
                                                    <td class="text-right align-items-center align-middle">
                                                        <div class="input-group align-middle align-items-center" style="padding-left:60px;">
                                                            <div class="input-group-prepend align-items-center">
                                                                <button class="btn btn-outline-secondary quantity-adjuster minus" type="button" style="border-radius: 0;"><a class="float-right hover-pointer"><i class="fas fa-minus"></i></a></button>
                                                                <button class="btn btn-outline-secondary quantity-adjuster plus" type="button"><a class="float-right hover-pointer"><i class="fas fa-plus"></i></a></button>
                                                            </div>
                                                            @if(is_null($item->options->variant))
                                                                <input type="number" name="quantity[{{$item->rowId}}]" class="item-quantity text-right align-items-center form-control {{ $errors->has('price') ? 'has-error' : ''}}"  min="1" {{(!($item->id->overselling_allowed)) ? "max=".$item->id->available_inventory : ""}} value="{{$item->qty}}" required data-company="{{$company->id}}" data-rowId="{{$item->rowId}}" >
                                                            @else
                                                                <input type="number" name="quantity[{{$item->rowId}}]" class="item-quantity text-right align-items-center form-control {{ $errors->has('price') ? 'has-error' : ''}}"  min="1" {{(!($item->id->overselling_allowed)) ? "max=".$item->options->variant->available_inventory : ""}} value="{{$item->qty}}" required data-company="{{$company->id}}" data-rowId="{{$item->rowId}}" >
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-right align-middle item_price">{{$item->options->currency . " " . number_format($item->price*$item->qty, 2, '.', ',')}}</td>
                                                    <td class="align-middle text-center"><a class="remove hover-pointer" data-rowId="{{$item->rowId}}" data-company="{{$company->slug}}"><i class="fas fa-times"></i></a></td>
                                                </tr>
                                            @endforeach

                                            @foreach($cart->where('name', 'Shipping') as $item)
                                                <tr class="cart-item {{$item->rowId}}">
                                                    <td class="align-middle" colspan="3"><span class="note">SHIPPING:<br></span>{{$item->id->name}}<br><span class="text-grey">{{$item->id->description}}</span></td>
                                                    <td class="text-right align-middle item_price">{{$item->options->currency . " " . number_format($item->price*$item->qty, 2, '.', ',')}}</td>
                                                    <td class="align-middle text-center"><a class="remove hover-pointer" data-rowId="{{$item->rowId}}" data-company="{{$company->slug}}"><i class="fas fa-times"></i></a></td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td></td>
                                                <td class="text-right text-bold">Total:</td>
                                                <td class="text-right text-bold" id="cart_itemcount">{{$cart_itemcount}}</td>
                                                <td class="text-right text-bold" id="cart_total">{{$company->currency}} {{$cart_total}}</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="action-buttons">
                                        <a class="button ghost" href="" data-dismiss="modal" aria-label="Close">< Add more to cart</a>
                                        <button type="submit">Add shipping ></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('custom_js')
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function(){
            $('.loader').addClass("hide");
        });
    </script>
    
@endsection