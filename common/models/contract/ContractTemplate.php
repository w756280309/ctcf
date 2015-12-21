<?php

namespace common\models\contract;

use Yii;

/**
 * This is the model class for table "contract_template".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property string $content
 * @property integer $status
 */
class ContractTemplate extends \yii\db\ActiveRecord
{
    const TYPE_TEMP_OFFLINE = 0;
    const TYPE_TEMP_ONLINE = 2;
    const TYPE_TEMP_ALL = 3;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status','type','pid'], 'integer'],
            [['content','path'], 'string'],
            [['name'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'content' => 'Content',
            'status' => 'Status',
        ];
    }
    
     public static function getContractTemplateData($pid=0,$type=1){
        return static::find()->where(['pid'=>$pid,'type'=>$type])->orWhere(['type'=>self::TYPE_TEMP_ALL])->all();
    }
}
