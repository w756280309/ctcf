<?php

namespace common\models\channel;

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
            [['type', 'status'], 'integer'],
            [['content'], 'string'],
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
}
