<!DOCTYPE html>
<html lang="en">

  <head>
    @include('home.css')
  </head>

  <body>
  @include('home.contactus')

  <!--header-->
  @include('home.header')

  <!-- ***** Main Banner Area Start ***** -->
  @include('home.banner')

  <!-- ***** Main Banner Area End ***** -->
  @include('home.section')

  <!-- Bootstrap core JavaScript -->
  <script src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('assets/js/isotope.min.js') }}"></script>
  <script src="{{ asset('assets/js/owl-carousel.js') }}"></script>
  <script src="{{ asset('assets/js/lightbox.js') }}"></script>
  <script src="{{ asset('assets/js/tabs.js') }}"></script>
  <script src="{{ asset('assets/js/video.js') }}"></script>
  <script src="{{ asset('assets/js/slick-slider.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  </body>

</html>
