<?php

namespace App\Console\Commands;

use App\Models\ImportBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupImportPreviews extends Command
{
    protected $signature = 'imports:cleanup';

    protected $description = 'Expire import previews and remove their staged workbooks';

    public function handle(): int
    {
        $expired = 0;

        ImportBatch::query()
            ->where('status', 'validated')
            ->where('expires_at', '<=', now())
            ->chunkById(100, function ($batches) use (&$expired) {
                foreach ($batches as $batch) {
                    Storage::disk('local')->deleteDirectory("import-previews/{$batch->getKey()}");
                    $batch->update([
                        'status' => 'expired',
                        'stored_path' => null,
                        'preview_data' => null,
                    ]);
                    $expired++;
                }
            });

        $this->info("Expired {$expired} import preview(s).");

        return self::SUCCESS;
    }
}
