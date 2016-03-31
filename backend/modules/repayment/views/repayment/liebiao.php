<?php
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
                        <a href="/product/productonline/list">贷款管理</a>
                        <i class="icon-angle-right"></i>
                    </li>

                    <li>
                        <a href="/product/productonline/list">项目列表</a>
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
                            <span class="title">项目名称：<?=$deal->title?></span>
                        </td>
                        <td>
                            <span class="title">应还款人数：<?=$count?></span>
                        </td>
                        <td>
                            <span class="title">应还款本金：<?=$yhbj?>元</span>
                        <td>
                            <span class="title">应还款利息：<?=$yhlixi?>元</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="title">应还款本息：<?=$total_bx?>元</span>
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
        <?php foreach ($model as $qk => $qi) : ?>
        <div class="portlet-body">
            <table class="list">
                <thead><th class="list">第<?= $qk ?>期:</th></thead>
                <tr>
                    <td class="list">
                        本期应还时间：<?= date('Y-m-d', $qi[count($qi) - 1]['refund_time']) ?><br>
                        本期应还款本金：<?= $bc->bcround(array_sum(array_column($qi, 'benjin')), 2)  ?>（元）&emsp;
                        本期应还款利息：<?= $bc->bcround(array_sum(array_column($qi, 'lixi')), 2)  ?>（元）
                        <img src="/image/you.png" class="jiantou<?= $qk ?>" onclick="tableShow('.jiantou<?= $qk ?>')" data="<?= $qk ?>" alt="" style="position: absolute; right: 30px; height:20px; width: 20px;">
                    </td>
                </tr>
                <tr>
                    <td>
                        <table class="table table-striped table-bordered table-advance table-hover table-content<?= $qk ?>" style="display: none;">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>期数</th>
                                    <th>真实姓名</th>
                                    <th>手机号</th>
                                    <th>应还款本金（元）</th>
                                    <th>应还款利息（元）</th>
                                    <th>应还款本息（元）</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($qi as $key => $val) : ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= $val['qishu'] ?></td>
                                    <td><?= $val['real_name'] ?></td>
                                    <td><?= $val['mobile'] ?></td>
                                    <td style="text-align: right; padding-right: 70px"><?= $val['benjin'] ?></td>
                                    <td style="text-align: right; padding-right: 70px"><?= $val['lixi'] ?></td>
                                    <td style="text-align: right; padding-right: 70px"><?= $val['benxi'] ?></td>
                                </tr>
                             <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <?php if (5 === (int) $deal->status && 0 === (int) $qi[count($qi) - 1]['status']) { ?>
        <div class="form-actions" style="text-align:right">
            <button type="button" class="btn blue button-repayment" qishu="<?= $qk ?>"><i class="icon-ok"></i> 确认还款</button>
        </div>
        <?php } else { ?>
        <br>
        <?php } ?>
        <?php endforeach; ?>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $('.button-repayment').click(function(){
            var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
            var pid = '<?= Yii::$app->request->get('pid');?>';
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

