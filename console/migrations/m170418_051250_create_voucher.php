<?php

use yii\db\Migration;

class m170418_051250_create_voucher extends Migration
{
    public function up()
    {
        $this->createTable('voucher', [
            'id' => $this->primaryKey(),
            'ref_type' => $this->string()->notNull(),
            'ref_id' => $this->string()->notNull(),
            'goodsType_sn' => $this->string()->notNull(),
            'card_id' => $this->integer()->null(),
            'promo_id' => $this->integer()->null(),
            'user_id' => $this->integer()->notNull(),
            'isRedeemed' => $this->boolean()->defaultValue(false),
            'redeemTime' => $this->dateTime()->null(),
            'redeemIp' => $this->string()->null(),
            'createTime' => $this->dateTime()->notNull(),
        ]);
        $this->createIndex('unique_ref_key', 'voucher', ['ref_type', 'ref_id'], true);
    }

    public function down()
    {
        echo "m170418_051250_create_voucher cannot be reverted.\n";

        return false;
    }
}
