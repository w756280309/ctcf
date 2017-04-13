<?php

namespace common\models\user;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\mall\PointRecord;
use common\service\PointsService;
use Yii;

/**
 * This is the model class for table "check_in".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $checkDate          签到日期
 * @property string $lastCheckDate      上次签到日期
 * @property integer $streak            连续签到天数
 * @property string $createTime         创建时间
 */
class CheckIn extends \yii\db\ActiveRecord
{

    public $points;//本次签到获取的积分
    public $couponType;//本次签到赠送的代金券

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'check_in';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'checkDate', 'createTime'], 'required'],
            [['user_id', 'streak'], 'integer'],
            [['checkDate', 'lastCheckDate', 'createTime'], 'safe'],
            [['user_id', 'checkDate'], 'unique', 'targetAttribute' => ['user_id', 'checkDate'], 'message' => 'The combination of User ID and Check Date has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'checkDate' => 'Check Date',
            'lastCheckDate' => 'Last Check Date',
            'streak' => 'Streak',
            'createTime' => 'Create Time',
        ];
    }


    public static function getAward(CheckIn $checkIn)
    {
        $streak = $checkIn->streak;//当次签到的连续签到次数
        $points = 0;//每天签到该赠送的积分
        $couponTypeSn = '';//达到条件之后额外赠送的代金券


        if ($streak >= 1 && $streak <= 7) {
            $points = 5;
        } elseif ($streak >= 8 && $streak <= 30) {
            $points = 8;
        }

        switch ($streak) {
            case 7:
                $couponTypeSn = 'check_in_10000_10';
                break;
            case 14:
                $couponTypeSn = 'check_in_20000_20';
                break;
            case 30:
                $couponTypeSn = 'check_in_50000_50';
                break;
        }

        return [
            'points' => $points,
            'couponTypeSn' => $couponTypeSn,
        ];
    }


    /**
     * 用户签到并发放奖励
     *
     * @param User $user
     * @param \DateTime $dateTime 签到日期
     * @param int $streakReset 重置阀值
     * @param bool $needAward 是否要发奖
     * @return false|CheckIn
     */
    public static function check(User $user, \DateTime $dateTime, $streakReset = 30, $needAward = true)
    {
        $lastDateTime = clone $dateTime;
        $lastDate = $lastDateTime->sub(new \DateInterval('P1D'))->format('Y-m-d');
        $lastRecord = CheckIn::find()->where(['user_id' => $user->id, 'checkDate' => $lastDate])->one();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            //保存签到记录
            $record = new CheckIn([
                'user_id' => $user->id,
                'checkDate' => $dateTime->format('Y-m-d'),
                'createTime' => date('Y-m-d H:i:s'),
            ]);
            if (!is_null($lastRecord)) {
                $record->lastCheckDate = $lastRecord->checkDate;
                if ($lastRecord->streak >= 30) {
                    $record->streak = ($lastRecord->streak + 1) % $streakReset;
                } else {
                    $record->streak = $lastRecord->streak + 1;
                }
            } else {
                $record->streak = 1;
            }

            if (!$record->save()) {
                throw new \Exception('签到记录保存失败');
            }

            //获取当次签到奖励
            if ($needAward) {
                $award = CheckIn::getAward($record);
            }

            //发放签到积分
            if (isset($award['points']) && $award['points'] > 0) {
                $pointRecord = new PointRecord([
                    'ref_type' => PointRecord::TYPE_CHECK_IN,
                    'ref_id' => $record->id,
                    'incr_points' => $award['points']
                ]);
                $res = PointsService::addUserPoints($pointRecord, false, $user, $dateTime->format('Y-m-d H:i:s'));

                if (!$res) {
                    throw new \Exception('积分发放失败');
                }
                $record->points = $award['points'];
            }

            //连续签到发放代金券
            if (isset($award['couponTypeSn']) && !empty($award['couponTypeSn'])) {
                $couponType = CouponType::findOne(['sn' => $award['couponTypeSn']]);
                $userCoupon = UserCoupon::addUserCoupon($user, $couponType);
                if (!$userCoupon->save()) {
                    throw new \Exception('代金券发放失败');
                }
                $record->couponType = $couponType;
            }


            $transaction->commit();
            return $record;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

    }
}
