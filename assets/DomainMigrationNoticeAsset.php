<?php

namespace humhub\modules\domainmigrationnotice\assets;

use yii\web\AssetBundle;

class DomainMigrationNoticeAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../resources';
    public $css = ['domain-migration-notice.css'];
    public $js = ['domain-migration-notice.js'];
}
