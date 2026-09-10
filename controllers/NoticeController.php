<?php

namespace humhub\modules\domainmigrationnotice\controllers;

use humhub\components\access\ControllerAccess;
use humhub\modules\domainmigrationnotice\services\FrequencyPolicy;
use humhub\modules\domainmigrationnotice\services\NoticeState;
use Yii;

/**
 * Guest-accessible endpoints are deliberately limited to CSRF-protected POST
 * requests that record the notice state for the current account or browser.
 */
class NoticeController extends \humhub\components\Controller
{
    public $access = ControllerAccess::class;
    public $layout = false;

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => ['seen' => ['POST'], 'dismiss-week' => ['POST']],
            ],
        ]);
    }

    public function actionBlocked()
    {
        $state = new NoticeState();
        $configuration = $state->configuration();
        if ($configuration === null || !$state->isBlocked($configuration)) {
            return $this->redirect(['/dashboard/dashboard']);
        }

        return $this->render('blocked', ['configuration' => $configuration]);
    }

    public function actionSeen()
    {
        $state = new NoticeState();
        $configuration = $state->configuration();
        if ($configuration === null || !$state->applies($configuration)) {
            return $this->asJson(['ok' => false, 'message' => Yii::t('DomainmigrationnoticeModule.base', 'The migration notice is not active for this address.')]);
        }

        $state->recordDisplay($configuration);
        return $this->asJson(['ok' => true]);
    }

    public function actionDismissWeek()
    {
        $state = new NoticeState();
        $configuration = $state->configuration();
        if ($configuration === null || !$state->applies($configuration)) {
            return $this->asJson(['ok' => false, 'message' => Yii::t('DomainmigrationnoticeModule.base', 'The migration notice is not active for this address.')]);
        }

        if ($state->stage($configuration) !== FrequencyPolicy::DAILY || !$configuration->enable_weekly_dismissal) {
            return $this->asJson(['ok' => false, 'message' => Yii::t('DomainmigrationnoticeModule.base', 'A one-week dismissal is not available at this time.')]);
        }

        $state->snoozeForWeek();
        return $this->asJson(['ok' => true]);
    }
}
