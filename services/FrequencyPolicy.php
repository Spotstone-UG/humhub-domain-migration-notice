<?php

namespace humhub\modules\domainmigrationnotice\services;

/**
 * Encapsulates the three requested pre-deadline notice stages.
 */
final class FrequencyPolicy
{
    public const DAILY = 'daily';
    public const HOURLY = 'hourly';
    public const EVERY_LOAD = 'every-load';
    public const BLOCKED = 'blocked';

    public static function stage(int $deadlineAt, int $now): string
    {
        if ($now >= $deadlineAt) {
            return self::BLOCKED;
        }

        $remaining = $deadlineAt - $now;
        if ($remaining <= 3 * 86400) {
            return self::EVERY_LOAD;
        }

        return $remaining <= 7 * 86400 ? self::HOURLY : self::DAILY;
    }

    public static function nextDisplayAt(string $stage, int $now): int
    {
        return match ($stage) {
            self::DAILY => $now + 86400,
            self::HOURLY => $now + 3600,
            default => $now,
        };
    }
}
