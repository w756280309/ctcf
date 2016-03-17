<?php
use yii\widgets\LinkPager;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->


    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                        会员管理 <small>会员融资管理模块【主要包含融资会员的融资明细管理】</small>
            </h3>
            <ul class="breadcrumb">
                   <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/<?= $type==='2'?'listr':'listt'?>">会员管理</a>
                        <i class="icon-angle-right"></i>
                    </li>

                   <li>
                            <a href="/user/user/listr">融资会员</a>
                            <i class="icon-angle-right"></i>
                        </li>
                    <li>
                        <a href="/user/user/<?= $type==='2'?'listr':'listt'?>">会员列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/detail?id=<?= $id ?>&type=<?= $type ?>">会员详情</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0)；">融资明细</a>
                    </li>
            </ul>
        </div>

         <div class="portlet-body">
            <table class="table">
                    <tr>
                        <td>
                            <span class="title">企业名称：<?=$org_name?></span>
                        </td>
                        <td>
                            <span class="title">融资金额总计（元）：<?=number_format($moneyTotal,2)?></span>
                        </td>
                        <td>
                            <span class="title">融资次数（次）：<?=$Num?></span>
                        </td>
                    </tr>
            </table>
        </div>


        <!--search start-->
        <div class="portlet-body">
            <form action="/order/onlineorder/detailr" method="get" target="_self">
                <table class="table">
                    <input type="hidden" name="id" value="<?= $id ?>"/>
                    <input type="hidden" name ='type' value="<?= $type ?>"/>
                    <tbody>
                        <tr>
                            <td>
                                <span class="title">项目名称</span>
                                <input type="input" name ='title' value = '<?= $title ?>'/>
                            </td>
                            <td>
                                <span class="title">状态</span>
                            </td>
                            <td>
                                <select name="status">
                                    <option value="">---未选择---</option>
                                    <option value="-1" <?= $status === '-1'?"selected='selected'":"" ?>>未上线</option>
                                    <?php foreach (Yii::$app->params['deal_status'] as $key => $val): ?>
                                    <option value=<?= $key ?>
                                        <?php if ($status === strval($key)) {
                                            echo "selected='selected'";
                                        }?>><?= $val ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><span class="title">融资时间</span></td>
                            <td>
                                <input type="text" value="<?= $time ?>" name = "time" onclick='WdatePicker({dateFmt:"yyyy-MM-dd",maxDate:"<?=  date('Y-m-d')?>"});'/>
                            </td>
                            <td colspan="6" align="right" style=" text-align: right">
                                <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
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
                        <th>项目编号</th>
                        <th>项目名称</th>
                        <th style="text-align: right;">融资金额（元）</th>
                        <th style="text-align: right;">实际融资金额（元）</th>
                        <th style="text-align: right;">融资期限</th>
                        <th style="text-align: right;">利率（%）</th>
                        <th>融资时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['sn'] ?></td>
                        <td><?= $val['title'] ?></td>
                        <td style="text-align: right;"><?= number_format($val['money'],2) ?></td>
                        <td style="text-align: right;"><?= number_format($val['funded_money'],2) ?></td>
                        <td style="text-align: right;">
                        <?php
                            if (1 === (int)$val['refund_method']) {
                                echo $val['expires']."天";
                            } else {
                                echo $val['expires']."个月";
                            }
                        ?>
                        </td>
                        <td style="text-align: right;"><?= doubleval(100*$val['yield_rate'])?></td>
                        <td><?= date('Y-m-d H:i:s',$val['created_at'])?></td>
                        <td><?php
                                if ($val['online_status'] === 0) {
                                   echo "未上线";
                                } else {
                                   echo Yii::$app->params['deal_status'][$val['status']];
                                }
                                ?></td>
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

