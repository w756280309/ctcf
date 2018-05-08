<?php

use yii\db\Migration;

/**
 * Class m180417_062346_alter_promo_table
 */
class m180417_062346_alter_promo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('promo', 'isHidden', $this->boolean()->defaultValue(false)->comment('是否隐藏'));
        $this->addColumn('promo', 'sortValue', $this->integer()->comment('排序值'));
        $this->addColumn('promo', 'advSn', $this->string()->comment('首页轮播sn'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m180417_062346_alter_promo_table cannot be reverted.\n";

        return false;
    }

}
