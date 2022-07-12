<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%auditing}}`.
 */
class m220712_164604_create_auditing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%auditing}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->bigInteger()->defaultValue(0),
            'description' => $this->string(),
            'event' => $this->string(45)->notNull()->comment("INSERT|UPDATE|DELETE"),
            'model' => $this->string()->notNull(),
            'attribute' => $this->string()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string(),
            'action' => $this->string()->comment("namespace\TheController::actionTheAction()"),
            'ip' => $this->string(45),
            'created_at' => $this->timestamp()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auditing}}');
    }
}
