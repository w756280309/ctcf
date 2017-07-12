<?php

namespace Zii\Model;

use yii\db\ActiveRecord as BaseActiveRecord;

/**
 * 扩展Yii的ActiveRecord类.
 */
class ActiveRecord extends BaseActiveRecord
{
    /**
     * 获取第一个错误信息
     */
    public function getSingleError()
    {
        return current($this->getFirstErrors());
    }
}
