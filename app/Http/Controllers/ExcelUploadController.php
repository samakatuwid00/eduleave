<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CardInfo;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;
use Illuminate\Http\Request;

class ExcelUploadController extends Controller
{
    public function upload(Request $request, $employee_number)
    {
        // Validate file input
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);
    
        // Retrieve the uploaded file
        $file = $request->file('excel_file');
        $path = $file->getRealPath();
    
        // Read the Excel file using SimpleXLSX
        if ($xlsx = SimpleXLSX::parse($path)) {
            // Loop through the rows and extract data
            foreach ($xlsx->rows() as $row) {
                // Skip the header row if necessary
                if ($row[0] === 'Inclusive Period') continue; // Skip the header row
    
                // Ensure values are valid and handle empty or missing numeric values
                $no_of_days_credited = is_numeric($row[2]) ? $row[2] : 0;
                $no_days_leave = is_numeric($row[5]) ? $row[5] : 0;
                $leave_without_pay = is_numeric($row[6]) ? $row[6] : 0;
                $service_cred_bal = is_numeric($row[7]) ? $row[7] : 0;

                // Insert each row into the database
                CardInfo::create([
                    'emp_num' => $employee_number,  // Use the employee_number from route or session
                    'inclusive_period' => $row[0],  // First column is inclusive_period now
                    'nature_of_activity' => $row[1],
                    'no_of_days_credited' => $no_of_days_credited,  // Handle null or invalid
                    'dso_no_vsr' => $row[3],
                    'inclusive_dates' => $row[4],
                    'no_days_leave' => $no_days_leave,  // Handle null or invalid
                    'leave_without_pay' => $leave_without_pay,  // Handle null or invalid
                    'service_cred_bal' => $service_cred_bal,  // Handle null or invalid
                    'nature_of_leave' => $row[8],
                    'dso_no_rol' => $row[9],
                    'remarks' => $row[10],
                ]);
            }
    
            return back()->with('success', 'Excel file has been uploaded successfully.');
        }
    
        return back()->with('error', 'Failed to read the Excel file.');
    }

    public function downloadTemplate($employee_number)
    {
        // Retrieve the selected user's data by employee number
        $user = User::where('employee_number', $employee_number)->first();
    
        if (!$user) {
            return back()->with('error', 'User not found.');
        }
    
        $userName = $user->name ?? 'Unknown User';
    
        // Define the headers
        $headers = [[
            'Inclusive Period', 'Nature of Activity', 'No of Days Credited', 'DSO No VSR',
            'Inclusive Dates', 'No Days Leave', 'Service Credit Balance', 'Leave Without Pay',
            'Nature of Leave', 'DSO No ROL', 'Remarks'
        ]];
    
        // Create the Excel file
        $xlsx = SimpleXLSXGen::fromArray($headers);
    
        // Clear output buffering to prevent file corruption
        if (ob_get_length()) {
            ob_end_clean();
        }
    
        // Set proper headers manually
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $userName . ' - Template.xlsx"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');
    
        // Stream the file
        $xlsx->downloadAs($userName . ' - Template.xlsx');
        exit;  // Ensure no further output occurs
    }
}
