<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="page-title mb-0"><strong>Dashboard</strong></h3>
                </div>
                <div class="col-md-6">
                    <ul class="breadcrumb mb-0 p-0 float-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/admin/dashboard') }}"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span>Dashboard</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="dash-widget dash-widget5">
                    <a href="{{ url('/admin/users/view-all_users') }}" class="d-block">
                        <span class="float-left">
                            <img src="{{ asset('admincss/assets/img/multiple-user.png') }}" alt="" width="80">
                        </span>
                    </a>
                    <div class="dash-widget-info text-right">
                        <span>All User</span>
                        <h3>{{ \App\Models\User::where('usertype', '!=', 'admin')->whereNotNull('email_verified_at')->count() }}</h3>
                        </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="dash-widget dash-widget5">
                    <a href="{{ url('/admin/users/view-pending_users') }}" class="d-block">
                        <span class="float-left">
                            <img src="{{ asset('admincss/assets/img/pending.png') }}" width="80" alt="">
                        </span>
                    </a>
                    <div class="dash-widget-info text-right">
                        <span>Pending User/s</span>
                        <h3>{{ \App\Models\User::where('usertype', '!=', 'admin')
                        ->where('status', 'pending')
                        ->whereNotNull('email_verified_at')
                        ->count() }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="dash-widget dash-widget5">
                    <a href="{{ url('/admin/users/view-approved_users') }}" class="d-block">
                        <span class="float-left">
                            <img src="{{ asset('admincss/assets/img/approve.png') }}" width="80" alt="">
                        </span>
                    </a>
                    <div class="dash-widget-info text-right">
                        <span>Approved User/s</span>
                        <h3>{{ \App\Models\User::where('usertype', '!=', 'admin')->where('status', 'active')->count() }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="dash-widget dash-widget5">
                    <a href="{{ url('/admin/users/view-rejected_users') }}" class="d-block">
                        <span class="float-left">
                            <img src="{{ asset('admincss/assets/img/rejection.png') }}" width="80" alt="">
                        </span>
                    </a>
                    <div class="dash-widget-info text-right">
                        <span>Rejected User/s</span>
                        <h3>{{ \App\Models\User::where('usertype', '!=', 'admin')->where('status', 'rejected')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <div class="page-title"><strong>New Users</strong></div>
                            </div>
                            <div class="col-sm-6 text-sm-right">
                                <div class="mt-sm-0 mt-2">
                                    <!-- <button class="btn btn-outline-primary mr-2">
                                        <img src="{{ asset('admincss/assets/img/excel.png') }}" alt="">
                                        <span class="ml-2">Excel</span>
                                    </button>
                                    <button class="btn btn-outline-danger mr-2">
                                        <img src="{{ asset('admincss/assets/img/pdf.png') }}" alt="" height="18">
                                        <span class="ml-2">PDF</span>
                                    </button> -->
                                    <button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ url('/admin/users/view-all_users') }}">View All Users</a>
                                        <div role="separator" class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ url('/admin/users/view-approved_users') }}">Approved Users</a>
                                        <div role="separator" class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ url('/admin/users/view-rejected_users') }}">Rejected Users</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table custom-table" data-user-table-filter="pending">
                                <thead class="thead-light">
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
</div>
@include('admin.sidebar.more_info')
