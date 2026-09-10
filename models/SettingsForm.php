<?php

namespace humhub\modules\domainmigrationnotice\models;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use yii\base\Model;

/**
 * Keeps validation and presentation-specific values outside the persistence model.
 */
class SettingsForm extends Model
{
    public bool $enabled = false;
    public string $targetUrl = '';
    public string $deadlineLocal = '';
    public string $heading = '';
    public string $message = '';
    public bool $showCountdown = true;
    public string $countdownLabel = '';
    public string $countdownFormat = '';
    public string $deadlineReachedLabel = '';
    public string $destinationLabel = '';
    public string $dismissLabel = '';
    public string $weeklyDismissLabel = '';
    public bool $enableWeeklyDismissal = true;
    public bool $showGuestCookieNotice = true;
    public string $guestCookieNoticeText = '';
    public bool $showFrontendAttribution = true;
    public string $customCss = '';

    private ?int $deadlineTimestamp = null;

    public function __construct(?Configuration $configuration = null, $config = [])
    {
        parent::__construct($config);

        if ($configuration !== null) {
            $this->loadConfiguration($configuration);
        }
    }

    public function rules(): array
    {
        return [
            [['enabled', 'showCountdown', 'enableWeeklyDismissal', 'showGuestCookieNotice', 'showFrontendAttribution'], 'boolean'],
            [['targetUrl', 'deadlineLocal', 'heading', 'message', 'countdownLabel', 'countdownFormat', 'deadlineReachedLabel', 'destinationLabel', 'dismissLabel', 'weeklyDismissLabel', 'guestCookieNoticeText', 'customCss'], 'filter', 'filter' => 'trim'],
            [['targetUrl', 'deadlineLocal', 'heading', 'message', 'countdownLabel', 'countdownFormat', 'deadlineReachedLabel', 'destinationLabel', 'dismissLabel', 'weeklyDismissLabel'], 'required'],
            ['targetUrl', 'url', 'defaultScheme' => 'https'],
            ['targetUrl', 'validateHttpUrl'],
            ['deadlineLocal', 'validateDeadline'],
            ['heading', 'string', 'max' => 255],
            [['countdownLabel', 'countdownFormat', 'deadlineReachedLabel', 'destinationLabel', 'dismissLabel', 'weeklyDismissLabel'], 'string', 'max' => 255],
            ['message', 'string', 'max' => 20000],
            ['guestCookieNoticeText', 'string', 'max' => 1000],
            ['customCss', 'string', 'max' => 20000],
            ['customCss', 'validateCss'],
        ];
    }

    public function attributeLabels(): array
    {
        $t = static fn(string $message): string => Yii::t('DomainmigrationnoticeModule.base', $message);

        return [
            'enabled' => $t('Enable migration notice'),
            'targetUrl' => $t('Destination URL'),
            'deadlineLocal' => $t('Deadline'),
            'heading' => $t('Popup heading'),
            'message' => $t('Popup message'),
            'showCountdown' => $t('Show countdown'),
            'countdownLabel' => $t('Countdown label'),
            'countdownFormat' => $t('Countdown format'),
            'deadlineReachedLabel' => $t('Deadline reached label'),
            'destinationLabel' => $t('Destination button label'),
            'dismissLabel' => $t('Dismiss button label'),
            'weeklyDismissLabel' => $t('One-week dismissal label'),
            'enableWeeklyDismissal' => $t('Offer one-week dismissal in the first stage'),
            'showGuestCookieNotice' => $t('Show the guest cookie notice'),
            'guestCookieNoticeText' => $t('Guest cookie notice text'),
            'showFrontendAttribution' => $t('Show the subtle frontend attribution'),
            'customCss' => $t('Custom CSS'),
        ];
    }

    public function validateHttpUrl(string $attribute): void
    {
        $url = $this->$attribute;
        $parts = parse_url($url);
        if (!is_array($parts) || empty($parts['host']) || !in_array(strtolower((string)($parts['scheme'] ?? '')), ['http', 'https'], true)) {
            $this->addError($attribute, Yii::t('DomainmigrationnoticeModule.base', 'Enter a complete HTTP or HTTPS destination URL with a host name.'));
            return;
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            $this->addError($attribute, Yii::t('DomainmigrationnoticeModule.base', 'The destination URL must not contain login details.'));
        }
    }

    public function validateDeadline(string $attribute): void
    {
        $this->deadlineTimestamp = null;
        $timezone = new DateTimeZone(Yii::$app->timeZone);
        $date = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $this->$attribute, $timezone);
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            $this->addError($attribute, Yii::t('DomainmigrationnoticeModule.base', 'Enter a valid local date and time.'));
            return;
        }

        $this->deadlineTimestamp = $date->getTimestamp();
        if ($this->enabled && $this->deadlineTimestamp <= time()) {
            $this->addError($attribute, Yii::t('DomainmigrationnoticeModule.base', 'Choose a deadline in the future before enabling the notice.'));
        }
    }

    public function validateCss(string $attribute): void
    {
        if (str_contains($this->$attribute, '<')) {
            $this->addError($attribute, Yii::t('DomainmigrationnoticeModule.base', 'Custom CSS must not contain HTML tags or angle brackets.'));
        }
    }

    public function save(Configuration $configuration): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $configuration->enabled = $this->enabled;
        $configuration->target_url = trim($this->targetUrl);
        $configuration->deadline_at = $this->deadlineTimestamp;
        $configuration->heading = trim($this->heading);
        $configuration->message = $this->message;
        $configuration->show_countdown = $this->showCountdown;
        $configuration->countdown_label = trim($this->countdownLabel);
        $configuration->countdown_format = trim($this->countdownFormat);
        $configuration->deadline_reached_label = trim($this->deadlineReachedLabel);
        $configuration->destination_label = trim($this->destinationLabel);
        $configuration->dismiss_label = trim($this->dismissLabel);
        $configuration->weekly_dismiss_label = trim($this->weeklyDismissLabel);
        $configuration->enable_weekly_dismissal = $this->enableWeeklyDismissal;
        $configuration->show_guest_cookie_notice = $this->showGuestCookieNotice;
        $configuration->guest_cookie_notice_text = trim($this->guestCookieNoticeText);
        $configuration->show_frontend_attribution = $this->showFrontendAttribution;
        $configuration->custom_css = trim($this->customCss);

        return $configuration->save(false);
    }

    private function loadConfiguration(Configuration $configuration): void
    {
        $this->enabled = (bool)$configuration->enabled;
        $this->targetUrl = (string)$configuration->target_url;
        $this->deadlineLocal = $configuration->deadline_at === null ? '' : (new DateTimeImmutable('@' . $configuration->deadline_at))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))->format('Y-m-d\\TH:i');
        $this->heading = (string)$configuration->heading;
        $this->message = (string)$configuration->message;
        $this->showCountdown = (bool)$configuration->show_countdown;
        $this->countdownLabel = (string)$configuration->countdown_label;
        $this->countdownFormat = (string)$configuration->countdown_format;
        $this->deadlineReachedLabel = (string)$configuration->deadline_reached_label;
        $this->destinationLabel = (string)$configuration->destination_label;
        $this->dismissLabel = (string)$configuration->dismiss_label;
        $this->weeklyDismissLabel = (string)$configuration->weekly_dismiss_label;
        $this->enableWeeklyDismissal = (bool)$configuration->enable_weekly_dismissal;
        $this->showGuestCookieNotice = (bool)$configuration->show_guest_cookie_notice;
        $this->guestCookieNoticeText = (string)$configuration->guest_cookie_notice_text;
        $this->showFrontendAttribution = (bool)$configuration->show_frontend_attribution;
        $this->customCss = (string)$configuration->custom_css;
    }
}
