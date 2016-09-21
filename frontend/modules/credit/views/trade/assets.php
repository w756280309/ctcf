<?php

use frontend\assets\FrontAsset;
use yii\web\JqueryAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/usercenter.css', ['depends' => FrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/transfering.css', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/transfering.js', ['depends' => JqueryAsset::class]);
?>
<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->renderFile('@frontend/views/left.php') ?>
        </div>
        <div class="rightcontent">
            <div class="myCoupon-box">
                <div class="myCoupon-header">
                    <div class="myCoupon-header-icon"></div>
                    <span class="myCoupon-header-font">我的转让</span>
                </div>
                <div class="myCoupon-content">
                    <div class="list-single">
                        <a class="a_first  select" href="">可转让的项目</a>
                        <a class="a_second" href="">转让中的项目</a>
                        <a class="a_third" href="">已转让的项目</a>
                    </div>
                    <div class="display_number">
                        <p class="p_left">待转让金额：<span>13,066.00</span>元</p>
                        <p class="p_right">总计：<span>20</span>笔</p>
                    </div>
                    <table>
                        <tr>
                            <th class="text-first" width="120">项目名称</th>
                            <th class="text-align-ct" width="100">剩余期限</th>
                            <th class="text-third" width="110">预期年化</th>
                            <th class="text-third" width="130">投资金额(元)</th>
                            <th class="text-align-ct" width="110">还款计划</th>
                            <th class="text-third" width="60">合同</th>
                            <th class="text-align-ct" width="60">操作</th>
                        </tr>
                    </table>
                    <!----------------------------无数据结束------------------------------->
                    <div class="table-kong"></div>
                    <div class="table-kong"></div>
                    <p class="without-font">暂无可转让的项目</p>
                    <a class="link-tender" href="#">立即投资</a>
                    <!----------------------------无数据结束------------------------------->
                </div>
                <!--<div class="pagination-content">-->
                    <!--<ul class="pagination">-->
                        <!--<li class="prev disabled"><span>上一页</span></li>-->
                        <!--<li class="active"><a href="/licai/index?page=1&amp;per-page=10" data-page="0">1</a></li>-->
                        <!--<li><a href="/licai/index?page=2&amp;per-page=10" data-page="1">2</a></li>-->
                        <!--<li><a href="/licai/index?page=3&amp;per-page=10" data-page="2">3</a></li>-->
                        <!--<li><a href="/licai/index?page=4&amp;per-page=10" data-page="3">4</a></li>-->
                        <!--<li><a href="/licai/index?page=5&amp;per-page=10" data-page="4">5</a></li>-->
                        <!--<li><a class="ellipsis">...</a></li>-->
                        <!--<li><a href="/licai/index?page=23&amp;per-page=10" data-page="22">23</a></li>-->
                        <!--<li class="next"><a href="/licai/index?page=2&amp;per-page=10" data-page="1">下一页</a></li>-->
                    <!--</ul>-->
                <!--</div>-->
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
