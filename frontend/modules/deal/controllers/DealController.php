<?php

namespace frontend\modules\deal\controllers;

use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\widgets\Pager;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;

class DealController extends BaseController
{

    public function actionDetail($sn)
    {
        $deal = $this->findOr404(OnlineProduct::className(), ['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'sn' => $sn]);
        //未登录或者登录了，但不是定向用户的情况下，报404
        if (1 === $deal->isPrivate) {
            if (Yii::$app->user->isGuest) {
                $this->ex404('未登录用户不能查看定向标');
            } else {
                $user_ids = explode(',', $deal->allowedUids);
                if (!in_array(Yii::$app->user->identity->getId(), $user_ids)) {
                    $this->ex404('不能查看他人的定向标');
                }
            }
        }
        //项目可投余额
        if (intval($deal->status) === OnlineProduct::STATUS_FOUND) {
            $dealBalance = 0;
        } else if ($deal->status >= OnlineProduct::STATUS_NOW) {
            //募集期的取剩余
            $dealBalance = $deal->getLoanBalance();
        } else {
            $dealBalance = $deal->money;
        }
        //拼接开始时间
        if (intval($deal->status) == OnlineProduct::STATUS_PRE) {
            $start = Yii::$app->functions->getDateDesc($deal['start_date']);
            $deal->start_date = $start['desc'] . date('H:i', $start['time']);
        }
        $deal->money = rtrim(rtrim($deal->money, '0'), '.');
        return $this->render('detail', [
            'deal' => $deal,
            'dealBalance' => $dealBalance,
        ]);
    }

    public function actionOrderList($pid)
    {
        $deal = $this->findOr404(OnlineProduct::className(), ['id' => $pid]);
        $query = OnlineOrder::find()->where(['online_pid' => $pid, 'status' => 1])->select('mobile,order_time time,order_money money')->orderBy("id desc");
        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 20
        ]);
        $data = $query->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $html = '<div><div><table>';
        foreach ($data as $key => $dat) {
            $html .= '<tr>';
            $html .= '<td>' . StringUtils::obfsMobileNumber($dat['mobile']) . '</td>';
            $html .= '<td>' . date('Y-m-d', $dat['time']) . ' ' . date('H:i:s', $dat['time']) . '</td>';
            $html .= '<td>' . rtrim(rtrim(number_format($dat['money'], 2), '0'), '.') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';
        $pageString = Pager::widget(['pagination' => $pages]);
        $html .= '<div>' . $pageString . '</div></div>';
        return $html;
    }
}