<?php

/**
 * Domain Migration Notice is a standalone HumHub module.
 *
 * Author: Ingo Fleckenstein — https://github.com/ingofleckenstein
 * Organisation: Spotstone UG — https://github.com/Spotstone-UG
 * Developed with assistance from GPT-5.6 Terra.
 *
 * "May this work leave a little more space for dignity, tenderness,
 * curiosity, and self-determination."
 */

namespace humhub\modules\domainmigrationnotice;

use yii\helpers\Url;

class Module extends \humhub\components\Module
{
    public function getConfigUrl(): string
    {
        return Url::to(['/domainmigrationnotice/admin/index']);
    }
}
