$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$(document).ready(function () {
  // Initialize DataTable
  $('#example').DataTable();
// Fetch the user name from the HTML element
var userName = $('#tableContainer').data('user-name'); // Correct target for data-user-name

$('#newTable').DataTable({
    scrollX: true,
    scrollY: '545px',
    scrollCollapse: true,
    paging: true,
    responsive: true,
    columnDefs: [
        { targets: [0, 1, 2, 3] }, // Vacation Section
        { targets: [4, 5, 6, 7, 8, 9, 10] }, // Record Section
        { targets: 1, orderable: false } // Action column non-sortable
    ],
    fixedHeader: true, // Ensure header remains in view when scrolling
    lengthMenu: [5, 10, 25, 50, 100], // Specify the entries options
    pageLength: 5, // Set the default number of entries to display
    dom: 'lBfrtip', // This tells DataTable where to place buttons (B for buttons)
    buttons: [
        {
            extend: 'excelHtml5', // Export to Excel
            text: '<i class="fa fa-file-excel"></i> Excel',
            // className: 'btn btn-danger', // Button styling
            title: function () {
              return 'Teachers Leave Data - ' + userName; // Use the user name dynamically in the title
            },
            exportOptions: {
                columns: ':not(.no-export)'
            },
        },
        {
            extend: 'pdfHtml5', // Export to PDF
            text: '<i class="fa fa-file-pdf"></i> PDF',
            // className: 'btn btn-danger', // Button styling
            title: function () 
            {
              return 'Teachers Leave Data - ' + userName; // Use the user name dynamically in the title
            },
            exportOptions: {
                columns: ':not(.no-export)'
            },
            orientation: 'landscape',
        },
        {
            extend: 'print', // Print the table
            text: '<i class="fa fa-print"></i> Print',
            // className: 'btn btn-danger', // Button styling
            title: function () {
              return 'Teachers Leave Data - ' + userName; // Use the user name dynamically in the title
            },
            exportOptions: {
                columns: ':not(.no-export)'
            },
            customize: function (win) {
                $(win.document.head).append(
                    '<style>' +
                    '@page { size: landscape; }' +
                    'table { font-size: 10px; }' +
                    '</style>'
                );
            },
        }
    ]
});


// Handle More Info Button
$('.more-info-btn').click(function () {
    const userId = $(this).data('id'); // Get the user ID from data-id attribute

    // AJAX request to fetch user details
    $.ajax({
        url: '/get-user-details', // Backend route to get user details
        method: 'GET',
        data: { id: userId }, // Send user ID to the server
        success: function (response) {
            // Format the date_employed field
            const dateEmployed = new Date(response.date_employed); // Convert to Date object
            const formattedDate = dateEmployed.toISOString().split('T')[0]; // Get the date part

            // Populate modal fields with the user details
            $('#modalName').text(response.name);
            $('#modalEmail').text(response.email);
            $('#modalPosition').text(response.position);
            $('#modalDateEmployed').text(formattedDate); // Display only the date
            $('#modalSex').text(response.sex);
            $('#modalDateOfBirth').text(response.date_of_birth);
            $('#modalPlaceOfBirth').text(response.place_of_birth);
            $('#modalEmployeeNumber').text(response.employee_number);
            $('#modalStation').text(response.station);
            $('#modalCivilStatus').text(response.civil_status);
            $('#modalStatus').text(response.status.charAt(0).toUpperCase() + response.status.slice(1)); // Capitalize first letter

            // Apply color styles based on status
            if (response.status === 'pending') {
            $('#modalStatus').css({ 'color': 'orange', 'font-weight': 'bold' });
            $('#modalApproveBtn').show();
            $('#modalRejectBtn').show();
            $('#modalCloseButton').hide();
            } else if (response.status === 'rejected') {
            $('#modalStatus').css({ 'color': 'red', 'font-weight': 'bold' });
            $('#modalApproveBtn').hide();
            $('#modalRejectBtn').hide();
            $('#modalCloseButton').show();
            } else if (response.status === 'active') {
            $('#modalStatus').css({ 'color': 'green', 'font-weight': 'bold' });
            $('#modalApproveBtn').hide();
            $('#modalRejectBtn').hide();
            $('#modalCloseButton').show();
            }
            // Assign user ID to Approve and Reject buttons in the modal
            $('#modalApproveBtn').data('id', userId);
            $('#modalRejectBtn').data('id', userId);

            // Show the modal
            $('#moreInfoModal').modal('show');
        },
        error: function () {
            Swal.fire('Error!', 'Failed to fetch user details. Please try again.', 'error');
        }
     });
});

// Handle More Info Button
$('.more-info-warning-btn').click(function () {
  const userId = $(this).data('id'); // Get the user ID from data-id attribute

  // AJAX request to fetch user details
  $.ajax({
      url: '/get-user-details', // Backend route to get user details
      method: 'GET',
      data: { id: userId }, // Send user ID to the server
      success: function (response) {
          // Format the date_employed field
          const dateEmployed = new Date(response.date_employed); // Convert to Date object
          const formattedDate = dateEmployed.toISOString().split('T')[0]; // Get the date part

          // Populate modal fields with the user details
          $('#modalName').text(response.name);
          $('#modalEmail').text(response.email);
          $('#modalPosition').text(response.position);
          $('#modalDateEmployed').text(formattedDate); // Display only the date
          $('#modalSex').text(response.sex);
          $('#modalDateOfBirth').text(response.date_of_birth);
          $('#modalPlaceOfBirth').text(response.place_of_birth);
          $('#modalEmployeeNumber').text(response.employee_number);
          $('#modalStation').text(response.station);
          $('#modalCivilStatus').text(response.civil_status);
          $('#modalStatus').text(response.status.charAt(0).toUpperCase() + response.status.slice(1)); // Capitalize first letter
          // Show the modal
          $('#moreInfoModal').modal('show');
      },
      error: function () {
          Swal.fire('Error!', 'Failed to fetch user details. Please try again.', 'error');
      }
   });
});

// Handle Approve Button in Modal
$(document).on('click', '#modalApproveBtn', function () {
const userId = $(this).data('id');

Swal.fire({
    title: 'Are you sure?',
    text: "You want to approve this user!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, approve it!'
}).then((result) => {
    if (result.isConfirmed) {
        $.ajax({
            url: '/admin/users/approve/' + userId,
            method: 'POST',
            success: function (response) {
                Swal.fire('Approved!', response.message, 'success').then(() => {
                    $('#moreInfoModal').modal('hide'); // Hide modal
                    location.reload(); // Reload the page
                    localStorage.setItem('alertMessage', 'User approved successfully!');
                });
            },
            error: function () {
                Swal.fire('Error!', 'Failed to approve user. Please try again.', 'error');
            }
        });
    }
});
});

// Handle Reject Button in Modal
$(document).on('click', '#modalRejectBtn', function () {
    const userId = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to reject this user!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, reject it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/users/reject/' + userId,
                method: 'POST',
                success: function (response) {
                    Swal.fire('Rejected!', response.message, 'success').then(() => {
                        $('#moreInfoModal').modal('hide'); // Hide modal
                        location.reload(); // Reload the page
                        localStorage.setItem('alertMessage', 'User rejected successfully!');
                    });
                },
                error: function () {
                    Swal.fire('Error!', 'Failed to reject user. Please try again.', 'error');
                }
            });
        }
    });
});

// Handle Approve Button in Modal
$(document).on('click', '.modal-approve-btn', function () {
    const userId = $(this).data('id');
    console.log('Approving User ID:', userId);

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to approve this user!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/users/approve/' + userId,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire('Approved!', response.message, 'success').then(() => {
                        $('#moreInfoModal').modal('hide'); // Hide modal
                        location.reload(); // Reload the page
                        localStorage.setItem('alertMessage', 'User approved successfully!');
                    });
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    Swal.fire('Error!', 'Failed to approve user. Please try again.', 'error');
                }
            });
        }
    });
});

// Handle Reject Button in Modal
$(document).on('click', '.modal-reject-btn', function () {
    const userId = $(this).data('id');
    console.log('Rejecting User ID:', userId);

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to reject this user!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, reject it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/users/reject/' + userId,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire('Rejected!', response.message, 'success').then(() => {
                        $('#moreInfoModal').modal('hide'); // Hide modal
                        location.reload(); // Reload the page
                        localStorage.setItem('alertMessage', 'User rejected successfully!');
                    });
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    Swal.fire('Error!', 'Failed to reject user. Please try again.', 'error');
                }
            });
        }
    });
});

$(document).ready(function () {
    // Attach click event for View Leave Card button
    $(document).on('click', '#viewLeaveCardBtn', function () {
        const userId = $('#moreInfoModal').data('user-id'); // Get user ID stored in modal's data attribute

        if (userId) {
            // Redirect to the leave card page
            window.location.href = `/admin/leave_card/${userId}`;
        } else {
            alert('User ID not found!');
        }
    });

    // Attach click event for More Info buttons
    $('.more-info-btn').click(function () {
        const userId = $(this).data('id'); // Get the user ID from the button

        // Set user ID in the modal for later use
        $('#moreInfoModal').data('user-id', userId);

        // Make AJAX request to get user details
        $.ajax({
            url: '/get-user-details',
            method: 'GET',
            data: { id: userId },
            success: function (response) {
                // Populate modal fields with user details
                $('#modalName').text(response.name);
                $('#modalEmail').text(response.email);
                $('#modalPosition').text(response.position);
                $('#modalDateEmployed').text(new Date(response.date_employed).toISOString().split('T')[0]);
                $('#modalSex').text(response.sex);
                $('#modalDateOfBirth').text(response.date_of_birth);
                $('#modalPlaceOfBirth').text(response.place_of_birth);
                $('#modalEmployeeNumber').text(response.employee_number);
                $('#modalStation').text(response.station);
                $('#modalCivilStatus').text(response.civil_status);
                $('#modalStatus').text(response.status.charAt(0).toUpperCase() + response.status.slice(1));

                // Show the modal
                $('#moreInfoModal').modal('show');
            },
            error: function () {
                Swal.fire('Error!', 'Failed to fetch user details. Please try again.', 'error');
            }
        });
    });
});

// Show Alertify notification after page reload (if any)
const alertMessage = localStorage.getItem('alertMessage');
if (alertMessage) {
    alertify.success(alertMessage); // Display the success notification
    localStorage.removeItem('alertMessage'); // Clear the message
}

$(document).ready(function () {
    let currentEditingRow = null;

    // Handle the edit button click
    $(".btn-edit").on("click", function () {
        if (currentEditingRow) {
            // Prevent multiple rows from being edited at once
            return;
        }

        var row = $(this).closest("tr");
        var actionCell = row.find("td:last-child");

        row.addClass("editable");
        row.find(".editable-cell").each(function () {
            var cell = $(this);
            var text = cell.text();
            cell.html('<input type="text" value="' + text + '" class="form-control">');
        });

        actionCell.find(".btn-edit, .btn-delete").hide(); // Hide edit and delete buttons
        actionCell.append('<button class="btn btn-danger btn-cancel" title="Cancel"><i class="fa fa-times"></i></button>'); // Add cancel button
        actionCell.find(".btn-save").show(); // Show save button
        currentEditingRow = row; // Mark the row as being edited
    });

    // Handle the save button click
    $(".btn-save").on("click", function () {
        var row = $(this).closest("tr");
        var id = row.data("id"); // Get the row ID
        var updatedData = {};
        var cardType = document.body.dataset.cardType;

        if (!cardType) {
            alertify.error("Unable to determine the leave-card type.");
            return;
        }

        row.find(".editable-cell").each(function () {
            var cell = $(this);
            var input = cell.find("input");
            var value = input.val();
            var field = cell.data("field");
            updatedData[field] = value; // Collect updated field data
        });

        $.ajax({
            url: `/admin/card_info/${encodeURIComponent(cardType)}/${id}`,
            type: "PUT",
            data: updatedData,
            success: function (response) {
                row.find(".editable-cell").each(function () {
                    var cell = $(this);
                    var field = cell.data("field");
                    cell.text(updatedData[field]); // Update cell text
                });

                alertify.success("Row updated successfully!");
                row.removeClass("editable");
                row.find(".btn-edit, .btn-delete").show();
                row.find(".btn-cancel").remove();
                row.find(".btn-save").hide();
                currentEditingRow = null; // Clear editing row
            },
            error: function () {
                alertify.error("Failed to update the row.");
            },
        });
    });

    // Handle the delete button click
    $(".btn-delete").on("click", function () {
        var row = $(this).closest("tr");
        var id = row.data("id"); // Get the row ID
        var cardType = document.body.dataset.cardType;

        if (!cardType) {
            alertify.error("Unable to determine the leave-card type.");
            return;
        }

        alertify.confirm(
            "Warning!",
            "Are you sure you want to delete this row?",
            function () {
                $.ajax({
                    url: `/admin/card_info/${encodeURIComponent(cardType)}/${id}`,
                    type: "DELETE",
                    success: function (response) {
                        row.remove(); // Remove the row from the table
                        alertify.success("Row deleted successfully!");
                    },
                    error: function () {
                        alertify.error("Failed to delete the row.");
                    },
                });
            },
            function () {
                alertify.error("Delete operation canceled.");
            }
        );
    });

    // Handle the cancel button click
    $(document).on("click", ".btn-cancel", function () {
        var row = $(this).closest("tr");
        row.find(".editable-cell").each(function () {
            var cell = $(this);
            var input = cell.find("input");
            var originalText = input.val(); // Optionally store original text if needed
            cell.text(originalText);
        });

        row.removeClass("editable");
        row.find(".btn-edit, .btn-delete").show();
        row.find(".btn-cancel").remove();
        row.find(".btn-save").hide();
        currentEditingRow = null; // Clear editing row
    });
});

// step progress bar
let currentStep = 1;

function showStep(step) {
  // Hide all steps
  document.getElementById('step1').style.display = 'none';
  document.getElementById('step2').style.display = 'none';

  // Show the current step
  document.getElementById('step' + step).style.display = 'block';

  // Update stepper items
  const stepItems = document.querySelectorAll('.stepper-item');
  stepItems.forEach((item, index) => {
    item.classList.remove('active');
    item.classList.remove('completed');
    if (index < step - 1) {
      item.classList.add('completed');
    } else if (index === step - 1) {
      item.classList.add('active');
    }
  });

  // Toggle the visibility of the Add button and navigation buttons
  if (step === 2) {
    document.getElementById('modalAddBtn').style.display = 'inline-block';
    document.getElementById('nextBtn').style.display = 'none';
  } else {
    document.getElementById('modalAddBtn').style.display = 'none';
    document.getElementById('nextBtn').style.display = 'inline-block';
  }

  document.getElementById('prevBtn').style.display = (step === 1) ? 'none' : 'inline-block';
}

document.getElementById('nextBtn').addEventListener('click', function () {
  if (currentStep < 2) {
    currentStep++;
    showStep(currentStep);
  }
});

document.getElementById('prevBtn').addEventListener('click', function () {
  if (currentStep > 1) {
    currentStep--;
    showStep(currentStep);
  }
});

// Add button click event to mark step 2 as completed
document.getElementById('modalAddBtn').addEventListener('click', function () {
  // Mark Step 2 as completed when Add button is clicked
  const stepItems = document.querySelectorAll('.stepper-item');
  stepItems.forEach((item, index) => {
    if (index === 1) { // Step 2 is at index 1 (second item)
      item.classList.add('completed');
    }
  });
  // Optionally, you can disable the next/prev buttons or perform other actions after clicking Add.
});
  
// Initially show the first step
showStep(currentStep);

$(document).on('click', '#modalAddBtn', function () {
    // Collect form data and user_id
    let formData = {
      inclusive_period: $('#inclusive_period').val(),
      nature_of_activity: $('#nature_of_activity').val(),
      no_of_days_credited: $('#no_of_days_credited').val(),
      dso_no_vsr: $('#dso_no_vsr').val(),
      inclusive_dates: $('#inclusive_dates').val(),
      no_days_leave: $('#no_days_leave').val(),
      service_cred_bal: $('#service_cred_bal').val(),
      leave_without_pay: $('#leave_without_pay').val(),
      nature_of_leave: $('#nature_of_leave').val(),
      dso_no_rol: $('#dso_no_rol').val(),
      remarks: $('#remarks').val(),
      user_id: $('#user_id').val(),  // Add user_id here
      _token: $('meta[name="csrf-token"]').attr('content'),  // CSRF Token
    };
  
    // Confirm before submitting the form
    Swal.fire({
      title: 'Are you sure?',
      text: "You want to add this entry!",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, add it!'
    }).then((result) => {
      if (result.isConfirmed) {
        // Send AJAX request to the server
        $.ajax({
          url: '/card-info/store', // Ensure this matches your route
          method: 'POST',
          data: formData,
          success: function (response) {
            if (response.success) {
              Swal.fire({
                title: 'Added!',
                text: response.message,
                icon: 'success'
              }).then(() => {
                $('#addModal').modal('hide'); // Hide the modal after success
                location.reload(); // Reload the page to reflect the new entry
              });
              // Show success alert using Alertify
              alertify.success('User entry successfully added!');
            }
          },
          error: function (xhr) {
            let errorMessage = 'Failed to add entry. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
              errorMessage = Object.values(xhr.responseJSON.errors).map(err => err.join(', ')).join('\n');
            }
            Swal.fire('Error!', errorMessage, 'error');
            // Show error alert using Alertify
            alertify.error('Error: ' + errorMessage);
          }
        });
      }
    });
  });
  $(document).on('click', '.btn-remarks', function () {
    // Get the ID from the button's data-id attribute
    var cardInfoId = $(this).data('id');
    
    // Make an AJAX request to fetch the remarks
    $.ajax({
      url: '/get-remarks', // Replace with your route for fetching remarks
      type: 'GET',
      data: { id: cardInfoId },
      success: function (response) {
        // Populate the modal's body with the remarks
        $('#remarksModal .modal-body').html('<p>' + response.remarks + '</p>');
        
        // Open the modal
        $('#remarksModal').modal('show');
      },
      error: function (xhr, status, error) {
        console.error('Error fetching remarks:', error);
        alert('An error occurred while fetching remarks.');
      }
    });
  });
});
const tableContainer = document.getElementById('tableContainer');
const toggleFullscreenBtn = document.getElementById('toggleFullscreenBtn');
const table = document.getElementById('newTable'); // The table element

toggleFullscreenBtn.addEventListener("click", function () {
  if (!tableContainer.classList.contains("fullscreen")) {
      tableContainer.classList.add("fullscreen");
      $("#newTable").DataTable().columns.adjust().draw(false); // Ensure refresh

      const exitBtn = document.createElement("button");
      exitBtn.classList.add("exit-fullscreen-btn");
      exitBtn.id = "exitFullscreenBtn";

      // Add icon and text to the button
      exitBtn.innerHTML = '<i class="fa fa-compress"></i> Exit Fullscreen';

      exitBtn.addEventListener("click", function () {
          tableContainer.classList.remove("fullscreen");
          $("#newTable").DataTable().columns.adjust().draw(false); // Ensure refresh
          exitBtn.remove();
      });

      tableContainer.appendChild(exitBtn);
  } else {
      tableContainer.classList.remove("fullscreen");
      const exitBtn = document.getElementById("exitFullscreenBtn");
      if (exitBtn) exitBtn.remove();
      $("#newTable").DataTable().columns.adjust().draw(false); // Ensure refresh
  }
});

