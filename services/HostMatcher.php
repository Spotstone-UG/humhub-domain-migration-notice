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
        $host = trim($host);

        // parse_url() may retain brackets for an IPv6 literal while a request
        // host normally does not. Treat both representations identically.
        if (str_starts_with($host, '[') && str_ends_with($host, ']')) {
            $host = substr($host, 1, -1);
        }

        $host = rtrim(strtolower($host), '.');
        if ($host !== '' && function_exists('idn_to_ascii')) {
            $asciiHost = idn_to_ascii($host, IDN_DEFAULT);
            if ($asciiHost !== false) {
                $host = strtolower($asciiHost);
            }
        }

        return $host;
    }
}
