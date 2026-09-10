<?php

use humhub\modules\domainmigrationnotice\widgets\NoticeWidget;

/** @var humhub\modules\domainmigrationnotice\models\Configuration $configuration */

$this->title = 'Preview domain migration notice';
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong>Preview</strong> domain migration notice</div>
    <div class="panel-body">
        <p class="help-block">This preview does not store display history and does not change the active domain rule.</p>
        <?= NoticeWidget::widget(['configuration' => $configuration, 'preview' => true]) ?>
    </div>
</div>
