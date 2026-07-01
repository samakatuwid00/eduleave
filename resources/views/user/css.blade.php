<meta charset="utf-8">
<title>Welcome To Edu Leave</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('assets/images/icons8-leave-48.png') }}" type="image/png">
<link href="{{ asset('css?family=Roboto:300,400,500,700,900') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('usercss/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/plugins/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/css/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/plugins/morris/morris.css') }}">
<link rel="stylesheet" href="{{ asset('usercss/assets/css/style.css') }}">
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
    /* Blurred Table Style */
  .blurred-table-container {
    position: relative;
    display: inline-block;
  }
  .blurred-table {
    filter: blur(5px);
    pointer-events: none;
    width: 100%;
  }
  /* Lock Image Style - Centered and Smaller */
  .lock-image {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px;  /* Smaller size */
    height: 100px; /* Smaller size */
    z-index: 10;
  }
  #newTable_wrapper {
    width: 100%;
    overflow: auto;
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
</style>
