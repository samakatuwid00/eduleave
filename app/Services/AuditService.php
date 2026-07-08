<?php

namespace App\Services;

use App\Models\AuditEvent;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class AuditService
{
    private ?string $correlationId = null;

    public function record(
        string $action,
        string $targetType,
        string|int|null $targetId,
        ?string $targetLabel = null,
        array $before = [],
        array $after = [],
        ?string $reason = null,
        array $metadata = [],
        ?string $employeeNumber = null,
        User|Authenticatable|null $actor = null,
        ?string $source = null,
    ): AuditEvent {
        $actor ??= auth()->user();

        return AuditEvent::query()->create([
            'actor_user_id' => $actor?->getAuthIdentifier(),
            'actor_label' => $actor instanceof User ? Str::limit($actor->name, 255, '') : null,
            'action' => Str::limit($action, 80, ''),
            'target_type' => Str::limit($targetType, 100, ''),
            'target_id' => $targetId === null ? null : Str::limit((string) $targetId, 100, ''),
            'target_label' => $targetLabel ? Str::limit($targetLabel, 255, '') : null,
            'employee_number' => $employeeNumber ? Str::limit($employeeNumber, 255, '') : null,
            'correlation_id' => $this->correlationId(),
            'source' => $source ?? ($this->isWebRequest() ? 'web' : 'console'),
            'reason' => $reason ? Str::limit(trim($reason), 500, '') : null,
            'previous_values' => $before === [] ? null : $this->redact($before),
            'new_values' => $after === [] ? null : $this->redact($after),
            'metadata' => $metadata === [] ? null : $this->redact($metadata),
        ]);
    }

    public function changedValues(array $before, array $after): array
    {
        $before = collect($before)->except(['created_at', 'updated_at'])->all();
        $after = collect($after)->except(['created_at', 'updated_at'])->all();
        $changed = collect($after)->filter(
            fn ($value, string $key) => ! array_key_exists($key, $before) || $before[$key] != $value,
        );

        return [
            'before' => $changed->mapWithKeys(fn ($value, string $key) => [$key => $before[$key] ?? null])->all(),
            'after' => $changed->all(),
        ];
    }

    public function correlationId(): string
    {
        if ($this->correlationId) {
            return $this->correlationId;
        }

        $provided = $this->isWebRequest() ? request()->header('X-Request-ID') : null;
        $this->correlationId = is_string($provided) && preg_match('/^[A-Za-z0-9._-]{8,64}$/', $provided)
            ? $provided
            : (string) Str::uuid();

        return $this->correlationId;
    }

    private function isWebRequest(): bool
    {
        return app()->bound('request') && request()->route() !== null;
    }

    private function redact(mixed $value, int $depth = 0): mixed
    {
        if ($depth >= 4) {
            return '[truncated]';
        }

        if (! is_array($value)) {
            return is_string($value) ? Str::limit($value, 1000, '') : $value;
        }

        $safe = [];
        foreach (array_slice($value, 0, 100, true) as $key => $item) {
            $keyText = strtolower((string) $key);
            $safe[$key] = preg_match('/password|remember_token|reset_token|verification_token|secret|stored_path|preview_data/', $keyText)
                ? '[redacted]'
                : $this->redact($item, $depth + 1);
        }

        return $safe;
    }
}
