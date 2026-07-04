<!DOCTYPE html>
<html lang="en">
<head>
  
  @include('admin.head')

</head>
<body>

  @include('admin.loader')

<div class="main-wrapper">
  @include('admin.header')
  @include('admin.sidebar')

  <div class="page-wrapper">
    <div class="content container-fluid">
      <div class="page-header">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="page-title"><strong>Pending Users</strong></h3>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="dashboard-table-shell">
          <table id="userTable" class="display" data-user-table-filter="pending">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Employee Number</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $counter = 1; // Initialize manual counter
              @endphp

              @foreach ($users as $row)
                @if ($row->usertype !== 'admin') <!-- Exclude admins -->
                @if ($row->status === 'pending') 
                @if ($row->email_verified_at != NULL) 
                <tr data-user-id="{{ $row->id }}">
                  <td>{{ $counter }}</td> <!-- Use manual counter -->
                  <td>{{ $row->name }}</td>
                  <td>{{ $row->email }}</td>
                  <td>{{ $row->employeeProfile?->employee_number ?? 'N/A' }}</td>
                  <td class="
                    @if($row->status == 'rejected') status-rejected
                    @elseif($row->status == 'active') status-approved
                    @elseif($row->status == 'pending') status-pending
                    @endif
                  ">
                    {{ ucfirst($row->status) }}
                  </td>
                  <td class="action-buttons">
                    <button class="btn btn-info more-info-btn" data-id="{{ $row->id }}" title="View">
                      <i class="fa fa-eye"></i>
                    </button>
                    <button class="btn btn-success modal-approve-btn" data-id="{{ $row->id }}" title="Approve">
                      <i class="fa fa-check"></i>
                    </button>
                    <button class="btn btn-danger modal-reject-btn" data-id="{{ $row->id }}" title="Reject">
                      <i class="fa fa-x"></i>
                    </button>
                  </td>
                </tr>
                @php
                  $counter++; // Increment counter only when the user is not admin
                @endphp
                @endif
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

@include('admin.sidebar.more_info')

@include('admin.footer')

</body>
</html>
