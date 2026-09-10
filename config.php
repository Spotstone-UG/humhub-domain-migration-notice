<?php

/**
 * Domain Migration Notice is a standalone HumHub module.
 *
 * Author: Ingo Fleckenstein — https://github.com/ingofleckenstein
 * Organisation: Spotstone UG — https://github.com/Spotstone-UG
 * Developed with assistance from GPT-5.6 Terra.
 */

use humhub\modules\domainmigrationnotice\Events;
use humhub\modules\domainmigrationnotice\Module;
use yii\web\Controller;
use yii\web\View;

return [
    'id' => 'domainmigrationnotice',
    'class' => Module::class,
    'namespace' => 'humhub\\modules\\domainmigrationnotice',
    'events' => [
        [Controller::class, Controller::EVENT_BEFORE_ACTION, [Events::class, 'onBeforeAction']],
        [View::class, View::EVENT_END_BODY, [Events::class, 'onEndBody']],
    ],
];
