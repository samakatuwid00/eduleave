$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).ready(function () {
    const userTable =
        $("#userTable").length && $.fn.DataTable
        ? $("#userTable").DataTable({
              order: [],
              scrollX: true,
          })
        : null;

    const actionCenterTable =
        $("#actionCenterTable").length && $.fn.DataTable
        ? $("#actionCenterTable").DataTable({
              order: [[0, "desc"], [4, "desc"]],
              pageLength: 10,
              lengthMenu: [10, 25, 50, 100],
              scrollX: true,
              columnDefs: [{ targets: 6, orderable: false }],
          })
        : null;

    function adjustVisibleDataTables() {
        if (!$.fn.DataTable) {
            return;
        }

        $("table.dataTable").each(function () {
            if (!$.fn.DataTable.isDataTable(this)) {
                return;
            }

            const dataTable = $(this).DataTable();
            dataTable.columns.adjust();

            if (dataTable.responsive) {
                dataTable.responsive.recalc();
            }
        });
    }

    let dataTableAdjustmentTimer;

    function scheduleDataTableAdjustment(delay = 80) {
        window.clearTimeout(dataTableAdjustmentTimer);
        dataTableAdjustmentTimer = window.setTimeout(
            adjustVisibleDataTables,
            delay
        );
    }

    function closeCollapsedSubmenus() {
        const submenus = $("#sidebar-menu .submenu").removeClass(
            "collapsed-submenu-open"
        );

        if ($("body").hasClass("mini-sidebar")) {
            submenus
                .children("a")
                .removeClass("subdrop")
                .attr("aria-expanded", "false");
            submenus.children("ul").stop(true, true).hide();
        }
    }

    function closeMobileSidebar() {
        $(".main-wrapper").removeClass("slide-nav");
        $(".sidebar-overlay").removeClass("opened");
        $("html").removeClass("menu-opened");
        $("#mobile_btn").attr("aria-expanded", "false");
        closeCollapsedSubmenus();
    }

    $(document).on("click", "#mobile_btn", function () {
        $(this).attr(
            "aria-expanded",
            $(".main-wrapper").hasClass("slide-nav") ? "true" : "false"
        );
    });

    $(document).on("click", "#sidebar_close", closeMobileSidebar);

    $(document).on("keydown", function (event) {
        if (event.key === "Escape") {
            closeMobileSidebar();
            closeCollapsedSubmenus();
        }
    });

    $(window).on("resize", function () {
        if (window.innerWidth > 991) {
            closeMobileSidebar();
        }

        scheduleDataTableAdjustment();
    });

    $(document).on("click", "#sidebar-menu a", function () {
        if (window.innerWidth <= 991 && !$(this).parent().hasClass("submenu")) {
            closeMobileSidebar();
        }
    });

    $(document).on("click", "#sidebar-menu .submenu > a", function (event) {
        if (window.innerWidth < 992 || !$("body").hasClass("mini-sidebar")) {
            return;
        }

        event.preventDefault();

        const link = $(this);
        const submenu = link.parent();
        const shouldOpen = link.hasClass("subdrop");

        $("#sidebar-menu .submenu")
            .not(submenu)
            .removeClass("collapsed-submenu-open")
            .children("a")
            .attr("aria-expanded", "false");

        submenu.toggleClass("collapsed-submenu-open", shouldOpen);
        link.attr("aria-expanded", shouldOpen ? "true" : "false");

        if (shouldOpen) {
            const submenuList = submenu.children("ul");
            const linkTop = link[0].getBoundingClientRect().top;
            const maximumTop = window.innerHeight - submenuList.outerHeight() - 10;
            const flyoutTop = Math.max(10, Math.min(linkTop, maximumTop));

            submenuList.css("top", `${flyoutTop}px`);
        }
    });

    $(document).on("click", function (event) {
        if (!$(event.target).closest("#sidebar-menu .submenu").length) {
            closeCollapsedSubmenus();
        }
    });

    $(document).on("click", "#toggle_btn", function () {
        const collapsed = $("body").hasClass("mini-sidebar");

        $("body").removeClass("expand-menu");
        closeCollapsedSubmenus();

        if (collapsed) {
            $("#sidebar-menu .submenu > ul").stop(true, true).hide();
        } else {
            $("#sidebar-menu .submenu").each(function () {
                const submenu = $(this);
                const hasActiveChild = submenu.find("li.active").length > 0;

                submenu
                    .children("a")
                    .toggleClass("subdrop", hasActiveChild)
                    .attr("aria-expanded", hasActiveChild);
                submenu.children("ul").toggle(hasActiveChild);
            });
        }

        scheduleDataTableAdjustment(420);
    });

    $(".sidebar, .page-wrapper").on("transitionend", function (event) {
        if (["width", "margin-left", "left"].includes(event.originalEvent.propertyName)) {
            adjustVisibleDataTables();
        }
    });

    // Fetch the user name from the HTML element
    var userName = $("#tableContainer").data("user-name"); // Correct target for data-user-name

    $("#newTable").DataTable({
        scrollX: true,
        scrollY: "545px",
        scrollCollapse: true,
        paging: true,
        responsive: true,
        // order: [[1, 'asc']],
        columnDefs: [
            { targets: [0, 1, 2, 3] }, // Vacation Section
            { targets: [4, 5, 6, 7, 8, 9, 10] }, // Record Section
            { targets: 1, orderable: false }, // Action column non-sortable
        ],
        fixedHeader: true,
        lengthMenu: [5, 10, 25, 50, 100],
        pageLength: 5,
        dom: "lBfrtip",
        buttons: [
            {
                extend: "excelHtml5",
                text: '<i class="fa fa-file-excel"></i> Excel',
                title: function () {
                    return "Teachers Leave Card - " + userName;
                },
                exportOptions: {
                    columns: ':not(.no-export)'
                },
            },
            {
                extend: "pdfHtml5",
                text: '<i class="fa fa-file-pdf"></i> PDF',
                title: function () {
                    return "Teachers Leave Card - " + userName;
                },
                exportOptions: {
                    columns: ':not(.no-export)'
                },
                orientation: 'landscape',
            },
            {
                extend: "print",
                text: '<i class="fa fa-print"></i> Print',
                title: function () {
                    return "Teachers Leave Card - " + userName;
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
            },
        ],
    });

    // Handle More Info Button
    $(document).on('click', '.more-info-btn', function() {
        const userId = $(this).data("id"); // Get the user ID from data-id attribute
        const modalSelector = $(this).data("details-modal") || "#moreInfoModal";
        const $detailsModal = $(modalSelector);

        // AJAX request to fetch user details
        $.ajax({
            url: "/get-user-details", // Backend route to get user details
            method: "GET",
            data: { id: userId }, // Send user ID to the server
            success: function (response) {
                const displayValue = (value) => {
                    return value === null || value === undefined || String(value).trim() === ""
                        ? "N/A"
                        : value;
                };

                // Populate modal fields with the user details
                $detailsModal.find("#modalName").text(displayValue(response.name));
                $detailsModal.find("#modalEmail").text(displayValue(response.email));
                $detailsModal.find("#modalPersonnelType").text(displayValue(response.personnel_type));
                $detailsModal.find("#modalPosition").text(displayValue(response.position));
                $detailsModal.find("#modalDateEmployed").text(displayValue(response.date_employed));
                $detailsModal.find("#modalSex").text(displayValue(response.sex));
                $detailsModal.find("#modalDateOfBirth").text(displayValue(response.date_of_birth));
                $detailsModal.find("#modalPlaceOfBirth").text(displayValue(response.place_of_birth));
                $detailsModal.find("#modalEmployeeNumber").text(displayValue(response.employee_number));
                $detailsModal.find("#modalStation").text(displayValue(response.station));
                $detailsModal.find("#modalCivilStatus").text(displayValue(response.civil_status));
                $detailsModal.find("#modalStatus").text(
                    response.status
                        ? response.status.charAt(0).toUpperCase() + response.status.slice(1)
                        : "N/A"
                ); // Capitalize first letter

                // Apply color styles based on status
                if (response.status === "pending") {
                    $detailsModal.find("#modalStatus").css({
                        color: "orange",
                        "font-weight": "bold",
                    });
                    $("#modalApproveBtn").show();
                    $("#modalRejectBtn").show();
                    $("#modalCloseButton").hide();
                } else if (response.status === "rejected") {
                    $detailsModal.find("#modalStatus").css({
                        color: "red",
                        "font-weight": "bold",
                    });
                    $("#modalApproveBtn").hide();
                    $("#modalRejectBtn").hide();
                    $("#modalCloseButton").show();
                } else if (response.status === "active") {
                    $detailsModal.find("#modalStatus").css({
                        color: "green",
                        "font-weight": "bold",
                    });
                    $("#modalApproveBtn").hide();
                    $("#modalRejectBtn").hide();
                    $("#modalCloseButton").show();
                }
                // Assign user ID to Approve and Reject buttons in the modal
                $("#modalApproveBtn").data("id", userId);
                $("#modalRejectBtn").data("id", userId);

                // Show the modal
                $detailsModal.modal("show");
            },
            error: function () {
                Swal.fire(
                    "Error!",
                    "Failed to fetch user details. Please try again.",
                    "error"
                );
            },
        });
    });

    function refreshUserTableRow(userId, status) {
        const row = $('[data-user-id="' + userId + '"]');

        if (!row.length) {
            return;
        }

        const sourceTable = row.closest("table");
        const filter = sourceTable.data("user-table-filter");
        const shouldRemove = filter === "pending" || (filter && filter !== "all" && filter !== status);

        if (shouldRemove) {
            if ($.fn.DataTable.isDataTable(sourceTable[0])) {
                sourceTable.DataTable().row(row).remove().draw(false);
            } else {
                row.remove();
            }

            return;
        }

        row.find(".status-pending, .status-approved, .status-rejected")
            .removeClass("status-pending status-approved status-rejected")
            .addClass(status === "active" ? "status-approved" : "status-" + status)
            .text(status === "active" ? "Active" : status.charAt(0).toUpperCase() + status.slice(1));

        if ($.fn.DataTable.isDataTable(sourceTable[0])) {
            sourceTable.DataTable().row(row).invalidate("dom").draw(false);
        }
    }

    function changeUserStatus(userId, status) {
        const approving = status === "active";
        const action = approving ? "approve" : "reject";
        const pastTense = approving ? "Approved" : "Rejected";

        Swal.fire({
            title: "Are you sure?",
            text: "You want to " + action + " this user!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, " + action + " it!",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: "/admin/users/" + action + "/" + userId,
                method: "POST",
                success: function (response) {
                    $("#moreInfoModal").modal("hide");
                    refreshUserTableRow(userId, response.status || status);
                    Swal.fire(pastTense + "!", response.message, "success");
                },
                error: function (xhr) {
                    console.error("Error:", xhr);
                    Swal.fire(
                        "Error!",
                        "Failed to " + action + " user. Please try again.",
                        "error"
                    );
                },
            });
        });
    }

    $(document).on("click", "#modalApproveBtn, .modal-approve-btn", function () {
        changeUserStatus($(this).data("id"), "active");
    });

    $(document).on("click", "#modalRejectBtn, .modal-reject-btn", function () {
        changeUserStatus($(this).data("id"), "rejected");
    });

    $(document).ready(function () {
        // Attach click event for View Leave Card button
        $(document).on("click", "#viewLeaveCardBtn", function () {
            // Get the employee number from the modal's Employee Number span
            const employeeNumber = $(this)
                .closest(".modal")
                .find("#modalEmployeeNumber")
                .text()
                .trim();

            if (employeeNumber && employeeNumber !== "N/A") {
                // Redirect to the leave card page with the employee number
                window.location.href = `/admin/leave_card/${employeeNumber}`;
            } else {
                alert("Employee number not found! Please try again.");
            }
        });
    });

    // Show Alertify notification after page reload (if any)
    const alertMessage = localStorage.getItem("alertMessage");
    if (alertMessage) {
        alertify.success(alertMessage); // Display the success notification
        localStorage.removeItem("alertMessage"); // Clear the message
    }

    // In-line editing
    $(document).ready(function () {
        let currentEditingRow = null;
        const table = $("#newTable").DataTable(); // Store DataTable instance
        let originalData = {}; // To store original data before editing
        let currentEditingRowIndex = null; // To store global row index

        // Edit button functionality
        $(document).on("click", ".btn-edit", function () {
            if (currentEditingRow) {
                // Display message for currently editing row with its global row number
                alertify.error(
                    "You are currently editing row number: " +
                        (currentEditingRowIndex + 1) // Show the global row index (1-based)
                );
                return; // Prevent multiple rows from being edited
            }

            var row = $(this).closest("tr");
            var actionCell = row.find("td:last-child");

            row.addClass("editable");

            row.find(".editable-cell").each(function () {
                var cell = $(this);
                var text = cell.text();
                var field = cell.data("field");
                originalData[field] = text;

                cell.html(
                    '<input type="text" value="' +
                        text +
                        '" class="form-control">'
                );
            });

            actionCell.find(".btn-edit, .btn-delete").hide();
            actionCell.append(
                '<button class="btn btn-danger btn-cancel" title="Cancel"><i class="fa fa-cancel"></i></button>'
            );
            actionCell.append(
                '<button style="margin: 0 3px;" class="btn btn-success btn-save" title="Save"><i class="fa fa-check"></i></button>'
            );
            currentEditingRow = row;
            currentEditingRowIndex = table.row(row).index(); // Store the global row index

            table.columns.adjust().draw(false);
        });

        // Save button functionality
        $(document).on("click", ".btn-save", function () {
            var row = $(this).closest("tr");
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
                updatedData[field] = value;
            });

            $.ajax({
                url: `/admin/card_info/${encodeURIComponent(cardType)}/${row.data("id")}`,
                type: "PUT",
                data: updatedData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.success) {
                        row.find(".editable-cell").each(function () {
                            var cell = $(this);
                            var field = cell.data("field");
                            cell.text(updatedData[field]); // Update cell text
                        });

                        alertify.success(
                            "Row " +
                                (currentEditingRowIndex + 1) +
                                " updated successfully" // Show global index on success
                        );
                        row.removeClass("editable");
                        row.find(".btn-edit, .btn-delete").show();
                        row.find(".btn-cancel").remove();
                        row.find(".btn-save").remove();
                        currentEditingRow = null;
                        currentEditingRowIndex = null; // Reset editing row index

                        table.columns.adjust().draw(false);
                    } else {
                        alertify.error("Failed to update the row.");
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alertify.error(
                        "Failed to update the row. " + xhr.responseText
                    );
                },
            });
        });

        // Cancel button functionality
        $(document).on("click", ".btn-cancel", function () {
            var row = $(this).closest("tr");

            row.find(".editable-cell").each(function () {
                var cell = $(this);
                var field = cell.data("field");
                var originalText = originalData[field];
                cell.text(originalText);
            });

            row.removeClass("editable");
            row.find(".btn-edit, .btn-delete").show();
            row.find(".btn-cancel").remove();
            row.find(".btn-save").remove();
            currentEditingRow = null;
            currentEditingRowIndex = null; // Reset editing row index

            table.columns.adjust().draw(false);
        });

        // Delete button functionality
        $(document).on("click", ".btn-delete", function () {
            var row = $(this).closest("tr");
            var globalRowIndex = table.row(row).index(); // Get global row index (across all pages)
            var cardType = document.body.dataset.cardType;

            if (!cardType) {
                alertify.error("Unable to determine the leave-card type.");
                return;
            }

            alertify
                .confirm(
                    "Confirm Deletion",
                    "Are you sure you want to delete row number: " +
                        (globalRowIndex + 1), // Show global row number (1-based)
                    function () {
                        $.ajax({
                            url: `/admin/card_info/${encodeURIComponent(cardType)}/${row.data("id")}`,
                            type: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": $(
                                    'meta[name="csrf-token"]'
                                ).attr("content"),
                            },
                            success: function (response) {
                                if (response.success) {
                                    table.row(row).remove().draw(false); // Ensure proper removal
                                    alertify.success(
                                        "Row " +
                                            (globalRowIndex + 1) +
                                            " deleted successfully" // Show global row number
                                    );
                                } else {
                                    alertify.error("Failed to delete the row.");
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error(xhr.responseText);
                                alertify.error(
                                    "Failed to delete the row. " +
                                        xhr.responseText
                                );
                            },
                        });
                    },
                    function () {
                        alertify.error("Deletion canceled.");
                    }
                )
                .set("labels", { ok: "Yes", cancel: "No" });
        });
    });

    // step progress bar
    let currentStep = 1;
    function showStep(step) {
        // Hide all steps
        document.getElementById("step1").style.display = "none";
        document.getElementById("step2").style.display = "none";

        // Show the current step
        document.getElementById("step" + step).style.display = "block";

        // Update stepper items
        const stepItems = document.querySelectorAll(".stepper-item");
        stepItems.forEach((item, index) => {
            item.classList.remove("active");
            item.classList.remove("completed");
            if (index < step - 1) {
                item.classList.add("completed");
            } else if (index === step - 1) {
                item.classList.add("active");
            }
        });

        // Toggle the visibility of the Add button and navigation buttons
        if (step === 2) {
            document.getElementById("modalAddBtn").style.display =
                "inline-block";
            document.getElementById("nextBtn").style.display = "none";
        } else {
            document.getElementById("modalAddBtn").style.display = "none";
            document.getElementById("nextBtn").style.display = "inline-block";
        }

        document.getElementById("prevBtn").style.display =
            step === 1 ? "none" : "inline-block";
    }
    document.getElementById("nextBtn")?.addEventListener("click", function () {
        if (currentStep < 2) {
            currentStep++;
            showStep(currentStep);
        }
    });
    document.getElementById("prevBtn")?.addEventListener("click", function () {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    // Add button click event to mark step 2 as completed
    document
        .getElementById("modalAddBtn")
        ?.addEventListener("click", function () {
            // Mark Step 2 as completed when Add button is clicked
            const stepItems = document.querySelectorAll(".stepper-item");
            stepItems.forEach((item, index) => {
                if (index === 1) {
                    // Step 2 is at index 1 (second item)
                    item.classList.add("completed");
                }
            });
            // Optionally, you can disable the next/prev buttons or perform other actions after clicking Add.
        });

    // Initially show the first step
    if (document.getElementById("step1")) {
        showStep(currentStep);
    }

    $(document).on("click", "#modalAddBtn", function () {
        // Collect form data and user_id
        let formData = {
            inclusive_period: $("#inclusive_period").val(),
            nature_of_activity: $("#nature_of_activity").val(),
            no_of_days_credited: $("#no_of_days_credited").val(),
            dso_no_vsr: $("#dso_no_vsr").val(),
            inclusive_dates: $("#inclusive_dates").val(),
            no_days_leave: $("#no_days_leave").val(),
            service_cred_bal: $("#service_cred_bal").val(),
            leave_without_pay: $("#leave_without_pay").val(),
            nature_of_leave: $("#nature_of_leave").val(),
            dso_no_rol: $("#dso_no_rol").val(),
            remarks: $("#remarks").val(),
            employee_number: $("#employee_number").val(), // Add user_id here
            _token: $('meta[name="csrf-token"]').attr("content"), // CSRF Token
        };

        // Confirm before submitting the form
        Swal.fire({
            title: "Are you sure?",
            text: "You want to add this entry!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, add it!",
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request to the server
                $.ajax({
                    url: "/card-info/store", // Ensure this matches your route
                    method: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Added!",
                                text: response.message,
                                icon: "success",
                            }).then(() => {
                                $("#addModal").modal("hide"); // Hide the modal after success
                                location.reload(); // Reload the page to reflect the new entry
                            });
                            // Show success alert using Alertify
                            alertify.success("User entry successfully added!");
                        }
                    },
                    error: function (xhr) {
                        let errorMessage =
                            "Failed to add entry. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(
                                xhr.responseJSON.errors
                            )
                                .map((err) => err.join(", "))
                                .join("\n");
                        }
                        Swal.fire("Error!", errorMessage, "error");
                        // Show error alert using Alertify
                        alertify.error("Error: " + errorMessage);
                    },
                });
            }
        });
    });

    $(document).on("click", ".btn-remarks", function () {
        // Get the ID from the button's data-id attribute
        var cardInfoId = $(this).data("id");

        // Make an AJAX request to fetch the remarks
        $.ajax({
            url: "/get-remarks", // Replace with your route for fetching remarks
            type: "GET",
            data: { id: cardInfoId },
            success: function (response) {
                // Populate the modal's body with the remarks
                $("#remarksModal .modal-body").html(
                    "<p>" + response.remarks + "</p>"
                );

                // Open the modal
                $("#remarksModal").modal("show");
            },
            error: function (xhr, status, error) {
                console.error("Error fetching remarks:", error);
                alert("An error occurred while fetching remarks.");
            },
        });
    });
});

// Fullscreen
const tableContainer = document.getElementById("tableContainer");
const toggleFullscreenBtn = document.getElementById("toggleFullscreenBtn");
const table = document.getElementById("newTable"); // The table element

toggleFullscreenBtn?.addEventListener("click", function () {
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

// Excel Batch Upload
function triggerFileInput() {
    const fileInput = document.getElementById("fileInput");

    // Reset the file input before opening the dialog
    fileInput.value = "";

    fileInput.click();

    // Detect if the file input loses focus without a file being selected
    fileInput.addEventListener(
        "change",
        function () {
            if (fileInput.files.length === 0) {
                setTimeout(() => {
                    triggerFileInput(); // Reopen the file selection dialog if no file was chosen
                }, 300); // Small delay to prevent infinite loops
            }
        },
        { once: true }
    ); // Ensures the event fires only once per trigger
}

function updateButton() {
    const fileInput = document.getElementById("fileInput");
    const uploadButton = document.getElementById("uploadButton");

    if (fileInput.files.length > 0) {
        uploadButton.classList.remove("btn-danger");
        uploadButton.classList.add("btn-success");
        uploadButton.innerHTML = `<i class="fa fa-upload"></i> Ready to Upload!`; // Add icon
        uploadButton.setAttribute("onclick", "confirmUpload()");
    } else {
        uploadButton.classList.remove("btn-success");
        uploadButton.classList.add("btn-danger");
        uploadButton.innerHTML = `<i class="fa fa-upload"></i>`; // Reset icon
        uploadButton.removeAttribute("onclick");
    }
}

function confirmUpload() {
    const fileInput = document.getElementById("fileInput");
    const fileName = fileInput.files[0]?.name;

    if (!fileName) {
        triggerFileInput();
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: `You want to upload "${fileName}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, upload it!",
        cancelButtonText: "No, choose another file",
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelector(".upload-form").submit();
        } else {
            const uploadButton = document.getElementById("uploadButton");
            uploadButton.classList.remove("btn-success");
            uploadButton.classList.add("btn-danger");
            uploadButton.innerHTML = `<i class="fa fa-upload"></i>`; // Reset icon

            fileInput.value = ""; // Reset the file input
            triggerFileInput(); // Reopen the file selection dialog
        }
    });
}

// Copy Employee Number
function copyEmployeeNumber(employeeNumber) {
    // Create a temporary input element
    const tempInput = document.createElement("input");
    tempInput.value = employeeNumber; // Set value to the employee number
    document.body.appendChild(tempInput);
    tempInput.select(); // Select the text
    document.execCommand("copy"); // Copy to clipboard
    document.body.removeChild(tempInput); // Remove the temporary input
    alertify.success("Employee Number Copied To Clipboard!");
}
