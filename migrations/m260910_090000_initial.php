<?php

use yii\db\Migration;

/**
 * Creates the single global module configuration record.
 *
 * Account-specific display state is stored through HumHub's content-container
 * settings service. HumHub removes those settings when the module is disabled.
 * The configuration table itself is removed by migrations/uninstall.php.
 */
class m260910_090000_initial extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%dmn_configuration}}', [
            'id' => $this->integer()->notNull(),
            'enabled' => $this->boolean()->notNull()->defaultValue(false),
            'target_url' => $this->string(2048)->notNull()->defaultValue(''),
            'deadline_at' => $this->integer(),
            'heading' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
            'show_countdown' => $this->boolean()->notNull()->defaultValue(true),
            'countdown_label' => $this->string(255)->notNull(),
            'destination_label' => $this->string(255)->notNull(),
            'dismiss_label' => $this->string(255)->notNull(),
            'weekly_dismiss_label' => $this->string(255)->notNull(),
            'enable_weekly_dismissal' => $this->boolean()->notNull()->defaultValue(true),
            'show_guest_cookie_notice' => $this->boolean()->notNull()->defaultValue(true),
            'guest_cookie_notice_text' => $this->string(1000)->notNull(),
            'show_frontend_attribution' => $this->boolean()->notNull()->defaultValue(true),
            'custom_css' => $this->text(),
            'PRIMARY KEY ([[id]])',
        ]);

        $this->insert('{{%dmn_configuration}}', [
            'id' => 1,
            'heading' => 'This community has moved',
            'message' => 'Please use our new address from now on.',
            'countdown_label' => 'Time remaining',
            'destination_label' => 'Open the new community',
            'dismiss_label' => 'Not now',
            'weekly_dismiss_label' => 'Do not show this again for one week',
            'guest_cookie_notice_text' => 'A necessary browser cookie remembers this choice on this device.',
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%dmn_configuration}}');
    }
}
