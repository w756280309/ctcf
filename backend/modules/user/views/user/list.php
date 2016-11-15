<?php
use yii\widgets\LinkPager;
use common\models\user\User;
?>

<?php $this->beginBlock('blockmain'); ?>
<style>
    .dropDownMenu {
        list-style: none;
        padding: 5px;
        margin: 0px;
        display: none;
        position: absolute;
    }
    .dropDownMenu li {
        height: 30px;
        line-height: 30px;
    }
    .dropDownMenu li:hover {
        background: #eeeeff;
    }
    .note {
        font-size: 1rem;
        color: #666;
        text-align: center;
        margin: 50px 0;
    }
</style>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                        会员管理 <small>会员管理模块【主要包含投资会员和融资会员的管理】</small>
                        <?php if($category==User::USER_TYPE_ORG){?>
                        <a href="/user/user/add" id="sample_editable_1_new" class="btn green" style="float: right;">
                        <i class="icon-plus"></i> 添加新融资客户
                        </a>
                        <?php }?>
                </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/<?=$category==1?"listt":"listr"?>">会员管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <?php if($category==User::USER_TYPE_PERSONAL){?>
                        <li>
                            <a href="/user/user/listt">投资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }else{?>
                        <li>
                            <a href="/user/user/listr">融资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <?php }?>
                        <li>
                            <a href="javascript:void(0);">会员列表</a>
                        </li>
            </ul>
        </div>

        <!--search start-->
        <div class="portlet-body">
            <form action="/user/user/<?=$category==1?"listt":"listr"?>" method="get" target="_self">
                <table class="table">
                    <tbody>
                        <tr>
                            <?php if($category==User::USER_TYPE_PERSONAL){ ?>
                                <td  style="margin-bottom: 0px;width:60px">
                                    <span class="title">真实姓名</span>
                                </td>
                                <td  style="margin-bottom: 0px;width:200px"><input type="text" name='name' value="<?= Yii::$app->request->get('name') ?>"  placeholder="真实姓名"/></td>
                                <td  style="margin-bottom: 0px;width:60px"><span class="title">手机号</span></td>
                                <td style="margin-bottom: 0px;width:250px">
                                    <input type="text"  name='mobile' value="<?= Yii::$app->request->get('mobile') ?>"  placeholder="手机号"/>
                                </td>
                                <td style="margin-bottom: 0px;width:100px">
                                    <div class="dropDownBox">
                                        <span class="btn btn-default menuBtn">
                                            其他条件
                                            <span class="caret"></span>
                                        </span>
                                        <ul class="dropDownMenu">
                                            <li>
                                                未投资时长（天）: <input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:50px" name='noInvestDays' value="<?= Yii::$app->request->get('noInvestDays') ?>"  placeholder="天数"/>
                                            </li>
                                            <li>
                                                可用余额（元）: <input type="text" name="balance" value="<?= Yii::$app->request->get('balance') ?: 0 ?>" style="margin-bottom: 0px;width:55px"/>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            <?php }else{?>
                                <td>
                                    <span class="title">企业名称</span>
                                </td>
                                <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='name' value="<?= Yii::$app->request->get('name') ?>"  placeholder="企业名称"/></td>
                            <?php }?>


                            <td><div align="right" class="search-btn">
                                <button type='submit' class="btn blue btn-block button-search">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                            </div></td>

                        </tr>
                        <!--添加导出投资会员信息按钮-->
                        <?php if($category === User::USER_TYPE_PERSONAL) { ?>
                        <tr>
                            <td colspan="8">
                                <div align="right" class="search-btn">
                                    <a class="btn green btn-block" style="width: 140px;" href="/user/user/lenderstats">导出投资会员信息</a>
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
                        <th>会员ID</th>
                <?php if($category==User::USER_TYPE_PERSONAL){?>
                        <th>手机号</th>
                        <th>真实姓名</th>
                <?php }else{?>
                        <th>企业名称</th>
                <?php }?>
                        <th>注册时间</th>
                        <th>可用余额（元）</th>
                <?php if($category==User::USER_TYPE_PERSONAL){?>
                        <th>未投资时长（天）</th>
                        <th>最后一次购买金额</th>
                        <th>联动状态</th>
                <?php }?>
                        <th><center>操作</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['usercode'] ?></td>
                <?php if($category==User::USER_TYPE_PERSONAL){?>
                        <td><?= $val['mobile'] ?></td>
                        <td><?= $val['real_name']?'<a href="/user/user/detail?id='.$val['id'].'&type='.$category.'">'.$val['real_name'].'</a>':"---" ?></td>
                <?php }else{?>
                        <td><?= $val['org_name'] ?></td>
                <?php }?>
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td><?= number_format(($category==User::USER_TYPE_PERSONAL)?($val->lendAccount['available_balance']):($val->borrowAccount['available_balance']),2) ?></td>
                        <?php if($category==User::USER_TYPE_PERSONAL){?>
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
                        <td>
                            <?= $val->info ? number_format($val->info->lastInvestAmount, 2) : 0?>
                        </td>
                        <td>
                            <button class="btn btn-primary get_order_status" uid="<?= $val['id'] ?>">查询联动状态</button>
                        </td>
                        <?php }?>
                        <td>
                        <center>
                             <?php if($category==User::USER_TYPE_PERSONAL){?>
                                <a href="/user/user/detail?id=<?= $val['id'] ?>&type=<?=$category;?>" class="btn mini green"><i class="icon-edit"></i> 查看用户详情</a>
                             <?php }else{?>
                                <a href="/user/user/edit?id=<?= $val['id'] ?>&type=<?=$category;?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                <a href="/user/user/detail?id=<?= $val['id'] ?>&type=<?=$category;?>" class="btn mini green"><i class="icon-edit"></i> 查看用户详情</a>
                            <?php }?>
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

