<?php

require_once __DIR__ . '/../services/HostMatcher.php';
require_once __DIR__ . '/../services/FrequencyPolicy.php';

use humhub\modules\domainmigrationnotice\services\FrequencyPolicy;
use humhub\modules\domainmigrationnotice\services\HostMatcher;

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . ' Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

assertSameValue(true, HostMatcher::matches('old.example.org', 'https://old.example.org/new'), 'The same host must match.');
assertSameValue(true, HostMatcher::matches('OLD.EXAMPLE.ORG.', 'http://old.example.org'), 'Schemes, case and trailing dots must be ignored.');
assertSameValue(false, HostMatcher::matches('community.example.org', 'https://example.org'), 'A subdomain must remain distinct.');
assertSameValue(null, HostMatcher::targetHost('not a URL'), 'An invalid URL must not produce a host.');
assertSameValue('2001:db8::1', HostMatcher::normalise('[2001:DB8::1]'), 'IPv6 host notation must be normalised.');
assertSameValue(FrequencyPolicy::DAILY, FrequencyPolicy::stage(10 * 86400, 0), 'More than seven days must be daily.');
assertSameValue(FrequencyPolicy::HOURLY, FrequencyPolicy::stage(7 * 86400, 0), 'Seven days must be hourly.');
assertSameValue(FrequencyPolicy::EVERY_LOAD, FrequencyPolicy::stage(3 * 86400, 0), 'Three days must display on every load.');
assertSameValue(FrequencyPolicy::BLOCKED, FrequencyPolicy::stage(100, 100), 'The deadline must block content.');
assertSameValue(86400, FrequencyPolicy::nextDisplayAt(FrequencyPolicy::DAILY, 0), 'Daily frequency must wait one day.');
assertSameValue(3600, FrequencyPolicy::nextDisplayAt(FrequencyPolicy::HOURLY, 0), 'Hourly frequency must wait one hour.');

echo "All isolated checks passed.\n";
