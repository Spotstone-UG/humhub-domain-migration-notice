<?php

namespace humhub\modules\domainmigrationnotice\widgets;

use humhub\modules\domainmigrationnotice\assets\DomainMigrationNoticeAsset;
use humhub\modules\domainmigrationnotice\models\Configuration;
use humhub\modules\domainmigrationnotice\services\NoticeState;
use Yii;
use yii\base\Widget;

class NoticeWidget extends Widget
{
    public Configuration $configuration;
    public bool $blocked = false;
    public bool $preview = false;

    public function run(): string
    {
        DomainMigrationNoticeAsset::register($this->view);
        if ($this->configuration->custom_css !== '') {
            $this->view->registerCss($this->configuration->custom_css, [], 'domainmigrationnotice-custom-css');
        }

        return $this->render('notice', [
            'configuration' => $this->configuration,
            'blocked' => $this->blocked,
            'preview' => $this->preview,
            'stage' => (new NoticeState())->stage($this->configuration),
            'csrfToken' => Yii::$app->request->csrfToken,
        ]);
    }
}
