<?php

namespace Xii\Crm\Model;


use yii\db\ActiveRecord;

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
 * @property string $comment            备注
 * @property string $reception          接待者
 *
 * @property int $duration              通话时长(分)
 * @property string $number             电话
 */
class PhoneCall extends ActiveRecord
{
    const TYPE_IN = 'inbound';//呼入
    const TYPE_OUT = 'outbound';//呼出

    const GENDER_MALE = 'm';//男性
    const GENDER_FEMALE = 'f';//女性

    public $duration;
    public $number;

    public function rules()
    {
        return [
            [['number', 'callTime', 'content', 'direction', 'gender'], 'required'],
            ['callTime', 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['content', 'callerName', 'comment'], 'trim'],
            ['duration', 'number'],
            ['number', 'validateNumber'],
            ['callerName', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}]{1,6}$/u'],
            ['direction', 'in', 'range' => [self::TYPE_IN, self::TYPE_OUT]],
        ];
    }

    public function validateNumber($attribute, $params)
    {
        if (
            !preg_match('/^\d{3}-\d{8}$/', $this->number)
            && !preg_match('/^\d{3}-\d{7}$/', $this->number)
            && !preg_match('/^\d{4}-\d{8}$/', $this->number)
            && !preg_match('/^\d{4}-\d{7}$/', $this->number)
            && !preg_match('/^1[34578]\d{9}$/', $this->number)
        ) {
            $this->addError($attribute, '电话号码格式不合法');
        }
    }

    public function beforeValidate()
    {
        if (empty($this->durationSeconds) && !empty($this->duration)) {
            $this->durationSeconds = bcmul($this->duration, 60);
        }
        $this->number = Contact::formatNumber($this->number);

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
            'content' => '通话内容',
            'duration' => '通话时长(分)',
            'callTime' => '通话开始时间',
            'direction' => '呼入方向',
            'gender' => '性别',
            'comment' => '评论',
        ];
    }

    public static function getDirectionLabels()
    {
        return [
            self::TYPE_IN => '呼入',
            self::TYPE_OUT => '呼出',
        ];
    }

    public function setIdentity()
    {
        if (!empty($this->callerName)) {
            $identity = new Identity([
                'account_id' => $this->account_id,
                'creator_id' => $this->creator_id,
            ]);
            $identity->setName($this->callerName);
            $identity->save(false);
        }
    }

    public function setActivity()
    {
        $activity = new Activity([
            'account_id' => $this->account_id,
            'creator_id' => $this->creator_id,
            'createTime' => $this->callTime,
            'ref_type' => Activity::TYPE_PHONE_CALL,
            'ref_id' => $this->id,
        ]);
        $activity->save(false);
    }

    public function setContact(Contact $contact)
    {
        if (is_null($contact->id)) {
            $account = new Account([
                'creator_id' => $this->creator_id,
                'type' => Account::TYPE_PERSON,
                'isConverted' => false,
            ]);
            $account->save(false);

            $contact->account_id = $account->id;
            $contact->creator_id = $this->creator_id;
            $contact->save(false);

            $account->primaryContact_id = $contact->id;
            $account->save(false);
        }

        $this->account_id = $contact->account_id;
        $this->contact_id = $contact->id;
    }

}