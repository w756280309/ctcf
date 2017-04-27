<?php

namespace Xii\Crm\Model;


use yii\db\ActiveRecord;
use Zii\Validator\CnMobileValidator;

/**
 * Class PhoneCall
 * @package Xii\Crm\Model
 *
 * @property int    $id
 * @property int    $account_id
 * @property int    $creator_id
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 * @property int    $recp_id            客服ID
 * @property int    $contact_id         联系方式ID
 * @property string $direction          呼入方向：inbound 呼入|outbound 呼出
 * @property string $callTime           通话开始时间
 * @property int    $durationSeconds    通话时长(秒)
 * @property string $callerName         客户称呼
 * @property string $gender             性别
 * @property string $content            内容
 *
 * @property int $duration              通话时长(分)
 * @property string $number             电话
 * @property string $numberType         电话类型
 * @property Contact $contact           联系方式
 */
class PhoneCall extends ActiveRecord
{
    const TYPE_IN = 'inbound';//呼入
    const TYPE_OUT = 'outbound';//呼出

    const GENDER_MALE = 'm';//男性
    const GENDER_FEMALE = 'f';//女性

    public $duration;
    public $number;
    public $numberType;
    public $contact;

    public function rules()
    {
        return [
            [['callerName', 'number', 'durationSeconds', 'callTime', 'content', 'direction', 'gender'], 'required'],
            ['callTime', 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['content', 'callerName'], 'trim'],
            ['number', CnMobileValidator::className(), 'when' => function ($model) {
                return $model->numberType === Contact::TYPE_MOBILE;
            }],
            ['number', 'validateNumber', 'when' => function ($model) {
                return $model->numberType === Contact::TYPE_LANDLINE;
            }],
            ['callerName', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}]{1,6}$/u'],
            ['direction', 'in', 'range' => [PhoneCall::TYPE_IN, PhoneCall::TYPE_OUT]],
        ];
    }

    public function validateNumber($attribute, $params)
    {
        if ($this->numberType === Contact::TYPE_LANDLINE) {
            if (
                !preg_match('/^\d{3}-\d{8}$/', $this->number)
                && !preg_match('/^\d{3}-\d{7}$/', $this->number)
                && !preg_match('/^\d{4}-\d{8}$/', $this->number)
                && !preg_match('/^\d{4}-\d{7}$/', $this->number)
            ) {
                $this->addError($attribute, '座机格式不正确');
            }
        }
    }

    public function beforeValidate()
    {
        if (empty($this->durationSeconds) && !empty($this->duration)) {
            $this->durationSeconds = bcmul($this->duration, 60);
        }
        $this->contact = Contact::initNew($this->number);
        $this->numberType = $this->contact->type;
        $this->number = $this->contact->number;

        return parent::beforeValidate();
    }

    public static function tableName()
    {
        return 'crm_phone_call';
    }

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => DateTimeBehavior::class,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'callerName' => '姓名',
            'number' => '电话',
            'content' => '备注',
            'duration' => '通话时长(分)',
            'callTime' => '通话开始时间',
            'direction' => '呼入方向',
            'gender' => '性别',
        ];
    }

    public static function getDirectionLabels()
    {
        return [
            PhoneCall::TYPE_IN => '呼入',
            PhoneCall::TYPE_OUT => '呼出',
        ];
    }

}