<?php

use yii\db\Migration;

class m160622_063725_update_category_kind extends Migration
{
    public function up()
    {
        $this->batchInsert("category",
            ['name', 'key', 'parent_id', 'level', 'description', 'sort', 'status', 'type', 'created_at', 'updated_at'],
            [
                ['媒体报道', 'media', 0, 1, '媒体报道', 1, 1, 1, time(), time()],
                ['网站公告', 'notice', 0, 1, '网站公告', 1, 1, 1, time(), time()],
            ]
        );


        $count = $this->getDb()->createCommand("select id from category where name='最新资讯'")->query()->getRowCount();
        if ($count > 0) {
            $this->update('category', ['key'=>'info'], ['name'=>'最新资讯']);
        } else {
            $this->insert("category", ['name'=>'最新资讯', 'key'=>'info', 'parent_id'=>0, 'level'=>1, 'description'=>'最新资讯', 'sort'=>1, 'status'=>1, 'type'=>1, 'created_at'=>time(), 'updated_at'=>time()]);
        }
    }

    public function down()
    {
        echo "m160622_063725_update_category_kind cannot be reverted.\n";

        return false;
    }
}
