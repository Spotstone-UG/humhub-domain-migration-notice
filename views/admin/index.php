<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\widgets\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var humhub\modules\domainmigrationnotice\models\SettingsForm $formModel */
/** @var humhub\modules\domainmigrationnotice\models\Configuration $configuration */

$t = static fn(string $message): string => Yii::t('DomainmigrationnoticeModule.base', $message);
$this->title = $t('Domain Migration Notice');
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><?= Html::encode($t('Domain Migration Notice')) ?></strong> <?= Html::encode($t('configuration')) ?></div>
    <div class="panel-body">
        <p class="help-block"><?= Html::encode($t('The notice is shown only on hosts different from the destination host. HTTP and HTTPS are treated as the same host; subdomains are not.')) ?></p>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($formModel, 'enabled')->checkbox() ?>
        <?= $form->field($formModel, 'targetUrl')->textInput(['type' => 'url', 'placeholder' => 'https://community.example.org']) ?>
        <?= $form->field($formModel, 'deadlineLocal')->input('datetime-local') ?>
        <?= $form->field($formModel, 'heading')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'message')->widget(RichTextField::class, ['exclude' => ['oembed', 'upload']]) ?>

        <hr>
        <h4><?= Html::encode($t('Visible frontend text and options')) ?></h4>
        <?= $form->field($formModel, 'showCountdown')->checkbox() ?>
        <?= $form->field($formModel, 'countdownLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'countdownFormat')->textInput(['maxlength' => true]) ?>
        <p class="help-block"><?= Html::encode($t('Use {days}, {hours}, and {minutes} as placeholders in the countdown format.')) ?></p>
        <?= $form->field($formModel, 'deadlineReachedLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'destinationLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'dismissLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'enableWeeklyDismissal')->checkbox() ?>
        <?= $form->field($formModel, 'weeklyDismissLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'showGuestCookieNotice')->checkbox() ?>
        <?= $form->field($formModel, 'guestCookieNoticeText')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'showFrontendAttribution')->checkbox() ?>
        <?= $form->field($formModel, 'customCss')->textarea(['rows' => 9, 'spellcheck' => 'false']) ?>
        <p class="help-block"><?= Html::encode($t('Custom CSS is for trusted administrators. It is added only while this module renders a notice. HTML tags are not accepted.')) ?></p>

        <p class="text-muted small">
            <?= Html::encode($t('Created by')) ?> <a href="https://github.com/ingofleckenstein" target="_blank" rel="noopener">Ingo Fleckenstein</a>
            <?= Html::encode($t('with')) ?> <a href="https://github.com/Spotstone-UG" target="_blank" rel="noopener">Spotstone UG</a>.
        </p>
        <div class="form-group">
            <?= Html::submitButton($t('Save settings'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a($t('Preview notice'), Url::to(['/domainmigrationnotice/admin/preview']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
