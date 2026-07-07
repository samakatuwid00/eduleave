<?php

namespace App\Console\Commands;

use App\Models\NonTeachingLeaveCard;
use App\Models\TeachingLeaveCard;
use App\Services\LeaveCardNormalizer;
use Illuminate\Console\Command;

class NormalizeLeaveCards extends Command
{
    protected $signature = 'leave-cards:normalize {--dry-run : Report results without saving changes}';

    protected $description = 'Populate canonical analytics fields from existing leave-card values';

    public function handle(LeaveCardNormalizer $normalizer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $counts = [
            LeaveCardNormalizer::PARSED => 0,
            LeaveCardNormalizer::PARTIAL => 0,
            LeaveCardNormalizer::UNPARSEABLE => 0,
            LeaveCardNormalizer::NOT_APPLICABLE => 0,
        ];

        $this->normalizeModel(
            TeachingLeaveCard::class,
            fn (TeachingLeaveCard $card) => $normalizer->teaching($card->only([
                'inclusive_period',
                'nature_of_leave',
            ])),
            $counts,
            $dryRun,
        );

        $this->normalizeModel(
            NonTeachingLeaveCard::class,
            fn (NonTeachingLeaveCard $card) => $normalizer->nonTeaching($card->only([
                'period',
                'particulars',
                'vacation_leave_with_pay',
                'vacation_leave_balance',
                'sick_leave_balance',
                'sick_leave_without_pay',
                'leave_application_action',
            ])),
            $counts,
            $dryRun,
        );

        $this->table(['State', 'Rows'], collect($counts)->map(
            fn (int $count, string $state) => [$state, $count],
        )->values()->all());
        $this->info($dryRun ? 'Dry run complete; no records were changed.' : 'Leave-card normalization complete.');

        return self::SUCCESS;
    }

    /**
     * @param  class-string<TeachingLeaveCard|NonTeachingLeaveCard>  $model
     * @param  callable(TeachingLeaveCard|NonTeachingLeaveCard): array<string, mixed>  $normalize
     * @param  array<string, int>  $counts
     */
    private function normalizeModel(string $model, callable $normalize, array &$counts, bool $dryRun): void
    {
        $model::query()->chunkById(200, function ($cards) use ($normalize, &$counts, $dryRun) {
            foreach ($cards as $card) {
                $attributes = $normalize($card);
                $state = $attributes['parse_state'];
                $counts[$state]++;

                if (! $dryRun) {
                    $card->forceFill($attributes)->saveQuietly();
                }
            }
        });
    }
}
