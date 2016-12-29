<?php

use common\models\category\Category;
use common\models\category\ItemCategory;
use common\models\news\News;
use yii\db\Migration;

class m161229_025958_alter_news_table extends Migration
{
    public function up()
    {
        $this->dropColumn('news', 'category_id');
        $this->addColumn('news', 'allowShowInList', $this->boolean()->notNull()->defaultValue(1));

        $c = Category::tableName();
        $itemCategory = ItemCategory::find()
            ->innerJoin($c, "category_id = $c.id")
            ->where(['name' => ['理财指南', '投资技巧']])
            ->select('item_id')
            ->column();

        News::updateAll(['allowShowInList' => false], ['id' => $itemCategory]);
    }

    public function down()
    {
        echo "m161229_025958_alter_news_table cannot be reverted.\n";

        return false;
    }
}
