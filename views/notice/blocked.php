<?php

use humhub\modules\domainmigrationnotice\widgets\NoticeWidget;

/** @var humhub\modules\domainmigrationnotice\models\Configuration $configuration */

echo NoticeWidget::widget(['configuration' => $configuration, 'blocked' => true]);
