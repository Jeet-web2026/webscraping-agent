<?php

namespace App\Exports;

use App\Models\ResearchRequest;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResearchResultExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(protected ResearchRequest $researchRequest) {}

    public function array(): array
    {
        $result = $this->researchRequest->result;

        if (is_string($result)) {
            $decoded = json_decode($result, true);
            $result = json_last_error() === JSON_ERROR_NONE ? $decoded : [[$result]];
        }

        return $result;
    }

    public function headings(): array
    {
        return ['Result'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}