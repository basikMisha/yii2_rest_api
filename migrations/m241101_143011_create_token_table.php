<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%token}}`.
 */
class m241101_143011_create_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%token}}', [
            'id' => $this->primaryKey(),
            'user_id' =>$this->integer()->notNull(),
            'token' => $this->string()->unique()->notNull(),
            'expires_at' => $this->timestamp()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-token-user_id',
            'token',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%token}}');
        $this->dropForeignKey('fk-token-user_id', 'token');
    }
}
