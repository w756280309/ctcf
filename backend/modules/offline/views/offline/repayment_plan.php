<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-15
 * Time: 下午4:41
 */
use common\lib\bchelp\BcRound;

$bc = new BcRound();
?>
<?php $this->beginBlock('blockmain'); ?>
<style>
    table.list {
        width: 100%;
    }
    tr td.list {
        border: 1px solid #666;
        padding: 10px;
        width: 100%;
        background-color: #f9f9f9;
    }
    thead th.list {
        border: 1px solid #666;
        background-color: #DDD;
        text-align: left;
    }
</style>
<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">
            <br />
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>

                <li>
                    <a href="/offline/offline/loanlist">线下标的</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">还款计划</a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <table class="table">
                <tr>
                    <td>
                        <span class="title">项目名称：<?= $model->title ?></span>
                    </td>
                    <td>
                        <span class="title">应还款人数：<?=$model->repaymentNumber ?></span>
                    </td>
                    <td>
                        <span class="title">应还款本金：<?=$model->benjin ?>元</span>
                    <td>
                        <span class="title">应还款利息：<?= $model->lixi ?>元</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="title">应还款本息：<?= $model->amount ?>元</span>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php foreach ($model->repayments as $key => $repayment) : ?>
    <div class="portlet-body">
        <table class="list">
            <thead><th class="list">第<?= $repayment->term ?>期:</th></thead>
            <tr>
                <td class="list">
                    本期应还款时间：<?= $repayment->dueDate ?>
                    <br>
                    本期应还款本金：<?= $repayment->principal ?>（元）
                    本期应还款利息：<?= $repayment->interest ?>（元）
                    <?php if($repayment->term == count($model->repayments)) echo '贴息：' . $repayment->loan->tiexi . '（元）'; ?>
                    <img src="/image/you.png" class="jiantou<?= $key ?>" onclick="tableShow('.jiantou<?= $key ?>')" data="<?= $key ?>" alt="" style="position: absolute; right: 30px; height:20px; width: 20px;">
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table table-striped table-bordered table-advance table-hover table-content<?= $key ?>" style="display: none;">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>期数</th>
                            <th>用户ID</th>
                            <th>真实姓名</th>
                            <th>手机号</th>
                            <th>应还款本金（元）</th>
                            <th>应还款利息（元）</th>
                            <th>应还款本息（元）</th>
                            <th>逾期天数</th>
                            <th>贴息（元）</th>
                            <th>应还款总金额（元）</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($repayment->plans as $plan) : ?>
                        <tr>
                            <td><?= $plan->id ?></td>
                            <td><?= $plan->qishu ?></td>
                            <td><?= $plan->uid ?></td>
                            <td><?= $plan->user->realName ?></td>
                            <td><?= $plan->user->mobile ?></td>
                            <td><?= $plan->benjin ?></td>
                            <td><?= $plan->lixi ?></td>
                            <td><?= $plan->benxi ?></td>
                            <td><?= $plan->yuqi_day ?></td>
                            <td><?= $plan->tiexi ?></td>
                            <td><?= bcadd($plan->benxi, $plan->tiexi, 2) ?></td>
                            <td><?= $plan->status ? '已还（'.$plan->actualRefundTime.'）' : '未还' ?></td>
                            <td>
                                <?php if (!$plan->status) : ?>
                                <a href="javascript:void(0)" onclick="openwin('/offline/offline/fuxi?id=<?= $plan->id ?>&type=plan', 500, 300)" class="btn mini green">
                                    <i class="icon-minus-sign"></i>付息</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
        <?php if (!$repayment->isAllRefunded) { ?>
            <div class="form-actions" style="text-align:right">
                <button type="button" class="btn blue button-repayment" qishu="<?= $key ?>" onclick="openwin('/offline/offline/fuxi?id=<?= $repayment->id ?>&type=repayment', 500, 300)"><i class="icon-ok"></i>本期全部付息</button>
            </div>
        <?php } else { ?>
            <br>
        <?php } ?>
    <?php endforeach; ?>
</div>
<script type="text/javascript">
    $(function(){
        $('.button-repaymentss').click(function(){
            var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
            var pid = '<?= Yii::$app->request->get('id');?>';
            var qishu = $(this).attr('qishu');
            $repbtn = $(this);
            layer.confirm('是否确认还款？',{title:'确认还款',btn:['确定','取消']},function(){
                layer.closeAll();
                $repbtn.attr('disabled','disabled');
                $repbtn.html('正在处理……');
                openLoading();
                $.post('/repayment/repayment/dorepayment',{pid:pid,qishu:qishu,_csrf:csrftoken},function(data)
                {
                    newalert(data.result,data.message,1);
                    cloaseLoading();
                    $repbtn.removeAttr('disabled');
                    $repbtn.html('<i class="icon-ok"></i> 确认还款');
                });
            },function(){
                layer.closeAll();
            });
        });
    });

    function tableShow (className)
    {
        var $btn = $(className);
        var data = $btn.attr('data');
        var $content = $('.table-content'+data);

        if ($content.css('display') === 'none') {
            $btn.attr('src', '/image/shang.png');
            $content.show();
        } else {
            $btn.attr('src', '/image/you.png');
            $content.hide();
        }
    }
</script>
<?php $this->endBlock(); ?>