<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="addModalLabel"><strong>Add New Entry</strong></h3>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="modalCloseBtn">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
        <!-- Step Progress Bar -->
        <div class="stepper-wrapper">
          <div class="stepper-item active">
            <div class="step-counter">1</div>
            <span>Step 1</span>
          </div>
          <div class="stepper-item">
            <div class="step-counter">2</div>
            <span>Step 2</span>
          </div>
        </div>

        <!-- Step 1: Vacation Rendered Service -->
        <div id="step1">
          <div class="mb-3 text-center">
            <label class="new-form-label" style="font-weight: bold; font-size: 18px; font-weight: bold;">Vacation Service Rendered</label>
          </div>
          <div class="mb-3">
            <label for="modalIncPer" class="new-form-label"><strong>Inclusive Period:</strong></label>
            <input type="text" id="inclusive_period" class="form-control" placeholder="Enter Inclusive Period">
          </div>
          <div class="mb-3">
            <label for="modalNatAct" class="new-form-label"><strong>Nature of Activity:</strong></label>
            <input type="text" id="nature_of_activity" class="form-control" placeholder="Enter Nature of Activity">
          </div>
          <div class="mb-3">
            <label for="modalNoDayCred" class="new-form-label"><strong>No. of Days Credited:</strong></label>
            <input type="number" id="no_of_days_credited" class="form-control" placeholder="Enter No. of Days Credited">
          </div>
          <div class="mb-3">
            <label for="modalDsoNo" class="new-form-label"><strong>DSO No.:</strong></label>
            <input type="text" id="dso_no_vsr" class="form-control" placeholder="Enter DSO No.">
          </div>
        </div>

        <!-- Step 2: Record of Leave -->
        <div id="step2" style="display: none;">
          <div class="mb-3 text-center">
            <label class="new-form-label" style="font-weight: bold; font-size: 18px; font-weight: bold;">Record of Leave</label>
          </div>
          <div class="mb-3">
            <label for="modalLeaveReason" class="new-form-label"><strong>Inclusive Dates:</strong></label>
            <input type="text" id="inclusive_dates" class="form-control" placeholder="Enter Inclusive Dates">
          </div>
          <div class="mb-3">
            <label for="modalLeaveDate" class="new-form-label"><strong>Days With Pay:</strong></label>
            <input type="number" id="no_days_leave" class="form-control" placeholder="Enter Days With Pay">
          </div>
          <div class="mb-3">
            <label for="modalLeaveDuration" class="new-form-label"><strong>Service Credit Balance:</strong></label>
            <input type="number" id="service_cred_bal" class="form-control" placeholder="Enter Service Credit Balance">
          </div>
          <div class="mb-3">
            <label for="modalLeaveDuration" class="new-form-label"><strong>Days Without Pay:</strong></label>
            <input type="number" id="leave_without_pay" class="form-control" placeholder="Enter Days Without Pay">
          </div>
          <div class="mb-3">
            <label for="modalLeaveDuration" class="new-form-label"><strong>Nature Of Leave:</strong></label>
            <input type="text" id="nature_of_leave" class="form-control" placeholder="Enter Nature Of Leave">
          </div>
          <div class="mb-3">
            <label for="modalLeaveDuration" class="new-form-label"><strong>DSO No.:</strong></label>
            <input type="text" id="dso_no_rol" class="form-control" placeholder="Enter DSO No.">
          </div>
          <div class="mb-3">
            <label for="remarks" class="new-form-label"><strong>Remarks:</strong></label>
            <textarea id="remarks" class="form-control" placeholder="Enter Remarks" rows="4" style="resize: none;"></textarea>
          </div>
        </div>
        <input type="hidden" id="employee_number" value="{{ $user->employee_number ?? '' }}">
      </div>
      <div class="modal-footer">
        <!-- Navigation Buttons -->
        <button type="button" id="prevBtn" class="btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Previous
        </button>
        <button type="button" id="nextBtn" class="btn btn-success">
          <i class="fa fa-arrow-right"></i> Next
        </button>
        <button id="modalAddBtn" class="btn btn-success approve-btn" style="display: none;">
            <i class="fa fa-add"></i> Add
        </button>
      </div>
    </div>
  </div>
</div>
<style>
    .modal input::placeholder {
  font-size: smaller; /* Adjust the size to your preference */
  color: #888; /* Optional: Change the color of the placeholder text */
  font-weight: normal; /* Optional: Change the font weight */
}
    .stepper-wrapper {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.stepper-item {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  font-size: 14px;
}

.stepper-item::before {
  position: absolute;
  content: "";
  border-bottom: 2px solid #ccc;
  width: 100%;
  top: 20px;
  left: -50%;
  z-index: 2;
}

.stepper-item::after {
  position: absolute;
  content: "";
  border-bottom: 2px solid #ccc;
  width: 100%;
  top: 20px;
  left: 50%;
  z-index: 2;
}

.stepper-item .step-counter {
  position: relative;
  z-index: 5;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  background: #ccc;
  margin-bottom: 6px;
}

.stepper-item.active {
  font-weight: bold;
}

.stepper-item.completed .step-counter {
  background-color: #4bb543;
}

.stepper-item.completed::after {
  position: absolute;
  content: "";
  border-bottom: 2px solid #4bb543;
  width: 100%;
  top: 20px;
  left: 50%;
  z-index: 3;
}

.stepper-item:first-child::before {
  content: none;
}
.stepper-item:last-child::after {
  content: none;
}
</style>
