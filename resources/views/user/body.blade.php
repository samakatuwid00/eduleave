@include('admin.loader')

@php
function formatDate($dateString) {
// Check if the string contains a time component
if (strtotime($dateString)) {
return date('Y-m-d', strtotime($dateString)); // Return only the date part (Y-m-d)
}
return $dateString; // If no time part, return the original string
}
@endphp

<div class="page-wrapper">
  <div class="content container-fluid">
    <!-- Warning Message -->
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Notice:</strong> Your account has been approved! You can view your leave card information.
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
                class="employee-link more-info-btn"
                data-id="{{ $user->id ?? '' }}"
                title="View Employee Info">
                ({{ $user->employee_number ?? 'N/A' }})
              </a>
            </h5>
          </div>
          <!-- Fullscreen toggle button -->
          <div class="table-actions d-flex justify-content-between align-items-center">
            <div class="dataTables_length" id="newTable_length"></div> <!-- For Show entries -->
            <button id="toggleFullscreenBtn" class="btn btn-primary mx-2">
              <i class="fa fa-expand"></i> Fullscreen
            </button>
            <div class="dataTables_filter" id="newTable_filter"></div> <!-- For Search -->
          </div>
        </div>
      </div>
    </div>
    <!-- Table Container -->
    <div class="row mt-4" id="tableContainer" data-user-name="{{ $user->name ?? 'Unknown User' }}">
      <div class="col-md-12">
        <table id="newTable" class="display nowrap cell-border ui celled table" style="width:100%; border-collapse: collapse;">
          <thead>
            <tr style="text-align: center;">
              <th colspan="5" style="border: 1px solid black; text-align: center;">Vacation Service Rendered</th>
              <th colspan="7" style="border: 1px solid black; text-align: center;">Record of Leave</th>
            </tr>
            <tr style="text-align: center;">
              <!-- Vacation Headers -->
              <th style="border: 1px solid black;">ID</th>
              <th style="border: 1px solid black;">Inclusive Period</th>
              <th style="border: 1px solid black;">Nature of Activity</th>
              <th style="border: 1px solid black;">No. of Days Credited</th>
              <th style="border: 1px solid black;">DSO No.</th>
              <!-- Record Headers -->
              <th style="border: 1px solid black;">Inclusive Dates</th>
              <th style="border: 1px solid black;">Days With Pay</th>
              <th style="border: 1px solid black;">Service Credit Balance</th>
              <th style="border: 1px solid black;">Days Without Pay</th>
              <th style="border: 1px solid black;">Nature of Leave</th>
              <th style="border: 1px solid black;">DSO No.</th>
              <th style="border: 1px solid black;">Remarks</th>
            </tr>
          </thead>
          <tbody>
            @php
            $counter = 1; // Initialize counter
            @endphp

            @foreach ($cardInfoss as $item)
            <tr data-id="{{ $item->id }}">
              <td style="border: 1px solid black;">{{ $counter }}</td> <!-- Use counter here -->
              <td style="border: 1px solid black;"> {{ formatDate($item->inclusive_period) }}</td>
              <td style="border: 1px solid black;">{{ $item->nature_of_activity }}</td>
              <td style="border: 1px solid black;">{{ $item->no_of_days_credited }}</td>
              <td style="border: 1px solid black;">{{ $item->dso_no_vsr }}</td>
              <td style="border: 1px solid black;">{{ formatDate($item->inclusive_dates) }}</td>
              <td style="border: 1px solid black;">{{ $item->no_days_leave }}</td>
              <td style="border: 1px solid black;">{{ $item->service_cred_bal }}</td>
              <td style="border: 1px solid black;">{{ $item->leave_without_pay }}</td>
              <td style="border: 1px solid black;">{{ $item->nature_of_leave }}</td>
              <td style="border: 1px solid black;">{{ $item->dso_no_rol }}</td>
              <td style="border: 1px solid black;">{{ $item->remarks }}</td>
            </tr>

            @php
            $counter++; // Increment counter after each row
            @endphp
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@include('user.more_info')
@include('user.remarks-modal')