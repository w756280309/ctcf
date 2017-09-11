<?php

use common\utils\SecurityUtils;
use common\utils\StringUtils;

    $this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
    $this->title = '投资记录';
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
                             <span class="title">标的名称：<?= $loan->title?></span>
                        </td>
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
                    </tr>
            </table>
        </div>
       
        
        <!--search start-->
        <div class="portlet-body">
            <form action="/order/onlineorder/list?id=<?= $loan->id ?>" method="get" target="_self">
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
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
                'columns' => [
                    [
                        'header' => '序号',
                        'value' => function ($order) {
                            return $order->sn;
                        }
                    ],
                    [
                        'header' => '真实姓名',
                        'value' => function ($order) {
                            return $order->username;
                        }
                    ],
                    [
                        'header' => '手机号',
                        'value' => function ($order) {
                            return SecurityUtils::decrypt($order->user->safeMobile);
                        }
                    ],
                    [
                        'header' => '投资金额（元）',
                        'value' => function ($order) {
                            return number_format($order->order_money , 2);
                        },
                        'contentOptions' => ['class' => 'money'],
                        'headerOptions' => ['class' => 'money'],
                    ],
                    [
                        'header' => '客户年化率（%）',
                        'value' => function ($order) {
                            return StringUtils::amountFormat2(bcmul($order->yield_rate, 100, 2));
                        }
                    ],
                    [
                        'header' => '注册时间',
                        'value' => function ($order) {
                            return date('Y-m-d H:i:s', $order->user->created_at);
                        }
                    ],
                    [
                        'header' => '投资时间',
                        'value' => function ($order) {
                            return date('Y-m-d H:i:s', $order->created_at);
                        }
                    ],
                    [
                        'header' => '状态',
                        'value' => function ($order) {
                            $status = (int) $order->status;
                            if (0 === $status) {
                                $msg = "投标失败";
                            } elseif (1 === $status) {
                                $msg =  "投标成功";
                            } elseif (2 === $status) {
                                $msg =  "撤标";
                            } else {
                                $msg =  "无效";
                            }
                            return $msg;
                        }
                    ],
                    [
                        'header' => '是否首次投资项',
                        'value' => function ($order) {
                            return $order->isFirstInvestment() ? '是' : '否';
                        }
                    ],
                    [
                        'header' => '操作',
                        'format' => 'raw',
                        'value' => function ($order) use ($loan) {
                            $html = '';
                            $hasCreatedBaoquan = $order->getBaoquanDownloadLink();
                            if($hasCreatedBaoquan) {
                                $html = "<a href=".$order->getBaoquanDownloadLink()." target='_blank' class='btn mini green'>下载保全合同</a> | ";
                                $html .= '<a href="/product/growth/order-cert?orderId='.$order->id.'" target="_blank" class="btn mini" style="background-color:#36A9CE;color: white;">&emsp;交易凭证&emsp;</a> | ';
                            } else {
                                $html = '<a href="javascript:void(0)" target="_blank" class="btn mini">&emsp;交易凭证&emsp;</a> | ';
                            }

                            if ($loan->is_jixi) {
                                $html .= '<a href="/product/growth/letter?orderId='.$order->id.'&isOnline=1" target="_blank" class="btn mini green">打印确认函</a>';
                            }
                            $html = rtrim($html, ' | ');
                            if (empty($html)) {
                                $html = '---';
                            }
                            return $html;
                        }
                    ],
                ]
            ]) ?>
        </div>
    </div>
                                    
</div>
<?php $this->endBlock(); ?>

