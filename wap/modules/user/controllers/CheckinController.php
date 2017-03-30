<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\mall\PointOrder;
use common\models\user\User;
use Yii;
use yii\filters\AccessControl;

class CheckinController extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], //登录用户退出
                    ],
                ],
                'except' => [
                    'index',
                ],
            ],
        ];
    }

    /**
     * 签到得积分.
     */
    public function actionIndex()
    {
        $user = $this->getAuthedUser();
        $pointOrders = null;
        $checkInToday = false;
        $checkInDays = 0;
        $cycleDays = 30;

        if ($user) {
            $pointOrders = $this->pointOrders($user)
                ->all();

            $num = 0;
            foreach ($pointOrders as $key => $pointOrder) {
                $orderTime = substr($pointOrders[$key]['orderTime'], 0, 10);
                $day = date('Y-m-d', strtotime('today - '.$num++.' days'));

                if (0 === $key) {
                    if ($day === $orderTime) {
                        ++$checkInDays;
                        $checkInToday = true;

                        continue;
                    }

                    $day = date('Y-m-d', strtotime('today - '.$num++.' days'));
                }

                if ($orderTime !== $day) {
                    break;
                }

                ++$checkInDays;
            }

            $cycle = $checkInDays % $cycleDays;    //余数不为零的,取余数作为连续签到天数;余数为零时,取周期天数作为连续签到天数;
            if ($cycle) {
                $checkInDays = $cycle;
            } else {
                $checkInDays = $cycleDays;
            }
        }

        return $this->render('index', [
            'user' => $user,
            'checkInDays' => $checkInDays,
            'checkInToday' => $checkInToday,
        ]);
    }

    /**
     * 签到记录.
     */
    public function actionList($page = 1, $size = 15)
    {
        $query = $this->pointOrders($this->getAuthedUser());

        $pg = \Yii::$container->get('paginator')->paginate($query, $page, $size);
        $pointOrders = $pg->getItems();

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            $html = $this->render('_list', ['pointOrders' => $pointOrders]);

            return [
                'header' => $pg->jsonSerialize(),
                'html' => $html,
                'code' => $code,
                'message' => $message,
            ];
        }

        return $this->render('list', [
            'pointOrders' => $pointOrders,
            'header' => $pg->jsonSerialize(),
        ]);
    }

    private function pointOrders(User $user)
    {
        $query = PointOrder::find()
            ->where([
                'user_id' => $user->id,
                'isPaid' => true,
                'status' => PointOrder::STATUS_SUCCESS,
                'isOffline' => false,
                'type' => [
                    'report',
                    'sign',
                ],
            ])
            ->orderBy(['orderTime' => SORT_DESC]);

        return $query;
    }
}