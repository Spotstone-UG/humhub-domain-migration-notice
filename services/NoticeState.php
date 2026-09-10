<?php

namespace humhub\modules\domainmigrationnotice\services;

use humhub\modules\domainmigrationnotice\models\Configuration;
use Yii;
use yii\web\Cookie;

/**
 * Decides whether the notice applies and stores per-account or per-browser
 * frequency state. Guest state intentionally never attempts cross-device tracking.
 */
class NoticeState
{
    public const COOKIE_NEXT_DISPLAY = 'dmn_next_display_at';
    public const COOKIE_SNOOZED_UNTIL = 'dmn_snoozed_until';

    public function configuration(): ?Configuration
    {
        return Configuration::get();
    }

    public function applies(?Configuration $configuration = null): bool
    {
        $configuration ??= $this->configuration();
        if ($configuration === null || !$configuration->enabled || !$configuration->deadline_at || $configuration->target_url === '') {
            return false;
        }

        return !HostMatcher::matches(Yii::$app->request->hostName, $configuration->target_url);
    }

    public function isBlocked(?Configuration $configuration = null): bool
    {
        $configuration ??= $this->configuration();
        return $this->applies($configuration) && time() >= (int)$configuration->deadline_at;
    }

    public function stage(Configuration $configuration): string
    {
        return FrequencyPolicy::stage((int)$configuration->deadline_at, time());
    }

    public function shouldDisplay(Configuration $configuration): bool
    {
        $stage = $this->stage($configuration);
        if ($stage === FrequencyPolicy::BLOCKED || $stage === FrequencyPolicy::EVERY_LOAD) {
            return true;
        }

        return $this->nextDisplayAt() <= time() && $this->snoozedUntil() <= time();
    }

    public function recordDisplay(Configuration $configuration): void
    {
        $stage = $this->stage($configuration);
        if ($stage === FrequencyPolicy::EVERY_LOAD || $stage === FrequencyPolicy::BLOCKED) {
            return;
        }

        $this->writeNextDisplayAt(FrequencyPolicy::nextDisplayAt($stage, time()));
    }

    public function snoozeForWeek(): void
    {
        $this->writeSnoozedUntil(time() + 7 * 86400);
    }

    private function nextDisplayAt(): int
    {
        return $this->readValue('nextDisplayAt', self::COOKIE_NEXT_DISPLAY);
    }

    private function snoozedUntil(): int
    {
        return $this->readValue('snoozedUntil', self::COOKIE_SNOOZED_UNTIL);
    }

    private function readValue(string $settingName, string $cookieName): int
    {
        if (!Yii::$app->user->isGuest) {
            $settings = Yii::$app->getModule('domainmigrationnotice')->settings->user();
            return $settings === null ? 0 : (int)$settings->get($settingName, 0);
        }

        return (int)Yii::$app->request->cookies->getValue($cookieName, 0);
    }

    private function writeNextDisplayAt(int $timestamp): void
    {
        $this->writeValue('nextDisplayAt', self::COOKIE_NEXT_DISPLAY, $timestamp);
    }

    private function writeSnoozedUntil(int $timestamp): void
    {
        $this->writeValue('snoozedUntil', self::COOKIE_SNOOZED_UNTIL, $timestamp);
    }

    private function writeValue(string $settingName, string $cookieName, int $timestamp): void
    {
        if (!Yii::$app->user->isGuest) {
            $settings = Yii::$app->getModule('domainmigrationnotice')->settings->user();
            $settings?->set($settingName, $timestamp);
            return;
        }

        Yii::$app->response->cookies->add(new Cookie([
            'name' => $cookieName,
            'value' => (string)$timestamp,
            'expire' => $timestamp,
            'httpOnly' => true,
            'secure' => Yii::$app->request->isSecureConnection,
            'sameSite' => 'Lax',
        ]));
    }
}
