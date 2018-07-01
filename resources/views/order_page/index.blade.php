@extends('layouts.order_page')

@section('content')
    <div class="container">
        <!-- collections -->
        @if($company->collections->where('is_available', true)->count() > 0)
            <div class="row">
                <h2>COLLECTIONS</h2>
            </div>
            <div class="row" style="margin-bottom:70px;">
            @foreach($company->collections->where('is_available', true)->chunk(3) as $collections)
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
        @if($company->products->where('is_available', true)->count() > 0)
            <div class="row">
                <h2>PRODUCTS</h2>
            </div>
            <div class="row">
            @foreach($company->products->where('is_available', true)->chunk(3) as $products)
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
                        @if($product->variants->where('is_available', true)->count() > 0)
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
                                                            <table class="table" style="margin-bottom:40px;">
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
                                                                        <tr class="variant_{{$variant->id}}" data-id="{{$variant->id}}" data-inventory="{{$variant->inventory}}" data-price="{{$variant->price}}" data-itemId="{{{ $variant->id }}}">
                                                                            <td>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" name="{{$product->id}}[{{$variant->id}}]" value="true">
                                                                                </div>
                                                                            </td>
                                                                            @foreach($company->settings->where('name', 'variant_' . $product->id) as $column)
                                                                                <td class="{{$column->value_2}}">{{ $variant->{$column->value_2} }}</td>
                                                                            @endforeach
                                                                            <td class="text-right">{{$variant->view_price}}</td>
                                                                        </tr>
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
                                        <div class="modal-body" style="margin-top:-30px;padding-left:20px;padding-right:20px">
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
                    <div class="modal-body" style="margin-top:-30px">
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
                                            <th class="text-right">Quantity</th>
                                            <th class="text-right">Total</th>
                                            <th style="width: 75px;"></th>
                                        </thead>
                                        <tbody>
                                            @foreach($cart->where('name', 'Product') as $item)
                                                
                                                <tr class="cart-item {{$item->rowId}}">
                                                    <td class="align-middle">{{$item->id->name}}<br><span class="text-grey">{{$item->options->description}}</span></td>
                                                    <td class="align-middle text-right">{{$item->options->currency . " " . number_format($item->price, 2, '.', ',')}}</td>
                                                    <td class="text-right align-middle"><input type="number" style="width:100px;float:right;" name="quantity[{{$item->rowId}}]" class="item-quantity text-right form-control {{ $errors->has('price') ? 'has-error' : ''}}"  min="1" {{(!($item->id->overselling_allowed)) ? "max=".$item->id->available_inventory : ""}} value="{{$item->qty}}" required data-company="{{$company->slug}}" data-rowId="{{$item->rowId}}" ></td>
                                                    <td class="text-right align-middle item_price">{{$item->options->currency . " " . number_format($item->price*$item->qty, 2, '.', ',')}}</td>
                                                    <td class="align-middle"><button type="button"  class="remove" data-rowId="{{$item->rowId}}" data-company="{{$company->slug}}">Remove</button></td>
                                                </tr>
                                            @endforeach

                                            @foreach($cart->where('name', 'Shipping') as $item)
                                                <tr class="cart-item {{$item->rowId}}">
                                                    <td class="align-middle" colspan="3"><span class="note">SHIPPING:<br></span>{{$item->id->name}}<br><span class="text-grey">{{$item->id->description}}</span></td>
                                                    <td class="text-right align-middle item_price">{{$item->options->currency . " " . number_format($item->price*$item->qty, 2, '.', ',')}}</td>
                                                    <td class="align-middle"><button type="button"  class="remove" data-rowId="{{$item->rowId}}" data-company="{{$company->slug}}">Remove</button></td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td></td>
                                                <td class="text-right text-bold">Total:</td>
                                                <td class="text-right text-bold" id="cart_itemcount">{{$cart_itemcount}}</td>
                                                <td class="text-right text-bold" id="cart_total">{{$cart_total}}</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <a class="button ghost" href="" data-dismiss="modal" aria-label="Close">< Add more to cart</a>
                                    <button type="submit">Add shipping ></button>
                                </form>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection