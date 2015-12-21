<?php

namespace common\models\news;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "news_category".
 *
 * @property string $id
 * @property string $name
 * @property string $parent
 * @property string $description
 * @property string $sort
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 */
class NewsFiles extends ActiveRecord
{
  
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_files';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id'], 'required'],
            ['status', 'default', 'value' => 1]
        ];
    }

}
