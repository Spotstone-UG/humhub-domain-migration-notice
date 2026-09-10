<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\widgets\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var humhub\modules\domainmigrationnotice\models\SettingsForm $formModel */
/** @var humhub\modules\domainmigrationnotice\models\Configuration $configuration */

$this->title = 'Domain Migration Notice';
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong>Domain Migration Notice</strong> configuration</div>
    <div class="panel-body">
        <p class="help-block">The notice is shown only on hosts different from the destination host. HTTP and HTTPS are treated as the same host; subdomains are not.</p>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($formModel, 'enabled')->checkbox() ?>
        <?= $form->field($formModel, 'targetUrl')->textInput(['type' => 'url', 'placeholder' => 'https://community.example.org']) ?>
        <?= $form->field($formModel, 'deadlineLocal')->input('datetime-local') ?>
        <?= $form->field($formModel, 'heading')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'message')->widget(RichTextField::class, ['exclude' => ['oembed', 'upload']]) ?>

        <hr>
        <h4>Visible frontend text and options</h4>
        <?= $form->field($formModel, 'showCountdown')->checkbox() ?>
        <?= $form->field($formModel, 'countdownLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'destinationLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'dismissLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'enableWeeklyDismissal')->checkbox() ?>
        <?= $form->field($formModel, 'weeklyDismissLabel')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'showGuestCookieNotice')->checkbox() ?>
        <?= $form->field($formModel, 'guestCookieNoticeText')->textInput(['maxlength' => true]) ?>
        <?= $form->field($formModel, 'showFrontendAttribution')->checkbox() ?>
        <?= $form->field($formModel, 'customCss')->textarea(['rows' => 9, 'spellcheck' => 'false']) ?>
        <p class="help-block">Custom CSS is for trusted administrators. It is added only while this module renders a notice. HTML tags are not accepted.</p>

        <div class="form-group">
            <?= Html::submitButton('Save settings', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Preview notice', Url::to(['/domainmigrationnotice/admin/preview']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="panel-footer text-muted small">
        Created by <a href="https://github.com/ingofleckenstein" target="_blank" rel="noopener">Ingo Fleckenstein</a>
        with <a href="https://github.com/Spotstone-UG" target="_blank" rel="noopener">Spotstone UG</a>.
        Developed with assistance from GPT-5.6 Terra.
    </div>
</div>
