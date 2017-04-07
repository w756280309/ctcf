<?php

namespace frontend\controllers;


class PromoController extends BaseController
{
    public function actionB1(){
        return $this->renderFile('@frontend/views/promo/201606/b1.php');
    }

    public function actionB2() {
        return $this->renderFile('@frontend/views/promo/201606/b2.php');
    }

    /**
     * 活动要放在对应的以年月命名的文件夹下.
     * @param string $name 活动文件名称
     */
    public function actionZadan160708()
    {
        return $this->render('@frontend/views/promo/201607/zadan.php');
    }

    /**
     * 邀请好友活动页
     */
    public function actionInvite160804()
    {
        return $this->render('@frontend/views/promo/201608/invite.php');
    }

    /**
     * 奥运活动页.
     */
    public function actionOlympic160809()
    {
        return $this->render('@frontend/views/promo/201608/olympic.php');
    }

    /**
     * 圣诞砸蛋活动页.
     */
    public function actionZadan161224()
    {
        return $this->render('@frontend/views/promo/201612/zadan.php');
    }

    /**
     * 15亿限时砸金蛋活动页.
     */
    public function actionSmashEgg()
    {
        return $this->render('@frontend/views/promo/201704/egg.php');
    }
}
