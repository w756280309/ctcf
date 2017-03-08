<?php

use common\models\user\User;
use common\utils\StringUtils;
use yii\widgets\LinkPager;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

$isPersonal = $category === User::USER_TYPE_PERSONAL;

?>

<?php $this->beginBlock('blockmain'); ?>
<style>
    .search_form td input {
        margin: 0px;
    }
</style>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>会员管理模块【主要包含投资会员和融资会员的管理】</small>
                <?php if (!$isPersonal) { ?>
                    <a href="/user/user/add" id="sample_editable_1_new" class="btn green" style="float: right;">
                        <i class="icon-plus"></i> 添加新融资客户
                    </a>
                <?php } ?>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/<?= $isPersonal ? 'listt' : 'listr' ?>">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <?php if ($isPersonal) { ?>
                    <li>
                        <a href="/user/user/listt">投资会员</a>
                        <i class="icon-angle-right"></i>
                    </li>
                <?php } else { ?>
                    <li>
                        <a href="/user/user/listr">融资会员</a>
                        <i class="icon-angle-right"></i>
                    </li>
                <?php } ?>
                <li>
                    <a href="javascript:void(0);">会员列表</a>
                </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/user/user/<?= $isPersonal ? 'listt' : 'listr' ?>" method="get" target="_self">
                <input type="hidden" name="search" value="user_list">
                <table class="table search_form">
                    <tbody>
                        <?php if ($isPersonal) { ?>
                            <tr>
                                <td>
                                    <span class="title">真实姓名</span>
                                    <input type="text" name='name' value="<?= Yii::$app->request->get('name') ?>" placeholder="真实姓名" class="m-wrap span4"/>
                                </td>
                                <td>
                                    <span class="title">手机账号</span>
                                    <input type="text" name='mobile' value="<?= Yii::$app->request->get('mobile') ?>" placeholder="手机号" class="m-wrap span8"/>
                                </td>
                                <td>
                                    <span class="title">未投时长</span>
                                    <input type="text" class="m-wrap span4"  name='noInvestDaysMin' value="<?= Yii::$app->request->get('noInvestDaysMin') ?>"  />
                                    <input type="text" class="m-wrap span4"  name='noInvestDaysMax' value="<?= Yii::$app->request->get('noInvestDaysMax') ?>"  />
                                </td>
                                <td>
                                    <span class="title">注册来源</span>
                                    <?php $regContext = Yii::$app->request->get('regContext') ?>
                                    <select name="regContext" class="m-wrap span6">
                                        <option value="">全部</option>
                                        <option <?= $regContext === 'm' ? 'selected' : '' ?> value="m">wap注册</option>
                                        <option <?= $regContext === 'm_intro1611' ? 'selected' : '' ?> value="m_intro1611">wap落地页注册</option>
                                        <option <?= $regContext === 'pc' ? 'selected' : '' ?> value="pc">pc注册</option>
                                        <option <?= $regContext === 'pc_landing' ? 'selected' : '' ?> value="pc_landing">pc落地页注册</option>
                                        <option <?= $regContext === 'other' ? 'selected' : '' ?> value="other">未知</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="title">投资次数</span>
                                    <input type="text" name='investCountMin' value="<?= Yii::$app->request->get('investCountMin') ?>" class="m-wrap span4"/>
                                    <input type="text" name='investCountMax' value="<?= Yii::$app->request->get('investCountMax') ?>" class="m-wrap span4"/>
                                </td>
                                <td>
                                    <span class="title">投资总额</span>
                                    <input type="text" name='investTotalMin' value="<?= Yii::$app->request->get('investTotalMin') ?>" class="m-wrap span4"/>
                                    <input type="text" name='investTotalMax' value="<?= Yii::$app->request->get('investTotalMax') ?>" class="m-wrap span4"/>
                                </td>
                                <td>
                                    <span class="title">可用余额</span>
                                    <input type="text"  class="m-wrap span4" name="balanceMin" value="<?= Yii::$app->request->get('balanceMin') ?>" />
                                    <input type="text"  class="m-wrap span4" name="balanceMax" value="<?= Yii::$app->request->get('balanceMax') ?>" />
                                </td>
                                <td>
                                    <span class="title">注册时间</span>
                                    <input type="text" class="m-wrap span4"  name='regTimeMin' value="<?= Yii::$app->request->get('regTimeMin') ?>"  onclick="WdatePicker()"/>
                                    <input type="text" class="m-wrap span4"  name='regTimeMax' value="<?= Yii::$app->request->get('regTimeMax') ?>"  onclick="WdatePicker()"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="title">已用代金券</span>
                                    <input type="text" class="m-wrap span4"  name='couponAmountMin' value="<?= Yii::$app->request->get('couponAmountMin') ?>"/>
                                    <input type="text" class="m-wrap span4"  name='couponAmountMax' value="<?= Yii::$app->request->get('couponAmountMax') ?>"/>
                                </td>
                                <td colspan="2">
                                    <span class="title">兑吧用户ID</span>
                                    <input type="text" class="m-wrap span8"  name='publicUserId' value="<?= Yii::$app->request->get('publicUserId') ?>"/>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div align="left" class="search-btn">
                                        <button type='submit' class="btn blue btn-block button-search">搜索
                                            <i class="m-icon-swapright m-icon-white"></i></button>
                                    </div>
                                </td>
                                <?php if (Yii::$app->request->get('search')) { ?>
                                    <td colspan="3">
                                        <div align="right" class="search-btn">
                                            <a class="btn green btn-block" style="width: 140px;" href="/user/user/lenderstats?<?= http_build_query(Yii::$app->request->get())?>">导出筛选用户信息</a>
                                        </div>
                                    </td>
                                <?php } else { ?>
                                    <td colspan="3">
                                        <div align="right" class="search-btn">
                                            <a class="btn green btn-block" style="width: 140px;" href="/user/user/export">导出全部用户信息</a>
                                        </div>
                                    </td>
                               <?php }?>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td>
                                    <span class="title">企业名称</span>
                                    <input type="text" class="m-wrap span6" name='name' value="<?= Yii::$app->request->get('name') ?>" placeholder="企业名称" />
                                </td>
                                <td>
                                    <div align="right" class="search-btn">
                                        <button type='submit' class="btn blue btn-block button-search">搜索
                                            <i class="m-icon-swapright m-icon-white"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>

        <!--search end -->
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                <?php if ($isPersonal) { ?>
                        <th>手机号</th>
                        <th>真实姓名</th>
                <?php } else { ?>
                        <th>企业名称</th>
                <?php } ?>
                        <th>注册时间</th>
                        <th class="money">可用余额（元）</th>
                <?php if ($isPersonal) { ?>
                        <th class="money">资产总额</th>
                        <th>未投资时长（天）</th>
                        <th class="money">最后一次购买金额</th>
                        <th>用户等级</th>
                        <th>注册位置</th>
                        <th>联动状态</th>
                <?php }?>
                        <th><center>操作</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                <?php if ($isPersonal) { ?>
                        <td><?= $val['mobile'] ?></td>
                        <td><?= $val['real_name'] ? '<a href="/user/user/detail?id='.$val['id'].'">'.$val['real_name'].'</a>' : '---' ?></td>
                <?php } else { ?>
                        <td><?= $val['org_name'] ?></td>
                <?php }?>
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td class="money"><?= StringUtils::amountFormat3($isPersonal ? $val->lendAccount['available_balance'] : $val->borrowAccount['available_balance']) ?></td>
                        <?php if ($isPersonal) { ?>
                            <td class="money"><?= number_format($val->lendAccount->totalFund, 2)?></td>
                            <td>
                                <?php
                                    $info = $val->info;
                                    if ($info){
                                        $days = (new \DateTime)->diff(new \DateTime($info->lastInvestDate))->days;
                                    } else {
                                        $days = 0;
                                    }
                                    echo $days ;
                                ?>
                            </td>
                            <td class="money">
                                <?= $val->info ? StringUtils::amountFormat3($val->info->lastInvestAmount) : 0 ?>
                            </td>
                            <td>VIP<?= $val->level ?></td>
                            <td><?= $val->regContext ? $val->regContext : '---' ?></td>
                            <td>
                                <button class="btn btn-primary get_order_status" uid="<?= $val['id'] ?>">查询联动状态</button>
                            </td>
                        <?php } ?>
                        <td>
                        <center>
                             <?php if($isPersonal) { ?>
                                <a href="/user/user/detail?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 查看用户详情</a>
                             <?php } else { ?>
                                <a href="/user/user/edit?id=<?= $val['id'] ?>&type=<?= $category ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                <a href="/user/user/detail?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 查看用户详情</a>
                            <?php } ?>
                        </center>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($model)) { ?>
            <div class="note">暂无数据</div>
        <?php } ?>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>

<script>
    $('.get_order_status').bind('click', function () {
        var csrf = '<?= Yii::$app->request->getCsrfToken(); ?>';
        var _this = $(this);
        if (_this.hasClass("isclicked")) {
            return false;
        }
        _this.addClass("isclicked");
        var uid = $(this).attr("uid");
        var xhr = $.ajax({
            type: 'POST',
            url: '/user/user/umpuserinfo?uid='+uid,
            data: {'_csrf': csrf},
            dataType: 'json'
        });

        xhr.done(function(data) {
            _this.removeClass("isclicked");
            if (parseInt(data.code) >= 0) {
                _this.parent().html(data.message);
            } else if (-1 === parseInt(data.code)) {
                _this.html("查询失败，点击重试");
            }
        });

        xhr.fail(function() {
            _this.removeClass("isclicked");
        });
    });
    $('.menuBtn').click(function(){
        var dis = $('.dropDownMenu').css('display');
        if (dis == 'block'){
            $('.dropDownMenu').slideUp();
        } else {
            $('.dropDownMenu').slideDown();
        }
    });
    $('body').mouseup(function(e) {
        var _con = $('.dropDownBox');   // 设置目标区域
        if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
            var dis = $('.dropDownMenu').css('display');
            if (dis == 'block'){
                $('.dropDownMenu').slideUp();
            }
        }
    });
</script>
<?php $this->endBlock(); ?>

