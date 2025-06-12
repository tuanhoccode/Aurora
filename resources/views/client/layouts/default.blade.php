<!doctype html>
<html class="no-js" lang="zxx">

<!-- Mirrored from html.storebuild.shop/shofy-prv/shofy/index-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 18 May 2025 07:19:23 GMT -->

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>@yield('title', 'Aurora') </title>
   <meta name="description" content="">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- Place favicon.ico in the root directory -->
   <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets2/img/logo/favicon.png')}}">

   <!-- CSS here -->
   <link rel="stylesheet" href="{{asset('assets2/css/bootstrap.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/animate.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/swiper-bundle.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/slick.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/magnific-popup.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/font-awesome-pro.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/flaticon_shofy.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/spacing.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/main.css')}}">

   <!-- Toastr CSS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

   <!-- jQuery + Toastr JS (trước </body>) -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

</head>

<body>
   <!--[if lte IE 9]>
      <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
      <![endif]-->


   <!-- pre loader area start -->

   <!-- cart mini area end -->

   <!-- header area start -->
   @include('client.layouts.partials.header')
   <!-- header area end -->

   @yield('content')



   <!-- footer area start -->
   @include('client.layouts.partials.footer')
   <!-- footer area end -->
   @if (session('success'))
   <script>
      toastr.options = {
         "positionClass": "toast-top-right",
         "timeOut": 2000
      };
      toastr.success(@json(session('success')));
   </script>
   @endif

   @if (session('error'))
   <script>
      toastr.options = {
         "positionClass": "toast-top-right",
         "timeOut": 2000
      };
      toastr.error(@json(session('error')));
   </script>
   @endif


   <!-- JS here -->
   <script src="{{asset('assets2/js/vendor/jquery.js')}}"></script>
   <script src="{{asset('assets2/js/vendor/waypoints.js')}}"></script>
   <script src="{{asset('assets2/js/bootstrap-bundle.js')}}"></script>
   <script src="{{asset('assets2/js/meanmenu.js')}}"></script>
   <script src="{{asset('assets2/js/swiper-bundle.js')}}"></script>
   <script src="{{asset('assets2/js/slick.js')}}"></script>
   <script src="{{asset('assets2/js/range-slider.js')}}"></script>
   <script src="{{asset('assets2/js/magnific-popup.js')}}"></script>
   <script src="{{asset('assets2/js/nice-select.js')}}"></script>
   <script src="{{asset('assets2/js/purecounter.js')}}"></script>
   <script src="{{asset('assets2/js/countdown.js')}}"></script>
   <script src="{{asset('assets2/js/wow.js')}}"></script>
   <script src="{{asset('assets2/js/isotope-pkgd.js')}}"></script>
   <script src="{{asset('assets2/js/imagesloaded-pkgd.js')}}"></script>
   <script src="{{asset('assets2/js/ajax-form.js')}}"></script>
   <script src="{{asset('assets2/js/main.js')}}"></script>
</body>

<!-- Mirrored from html.storebuild.shop/shofy-prv/shofy/index-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 18 May 2025 07:20:23 GMT -->

</html>