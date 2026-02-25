<!-- Scripts -->
<script src="{{ asset('usercss/assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('usercss/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('usercss/assets/js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('usercss/assets/js/select2.min.js') }}"></script>
<script src="{{ asset('usercss/assets/js/moment.min.js') }}"></script>
<script src="{{ asset('usercss/assets/js/fullcalendar.min.js') }}"></script>
<script src="{{ asset('usercss/assets/js/jquery.fullcalendar.js') }}"></script>
<script src="{{ asset('usercss/assets/plugins/morris/morris.min.js') }}"></script>
<script src="{{ asset('usercss/assets/plugins/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('usercss/assets/js/apexcharts.js') }}"></script>
<script src="{{ asset('usercss/assets/js/chart-data.js') }}"></script>
<script src="{{ asset('usercss/assets/js/app.js') }}"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('usercss/assets/js/custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Include AlertifyJS JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<script>
  // Set AlertifyJS notification position
  alertify.set('notifier', 'position', 'top-right');

  // Check if login_success session variable exists using Blade syntax and passing it to JavaScript
  @if(session('login_success'))
    window.onload = function() {
        alertify.success('Welcome To Your Dashboard!');
    };
    // Remove the session variable after showing the notification
    @php session()->forget('login_success'); @endphp
  @endif
</script>
<!-- DataTable JS -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.53/build/pdfmake.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.53/build/vfs_fonts.js"></script>
