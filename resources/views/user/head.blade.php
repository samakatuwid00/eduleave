<meta charset="utf-8">
<title>Welcome To Edu Leave</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
  (() => {
    try {
      const savedTheme = localStorage.getItem('admin-theme');
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      document.documentElement.dataset.adminTheme = savedTheme || (prefersDark ? 'dark' : 'light');
    } catch (error) {
      document.documentElement.dataset.adminTheme = 'light';
    }
  })();
</script>
<link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
<link href="{{ asset('css?family=Roboto:300,400,500,700,900') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('usercss/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/plugins/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/css/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/plugins/morris/morris.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/css/style.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- DataTable CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<!-- Include AlertifyJS CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>

<link rel="stylesheet" href="{{ asset('usercss/assets/css/eduleave.css') }}">

