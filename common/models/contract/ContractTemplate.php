<?php

namespace common\models\contract;

use Yii;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;

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
    
    /**
     * 根据合同替换合同
     * @param ContractTemplate $temp
     * @param OnlineProduct $loan
     * @param OnlineOrder $ord
     * @return ContractTemplate
     */
    public static function replaceTemplate(ContractTemplate $temp, OnlineOrder $ord = null, OnlineProduct $loan = null)
    {
        $temp->content = preg_replace("/{{投资人}}/is", (null === $ord) ? "" : $ord->user->real_name, $temp->content);
        $temp->content = preg_replace("/{{身份证号}}/is", (null === $ord) ? "" : $ord->user->idcard, $temp->content);
        $temp->content = preg_replace("/{{认购日期}}/is", (null === $ord || null === $ord->order_time) ? "年月日" : date("Y年m月d日", $ord->order_time), $temp->content);
        $temp->content = preg_replace("/{{认购金额}}/is",  (null === $ord && null !== $ord->order_money) ? "" : $ord->order_money, $temp->content);
        $temp->content = preg_replace("/｛｛投资人｝｝/is", (null === $ord) ? "" : $ord->user->real_name, $temp->content);
        $temp->content = preg_replace("/｛｛身份证号｝｝/is", (null === $ord) ? "" : $ord->user->idcard, $temp->content);
        $temp->content = preg_replace("/｛｛认购日期｝｝/is", (null === $ord || null === $ord->order_time) ? "年月日" : date("Y年m月d日", $ord->order_time), $temp->content);
        $temp->content = preg_replace("/｛｛认购金额｝｝/is",  (null === $ord && null !== $ord->order_money) ? "" : $ord->order_money, $temp->content);
        return $temp;
    }
    
}
