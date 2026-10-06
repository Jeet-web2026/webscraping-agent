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

        return collect($result['result'] ?? $result)
            ->filter(fn($item) => is_array($item))
            ->values()
            ->map(fn(array $item, int $i) => isset($item['asin'])
                ? $this->mapEcommerceItem($item, $i)
                : $this->mapSearchItem($item, $i))
            ->all();
    }

    private function mapSearchItem(array $item, int $i): array
    {
        return [
            $i + 1,                         // Number of Records
            $item['type'] ?? null,          // Brand Name
            $item['thumbnail'] ?? null,     // Recent Photo
            null,                           // Product Video
            $item['price'] ?? null,         // Product Rate
            $item['reviews'] ?? null,       // Feedback
            $item['title'] ?? null,         // Seller Name
            $item['address'] ?? null,       // Seller Address
            $item['phone'] ?? null,         // Seller Contact
            $item['website'] ?? null,       // Website
            $item['rating'] ?? null,        // Seller Rating
        ];
    }

    private function mapEcommerceItem(array $item, int $i): array
    {
        return [
            $i + 1,                                  // Number of Records
            $item['title'],                                    // Brand Name: not in the response
            $item['imageUrl'] ?? null,               // Recent Photo
            $item['video'] ?? null,                                  // Product Video: not in the response
            data_get($item, 'price.amount'),         // Product Rate
            $item['reviewCount'] ?? null,            // Feedback
            $item['seller_name'] ?? null,            // Seller Name: not in the response
            $item['address'] ?? null,                // Seller Address: not in the response
            $item['contact_details'] ?? null,                  // Seller Contact: not in the response
            $item['url'] ?? null,                    // Website (Amazon product page)
            data_get($item, 'rating.value'),         // Seller Rating (this is the product rating)
        ];
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
