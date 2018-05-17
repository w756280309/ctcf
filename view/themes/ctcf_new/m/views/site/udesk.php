<?php

use common\view\UdeskWebIMHelper;
use yii\web\JqueryAsset;

$this->title="服务中心";
UdeskWebIMHelper::init($this);
$this->registerJsFile(FE_BASE_URI . 'libs/lib.flexible3.js', ['depends' => JqueryAsset::class, 'position' => 1]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/udesk/index.min.css">
<div class="flex-content">
    <dl class="clearfix" >
        <dt class="lf" id="btn_udesk_im">
            <a href="javascript:void(0)">
                <div></div>
                <div>立即咨询</div>
                <div>9:00-17:30(工作日时间)</div>
            </a>
        </dt>
        <dd class="rg">
            <a href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>">
                <div></div>
                <div>立即拨打</div>
                <div>8:30-20:00</div>
            </a>
        </dd>
    </dl>
    <ul class="question">
        <li>常见问题</li>
        <li>
            <p>Q1:如何实现大额充值？</p>
            <p>A1:登录楚天财富PC端<br>https://www.hbctcf.com/ ，进入账户中心-充值，选择个人网银进行充值。</p>
        </li>
        <li>
            <p>Q2:如何修改登录密码？</p>
            <p>A2:进入账户中心-设置-安全中心-修改登录密码，进行修改。如果忘记原密码导致不能修改，可以退出登录，点击忘记密码来重置登录密码。</p>
        </li>
        <li>
            <p>Q3:如何修改/重置交易密码？</p>
            <p>A3:进入账户中心-设置-安全中心-修改交易密码，来设置。</p>
        </li>
        <li><a href="/site/help">点击进入帮助中心查看更多问题</a></li>
    </ul>
</div>
