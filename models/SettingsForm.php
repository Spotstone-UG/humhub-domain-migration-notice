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
            [['targetUrl', 'deadlineLocal', 'heading', 'message', 'countdownLabel', 'destinationLabel', 'dismissLabel', 'weeklyDismissLabel'], 'required'],
            ['targetUrl', 'url', 'defaultScheme' => 'https'],
            ['targetUrl', 'validateHttpUrl'],
            ['deadlineLocal', 'validateDeadline'],
            ['heading', 'string', 'max' => 255],
            [['countdownLabel', 'destinationLabel', 'dismissLabel', 'weeklyDismissLabel'], 'string', 'max' => 255],
            ['message', 'string', 'max' => 20000],
            ['guestCookieNoticeText', 'string', 'max' => 1000],
            ['customCss', 'string', 'max' => 20000],
            ['customCss', 'validateCss'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'enabled' => 'Enable migration notice',
            'targetUrl' => 'Destination URL',
            'deadlineLocal' => 'Deadline',
            'heading' => 'Popup heading',
            'message' => 'Popup message',
            'showCountdown' => 'Show countdown',
            'countdownLabel' => 'Countdown label',
            'destinationLabel' => 'Destination button label',
            'dismissLabel' => 'Dismiss button label',
            'weeklyDismissLabel' => 'One-week dismissal label',
            'enableWeeklyDismissal' => 'Offer one-week dismissal in the first stage',
            'showGuestCookieNotice' => 'Show the guest cookie notice',
            'guestCookieNoticeText' => 'Guest cookie notice text',
            'showFrontendAttribution' => 'Show the subtle frontend attribution',
            'customCss' => 'Custom CSS',
        ];
    }

    public function validateHttpUrl(string $attribute): void
    {
        $url = trim($this->$attribute);
        $parts = parse_url($url);
        if (!is_array($parts) || empty($parts['host']) || !in_array(strtolower((string)($parts['scheme'] ?? '')), ['http', 'https'], true)) {
            $this->addError($attribute, 'Enter a complete HTTP or HTTPS destination URL with a host name.');
        }
    }

    public function validateDeadline(string $attribute): void
    {
        $timezone = new DateTimeZone(Yii::$app->timeZone);
        $date = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $this->$attribute, $timezone);
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            $this->addError($attribute, 'Enter a valid local date and time.');
            return;
        }

        $this->deadlineTimestamp = $date->getTimestamp();
    }

    public function validateCss(string $attribute): void
    {
        if (str_contains($this->$attribute, '<')) {
            $this->addError($attribute, 'Custom CSS must not contain HTML tags or angle brackets.');
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
