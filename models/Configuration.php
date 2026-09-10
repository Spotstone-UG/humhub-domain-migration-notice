<?php

namespace humhub\modules\domainmigrationnotice\models;

use yii\db\ActiveRecord;

/**
 * The one global module configuration record.
 */
class Configuration extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%dmn_configuration}}';
    }

    public static function get(): ?self
    {
        return static::findOne(1);
    }
}
