<meta charset="utf-8">
<title>Welcome To Edu Leave</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
<link href="{{ asset('css?family=Roboto:300,400,500,700,900') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admincss/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admincss/assets/plugins/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('admincss/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('admincss/assets/css/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('admincss/assets/plugins/morris/morris.css') }}">
<link rel="stylesheet" href="{{ asset('admincss/assets/css/style.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- DataTable CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<!-- Include AlertifyJS CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>

<style>
  /* Add a specific class when in fullscreen mode */
  table.fullscreen-table {
    /* You can add any fullscreen-specific styles here if needed */
    width: 100%;
    table-layout: fixed; /* Example: This can help with column alignment */
  }
  table.dataTable {
    width: 100%;
  }
  .action-buttons button {
    margin: 0 5px;
  }
  /* Custom styles for the status column */
  .status-rejected {
    color: red;
    font-weight: bold;
    text-transform: capitalize;
  }
  .status-approved {
    color: green;
    font-weight: bold;
    text-transform: capitalize;
  }
  .status-pending {
    color: orange;
    font-weight: bold;
    text-transform: capitalize;
  }
  .employee-link {
    font-size: 14px;
    color: #007bff; /* Bootstrap's primary color for links */
    text-decoration: none;
  }
  .employee-link:hover {
    text-decoration: underline;
    color: #0056b3; /* Darker shade for hover state */
  }
    /* Style for the container holding the show entries, fullscreen, and search buttons */
    .table-actions {
    display: flex;
    align-items: center;
  }

  /* Adjust the margins of the buttons to space them out nicely */
  #toggleFullscreenBtn {
    margin: 0 10px; /* Add spacing between the buttons */
  }

  /* Optional: Style the DataTable's search and show entries elements */
  .dataTables_length, .dataTables_filter {
    margin-right: 10px; /* Space between elements */
  }

  .fullscreen {
    position: fixed;
    top: -25px;
    left: 10px;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 1000;
    padding: 20px;
    overflow: auto;
  }

  .fullscreen #newTable {
    width: auto;
    height: auto;
  }

  .exit-fullscreen-btn {
    position: fixed;
    top: 10px; /* Adjust this to change the distance from the top */
    left: 50%;
    transform: translateX(-50%); /* Center the button horizontally */
    z-index: 1100;
    background-color: red;
    color: white;
    border: none;
    padding: 5px 10px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: auto; /* Set width if needed */
    height: 40px; /* Set height if needed */
  }
  #newTable {
    table-layout: auto; /* Allow columns to adjust dynamically */
    width: 100%; /* Ensure full-width usage */
    white-space: nowrap; /* Prevent content from wrapping */
  }
  #newTable th, #newTable td {
    text-align: center; /* Center-align content */
    vertical-align: middle; /* Align text vertically */
  }
  #newTable th {
    font-weight: bold; /* Keep headers bold for better readability */
  }
  #newTable tbody tr:hover {
    background-color: #f2f2f2; /* Optional: Add a hover effect for rows */
  }
  #tableContainer {
    overflow-x: auto; /* Enable horizontal scroll for small screens */
  }
  .btn-success,
  .btn-info {
    color: white !important; /* Ensures the font color stays white */
  }
  .dash-widget img {
    transition: transform 0.3s ease; /* Smooth transition */
  }

  .dash-widget img:hover {
      transform: scale(1.1); /* Slight zoom on hover */
  }
  .editable-cell input {
    width: 100%; /* Matches the width of the original table cell */
    height: 40px; /* Set a consistent height */
    overflow-wrap: break-word; /* Enable wrapping for long text */
    white-space: pre-wrap; /* Preserve spaces and line breaks */
    resize: none; /* Prevent resizing by users */
    box-sizing: border-box; /* Ensures padding is included in width/height */
  }
</style>

