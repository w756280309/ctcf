<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-5-26
 * Time: 下午2:03
 */
namespace common\models\product;

use common\models\user\User;
use common\utils\SecurityUtils;
use yii\db\ActiveRecord;

class Asset extends ActiveRecord
{
    const STATUS_INIT = 0;  //初始状态
    const STATUS_SUCCESS = 1;   //已经发标

    public static function tableName()
    {
        return 'asset';
    }

    public function rules()
    {
        return [
            [['source', 'borrowerName', 'sn', 'amount', 'repaymentType', 'rate', 'expires', 'expiresType', 'borrowerIdCardNumber', 'borrowerType', 'issue'], 'trim'],
            [['source', 'borrowerName', 'sn', 'amount', 'repaymentType', 'rate', 'expires', 'expiresType', 'borrowerIdCardNumber', 'borrowerType', 'issue'], 'required'],
            [['sn'], 'unique'],
        ];
    }
    /**
     * 创建对象
     * @param $data
     * @return Asset
     */
    public static function initNew($data)
    {
        $model = new self([
            'source' => $data['source'],
            'createTime' => date('Y-m-d H:i:s'),
            'borrowerName' => $data['name'],
            'sn' => $data['sn'],
            'amount' => $data['amount'],
            'repaymentType' => $data['repaymentType'],
            'rate' => $data['rate'],
            'expires' => $data['expires'],
            'expiresType' => $data['expiresType'],
            'borrowerIdCardNumber' => SecurityUtils::encrypt($data['loanUserIdcard']),  //身份证加密
            'borrowerType' => $data['account_type'],
            'extendInfo' => json_encode($data['extendInfo']),
            'status' => self::STATUS_INIT,
            'issue' => $data['issue'],
            'itemInfo' => $data['itemInfo'],
        ]);
        return $model;
    }
    //验签方法(小微提供)
    public static function sign($params, $signKey = '', $method = 'md5', $isReturnString = false)
    {
        $str = '';
        //添加上时间校验
        $params['signDate'] = date('Y-m-d');
        ksort($params, SORT_STRING);
        foreach ($params as $key => $one) {
            if (in_array($key, ['sign'], true)) {
                continue;
            } elseif (is_array($one)) {
                $str .= self::sign($one, $signKey, $method, true);
            } else {
                $str .= urldecode($one);
            }
        }
        if ($isReturnString) {
            return $str;
        }
        $str .= $signKey;
        $functionName = strtolower($method);
        $resultStr = $functionName($str);

        return $resultStr;
    }
    /**
     * 获取资产包的还款方式
     * @return mixed|string
     */
    public function getRepaymentMethod()
    {
        $arr = [
            '2' => '按天计息,付息还本',
            '3' => '到期本息',
            '6' => '等额本息',
        ];
        if (in_array($this->repaymentType, array_keys($arr))) {
            return $arr[$this->repaymentType];
        } else {
            return '未知';
        }
    }
    /**
     * 资产包签约状态
     * @return mixed
     */
    public function getSignState()
    {
        if ($this->issue == 1) {
            return '已签约';
        } else {
            return '未签约';
        }
    }
    public function getProduct()
    {
        return OnlineProduct::findOne([
            'asset_id' => $this->id,
            'del_status' => OnlineProduct::STATUS_USE,
        ]);
    }
    /**
     * 项目期限
     */
    public function getExpiresValue()
    {
        if ($this->expiresType == 1) {
            return $this->expires . '天';
        } else {
            return $this->expires . '个月';
        }
    }
    //将小微的还款方式转为温都的还款方式
    public function exchangeRepaymentType()
    {
        switch ($this->repaymentType) {
            case 3:
                return OnlineProduct::REFUND_METHOD_DAOQIBENXI;
                break;
            case 6:
                return OnlineProduct::REFUND_METHOD_DEBX;
                break;
        }
    }
    /**
     * 判断资产包的融资方是否开户
     */
    public function getBorrower()
    {
        return User::findOne([
            'safeIdCard' => $this->borrowerIdCardNumber,
            'status' => User::STATUS_ACTIVE,
            'is_soft_deleted' => 0,
            'type' => User::USER_TYPE_ORG,
        ]);
    }
}