<?php

namespace console\controllers;

use common\models\user\CheckIn;
use common\models\mall\PointRecord;
use common\models\user\User;
use yii\console\Controller;

Class CheckinController extends Controller
{
    /**
     * 根据已有的PointRecord记录补充所有的签到记录
     */
    public function actionInit()
    {
        $pointRecords = PointRecord::find()
            ->select(['user_id', 'recordTime'])
            ->where(['isOffline' => false])
            ->andWhere(['ref_type' => PointRecord::TYPE_MALL_INCREASE])
            ->orderBy([
                'user_id' => SORT_ASC,
                'recordTime' => SORT_ASC,
            ])
            ->asArray()
            ->all();

        $this->stdout('当前积分流水数量：' . count($pointRecords));

        $num = 0;
        $lastUserId = null;
        foreach ($pointRecords as $pointRecord) {
            $user = User::findOne($pointRecord['user_id']);
            if (null !== $user) {
                if ($lastUserId !== $user->id) {
                    $num++;
                    $lastUserId = $user->id;
                }
                CheckIn::check($user, (new \DateTime($pointRecord['recordTime'])), 30, false);
            }
        }

        $this->stdout('涉及的用户数' . $num);
        return self::EXIT_CODE_NORMAL;
    }

    public function actionSupplement()
    {
        $query = User::find()
            ->where(['type' => User::USER_TYPE_PERSONAL])
            ->andWhere(['is_soft_deleted' => false]);

        $num = $query->count();
        $users = $query->all();

        foreach ($users as $user) {
            CheckIn::check($user, (new \DateTime()));
        }

        $finalNum = 0;
        $finalNum = CheckIn::find()->count();

        $this->stdout($num === $finalNum);
        return self::EXIT_CODE_NORMAL;
    }
}
