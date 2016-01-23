<?php
namespace common\models\draw;

use Yii;
use yii\base\Model;

/**
 * draw form
 */
class Draw extends Model
{
    public $money;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['money'], 'required'], 
            [['money'], 'match', 'pattern' => '/^[0-9]+([.]{1}[0-9]{1,2})?$/', 'message' => '提现金额格式错误'],
            [['money'], 'number', 'min' => 1, 'max' => 10000000],
        ];
    }  

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'money' => '金额',
        ];
    }

}
