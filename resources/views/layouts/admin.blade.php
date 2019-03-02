<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ empty($title) ? $sidebar : $title  }}</title>
    <link rel="shortcut icon" href="{{ URL::to('/img/favicon.png') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/solid.css" integrity="sha384-29Ax2Ao1SMo9Pz5CxU1KMYy+aRLHmOu6hJKgWiViCYpz3f9egAJNwjnKGgr+BXDN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/fontawesome.css" integrity="sha384-Lyz+8VfV0lv38W729WFAmn77iH5OSroyONnUva4+gYaQTic3iI2fnUKtDSpbVf0J" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script type="text/javascript" src="{{ elixir('js/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ elixir('js/app.js') }}"></script>
    
</head>
<body class="admin {{$action}} {{$controller}}">
    <div id="app">
        {{-- <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light justify-content-end">
            <a class="navbar-brand logo d-md-none d-lg-none d-xl-none">
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
            </a>
        <a class="navbar-brand d-none d-md-block d-lg-block d-xl-block" href="/dashboard">{{ Auth::user()->company->name }}</a>
        <div class="nav-item d-md-none d-lg-none d-xl-none {{ $sidebar == "Dashboard" ? "active" : ""}}">
            <a href="/dashboard" class="">
            <i class="fas fa-chart-bar mx-auto d-block text-center"></i>
            </a>
        </div>
        <div class="nav-item d-md-none d-lg-none d-xl-none {{ $sidebar == "Products" ? "active" : ""}}">
            <a href="/products">
            <i class="fas fa-shopping-cart mx-auto d-block text-center"></i>
            </a>
        </div>
        
        <div class="nav-item d-md-none d-lg-none d-xl-none {{ $sidebar == "Orders" ? "active" : ""}}">
            <a href="/orders">
            @if(Auth::user()->unreadNotifications->count() > 0)
            <span class="badge badge-light">{{Auth::user()->unreadNotifications->count()}}</span>
            @endif
            <i class="fas fa-clipboard-list mx-auto d-block text-center"></i>
            </a>
        </div>
        <div class="nav-item d-md-none d-lg-none d-xl-none {{ $sidebar == "Inventory" ? "active" : ""}}">
            <a href="/inventory">
            <i class="fas fa-dolly mx-auto d-block text-center"></i>
            </a>
        </div>
        <div class="nav-item d-md-none d-lg-none d-xl-none {{ $sidebar == "Settings" ? "active" : ""}}">
            <a href="/settings">
            <i class="fas fa-cog mx-auto d-block text-center"></i>
            </a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-md-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
                        </div>
                    </li>
                </ul>

            </div>
        </nav> --}}
        <div class="container-fluid">
            <div class="row">
                <div class="sidebar">
                    {{-- <div class="item logo">
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

                    </div> --}}
                    <div class="item d-flex align-items-center justify-content-center {{ $sidebar == "Dashboard" ? "active" : ""}}">
                        <a href="/dashboard" class="">
                        <i class="fas fa-chart-bar mx-auto d-block text-center"></i>
                        <p class="text-center">Dashboard</p>
                        </a>
                    </div>
                    <div class="item d-flex align-items-center justify-content-center {{ $sidebar == "Products" ? "active" : ""}}">
                        <a href="/products">
                        <i class="fas fa-shopping-cart mx-auto d-block text-center"></i>
                        <p class="text-center" href="">Products</p>
                        </a>
                    </div>
                    <div class="item d-flex align-items-center justify-content-center {{ $sidebar == "Orders" ? "active" : ""}}">
                        <a href="/orders">
                        @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="badge badge-light">{{Auth::user()->unreadNotifications->count()}}</span>
                        @endif
                        <i class="fas fa-clipboard-list mx-auto d-block text-center"></i>
                        <p class="text-center" href="">Orders</p>
                        </a>
                    </div>
                    <div class="item d-flex align-items-center justify-content-center {{ $sidebar == "Inventory" ? "active" : ""}}">
                        <a href="/inventory">
                        <i class="fas fa-dolly mx-auto d-block text-center"></i>
                        <p class="text-center" href="">Inventory</p>
                        </a>
                    </div>
                    <div class="item d-flex align-items-center justify-content-center {{ $sidebar == "Settings" ? "active" : ""}}">
                        <a href="/settings">
                        <i class="fas fa-cog mx-auto d-block text-center"></i>
                        <p class="text-center" href="">Settings</p>
                        </a>
                    </div>
                </div>
                <div class="content">
                    <div class="page-header">
                        <h1>{{empty($title) ? $sidebar : $title}}</h1>
                    </div>
                    <div class="container-fluid">
                    @yield('content')
                    </div>
                </div>      
            </div>
        </div>
        
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <!-- <script src="http://code.jquery.com/jquery-3.3.1.min.js"  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="  crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
    @yield('custom_js')
</body>
</html>
