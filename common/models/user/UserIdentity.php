<?php
/**
 * Created by ShiYang.
 * Date: 17-1-4
 * Time: 下午2:59
 */

namespace common\models\user;
use yii\base\Model;
use Zii\Validator\CnIdCardValidator;
use Zii\Validator\NameValidator;

/**
 * 用户实名认证验证表单
 * @package common\models\user
 * @property string $real_name          真是姓名
 * @property string $idcard             身份证号码
 */
class UserIdentity extends Model
{
    public $real_name;
    public $idcard;

    public function rules()
    {
        return [
            [['idcard', 'real_name'], 'trim'],
            [['idcard'], CnIdCardValidator::className()],
            [['real_name'], NameValidator::className()],
        ];
    }

    public function scenarios()
    {
        return ['default' => ['idcard', 'real_name']];
    }

    public function attributeLabels()
    {
        return [
            'real_name' => '姓名',
            'idcard' => '身份证号',
        ];
    }
    //删除字符串里面所有的空格
    public static function trimAll($str)
    {
        $oldChar = array(" ", "\t", "\n", "\r");
        $newChar = array("", "", "", "");

        return str_replace($oldChar, $newChar, $str);
    }
}