<?php

namespace humhub\modules\domainmigrationnotice\controllers;

use humhub\modules\domainmigrationnotice\models\Configuration;
use humhub\modules\domainmigrationnotice\models\SettingsForm;
use humhub\modules\domainmigrationnotice\services\FrequencyPolicy;
use humhub\modules\domainmigrationnotice\services\NoticeState;
use Yii;
use yii\filters\VerbFilter;

/**
 * Uses HumHub's administration controller, so the settings page follows the
 * same permission rules as other module configuration pages.
 */
class AdminController extends \humhub\modules\admin\components\Controller
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['reset-user-state' => ['POST']],
            ],
        ]);
    }

    public function actionIndex()
    {
        $configuration = Configuration::get();
        if ($configuration === null) {
            throw new \yii\web\ServerErrorHttpException(Yii::t('DomainmigrationnoticeModule.base', 'The module configuration is missing. Re-enable the module so that its migration can run.'));
        }

        $form = new SettingsForm($configuration);
        if ($form->load(Yii::$app->request->post()) && $form->save($configuration)) {
            Yii::$app->session->setFlash('success', Yii::t('DomainmigrationnoticeModule.base', 'Domain migration notice settings saved.'));
            return $this->redirect(['index']);
        }

        return $this->render('index', ['formModel' => $form, 'configuration' => $configuration]);
    }

    public function actionPreview()
    {
        $configuration = Configuration::get();
        if ($configuration === null) {
            throw new \yii\web\ServerErrorHttpException(Yii::t('DomainmigrationnoticeModule.base', 'The module configuration is missing.'));
        }

        // The preview mirrors the current deadline stage without requiring an
        // old host. It must therefore also show the post-deadline layout.
        $blocked = $configuration->deadline_at !== null
            && FrequencyPolicy::stage((int)$configuration->deadline_at, time()) === FrequencyPolicy::BLOCKED;

        return $this->render('preview', ['configuration' => $configuration, 'blocked' => $blocked]);
    }

    /**
     * Lets an administrator make the next notice eligible for every signed-in
     * person again, without changing the migration configuration itself.
     */
    public function actionResetUserState()
    {
        try {
            $count = NoticeState::resetAllAccountDisplayState();
            Yii::$app->session->setFlash('success', Yii::t(
                'DomainmigrationnoticeModule.base',
                '{count} account notice state(s) reset. Guest browser cookies cannot be reset centrally.',
                ['count' => $count]
            ));
        } catch (\Throwable $exception) {
            Yii::error($exception, 'domainmigrationnotice');
            Yii::$app->session->setFlash('error', Yii::t(
                'DomainmigrationnoticeModule.base',
                'Account notice states could not be reset. Check the server log and try again.'
            ));
        }

        return $this->redirect(['index']);
    }
}
