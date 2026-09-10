<?php

namespace humhub\modules\domainmigrationnotice\services;

/**
 * Host comparison deliberately ignores URL schemes, paths and trailing dots.
 * A subdomain remains distinct from its parent domain.
 */
final class HostMatcher
{
    public static function targetHost(string $url): ?string
    {
        $parts = parse_url($url);
        return is_array($parts) && isset($parts['host']) ? self::normalise((string)$parts['host']) : null;
    }

    public static function matches(string $currentHost, string $targetUrl): bool
    {
        $targetHost = self::targetHost($targetUrl);
        return $targetHost !== null && self::normalise($currentHost) === $targetHost;
    }

    public static function normalise(string $host): string
    {
        return rtrim(strtolower(trim($host)), '.');
    }
}
