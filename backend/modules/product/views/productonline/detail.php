<?php

use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">

        <div class="span12">
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/news/">贷款管理</a> 
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/news/news/index">项目列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/news/news/index">投资记录</a>
                    <i class="icon-angle-right"></i>
                </li>
            </ul>
            <ul class="breadcrumb zuoyoukuan">
                <li>已募集金额：170000.00元</li>
                <li>&nbsp;</li>
                <li>剩余可投金额：1700.00元</li>
                <li>&nbsp;</li>
                <li>已投资人数：15人</li>
                <li>&nbsp;</li>
                <li>剩余时间：1天15小时6分</li>
            </ul>
        </div>
    </div>
    <!--search start-->
        <div class="portlet-body">
            <form action="/product/productonline/search" method="get" target="_self">
            <table class="table">
                <tbody>
                <tr>
                    <td>
                        <span class="title">真实姓名</span>
                    </td>
                    <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='name' value="<?=$_GET['name']?>"  placeholder="输入姓名查询"/></td>
                    <td>
                        <span class="title">手机号码</span>
                    </td>
                    <td><input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='tel' value="<?=$_GET['name']?>"  placeholder="输入手机号码"/></td>
                    
                    <td colspan="6" align="right" style=" text-align: right">
                        <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索</button>
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
                                <th><input class="chooseall" type='checkbox'></th>
                                <th>序号</th>
                                <th>真实姓名</th>
                                <th>手机号</th>
                                <th>投资金额（元）</th>
                                <th>投标时间</th>
                                <th>状态</th>
                            </tr>
                    </thead>
                    <tbody>
                            <?php foreach ($info as $key => $val) : ?>
                                <tr>
                                        <td>
                                             <input class="choice" type='checkbox' name='choose[]' value='<?= $val['id'] ?>'>
                                        </td>
                                        <td><?= $val['id'] ?></td>
                                        <td><?= $val['real_name'] ?></td>
                                        <td><?= $val['mobile'] ?></td>
                                        <td><?= $val['order_money'] ?></td>
                                        <td><?= $val['order_time'] ?></td>
                                        <td><?= $val['status']==0?"未上线":'上线' ?></td>
                                        <td>
                                        <center>
                                            <a href="/product/productonline/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a> |
                                            <a href="" class="btn mini red ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-minus-sign"></i>删除</a>
                                            <a href="/product/productonline/detail?id=<?=$val['id']?>" class="btn mini green" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-minus-sign"></i>贷款管理</a>
                                            <?php if ($val['online_status'] == 1 && $val['status'] > 2){ ?>
                                            | <a href="/product/productonline/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 放款审核</a>
                                            <?php } ?>
                                            <?php if ($val['online_status'] == 1 && $val['status'] > 1){ ?>
                                            | <a href="//product/productonline/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 投标记录</a>
                                            <?php } ?>
                                        </center>
                                        </td>
                                </tr>
                                <?php endforeach; ?>   
                        </tbody>
                </table>
                <button class="btn green btn-block" style="width: 100px;float:left;">保存</button>
        </div>
        
    </div>
                                    
        
                                    
</div>
<style type="text/css">
    .breadcrumb li{
        margin-top: 10px;
        margin-bottom: 10px;
    }
   
</style>

<script type="text/javascript">
    $(function(){
            $(".chooseall").click(function(){
                var isChecked = $(this).prop("checked");
                alert("aaaaaaaaaaa");
                $("input[name='choose[]']").prop("checked", isChecked);
                alert("bbbbbbbbb");
            });
    })
</script> 
<?php $this->endBlock(); ?>

