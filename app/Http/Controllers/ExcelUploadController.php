<?php

namespace App\Http\Controllers;

use App\Services\AutomationService;
use App\Services\LeaveCardImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Shuchkin\SimpleXLSXGen;
use Symfony\Component\HttpFoundation\HeaderUtils;

class ExcelUploadController extends Controller
{
    public function __construct(
        private readonly LeaveCardImportService $imports,
        private readonly AutomationService $automation,
    ) {}

    public function upload(Request $request, string $cardType, string $employeeNumber): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'extensions:xlsx', 'max:10240'],
        ]);
        $profile = $this->imports->profileForCardType($employeeNumber, $cardType);
        $batch = $this->imports->importImmediately(
            $request->file('excel_file'),
            $profile,
            $request->user(),
        );
        $this->automation->notifyEmployeeChange(
            $profile,
            $batch->row_count.' leave-card row(s) were imported by an administrator.',
        );

        return back()->with('success', $batch->row_count.' leave-card row(s) imported successfully.');
    }

    public function downloadTemplate(string $cardType, string $employeeNumber): Response
    {
        $profile = $this->imports->profileForCardType($employeeNumber, $cardType);
        $xlsx = SimpleXLSXGen::fromArray([$this->imports->headersFor($cardType)]);
        $filename = "{$profile->user->name} - {$profile->personnelType->name} Leave Card Template.xlsx";

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $filename,
                Str::ascii($filename),
            ),
        ]);
    }
}
