<?php

namespace App\Console\Commands;

use App\Services\AutomationService;
use Illuminate\Console\Command;
use InvalidArgumentException;

class RunAutomation extends Command
{
    protected $signature = 'automation:run {rule} {--window=}';

    protected $description = 'Run one idempotent EduLeave automation rule';

    public function handle(AutomationService $automation): int
    {
        try {
            $run = $automation->run(
                (string) $this->argument('rule'),
                $this->option('window') ?: null,
            );
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::INVALID;
        }

        $this->info("Automation run {$run->getKey()}: {$run->status} ({$run->item_count} item(s)).");

        return $run->status === 'failed' ? self::FAILURE : self::SUCCESS;
    }
}
