<?php

namespace common\models\user;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "original_borrower".
 */
class OriginalBorrower extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'original_borrower';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name','required','message'=>'用户名不能为空'],
            ['name','unique','message'=>'用户名已占用']
        ];
    }
}
