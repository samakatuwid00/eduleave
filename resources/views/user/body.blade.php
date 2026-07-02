@include('admin.loader')

@php
$formatDate = static function ($dateString) {
  if ($dateString && strtotime($dateString)) {
    return date('Y-m-d', strtotime($dateString));
  }

  return $dateString;
};
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
            <h3 class="page-title" style="font-size: small">Your {{ $profile->personnelType->name }} Leave Card</h3>
            <h5 class="page-title">
              {{ $user->name ?? 'Unknown User' }}
              <a href="#"
                class="employee-link more-info-btn"
                data-id="{{ $user->id ?? '' }}"
                title="View Employee Info">
                ({{ $profile->employee_number }})
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

    @if ($profile->personnelType->code === \App\Models\PersonnelType::CODE_NON_TEACHING)
      <table id="newTable" class="display nowrap cell-border ui celled table" style="width:100%; border-collapse: collapse;">
        <thead>
          <tr style="text-align: center;">
            <th style="border: 1px solid black; text-align: center;" class="no-export">ID</th>
            <th style="border: 1px solid black;">Period</th>
            <th style="border: 1px solid black;">Particulars</th>
            <th style="border: 1px solid black;">Vacation Leave Earned</th>
            <th style="border: 1px solid black;">Absence/Undertime With Pay</th>
            <th style="border: 1px solid black;">Balance</th>
            <th style="border: 1px solid black;">Absence/Undertime Without Pay</th>
            <th style="border: 1px solid black;">Sick Leave Earned</th>
            <th style="border: 1px solid black;">Absence/Undertime With Pay</th>
            <th style="border: 1px solid black;">Balance</th>
            <th style="border: 1px solid black;">Absence/Undertime Without Pay</th>
            <th style="border: 1px solid black;">Date & Action On Application For Leave</th>
          </tr>
        </thead>
        <tbody>
          @php $counter = 1; @endphp
          @foreach ($cardInfoss as $item)
            <tr data-id="{{ $item->id }}">
              <td>{{ $counter }}</td>
              <td class="no-export">{{ $item->period }}</td>
              <td>{{ $item->particulars }}</td>
              <td>{{ $item->vacation_leave_earned }}</td>
              <td>{{ $item->vacation_leave_with_pay }}</td>
              <td>{{ $item->vacation_leave_balance }}</td>
              <td>{{ $item->vacation_leave_without_pay }}</td>
              <td>{{ $item->sick_leave_earned }}</td>
              <td>{{ $item->sick_leave_with_pay }}</td>
              <td>{{ $item->sick_leave_balance }}</td>
              <td>{{ $item->sick_leave_without_pay }}</td>
              <td>{{ $item->leave_application_action }}</td>
            </tr>
            @php $counter++; @endphp
          @endforeach
        </tbody>
      </table>

    @elseif ($profile->personnelType->code === \App\Models\PersonnelType::CODE_TEACHING)
      <table id="newTable" class="display nowrap cell-border ui celled table" style="width:100%; border-collapse: collapse;">
        <thead>
          <tr style="text-align: center;">
            <th colspan="5" style="border: 1px solid black; text-align: center;">Vacation Service Rendered</th>
            <th colspan="7" style="border: 1px solid black; text-align: center;">Record of Leave</th>
          </tr>
          <tr style="text-align: center;">
            <th style="border: 1px solid black;" class="no-export">ID</th>
            <th style="border: 1px solid black;">Inclusive Period</th>
            <th style="border: 1px solid black; text-align: center;">Nature of Activity</th>
            <th style="border: 1px solid black;">No. of Days Credited</th>
            <th style="border: 1px solid black;">DSO No.</th>
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
          @php $counter = 1; @endphp
          @foreach ($cardInfoss as $item)
            <tr data-id="{{ $item->id }}" class="no-export">
              <td>{{ $counter }}</td>
              <td>{{ $formatDate($item->inclusive_period) }}</td>
              <td>{{ $item->nature_of_activity }}</td>
              <td>{{ $item->days_credited }}</td>
              <td>{{ $item->vacation_service_dso_number }}</td>
              <td>{{ $formatDate($item->inclusive_leave_dates) }}</td>
              <td>{{ $item->days_with_pay }}</td>
              <td>{{ $item->service_credit_balance }}</td>
              <td>{{ $item->days_without_pay }}</td>
              <td>{{ $item->nature_of_leave }}</td>
              <td>{{ $item->record_of_leave_dso_number }}</td>
              <td>{{ $item->remarks }}</td>
            </tr>
            @php $counter++; @endphp
          @endforeach
        </tbody>
      </table>
    @endif

  </div>
</div>
  </div>
</div>
@include('user.more_info')
@include('user.remarks-modal')
