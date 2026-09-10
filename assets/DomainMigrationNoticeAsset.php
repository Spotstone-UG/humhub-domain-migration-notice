<?php

namespace humhub\modules\domainmigrationnotice\assets;

use yii\web\AssetBundle;
use yii\web\View;

class DomainMigrationNoticeAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../resources';
    public $css = ['domain-migration-notice.css'];
    public $js = ['domain-migration-notice.js'];
    public $jsOptions = ['position' => View::POS_END];
}
