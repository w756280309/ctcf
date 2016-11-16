<?php

    use yii\widgets\LinkPager;
    use common\utils\StringUtils;

    $this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">
                <h3 class="page-title">
                    贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
                </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/product/productonline/list">贷款管理</a> 
                        <i class="icon-angle-right"></i>
                    </li>
                    
                    <li>
                        <a href="/product/productonline/list">项目列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0)">投资记录</a>
                    </li>
            </ul>
        </div>
        
         <div class="portlet-body">
            <table class="table">
                    <tr>
                        <td>
                            <span class="title">已募集金额：<?= $moneyTotal ? $moneyTotal : 0 ?>元</span>
                        </td>
                        <td>
                            <span class="title">剩余可投金额：<?= $shengyuKetou ?>元</span>
                        </td>
                        <td>
                            <span class="title">已投资人数：<?= $renshu ?></span></td>
                        <td>
                            <span class="title">募捐时间：<?= $mujuanTime ?></span></td>
                        </td>
                    </tr>
            </table>
        </div>
       
        
        <!--search start-->
        <div class="portlet-body">
            <form action="/order/onlineorder/list?id=<?= $model->online_pid ?>" method="get" target="_self">
                <table class="table">
                    <input type="hidden" name="id" value="<?= Yii::$app->request->get('id') ?>"
                    <tbody>
                        <tr>
                            <td>
                                <span class="title">真实姓名</span>
                            </td>
                            <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='username' value="<?= $_GET['username'] ?>"  placeholder="真实姓名"/></td>
                            <td><span class="title">手机号</span></td>
                            <td>
                                <input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='mobile' value="<?= $_GET['mobile'] ?>"  placeholder="手机号"/>
                            </td>
                            <td colspan="6" align="right" style=" text-align: right">
                                <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right">
                                <a href="/order/onlineorder/export?id=<?= $id ?>" class="btn btn-primary" style="display: block;float: right;margin-right: 17px;">导出投标记录</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        
        <!--search end -->
        
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>真实姓名</th>
                        <th>手机号</th>
                        <th>投资金额（元）</th>
                        <th>客户年化率（%）</th>
                        <th>投资时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= $val['username'] ?></td>                   
                        <td><?= $val['mobile'] ?></td>                   
                        <td><?= $val['order_money'] ?></td>
                        <td><?= StringUtils::amountFormat2(bcmul($val['yield_rate'], 100, 2)); ?></td>
                        <td><?= date('Y-m-d H:i:s',$val['created_at']) ?></td>
                        <td>
                            <?php
                                $status = (int) $val['status'];
                                if (0 === $status) {
                                    echo "投标失败";
                                } elseif (1 === $status){
                                    echo "投标成功";
                                } elseif (2 === $status) {
                                    echo "撤标";
                                } else {
                                    echo "无效";
                                }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>   
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div> 
    </div>
                                    
</div>
<?php $this->endBlock(); ?>

