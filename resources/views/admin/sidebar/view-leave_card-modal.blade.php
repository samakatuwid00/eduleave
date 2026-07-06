<!-- View Leave Card Modal -->
<div id="leaveCardInfoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="leaveCardInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h3 class="modal-title" id="leaveCardInfoModalLabel"><strong>User Information</strong></h3>
        <button
          type="button"
          class="close"
          data-bs-dismiss="modal"
          aria-label="Close"
          id="modalCloseBtn"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <p><strong>Name:</strong> <span id="modalName"></span></p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Personnel Type:</strong> <span id="modalPersonnelType"></span></p>
        <p><strong>Position:</strong> <span id="modalPosition"></span></p>
        <p><strong>Date Employed:</strong> <span id="modalDateEmployed"></span></p>
        <p><strong>Sex:</strong> <span id="modalSex"></span></p>
        <p><strong>Date of Birth:</strong> <span id="modalDateOfBirth"></span></p>
        <p><strong>Place of Birth:</strong> <span id="modalPlaceOfBirth"></span></p>
        <p><strong>Employee Number:</strong> <span id="modalEmployeeNumber"></span></p>
        <p><strong>Station:</strong> <span id="modalStation"></span></p>
        <p><strong>Civil Status:</strong> <span id="modalCivilStatus"></span></p>
        <p><strong>Account Status:</strong> <span id="modalStatus"></span></p>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
      <button
          id="viewLeaveCardBtn"
          class="btn btn-success"
          title="View Leave Card"
      >
          <i class="fa fa-pen"></i> Edit Leave Card
      </button>
      </div>
    </div>
  </div>
</div>
