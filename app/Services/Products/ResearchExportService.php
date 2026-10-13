<?php

namespace App\Services\Products;

use App\Exports\ResearchResultExport;
use App\Models\ResearchRequest;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ResearchExportService
{
    public function fileName(ResearchRequest $r): string
    {
        $product  = $r->product_service;
        $category = $r->category_name;

        $clean = fn($v) => trim(preg_replace('/[^A-Za-z0-9]+/', '_', (string) $v), '_');

        $parts = array_filter([
            $r->created_at->format('Y-m-d_H-i-s'),
            $clean($product),
            $clean($category),
        ]);

        return Str::limit(implode('_', $parts), 150, '') . '.csv';
    }

    public function path(ResearchRequest $r): string
    {
        return 'research-exports/' . $this->fileName($r);
    }

    public function generate(ResearchRequest $r): string
    {
        $path = $this->path($r);

        Excel::store(new ResearchResultExport($r), $path, 'local', ExcelType::CSV);

        return $path;
    }

    public function exists(ResearchRequest $r): bool
    {
        return Storage::disk('local')->exists($this->path($r));
    }
}
