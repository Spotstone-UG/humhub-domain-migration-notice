<?php

use yii\db\Migration;

/**
 * HumHub executes this migration when an administrator disables the module.
 *
 * It is intentionally separate from the regular version migrations: HumHub's
 * uninstall service invokes uninstall.php, not a migration's safeDown().
 */
class uninstall extends Migration
{
    public function up(): bool
    {
        if ($this->db->schema->getTableSchema('{{%dmn_configuration}}', true) !== null) {
            $this->dropTable('{{%dmn_configuration}}');
        }

        return true;
    }
}
