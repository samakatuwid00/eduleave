<!-- Remarks Modal -->
<div id="remarksModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="moreInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="moreInfoModalLabel"><strong>Remarks</strong></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modalCloseBtn">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal" id="modalCloseButton" title="View">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).on('click', '.btn-remarks', function () {
  // Get the ID from the button's data-id attribute
  var cardInfoId = $(this).data('id');
  
  // Make an AJAX request to fetch the remarks
  $.ajax({
    url: '/get-remarks', // Your route to fetch remarks
    type: 'GET',
    data: { id: cardInfoId },
    success: function (response) {
      // Populate the modal's body with the remarks
      $('#remarksModal .modal-body').html('<p>' + response.remarks + '</p>');
      
      // Open the modal using Bootstrap 5 modal method
      var myModal = new bootstrap.Modal(document.getElementById('remarksModal'));
      myModal.show();
    },
    error: function (xhr, status, error) {
      console.error('Error fetching remarks:', error);
      alert('An error occurred while fetching remarks.');
    }
  });
});
</script>
