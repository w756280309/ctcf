<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\tx\CreditNote;
use common\models\user\User;
use common\models\user\UserInfo;
use Yii;
use yii\db\Query;
use yii\web\Controller;

class LicaiController extends Controller
{
    use HelpersTrait;

    public function actionNotes($page = 1)
    {
        $expireStart = (int)Yii::$app->request->get('expireStart');//剩余期限段开始时间
        $expireEnd = (int)Yii::$app->request->get('expireEnd');//剩余期限段结束时间
        $yieldRateStart = (float)Yii::$app->request->get('projectRateStart');//标的利率范围起始值
        $yieldRateEnd = (float)Yii::$app->request->get('projectRateEnd');//标的利利率范围结束值
        $isDiscount = Yii::$app->request->get('isDiscount');//折让利率
        $multiName = trim(Yii::$app->request->get('multiName'));//名称 目前为标的名称和转让用户名称
        $selectType = (int)Yii::$app->request->get('selectType', 1);//查询类型
        $selectTypeSet = [1, 2, 3];
        $user = Yii::$app->user->getIdentity();
        //未登录或者未投资的用户不可见
        if (
            Yii::$app->params['plat_code'] == 'WDJF'
            && (empty($user) || ($user->orderCount() <= 0 && $user->creditOrderCount() <= 0))
        ) {
            throw $this->ex404();
        }
        if (is_null($user) || (null !== $user && $user->getJGMoney() < 50000)) {
            $jianguan = true;
        } else {
            $jianguan = false;
        }
        $array = [];
        if ($jianguan) {
            $query = OnlineProduct::find()->select('id');
            $query->andWhere(['isLicai' => false]);
            $loans = $query->andWhere("NOT((cid = 2) and if(refund_method = 1, expires > 180, expires > 6))")->asArray()->all();
            foreach ($loans as $v) {
                $array[] = $v['id'];
            }
        }
        $noteIds = [];
        $selectSet =  [];
        $isShowPop = false;//是否展示弹窗
        $selectNoteIds = [];//筛选出的转让id
        $notLoanIds = [];//非网贷标的
        //温都项目且非ajax请求则查询转让中的项目期限、利率、折让率,如果没有转让中项目，则查询项无条件。与产品确认，如果无转让中项目，此筛选功能要下架
        $p = OnlineProduct::tableName();
        //如果筛选类型值非法，则设置default为真，表示按照默认方式进行查询，目前为查询方式1
        if (!in_array($selectType, $selectTypeSet)) {
            $default = true;
        }
        if ($selectType == 2) {
            $default = true;
            //查询方式2 根据名称搜索
            if (!empty($multiName)) {
                //先根据用户名查询id 再根据转让表查询符合要求的标的id 此功能先注释，有需要再解开使用
                //*****注释开始*****
                /*$u = User::tableName();
                $userIds = (new Query())
                    ->select('id')
                    ->from("$u")
                    ->where(['real_name' => $multiName])
                    ->indexBy('id')
                    ->column();
                $selectNoteIds = CreditNote::find()
                    ->select('id')
                    ->where(['in', 'user_id', $userIds])
                    ->andWhere(['isClosed' => false])
                    ->indexBy('id')
                    ->column();*/
                //*****注释结束*****
                if (empty($selectNoteIds)) {
                    //根据标的名称查询出符合要求的标的
                    $loadIds = (new Query())
                        ->select('id')
                        ->from("$p")
                        ->where(['title' => $multiName])
                        ->indexBy('id')
                        ->column();
                    $selectNoteIds = CreditNote::find()
                        ->select('id')
                        ->where(['isClosed' => false])
                        ->andWhere(['in', 'loan_id', $loadIds])
                        ->indexBy('id')
                        ->column();
                }
                if (empty($selectNoteIds)) {
                    $isShowPop = true;
                }
                $default = false;
            }
        } elseif ($selectType == 3) {
            $default = true;
            //查询方式3 根据利率等条件进行转让筛选查询
            if (!is_null($isDiscount)
                && !is_null($expireStart) && !is_null($expireEnd)
                && !is_null($yieldRateStart) && !is_null($yieldRateEnd)
            ) {
                //查询符合转让要求的转让标的（转让中是否有折让率的）
                $creditQuery = CreditNote::find()
                    ->select('id,loan_id')
                    ->where(['isClosed' => false]);
                if ($isDiscount) {
                    $creditQuery->andWhere(['>', 'discountRate' , 0]);
                } else {
                    $creditQuery->andWhere(['discountRate' => 0]);
                }
                $credit = $creditQuery->asArray()->all();
                $ids = [];//符合要求的标的id
                foreach ($credit as $creditRecord) {
                    $ids[] = $creditRecord['loan_id'];
                    $selectNoteIds[] = $creditRecord['id'];
                }

                //根据剩余期限查询符合要求的标的
                $finishStart = strtotime("$expireStart day");
                $finishEnd = $expireEnd === 0 ? 0 : strtotime("$expireEnd day");
                $ids = array_unique($ids);
                //查询出符合要求的标的
                $loadIdsQuery = (new Query())
                    ->select('id')
                    ->from("$p")
                    ->where(['in', 'id', $ids])
                    ->andWhere(['>=', 'finish_date', $finishStart])
                    ->andWhere(['>=', 'yield_rate', $yieldRateStart]);
                if ($finishEnd > 0) {
                    $loadIdsQuery->andWhere(['<', 'finish_date', $finishEnd]);
                }
                if ($yieldRateEnd > 0) {
                    $loadIdsQuery->andWhere(['<', 'yield_rate', $yieldRateEnd]);
                }
                $loadIds = $loadIdsQuery->all();
                if (!empty($loadIds) && !empty($selectNoteIds)) {
                    foreach ($loadIds as $v) {
                        $array[] = $v['id'];
                    }
                } else {
                    $isShowPop = true;
                }
                $default = false;
            }
        }
        if ($selectType == 1 || $default) {
            //查询方式1 默认之前展示规则：展示给用户特定转让标的
            //获得所有可见的转让的id
            $userId = null === $user ? null : $user->id;
            $noteIds = CreditNote::getVisibleTradingIds($userId);
            if (null !== $user && $user->getJGMoney() < 50000) {
                $noteIds = [];
                $notLoanIds = OnlineProduct::find()
                    ->select('id')
                    ->where(['!=', 'cid', 3])
                    ->column();
            }
        }
        $notes = [];
        $tp = 0;
        $txClient = Yii::$container->get('txClient');
        $response = $txClient->post('credit-note/list', [
            'page' => $page,
            'page_size' => 5,
            'isCanceled' => false,
            'loans' => $array,
            'noteIds' => $noteIds,
            'selectNoteIds' => $selectNoteIds,
            'notLoanIds' => $notLoanIds,
        ]);
        if (null !== $response) {
            $user = Yii::$app->user->getIdentity();
            if (!is_null($user)) {
                $userIn = UserInfo::findOne(['user_id' => $user->id]);
            }

            $notes = $response['data'];

            foreach ($notes as $key => $note) {
                $loan_id = (int) $note['loan_id'];
                $order_id = (int) $note['order_id'];

                $notes[$key]['loan'] = OnlineProduct::findOne($loan_id);
                $notes[$key]['order'] = OnlineOrder::findOne($order_id);
            }

            $tp = ceil($response['total_count'] / $response['page_size']);
            $header = [
                'count' => $response['total_count'],
                'size' => $response['page_size'],
                'tp' => $tp,
                'cp' => $response['page'],
            ];
            $code = ($page > $tp) ? 1 : 0;
            $message = ($page > $tp) ? '数据错误' : '消息返回';
        }

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/views/licai/_more_note.php', ['notes' => $notes]);

            return [
                'header' => $header,
                'code' => $code,
                'message' => $message,
                'notes' => $notes,
                'html' => $html,
                'isShowPop' => $isShowPop,
            ];
        }

        return $this->render('notes', [
            'notes' => $notes,
            'tp' => $tp,
            'selectSet' => $selectSet,
        ]);
    }
}
