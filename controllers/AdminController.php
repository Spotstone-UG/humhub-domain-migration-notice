<?php

namespace humhub\modules\domainmigrationnotice\controllers;

use humhub\modules\domainmigrationnotice\models\Configuration;
use humhub\modules\domainmigrationnotice\models\SettingsForm;
use Yii;

/**
 * Uses HumHub's administration controller, so the settings page follows the
 * same permission rules as other module configuration pages.
 */
class AdminController extends \humhub\modules\admin\components\Controller
{
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

        return $this->render('preview', ['configuration' => $configuration]);
    }
}
