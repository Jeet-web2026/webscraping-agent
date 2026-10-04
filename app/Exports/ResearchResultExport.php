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
            $result = json_decode($result, true);
        }

        if (! is_array($result)) {
            return [];
        }

        $items = $result['result'] ?? $result;

        return collect($items)
            ->filter(fn($item) => is_array($item))
            ->values()
            ->map(fn(array $item, int $i) => [
                $i + 1,                         // Number of Records
                $item['type'] ?? null,           // Brand Name
                $item['thumbnail'] ?? null,     // Recent Photo
                null,                           // Product Video
                $item['price'] ?? null,         // Product Rate
                $item['reviews'] ?? null,       // Feedback
                $item['title'] ?? null,         // Seller Name
                $item['address'] ?? null,       // Seller Address
                $item['phone'] ?? null,         // Seller Contact
                $item['website'] ?? null,       // Website
                $item['rating'] ?? null,        // Seller Rating
            ])
            ->all();
    }

    public function headings(): array
    {
        return [
            'Number of Records',
            'Brand Name',
            'Recent Photo',
            'Product Video',
            'Product Rate',
            'Feedback',
            'Seller Name',
            'Seller Address',
            'Seller Contact',
            'Website',
            'Seller Rating',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
