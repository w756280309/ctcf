<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;


class InfoController extends Controller
{
    private $config = [
        'success' => [
            'tuoguan' => [
                'title' => '结果',
                'mainTitle' => '恭喜您，资金托管账户开通成功！',
                'firstFuTitle' => '<em class="em">支付密码将会以短信的形式发送到您的手机上</em>，请注意查收并妥善保存。<br>支付密码为6位随机数，可根据短信内容修改密码。',
                'requireJump' => true,
                'linkType' => 1,
                'jumpReferWords' => '下一步',
            ],
            'touzi' => [
                'title' => '购买结果',
                'mainTitle' => '恭喜您，购买成功！',
                'firstFuTitle' => '页面将在<em class="em_time">5</em>秒后自动关闭，<a href="javascript:;" class="a-close">点击这里</a>立即关闭!',
                'requireJump' => true,
                'linkType' => 2,
                'jumpReferWords' => '',
            ],
            'bangka' => [
                'title' => '绑卡结果',
                'mainTitle' => '恭喜您，银行卡绑定成功！',
                'firstFuTitle' => '页面将在<em class="em_time">5</em>秒后自动关闭，<a href="javascript:;" class="a-close">点击这里</a>立即关闭!',
                'requireJump' => true,
                'linkType' => 2,
                'jumpReferWords' => '',
            ],
            'huanka' => [
                'title' => '换卡结果',
                'mainTitle' => '换卡申请已成功提交！',
                'firstFuTitle' => '页面将在<em class="em_time">5</em>秒后自动关闭，<a href="javascript:;" class="a-close">点击这里</a>立即关闭!',
                'requireJump' => true,
                'linkType' => 2,
                'jumpReferWords' => '',
            ],
        ],
        'fail' => [
            'tuoguan' => [
                'title' => '结果',
                'mainTitle' => '资金托管账户开通失败！',
                'firstFuTitle' => '如有疑问，请联系客服，电话：400-101-5151(09:00-20:00)',
                'requireJump' => true,
                'linkType' => 1,
                'jumpReferWords' => '重新开通',
            ],
            'touzi' => [
                'title' => '购买结果',
                'mainTitle' => '购买失败！',
                'firstFuTitle' => '如有疑问，请联系客服，电话：400-101-5151(09:00-20:00)',
                'requireJump' => false,
                'linkType' => 0,
                'jumpReferWords' => '',
            ],
            'bangka' => [
                'title' => '绑卡结果',
                'mainTitle' => '银行卡绑定失败！',
                'firstFuTitle' => '如有疑问，请联系客服，电话：400-101-5151(09:00-20:00)',
                'requireJump' => false,
                'linkType' => 0,
                'jumpReferWords' => '',
            ],
            'huanka' => [
                'title' => '换卡结果',
                'mainTitle' => '换卡申请提交失败！',
                'firstFuTitle' => '如有疑问，请联系客服，电话：400-101-5151(09:00-20:00)',
                'requireJump' => false,
                'linkType' => 0,
                'jumpReferWords' => '',
            ],
        ],
    ];

    private $sources = [
        'tuoguan',
        'touzi',
        'bangka',
        'huanka',
    ];

    public function actionSuccess()
    {
        $source = \Yii::$app->request->get("source");
        $this->paramValid($source);
        $info = $this->config['success'][$source];
        $info['jumpUrl'] = \Yii::$app->request->get("jumpUrl") != '' ? \Yii::$app->request->get("jumpUrl") : '';
        return $this->render("success", ['info' => $info]);
    }

    public function actionFail()
    {
        $source = \Yii::$app->request->get("source");
        $this->paramValid($source);
        $info = $this->config['fail'][$source];
        $info['jumpUrl'] = \Yii::$app->request->get("jumpUrl") ? \Yii::$app->request->get("jumpUrl") : '';
        return $this->render("fail", ['info' => $info]);
    }

    private function paramValid($source)
    {
        if (!in_array($source, $this->sources)) {
            return $this->goHome();
        }
    }
}
