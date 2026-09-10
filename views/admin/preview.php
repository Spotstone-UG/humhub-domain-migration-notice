<?php

use humhub\modules\domainmigrationnotice\widgets\NoticeWidget;

/** @var humhub\modules\domainmigrationnotice\models\Configuration $configuration */
/** @var bool $blocked */

$t = static fn(string $message): string => Yii::t('DomainmigrationnoticeModule.base', $message);
$this->title = $t('Preview domain migration notice');
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><?= \yii\helpers\Html::encode($t('Preview')) ?></strong> <?= \yii\helpers\Html::encode($t('domain migration notice')) ?></div>
    <div class="panel-body">
        <p class="help-block"><?= \yii\helpers\Html::encode($t('This preview does not store display history and does not change the active domain rule.')) ?></p>
        <?= NoticeWidget::widget(['configuration' => $configuration, 'preview' => true, 'blocked' => $blocked]) ?>
    </div>
</div>
