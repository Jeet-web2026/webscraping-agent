<?php

namespace App\Jobs;

use App\Models\ResearchRequest;
use App\Services\Products\ResearchExportService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateResearchExportJob implements ShouldQueue
{
    use Queueable, Batchable;

    public function __construct(public int $researchRequestId) {}

    public function handle(ResearchExportService $exports): void
    {
        $record = ResearchRequest::with('productDetails')->findOrFail($this->researchRequestId);

        if ($record->status !== 'completed') {
            Log::warning('Export skipped, request not completed', ['id' => $record->id, 'status' => $record->status]);
            return;
        }

        $path = $exports->generate($record);
    }
}
