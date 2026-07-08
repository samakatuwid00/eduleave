<?php

namespace App\Services;

use DateTimeImmutable;
use Shuchkin\SimpleXLSXGen;

class ReportExcelService
{
    public function generate(array $report): string
    {
        $metadata = [
            ['<b>EduLeave Report</b>', SimpleXLSXGen::raw($report['label'])],
            ['Generated', $report['generated_at']->format('Y-m-d H:i:s T')],
            ['Definition', SimpleXLSXGen::raw($report['definition'])],
            ['Matched rows', $report['row_count']],
            ['Excluded data rows', $report['excluded_count']],
            ['Rows omitted by synchronous limit', $report['truncated_count']],
        ];

        foreach ($report['filters_applied'] as $key => $value) {
            $metadata[] = ['Filter: '.str_replace('_', ' ', ucfirst($key)), SimpleXLSXGen::raw((string) $value)];
        }
        foreach ($report['totals'] as $label => $value) {
            $metadata[] = ['Total: '.$label, is_numeric($value) ? $value + 0 : SimpleXLSXGen::raw((string) $value)];
        }

        $headers = collect($report['columns'])
            ->map(fn (array $column) => '<b>'.$column['label'].'</b>')
            ->all();
        $data = [$headers];

        foreach ($report['rows'] as $row) {
            $data[] = collect($report['columns'])->map(
                fn (array $column) => $this->cell($row[$column['key']] ?? null, $column['type']),
            )->all();
        }

        if ($report['rows']->isEmpty()) {
            $data[] = [SimpleXLSXGen::raw('No records matched.')];
        }

        $lastColumn = $this->columnLetter(max(1, count($headers)));
        $xlsx = SimpleXLSXGen::fromArray($metadata, 'Metadata')
            ->setTitle($report['label'])
            ->setSubject($report['definition'])
            ->setAuthor('EduLeave')
            ->setCompany('EduLeave');
        $xlsx->setColWidth('A', 28)->setColWidth('B', 70);
        $xlsx->addSheet($data, 'Data')
            ->freezePanes('A2')
            ->autoFilter("A1:{$lastColumn}".count($data));

        for ($column = 1; $column <= count($headers); $column++) {
            $xlsx->setColWidth($column, 20);
        }

        return (string) $xlsx;
    }

    private function cell(mixed $value, string $type): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($type === 'number' && is_numeric($value)) {
            return $value + 0;
        }

        if ($type === 'date') {
            return new DateTimeImmutable((string) $value);
        }

        return SimpleXLSXGen::raw((string) $value);
    }

    private function columnLetter(int $number): string
    {
        $letters = '';

        while ($number > 0) {
            $number--;
            $letters = chr(65 + ($number % 26)).$letters;
            $number = intdiv($number, 26);
        }

        return $letters;
    }
}
