<?php

namespace App\Jobs;

use App\Ai\Agents\DocFormatterAgent;
use App\Models\ResearchRequest;
use App\Services\ModelFailoverManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Throwable;

class DocBuilderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected int $requestId) {}

    public function handle(ModelFailoverManager $failover): void
    {
        $record = ResearchRequest::findOrFail($this->requestId);
        $record->update(['status' => 'doc_building']);

        $formatted = $this->format($record, $failover);

        $tp = new TemplateProcessor(storage_path('templates/research-report-template.docx'));
        $tp->setValue('title', $formatted['title']);
        $tp->setValue('summary', $formatted['summary']);

        if (!empty($formatted['sections'])) {
            $tp->cloneRowAndSetValues('section_heading', collect($formatted['sections'])->map(fn($s) => [
                'section_heading' => $s['heading'],
                'section_body' => $s['body'],
            ])->toArray());
        }

        $filename = "research-{$record->id}.docx";
        $localPath = storage_path("app/generated/{$filename}");
        File::ensureDirectoryExists(storage_path('app/generated'));
        $tp->saveAs($localPath);

        $documentsPath = "generated/{$filename}";
        Storage::disk('documents')->put($documentsPath, file_get_contents($localPath));

        $record->update([
            'status' => 'done',
            'generated_file_path' => $documentsPath,
        ]);
    }

    protected function format(ResearchRequest $record, ModelFailoverManager $failover)
    {
        $candidates = $failover->availableCandidates('research'); // reuse same chain

        foreach ($candidates as $candidate) {
            try {
                $response = (new DocFormatterAgent)->prompt(
                    json_encode($record->result),
                    provider: $candidate['provider'],
                    model: $candidate['model'],
                );
                return $response;
            } catch (Throwable $e) {
                continue;
            }
        }

        return [
            'title' => $record->subject,
            'summary' => $record->result['summary'] ?? '',
            'sections' => [],
        ];
    }
}
