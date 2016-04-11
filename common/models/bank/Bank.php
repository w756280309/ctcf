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
            return $singleLimit . ' 万/次，' . $dailyLimit . ' 万/日';
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

    /**
     * 保存银行信息
     * @param array $post
     * @return bool
     */
    public function saveBank(array $post)
    {
        $isPersonal = isset($post['isPersonal']) ? intval($post['isPersonal']) : 0;
        $isBusiness = isset($post['isBusiness']) ? intval($post['isBusiness']) : 0;
        $isQuick = isset($post['isQuick']) ? intval($post['isQuick']) : 1;
        $singleLimit = isset($post['singleLimit']) ? floatval($post['singleLimit']) : 0;
        $dailyLimit = isset($post['dailyLimit']) ? floatval($post['dailyLimit']) : 0;
        $qPayConfig = QpayConfig::find()->where(['bankId' => $this->id])->one();
        $eBankConfig = EbankConfig::find()->where(['bankId' => $this->id])->one();
        if (!$qPayConfig || !$eBankConfig) {
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $eBankConfig->typePersonal = $isPersonal;
            $eBankConfig->typeBusiness = $isBusiness;
            if (!$eBankConfig->save(false)) {
                $transaction->rollBack();
                return false;
            }
            $qPayConfig->singleLimit = $singleLimit;
            $qPayConfig->dailyLimit = $dailyLimit;
            $qPayConfig->isDisabled = $isQuick;
            if (!$qPayConfig->save(false)) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
