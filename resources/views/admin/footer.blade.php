<footer class="application-footer">
  <p>&copy; {{ now()->year }} EduLeave. All rights reserved.</p>
</footer>

<style>
    .application-footer {
        position: static !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
        display: block !important;
        float: none !important;
        clear: both !important;
        width: auto !important;
        margin: 24px 10px 10px 290px !important;
        padding: 16px 20px !important;
        box-sizing: border-box;
        text-align: center;
    }

    .application-footer p {
        margin: 0 !important;
    }

    .mini-sidebar .application-footer {
        margin-left: 110px !important;
    }

    @media (max-width: 991.98px) {
        .application-footer,
        .mini-sidebar .application-footer {
            margin-left: 10px !important;
        }
    }
</style>

<!-- Scripts -->
<script src="{{ asset('/admincss/assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/select2.min.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/moment.min.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/fullcalendar.min.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/jquery.fullcalendar.js') }}"></script>
<script src="{{ asset('/admincss/assets/plugins/morris/morris.min.js') }}"></script>
<script src="{{ asset('/admincss/assets/plugins/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/apexcharts.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/chart-data.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/app.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/admin-shell.js') }}"></script>
<script src="{{ asset('/admincss/assets/js/custom.js') }}?v={{ filemtime(public_path('admincss/assets/js/custom.js')) }}"></script>

<!-- datatables -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>

<!-- sweet alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Include AlertifyJS JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<!-- Include Bootstrap JS for tabs -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Set AlertifyJS notification position
  alertify.set('notifier', 'position', 'top-right');

  // Check if login_success session variable exists
  @if(session('login_success'))
    // Show success notification after login
    alertify.success('Welcome To The Admin Dashboard!');
    // Remove the session variable after showing the notification
    @php session()->forget('login_success'); @endphp
  @endif
</script>
