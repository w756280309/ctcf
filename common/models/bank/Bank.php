<?php

namespace common\models\bank;
use yii\base\Exception;

/**
 * This is the model class for table "bank".
 */
class Bank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Bank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * 判断银行卡是否支持个人网银充值
     * @return bool
     */
    public function getIsPersonal()
    {
        $eBankConfig = EbankConfig::find()->where(['bankId' => $this->id])->one();
        if (null !== $eBankConfig) {
            $typePersonal = $eBankConfig->typePersonal;
            return ($typePersonal === 0) ? false : true;
        }
        return false;
    }

    /**
     * 判断银行卡是否支持企业网银充值
     * @return bool
     */
    public function getIsBusiness()
    {
        $eBankConfig = EbankConfig::find()->where(['bankId' => $this->id])->one();
        if (null !== $eBankConfig) {
            $typeBusiness = $eBankConfig->typeBusiness;
            return ($typeBusiness === 0) ? false : true;
        }
        return false;
    }

    /**
     * 判断用户是否支持快捷充值
     * @return bool
     */
    public function getIsQuick()
    {
        $qPayConfig = QpayConfig::find()->where(['bankId' => $this->id, 'isDisabled' => 0])->one();
        if (null !== $qPayConfig) {
            return true;
        }
        return false;
    }

    /**
     * 获取银行限额
     * @return string
     */
    public function getQuota()
    {
        $qPayConfig = QpayConfig::find()->where(['bankId' => $this->id, 'isDisabled' => 0])->one();
        if (null !== $qPayConfig) {
            $singleLimit = $qPayConfig->singleLimit;
            $dailyLimit = $qPayConfig->dailyLimit;
            return number_format($singleLimit/10000,0) . ' 万/次，' . number_format($dailyLimit/10000,0) . ' 万/日';
        }
        return '无';
    }

    /**
     * 获取单日限额
     * @return mixed|null
     */
    public function getDailyLimit()
    {
        $qPayConfig = QpayConfig::find()->where(['bankId' => $this->id, 'isDisabled' => 0])->one();
        if (null !== $qPayConfig) {
            $dailyLimit = $qPayConfig->dailyLimit;
            return $dailyLimit;
        }
        return null;
    }

    /**
     * 获取每次限额
     * @return mixed|null
     */
    public function getSingleLimit()
    {
        $qPayConfig = QpayConfig::find()->where(['bankId' => $this->id, 'isDisabled' => 0])->one();
        if (null !== $qPayConfig) {
            $singleLimit = $qPayConfig->singleLimit;
            return $singleLimit;
        }
        return null;
    }


    /**
     * 判断银行卡是否禁用
     * @return bool
     */
    public function getIsDisabled()
    {
        if (false === $this->isPersonal && false === $this->isBusiness && false === $this->isQuick) {
            return true;
        } else {
            return false;
        }
    }

}
