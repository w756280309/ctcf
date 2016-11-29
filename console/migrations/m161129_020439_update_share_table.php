<?php

use yii\db\Migration;

class m161129_020439_update_share_table extends Migration
{
    public function up()
    {
        $this->update('share', ['description' => '市民身边的财富管家，各种优质企业金融机构理财产品，安全理财，幸福千万家。'], ['shareKey' => 'h5']);
    }

    public function down()
    {
        echo "m161129_020439_update_share_table cannot be reverted.\n";

        return false;
    }
}
