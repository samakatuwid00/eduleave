<!DOCTYPE html>
<html lang="en">
<head>

  @include('user.head')
  
</head>
<body data-card-type="{{ $profile->personnelType->code }}">
<div class="main-wrapper">

  @include ('user.header')

  @include('user.contactus')

  @include ('user.sidebar')

  @include('user.body')
  
  @include('user.footer')
</div>
</body>
</html>
