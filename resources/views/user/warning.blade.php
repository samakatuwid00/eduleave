<!DOCTYPE html>
<html lang="en">
<head>
  @include('user.head')
</head>
<body data-card-type="{{ $profile->personnelType->code }}">
  <div class="main-wrapper">
    @include('user.header')
    @include('user.sidebar')
    @include('admin.loader')
    <div class="page-wrapper">
      <div class="content container-fluid">
        <!-- Warning Message -->
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Notice:</strong> Kindly wait for your account to be approved by the administrator to view your leave card. You will receive an email.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- Page Header -->
        <div class="page-header">
          <div class="row">
            <div class="col-sm-12 d-flex justify-content-between align-items-center">
              <div>
                <h3 class="page-title" style="font-size: small">Your Leave Card</h3>
                  <h5 class="page-title">
                    {{ $user->name ?? 'Unknown User' }}
                    <a href="#" 
                      class="employee-link more-info-warning-btn" 
                      data-id="{{ $user->id ?? '' }}" 
                      title="View Employee Info">
                      ({{ $profile->employee_number }})
                    </a>
                  </h5>
              </div>
            </div>
          </div>
        </div>
        <!-- Table with Blurred Data and Lock Image -->
        <div class="table-container">
          <table id="example" class="table blurred-table">
            <thead>
              <tr>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
              </tr>
              <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
              </tr>
              <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
              </tr>
              <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
              </tr>
            </tbody>
          </table>
          <!-- Lock Image Overlay -->
          <!-- <img src="{{ asset('usercss/assets/img/lock.png') }}" alt="Lock" class="lock-image" /> -->
        </div>
      </div>
    </div>
  </div>
  @include('user.more_info')
</body>
</html>
@include('user.contactus')
@include('user.footer')
@if(session('welcome_message'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        alertify.success("{{ session('welcome_message') }}");
    });
</script>
@endif
