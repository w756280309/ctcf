<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-4-17
 * Time: 上午9:48
 */
namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\helpers\HttpHelper;
use common\models\thirdparty\Channel;
use common\models\user\User;
use common\utils\SecurityUtils;
use Njq\Crypto;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class NjqController extends Controller
{
    use HelpersTrait;
    /**
     * 南金中心理财列表
     * @param int $page
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionLoanList($page = 1)
    {
        throw $this->ex404();   //关闭南金中心理财列表
        $user = Yii::$app->user->getIdentity();

        if (empty($user) || !$user->isShowNjq) {    //不允许不符合条件的用户直接访问
            throw $this->ex404();
        }
        $pageSize = 10;
        $count = 0;
        $crypto = new Crypto();
        $data = [
            'page' => $page,
            'pageSize' => $pageSize,
        ];
        $signData = $crypto->sign($data);
        $res = HttpHelper::doGet(Yii::$app->params['njq']['baseUri'] . 'product/list?' . http_build_query($signData));
        // todo 请求无结果
        if ($res) {
            $res = json_decode($res, true);
        }
        if ($res['code'] == '2000') {
            $loans = $res['data']['loans'];
            $count = $res['data']['total'];
        } else {
            $loans = [];
        }
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        return $this->render('list', [
            'loans' => $loans,
            'pages' => $pages,
            ]);
    }

    /**
     * 生成免登url
     * todo 与M端统一
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionConnect()
    {
        $redirect = Yii::$app->request->get('redirect');
        $user = Yii::$app->user->getIdentity();
        $crypto = new Crypto();

        //不允许不符合条件的用户直接访问
        if (empty($user) || !$user->isShowNjq) {
            throw $this->ex404();
        }

        if (null !== $redirect) {
            $redirect = Yii::$app->params['njq']['host_pc'] . $redirect;
        }

        if (is_null($user->channel)) {
            //todo 注册南金中心失败待处理
            $uid = $crypto->signUp($user);
        } else {
            $uid = $user->channel->thirdPartyUser_id;
        }
        if (!$uid) {
            throw $this->ex404();
        }
        $data = [
            'uid' => $uid,
            'device' => 'desktop',
            'redirect' => $redirect,
        ];

        $signData = $crypto->sign($data);
        unset($signData['appSecret']);

        return $this->redirect(Yii::$app->params['njq']['baseUri'] . 'user/account/connect?' . http_build_query($signData));
    }
}