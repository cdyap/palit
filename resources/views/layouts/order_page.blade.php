<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Palit: {{ $company->name  }}</title>
    <link rel="shortcut icon" href="{{ URL::to('/img/favicon.png') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/solid.css" integrity="sha384-TbilV5Lbhlwdyc4RuIV/JhD8NR+BfMrvz4BL5QFa2we1hQu6wvREr3v6XSRfCTRp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/fontawesome.css" integrity="sha384-ozJwkrqb90Oa3ZNb+yKFW2lToAWYdTiF1vt8JiH5ptTGHTGcN7qdoR1F95e0kYyG" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
</head>
<body class="order_page">
    @yield('header')
    <div class="body">
        <div class="alerts-holder">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>
                <strong>{{session('emphasize')}}</strong> {{session('success')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>
                <strong>{{session('emphasize')}}</strong> {{session('error')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        </div>
        <!-- collections -->
        @yield('content')
    </div>
    <footer>
        <div class="row">
            <div class="col-xs-12 col-md-4">
                This website is powered by: 
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
        </div>
    </footer>

    <!-- Scripts -->
    <!-- <script src="https://unpkg.com/axios/dist/axios.min.js"></script> -->
    <!-- <script src="http://code.jquery.com/jquery-3.3.1.min.js"  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="  crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
    <script type="text/javascript" src="{{ elixir('js/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ elixir('js/app.js') }}"></script>
    @yield('custom_js')
</body>
</html>
