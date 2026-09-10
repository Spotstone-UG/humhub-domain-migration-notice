<?php

use yii\db\Migration;

/**
 * Makes the remaining client-side countdown text configurable as well.
 */
class m260910_100000_add_countdown_texts extends Migration
{
    public function safeUp(): void
    {
        $table = $this->db->schema->getTableSchema('{{%dmn_configuration}}', true);
        if ($table === null) {
            throw new \RuntimeException('The Domain Migration Notice configuration table is missing. Apply the initial migration first.');
        }

        // The existence checks keep an interrupted development installation
        // recoverable without weakening normal versioned migrations.
        if ($table->getColumn('countdown_format') === null) {
            $this->addColumn('{{%dmn_configuration}}', 'countdown_format', $this->string(255)->notNull()->defaultValue('{days}d {hours}h {minutes}m'));
        }
        if ($table->getColumn('deadline_reached_label') === null) {
            $this->addColumn('{{%dmn_configuration}}', 'deadline_reached_label', $this->string(255)->notNull()->defaultValue('The deadline has been reached.'));
        }
    }

    public function safeDown(): void
    {
        $table = $this->db->schema->getTableSchema('{{%dmn_configuration}}', true);
        if ($table === null) {
            return;
        }

        if ($table->getColumn('deadline_reached_label') !== null) {
            $this->dropColumn('{{%dmn_configuration}}', 'deadline_reached_label');
        }
        if ($table->getColumn('countdown_format') !== null) {
            $this->dropColumn('{{%dmn_configuration}}', 'countdown_format');
        }
    }
}
