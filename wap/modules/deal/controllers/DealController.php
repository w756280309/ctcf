<?php

namespace app\modules\deal\controllers;

use common\controllers\HelpersTrait;
use common\models\product\LoanFinder;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\models\user\UserInfo;
use common\service\PayService;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use Wcg\Xii\Risk\Model\Risk;
use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\ServerErrorHttpException;

class DealController extends Controller
{
    use HelpersTrait;

    /**
     * 获取理财列表.
     */
    public function actionIndex($page = 1)
    {
        $size = 5;
        $query = LoanFinder::queryPublicLoans();
        $query = $query->andWhere('cid != 3');
        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $size]);
        $deals = $query->orderBy([
            'xs_status' => SORT_DESC,
            'recommendTime' => SORT_DESC,
            'sort' => SORT_ASC,
            'raiseDays' => SORT_DESC,
            'finish_rate' => SORT_DESC,
            'raiseSn' => SORT_DESC,
            'isJiaxi' => SORT_ASC,
            'finish_date' => SORT_DESC,
            'id' => SORT_DESC,
        ])->offset($pages->offset)->limit($pages->limit)->all();

        $tp = ceil($count / $size);
        $code = ($page > $tp) ? 1 : 0;
        $header = [
            'count' => intval($count),
            'size' => $size,
            'tp' => $tp,
            'cp' => intval($page),
        ];

        if (Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';
            $html = $this->renderFile('@wap/modules/deal/views/deal/_more.php', ['deals' => $deals, 'header' => $header]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }
        return $this->render('index', [
            'deals' => $deals,
            'header' => $header,
            ]);
    }

    /**
     * 标的详情页面.
     */
    public function actionDetail($sn)
    {
        if (empty($sn)) {    //参数无效时,抛出异常
            throw new ServerErrorHttpException('标的编号不能为空');
        }

        $deal = $this->findOr404(OnlineProduct::class, [
            'online_status' => OnlineProduct::STATUS_ONLINE,
            'del_status' => OnlineProduct::STATUS_USE,
            'sn' => $sn,
        ]);
        $user = $this->getAuthedUser();

        //未登录或者登录了，但不是定向用户的情况下，报404
        if (1 === $deal->isPrivate) {
            if (Yii::$app->user->isGuest) {
                throw $this->ex404();
            } else {
                $uids = explode(',', $deal->allowedUids);
                if (!in_array($user->id, $uids)) {
                    throw $this->ex404();
                }
            }
        }

        return $this->render('detail', [
            'deal' => $deal,
            'user' => $user,
            'allowTransfer' => $deal->allowTransfer(),
            'pointsMultiple' => $deal->pointsMultiple,
        ]);
    }

    /**
     * 获取投资记录.
     * @param type $pid
     * @return type
     */
    public function actionOrderlist($pid)
    {
        if (empty($pid)) {
            return ['orders' => [], 'code' => 1, 'message' => 'pid参数不能为空'];
        }
        $u = User::tableName();
        $o = OnlineOrder::tableName();
        $data = OnlineOrder::find()->innerJoin('user', "$u.id = $o.uid")->select("$u.safeMobile,order_time time,order_money money")->where(['online_pid' => $pid, "$o.status" => 1])->orderBy("$o.id desc")->asArray()->all();
        foreach ($data as $key => $dat) {
            $data[$key]['mobile'] = StringUtils::obfsMobileNumber(SecurityUtils::decrypt($dat['safeMobile']));
            $data[$key]['time'] = date('Y-m-d', $dat['time']);
            $data[$key]['his'] = date('H:i:s', $dat['time']);
            $data[$key]['money'] = rtrim(rtrim(number_format($dat['money'], 2), '0'), '.');
        }

        return ['orders' => $data, 'code' => 0, 'message' => '消息返回'];
    }

    /**
     * 立即认购.
     * @param type $sn 标的sn
     */
    public function actionToorder($sn = null, $rand)
    {
        $pay = new PayService(PayService::REQUEST_AJAX);
        $user = $this->getAuthedUser();
        $ret = $pay->toCart($this->getAuthedUser(), $sn, $rand);
        if (0 === $ret['code']) {
            $isInvested = UserInfo::find()
                ->select('isInvested')
                ->where(['user_id' => $user->id])
                ->scalar();
            if (!$isInvested) {
                $risk = Risk::find()
                    ->where(['user_id' => $user->id])
                    ->andWhere(['isDel' => false])
                    ->one();
                if (null === $risk) {
                    $ret = [
                        'code' => 1,
                        'message' => '您还没有进行风险测评',
                        'tourl' => '/risk/risk/index?backUrl='.Yii::$app->request->getReferrer(),
                    ];
                }
            }
        }
        return $ret;
    }

    /**
     * 获取网贷列表.
     */
    public function actionLoan($page = 1)
    {
        $size = 5;
        $query = LoanFinder::queryP2pLoans();
        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $size]);
        $deals = $query->orderBy([
            'xs_status' => SORT_DESC,
            'recommendTime' => SORT_DESC,
            'sort' => SORT_ASC,
            'raiseDays' => SORT_DESC,
            'finish_rate' => SORT_DESC,
            'raiseSn' => SORT_DESC,
            'isJiaxi' => SORT_ASC,
            'finish_date' => SORT_DESC,
            'id' => SORT_DESC,
        ])->offset($pages->offset)->limit($pages->limit)->all();

        $tp = ceil($count / $size);
        $code = ($page > $tp) ? 1 : 0;
        $header = [
            'count' => intval($count),
            'size' => $size,
            'tp' => $tp,
            'cp' => intval($page),
        ];

        if (Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';
            $html = $this->renderFile('@wap/modules/deal/views/deal/_more.php', ['deals' => $deals, 'header' => $header]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }
        return $this->render('index', [
            'deals' => $deals,
            'header' => $header,
        ]);
    }
}
