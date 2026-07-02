<!DOCTYPE html>
<html lang="en">

<head>
  @include('admin.css')
</head>

<body data-card-type="{{ $profile->personnelType->code }}">

  @php
  $formatDate = static function ($dateString) {
    if ($dateString && strtotime($dateString)) {
      return date('Y-m-d', strtotime($dateString));
    }

    return $dateString;
  };
  @endphp

  @include('admin.loader')

  <div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
      <div class="content container-fluid">
        <div class="page-header">
          <div class="row">
            <div class="col-sm-12 d-flex justify-content-between align-items-center">
              <div>
                <h3 class="page-title" style="font-size: small">{{ $profile->personnelType->name }} Leave Card</h3>
                <h5 class="page-title">
                  {{ $user->name ?? 'Unknown User' }}
                  <a href="#"
                    class="employee-link more-info-btn"
                    data-id="{{ $user->id ?? '' }}"
                    title="View Employee Info">
                    ({{ $profile->employee_number }})
                  </a>
                  <button class="btn btn-sm btn-light" onclick="copyEmployeeNumber('{{ $profile->employee_number }}')" title="Copy Employee Number">
                    <i class="fa fa-copy"></i>
                  </button>
                </h5>
              </div>
              <div class="d-flex justify-content-end align-items-center my-3">
                <!-- Back Button -->
                <a href="{{ url('admin/teacher_leave_cards') }}" class="btn btn-info me-2">
                  <i class="fa fa-arrow-left"></i> Back
                </a>
                <!-- Download Template Button -->
                <a
                  href="{{ route('admin.leave-card.template', ['cardType' => $profile->personnelType->code, 'employeeNumber' => $profile->employee_number]) }}"
                  class="btn btn-info"
                  title="Download Excel Template"
                  style="margin-left: 4px">
                  <i class="fa fa-download"></i>
                </a>
                <!-- Upload Excel File -->
                <form class="upload-form"
                  action="{{ route('admin.leave-card.import', ['cardType' => $profile->personnelType->code, 'employeeNumber' => $profile->employee_number]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <input id="fileInput" class="form-control-sm" type="file" name="excel_file" accept=".xlsx" required style="display: none;" onchange="updateButton()">
                  <button type="button" id="uploadButton" class="btn btn-danger" onclick="triggerFileInput()" title="Upload Excel File" style="margin-left: 4px">
                    <i class="fa fa-upload"></i>
                  </button>
                </form>
                <!-- Add Button -->
                <a
                  class="btn btn-success add-new-entry"
                  style="margin-left: 4px"
                  title="Add New Entry"
                  data-bs-toggle="modal"
                  data-bs-target="#addModal">
                  <i class="fa fa-plus"></i>
                </a>
                <!-- Fullscreen Button -->
                <button id="toggleFullscreenBtn" class="btn btn-success mx-1" title="Toggle Fullscreen">
                  <i class="fa fa-expand"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- Scrollable Combined Table -->
        <div class="row mt-4" id="tableContainer" data-user-name="{{ $user->name ?? 'Unknown User' }}">
          <div class="col-md-12">
            <table id="newTable" class="display nowrap cell-border ui celled table" style="width:100%; border-collapse: collapse;">
              <thead>
                @if ($profile->personnelType->code === \App\Models\PersonnelType::CODE_NON_TEACHING)
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
                  <th style="border: 1px solid black;" class="no-export">Actions</th>
                </tr>
                @elseif ($profile->personnelType->code === \App\Models\PersonnelType::CODE_TEACHING)
                <tr style="text-align: center;">
                  <th colspan="5" style="border: 1px solid black; text-align: center;">Vacation Service Rendered</th>
                  <th colspan="7" style="border: 1px solid black; text-align: center;">Record of Leave</th>
                  <th rowspan="2" style="border: 1px solid black; vertical-align: middle; text-align: center;" class="no-export">Action</th>
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
                @endif
              </thead>
              <tbody>
                @php
                $counter = 1; // Initialize manual counter
                @endphp

                @foreach ($cardInfoss as $item)
                <tr data-id="{{ $item->id }}">
                  <td data-field="id" style="border: 1px solid black;">{{ $counter }}</td> <!-- Display counter -->
                  @if ($profile->personnelType->code === \App\Models\PersonnelType::CODE_TEACHING)
                  <td class="editable-cell" data-field="inclusive_period" style="border: 1px solid black;">
                    {{ $formatDate($item->inclusive_period) }}
                  </td>
                  <td class="editable-cell" data-field="nature_of_activity" style="border: 1px solid black;">{{ $item->nature_of_activity }}</td>
                  <td class="editable-cell" data-field="no_of_days_credited" style="border: 1px solid black;">{{ $item->days_credited }}</td>
                  <td class="editable-cell" data-field="dso_no_vsr" style="border: 1px solid black;">{{ $item->vacation_service_dso_number }}</td>
                  <td class="editable-cell" data-field="inclusive_dates" style="border: 1px solid black;">
                    {{ $formatDate($item->inclusive_leave_dates) }}
                  </td>
                  <td class="editable-cell" data-field="no_days_leave" style="border: 1px solid black;">{{ $item->days_with_pay }}</td>
                  <td class="editable-cell" data-field="service_cred_bal" style="border: 1px solid black;">{{ $item->service_credit_balance }}</td>
                  <td class="editable-cell" data-field="leave_without_pay" style="border: 1px solid black;">{{ $item->days_without_pay }}</td>
                  <td class="editable-cell" data-field="nature_of_leave" style="border: 1px solid black;">{{ $item->nature_of_leave }}</td>
                  <td class="editable-cell" data-field="dso_no_rol" style="border: 1px solid black;">{{ $item->record_of_leave_dso_number }}</td>
                  <td class="editable-cell" data-field="remarks" style="border: 1px solid black;">{{ $item->remarks }}</td>
                  @else
                  <td class="editable-cell" data-field="inclusive_period" style="border: 1px solid black;">{{ $item->period }}</td>
                  <td class="editable-cell" data-field="nature_of_activity" style="border: 1px solid black;">{{ $item->particulars }}</td>
                  <td class="editable-cell" data-field="no_of_days_credited" style="border: 1px solid black;">{{ $item->vacation_leave_earned }}</td>
                  <td class="editable-cell" data-field="dso_no_vsr" style="border: 1px solid black;">{{ $item->vacation_leave_with_pay }}</td>
                  <td class="editable-cell" data-field="inclusive_dates" style="border: 1px solid black;">{{ $item->vacation_leave_balance }}</td>
                  <td class="editable-cell" data-field="no_days_leave" style="border: 1px solid black;">{{ $item->vacation_leave_without_pay }}</td>
                  <td class="editable-cell" data-field="leave_without_pay" style="border: 1px solid black;">{{ $item->sick_leave_earned }}</td>
                  <td class="editable-cell" data-field="service_cred_bal" style="border: 1px solid black;">{{ $item->sick_leave_with_pay }}</td>
                  <td class="editable-cell" data-field="nature_of_leave" style="border: 1px solid black;">{{ $item->sick_leave_balance }}</td>
                  <td class="editable-cell" data-field="dso_no_rol" style="border: 1px solid black;">{{ $item->sick_leave_without_pay }}</td>
                  <td class="editable-cell" data-field="remarks" style="border: 1px solid black;">{{ $item->leave_application_action }}</td>
                  @endif
                  <td style="border: 1px solid black;" class="no-export">
                    <button class="btn btn-success btn-edit" title="Edit">
                      <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-delete" title="Delete">
                      <i class="fa fa-trash"></i>
                    </button>
                    <button class="btn btn-success btn-save" title="Save" style="display:none;">
                      <i class="fa fa-save"></i>
                    </button>
                    <button class="btn btn-danger btn-cancel" title="Cancel" style="display:none;">
                      <i class="fa fa-cancel"></i>
                    </button>
                  </td>
                </tr>

                @php
                $counter++; // Increment the counter
                @endphp
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('admin.sidebar.remarks-modal')
  @include('admin.sidebar.more_info')
  @include('admin.sidebar.add-modal')
  @include('admin.footer')
  @if (session('success'))
  <script>
    Swal.fire({
      title: 'Import complete',
      text: {{ \Illuminate\Support\Js::from(session('success')) }},
      icon: 'success'
    });
  </script>
  @elseif ($errors->has('excel_file') || $errors->has('card_type'))
  <script>
    Swal.fire({
      title: 'Import failed',
      text: {{ \Illuminate\Support\Js::from(implode(' ', $errors->all())) }},
      icon: 'error'
    });
  </script>
  @endif
</body>

</html>
<!-- DataTable JS -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.53/build/pdfmake.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.53/build/vfs_fonts.js"></script>
