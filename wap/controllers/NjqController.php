<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-4-17
 * Time: 上午9:18
 */
namespace app\controllers;

use common\controllers\HelpersTrait;
use common\helpers\HttpHelper;
use Njq\Crypto;
use yii\web\Controller;
use Yii;

class NjqController extends Controller
{
    use HelpersTrait;
    /**
     * 获取南金中心理财列表
     */
    public function actionLoanList($page = 1)
    {
        $user = Yii::$app->user->getIdentity();
        if (empty($user) || !$user->isShowNjq) {    //不允许不符合条件的用户直接访问
            throw $this->ex404();
        }
        $pageSize = 5;  //每页5条记录
        $data = [
            'page' => $page,
            'pageSize' => $pageSize,
        ];
        $deals = [];
        $count = 0; //默认0条记录
        // @todo 请求失败如何处理
        try {
            $crypto = new Crypto();
            $signData = $crypto->sign($data);
            $res = HttpHelper::doGet(Yii::$app->params['njq']['baseUri'] . 'product/list?' . http_build_query($signData));
            $res = json_decode($res, true);
            if ($res['code'] == '2000') {
                $deals = $res['data']['loans'];
                $count = $res['data']['total'];
            }
        } catch (\Exception $ex) {
            Yii::info('请求南金中心理财列表失败,，原因：' . $ex->getMessage());
        }
        //用于分页
        $tp = ceil($count / $pageSize);
        $code = ($page > $tp) ? 1 : 0;
        $header = [
            'count' => intval($count),
            'size' => $pageSize,
            'tp' => $tp,
            'cp' => intval($page),
        ];
        if (Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';
            $html = $this->renderFile('@view/themes/wdjf/m/views/njq/_more.php', ['deals' => $deals, 'header' => $header]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
        }
        return $this->render('loan_list', [
            'deals' => $deals,
            'header' => $header,
        ]);

    }

    /**
     * 生成免登Url
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionConnect()
    {
        $user = Yii::$app->user->getIdentity();
        if (empty($user) || !$user->isShowNjq) {    //不允许不符合条件的用户直接访问
            throw $this->ex404();
        }
        $redirect = Yii::$app->request->get('redirect');
        $redirect = !empty($redirect) ? Yii::$app->params['njq']['host_m'] . $redirect : null;
        $crypto = new Crypto();
        if (empty($user) || !$user->isShowNjq) {    //不允许不符合条件的用户直接访问
            throw $this->ex404();
        }
        if (is_null($user->channel)) {
            // @todo 注册南金中心失败如何处理
            $uid = $crypto->signUp($user);
        } else {
            $uid = $user->channel->thirdPartyUser_id;
        }
        if (!$uid) {
            throw $this->ex404();
        }
        $data = [
            'uid' => $uid,
            'device' => 'mobile',
            'redirect' => $redirect,
        ];
        $signData = $crypto->sign($data);
        return $this->redirect(Yii::$app->params['njq']['baseUri'] . 'user/account/connect?' . http_build_query($signData));
    }
}