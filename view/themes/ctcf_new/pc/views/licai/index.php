<?php

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\widgets\Pager;
use common\view\LoanHelper;
use frontend\assets\CtcfFrontAsset;

$this->title = '我要理财';

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/financial.min.css?v=1.1', ['depends' => CtcfFrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/pagination.min.css', ['depends' => CtcfFrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'ctcf/js/jquery.pagination.js', ['depends' => CtcfFrontAsset::class, 'position' => 1]);

$action = Yii::$app->controller->action->getUniqueId();

?>

<div class="ctcf-container">
    <div class="main">
        <?php if (Yii::$app->params['feature_credit_note_on']) {  ?>
            <ul class="product-nav clear-fix fz18">
                <li class="lf <?= 'licai/index' === $action ? 'active-nav-product' : '' ?>"><a href='/licai/'>理财列表</a></li>
                <li class="lf <?= 'licai/notes' === $action ? 'active-nav-product' : '' ?>"><a href="/licai/notes">转让列表</a></li>
            </ul>
        <?php } ?>
        <div class="financial-product-list fz-gray">
            <ul class="financial-list">
                <!--预告期-->
                <?php foreach ($loans as $key => $val) : ?>
                <li class="financial-preview-period">
                    <a class="fz-gray" target="_blank" href="/deal/deal/detail?sn=<?= $val->sn ?>">
                        <div class="product-top-part">
                            <!--新手专享角标-->
                            <?php if ($val->is_xs) { ?>
                                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/sup_icon.png" alt="新手专享">
                            <?php } ?>
                            <div class="product-title clear-fix">
                                <h5 class="fz18 product-top-title overflow"><?= $val->title ?></h5>
                                <?php if (!empty($val->tags) || $val->pointsMultiple > 1) : ?>
                                        <?= $this->renderFile("@common/views/tags.php", ['loan' => $val]) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-bottom-part clear-fix">
                            <div class="interest-rate">
                                <div class="rate-top-part fz28 fz-orange-strong">
                                    <span class="fz42 "><?= LoanHelper::getDealRate($val) ?><?php if (!empty($val->jiaxi)) { ?><i class="add-rate-percentage fz16">+<?= StringUtils::amountFormat2($val->jiaxi) ?>%</i><?php } ?></span>%
                                </div>
                                <div class="rate-bottom-part fz14">预期年化收益率</div>
                            </div>
                            <div class="term-rate">
                                <div class="term-top-part fz18 fz-black">
                                    <span class="fz30"><?php $ex = $val->getDuration(); ?><?= $ex['value']?></span><?= $ex['unit'] ?>
                                </div>
                                <div class="term-bottom-part fz14">项目期限</div>
                            </div>
                            <div class="mode-style fz14">
                                <div class="mode-top-part">起投金额<span class="fz14 fz-black"><?= StringUtils::amountFormat2($val->start_money) ?>元</span></div>
                                <div class="mode-bottom-part">计息方式<span class="fz14 fz-black"><?= Yii::$app->params['refund_method'][$val->refund_method] ?></span></div>
                            </div>
                            <div class="raise-mode">
                                <div class="raise-top-part">募集总额<span class="fz14 fz-black"><?= StringUtils::amountFormat1('{amount}{unit}', $val->money) ?></span></div>
                                <div class="raise-bottom-part">剩余可投<span class="fz14 fz-black"><?= ($val->status == 1) ? (StringUtils::amountFormat1('{amount}{unit}', $val->money)) : StringUtils::amountFormat2($val->getLoanBalance()).'元' ?></span>
                                    <div class="speed-raise">
                                        <!--进度条-->
                                        <i style="width: <?= $val->getProgressForDisplay() ?>%;"></i>
                                    </div>
                                    <div class="speed-raise-number fz14"><?= $val->getProgressForDisplay()?>%</div>
                                </div>
                            </div>
                            <div class="btn-check">
                                <?php if (OnlineProduct::STATUS_PRE === $val->status) { ?>
                                    <div class="btn-light-orange">预告期</div>
                                <?php } elseif (OnlineProduct::STATUS_NOW === $val->status) { ?>
                                    <div class="btn-orange">立即投资</div>
                                <?php } elseif (OnlineProduct::STATUS_FULL === $val->status || OnlineProduct::STATUS_FOUND === $val->status) { ?>
                                    <div class="btn-gray">已售罄</div>
                                <?php } elseif (OnlineProduct::STATUS_HUAN === $val->status) { ?>
                                    <div class="btn-light-orange">收益中</div>
                                <?php } elseif (OnlineProduct::STATUS_OVER === $val->status) { ?>
                                    <div class="btn-gray">已还清</div>
                                <?php } ?>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <div id="prjlistPage" style="">
                <div id="projectListPagination" class="pagination ">

                </div>
            </div>
        </div>
        <script>
            //初始化函数 参数num_entries为数据总量  pageSize为每一页显示的条数
            function initPagination (num_entries) {
                var pageIndex1 = 0;     //页面索引初始值
                var pageSize = 5;     //每页显示条数初始化，修改显示条数，
                $("#projectListPagination").pagination(num_entries, {
                    num_edge_entries: 1,        //两侧首尾分页条目数
                    num_display_entries: 4,    //连续分页主体部分分页条目数
                    callback: PageCallback,
                    items_per_page: pageSize,  //显示条数
                    current_page: pageIndex1,   //当前页索引
                    prev_show: false,    //是否总是显示上一页按钮
                    prev_text: '上一页',       //下一页按钮里text
                    next_text: '下一页'       //下一页按钮里text
                });
            }
            //翻页调用
            function PageCallback(pageindex1, jq){
//        InitTable(pageindex1,0);
            }
            //请求数据
            //    InitTable(1,1);
            //    function InitTable(pageindex1, flag){
            //        var page_Num = parseInt(pageindex1)+1;
            //
            //        $.ajax({
            //            type: "get",
            //            url: '',
            //            data:{},
            //            dataType: 'json',
            //            success:function (data) {
            //请求成功后填入数据
            //            }
            //        })
            //
            //    }
            initPagination(100)
        </script>



    </div>
</div>
