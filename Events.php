<?php

namespace humhub\modules\domainmigrationnotice;

use humhub\modules\domainmigrationnotice\services\NoticeState;
use humhub\modules\domainmigrationnotice\widgets\NoticeWidget;
use Yii;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\web\Controller;

/**
 * The global hooks keep migration protection independent of the current page.
 */
class Events
{
    public static function onBeforeAction(Event $event): void
    {
        if (Yii::$app->request->isConsoleRequest || !$event instanceof ActionEvent) {
            return;
        }

        $state = new NoticeState();
        if (!$state->isBlocked() || self::isAllowedDuringBlock(Yii::$app->controller)) {
            return;
        }

        // A normal redirect guarantees that the original community action never runs.
        Yii::$app->response->redirect(['/domainmigrationnotice/notice/blocked']);
        $event->isValid = false;
    }

    public static function onEndBody(Event $event): void
    {
        if (Yii::$app->request->isConsoleRequest || Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
            return;
        }

        $state = new NoticeState();
        $configuration = $state->configuration();
        if ($configuration === null || !$state->applies($configuration) || $state->isBlocked($configuration) || !$state->shouldDisplay($configuration)) {
            return;
        }

        echo NoticeWidget::widget(['configuration' => $configuration]);
    }

    private static function isAllowedDuringBlock(?Controller $controller): bool
    {
        if ($controller === null) {
            return false;
        }

        // The migration page and its POST endpoints must remain reachable.
        if ($controller->module?->id === 'domainmigrationnotice') {
            return true;
        }

        // An administrator must be able to sign in and correct or disable a migration rule.
        if ($controller->module?->id === 'user' && $controller->id === 'auth') {
            return true;
        }

        return $controller->module?->id === 'admin' && !Yii::$app->user->isGuest && Yii::$app->user->isAdmin();
    }
}
