<!DOCTYPE html>
<html lang="en">
<head>

  @include('admin.head')

</head>
  
@include('admin.loader')

<div class="main-wrapper">
  @include('admin.header')
  @include('admin.sidebar')

  <div class="page-wrapper">
    <div class="content container-fluid">
      <div class="page-header">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="page-title"><strong>Teaching & Non-Teaching Leave Cards</strong></h3>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="dashboard-table-shell">
          <table id="userTable" class="display" data-user-table-filter="leave-cards">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Employee Number</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $counter = 1; // Initialize manual counter
              @endphp

              @foreach ($users as $row)
                @if ($row->usertype !== 'admin') <!-- Exclude admins -->
                @if ($row->status === 'active') 
                <tr data-user-id="{{ $row->id }}">
                  <td>{{ $counter }}</td> <!-- Use manual counter -->
                  <td>{{ $row->name }}</td>
                  <td>{{ $row->email }}</td>
                  <td>{{ $row->employeeProfile?->employee_number ?? 'N/A' }}</td>
                  <td class="action-buttons">
                    <!-- View Button -->
                    <button class="btn btn-info more-info-btn" data-id="{{ $row->id }}" title="View">
                      <i class="fa fa-eye"></i>
                    </button>
                    <!-- Edit Button -->
                    <!-- <button class="btn btn-danger more-delete-btn" data-id="{{ $row->id }}" title="Delete">
                      <i class="fa fa-trash"></i>
                    </button> -->
                  </td>
                </tr>
                @php
                  $counter++; // Increment counter only when the user is not admin
                @endphp
                @endif
                @endif
              @endforeach
            </tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
    
@include('admin.sidebar.view-leave_card-modal')

@include('admin.footer')

</body>
</html>
