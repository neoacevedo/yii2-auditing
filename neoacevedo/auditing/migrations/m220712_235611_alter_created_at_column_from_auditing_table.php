<?php

use yii\db\Migration;

/**
 * Class m220712_235611_alter_created_at_column_from_audit_table
 */
class m220712_235611_alter_created_at_column_from_auditing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("{{%auditing}}", "created_at", $this->integer()->notNull()->after("ip"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("{{%auditing}}", "created_at", $this->timestamp()->notNull()->after("ip"));
    }
}
