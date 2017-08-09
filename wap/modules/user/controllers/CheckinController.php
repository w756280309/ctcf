<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\code\Voucher;
use common\models\mall\PointRecord;
use common\models\promo\PromoService;
use common\models\thirdparty\SocialConnect;
use common\models\user\CheckIn;
use common\models\user\User;
use common\service\PointsService;
use common\utils\StringUtils;
use Yii;
use yii\filters\AccessControl;

class CheckinController extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function behaviors()
    {
        $access = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], //登录用户退出
                    ],
                ],
            ],
        ];

        if (!defined('IN_APP')) {
            $access['access']['except'] = [
                'index',
            ];
        }

        return $access;
    }

    /**
     * 签到得积分.
     */
    public function actionIndex($back_url = null)
    {
        $user = $this->getAuthedUser();
        $pointOrders = null;
        $checkInToday = false;
        $checkInDays = 0;
        $isBindWx = false;

        if (!empty($back_url)) {
            $back_url = filter_var($back_url, FILTER_VALIDATE_URL);

            if (!$back_url) {
                $back_url = null;
            }
        }

        if ($user) {
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('yesterday'));

            $check = CheckIn::find()
                ->where(['user_id' => $user->id])
                ->orderBy(['checkDate' => SORT_DESC])
                ->one();

            if ($check) {
                if ($today === $check->checkDate) {
                    $checkInToday = true;
                    $checkInDays = $check->streak;
                } elseif ($yesterday === $check->checkDate) {
                    $checkInDays = 30 === $check->streak ? 0 : $check->streak;   //昨天签到满30天的,且今天未签到的,前端连续签到次数显示为0
                }
            }

            $isBindWx = null !== SocialConnect::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['provider_type' => SocialConnect::PROVIDER_TYPE_WECHAT])
                ->one();
        }

        return $this->render('index', [
            'user' => $user,
            'checkInDays' => $checkInDays,
            'checkInToday' => $checkInToday,
            'backUrl' => $back_url,
            'isBindWx' => $isBindWx,
        ]);
    }

    /**
     * 签到.
     */
    public function actionCheck()
    {
        $user = $this->getAuthedUser();
        $flag = false;
        $check = CheckIn::check($user, (new \DateTime()));

        if ($check) {
            $extraPoints = 0;
            $flag = true;

            //签到召回送额外积分
            $voucher = Voucher::find()
                ->where(['>=', 'expireTime', date('Y-m-d H:i:s')])
                ->andWhere(['user_id' => $user->id])
                ->andWhere(['isOp' => true])
                ->andWhere(['isRedeemed' => false])
                ->andWhere(['ref_type' => PointRecord::REF_TYPE_CHECK_IN_RETENTION])
                ->one();

            if (null !== $voucher) {
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    $sql = "update voucher set isRedeemed = :isRedeemed, redeemTime = :redeemTime, redeemIp = :redeemIp where id = :id and isRedeemed = false ";
                    $rows = $db->createCommand($sql, [
                        'isRedeemed' => true,
                        'redeemTime' => date('Y-m-d H:i:s'),
                        'redeemIp' => Yii::$app->request->getUserIP(),
                        'id' => $voucher->id,
                    ])->execute();
                    if ($rows > 0) {
                        $pointRecord = new PointRecord([
                            'ref_id' => $voucher->id,
                            'ref_type' => PointRecord::REF_TYPE_CHECK_IN_RETENTION,
                            'incr_points' => $voucher->amount,
                            'remark' => PointRecord::getTypeName(PointRecord::REF_TYPE_CHECK_IN_RETENTION),
                        ]);
                        PointsService::addUserPoints($pointRecord, false, $user);
                    }
                    $transaction->commit();
                    $extraPoints = $voucher->amount;
                } catch (\Exception $ex) {
                    $transaction->rollBack();
                }
            }
            if ($flag) {
                try {
                    PromoService::doAfterCheckIn($check);
                } catch (\Exception $ex) {
                }
            }
            $res = [
                'streak' => $check->streak,
                'points' => $check->points + $extraPoints,
                'coupon' => $check->couponType ? $check->couponType->amount : 0,
                'extraPoints' => $extraPoints,
            ];
        } else {
            Yii::$app->response->statusCode = 400;

            $res = [
                'message' => '签到失败, 请稍后重试!',
            ];
        }

        return $res;
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
        $query = PointRecord::find()
            ->where([
                'user_id' => $user->id,
                'ref_type' => PointRecord::TYPE_CHECK_IN,
                'isOffline' => false,
            ])
            ->orderBy(['recordTime' => SORT_DESC]);

        return $query;
    }
}