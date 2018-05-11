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
            try {
                $uid = $crypto->signUp($user);
            } catch (\Exception $ex) {
                return $this->redirect('/njq/fail?code='.$ex->getCode());
            }
        } else {
            $uid = $user->channel->thirdPartyUser_id;
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

    /**
     * 授权登录失败
     */
    public function actionFail()
    {
        $code = (string) Yii::$app->request->get('code');
        if ('99999' === $code) {
            $message = '您在南金中心开户的手机号和身份证与温都金服不一致，无法进行账号授权登录。';
        } else {
            $message = '授权登录服务请求超时。';
        }

        return $this->render('fail', [
            'message' => $message,
        ]);
    }
}