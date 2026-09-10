<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\domainmigrationnotice\services\FrequencyPolicy;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var humhub\modules\domainmigrationnotice\models\Configuration $configuration */
/** @var bool $blocked */
/** @var bool $preview */
/** @var string $stage */
/** @var string $csrfToken */

$isGuest = Yii::$app->user->isGuest;
$canSnooze = !$blocked && $stage === FrequencyPolicy::DAILY && (bool)$configuration->enable_weekly_dismissal;
$classes = ['dmn-notice'];
if ($blocked) {
    $classes[] = 'dmn-notice--blocked';
}
if ($preview) {
    $classes[] = 'dmn-notice--preview';
}
?>
<section class="<?= Html::encode(implode(' ', $classes)) ?>"
    role="dialog"
    aria-modal="true"
    aria-labelledby="dmn-notice-heading"
    aria-describedby="dmn-notice-message"
    data-dmn-notice
    data-preview="<?= $preview ? '1' : '0' ?>"
    data-blocked="<?= $blocked ? '1' : '0' ?>"
    data-deadline="<?= (int)$configuration->deadline_at ?>"
    data-countdown-format="<?= Html::encode($configuration->countdown_format) ?>"
    data-deadline-reached-label="<?= Html::encode($configuration->deadline_reached_label) ?>"
    data-seen-url="<?= Html::encode(Url::to(['/domainmigrationnotice/notice/seen'])) ?>"
    data-dismiss-week-url="<?= Html::encode(Url::to(['/domainmigrationnotice/notice/dismiss-week'])) ?>"
    data-csrf-token="<?= Html::encode($csrfToken) ?>">
    <div class="dmn-notice__backdrop"></div>
    <div class="dmn-notice__card" role="document">
        <?php if (!$blocked): ?>
            <button class="dmn-notice__close" type="button" data-dmn-close aria-label="<?= Html::encode($configuration->dismiss_label) ?>">×</button>
        <?php endif; ?>
        <h1 id="dmn-notice-heading" class="dmn-notice__heading"><?= Html::encode($configuration->heading) ?></h1>
        <div id="dmn-notice-message" class="dmn-notice__message richtext">
            <?= RichText::output($configuration->message) ?>
        </div>
        <?php if ($configuration->show_countdown): ?>
            <div class="dmn-notice__countdown" aria-live="polite">
                <span class="dmn-notice__countdown-label"><?= Html::encode($configuration->countdown_label) ?></span>
                <strong data-dmn-countdown>—</strong>
            </div>
        <?php endif; ?>
        <?php if ($isGuest && !$blocked && !$preview && $configuration->show_guest_cookie_notice): ?>
            <p class="dmn-notice__cookie-notice"><?= Html::encode($configuration->guest_cookie_notice_text) ?></p>
        <?php endif; ?>
        <div class="dmn-notice__actions">
            <?= Html::a(Html::encode($configuration->destination_label), $configuration->target_url, ['class' => 'btn btn-primary dmn-notice__destination']) ?>
            <?php if (!$blocked): ?>
                <button class="btn btn-default" type="button" data-dmn-close><?= Html::encode($configuration->dismiss_label) ?></button>
            <?php endif; ?>
            <?php if ($canSnooze): ?>
                <button class="btn btn-link dmn-notice__snooze" type="button" data-dmn-snooze><?= Html::encode($configuration->weekly_dismiss_label) ?></button>
            <?php endif; ?>
        </div>
        <?php if ($configuration->show_frontend_attribution): ?>
            <p class="dmn-notice__attribution">With support from <a href="https://selbstsein.events" target="_blank" rel="noopener">selbstsein.events</a> and <a href="https://github.com/Spotstone-UG" target="_blank" rel="noopener">Spotstone UG</a></p>
        <?php endif; ?>
    </div>
</section>
