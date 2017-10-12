<?php
$this->title = '复投率';
use yii\grid\GridView;
use yii\web\YiiAsset;

$this->title = '复投率';
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);

?>
<?php $this->beginBlock('blockmain'); ?>
<style>
    #platform_rate tr td.td_content{
        width: 200px;
        text-align: left;
        color: red;
        height: 30px;
        line-height: 30px;
    }
    #platform_rate tr td.td_title{
        width: 60px;
    }
    #platform_rate td.td_title span{
        white-space: nowrap;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                复投率
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/datatj/datatj/huizongtj">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">复投率</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <form action="" method="get" target="_self">
            <table class="table">
                <tr>
                    <td>
                        <span class="title">复投率种类</span>
                    </td>
                    <td>
                        <select class="m-wrap" name="aff_id">
                            <option value="1" <?= $aff_id== '1' ? 'selected' : '' ?>>平台复投</option>
                            <option value="2" <?= $aff_id== '2' ? 'selected' : '' ?>>项目复投</option>
                        </select>
                    </td>
                    <td>
                        <span class="title">时间范围</span>
                    </td>
                    <td>
                        <input type="text" placeholder="开始时间" value="<?= $startDate ? $startDate : '' ?>"
                               autocomplete="off"
                               name="startDate" class="m-wrap"
                               onclick="WdatePicker()">-
                        <input type="text" placeholder="结束时间" value="<?= $endDate ? $endDate : '' ?>"
                               autocomplete="off"
                               name="endDate" class="m-wrap"
                               onclick="WdatePicker()">
                    </td>
                    <td align="right" class="span2">
                        <button type='submit' class="btn blue btn-block">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <hr>
    <div class="portlet-body">
        <h3>平台复投</h3>
        <?php
            if($message){
        ?>
        <h4 style="color: red"><?= $message ?></h4>
        <?php
            }else {
        ?>
                <table class="table" id="platform_rate">
                    <tr>
                        <td class="td_title">
                            <span class="title">复投总额</span>
                        </td>
                        <td class="td_content"><?= $reinvestAmount ?></td>
                        <td class="td_title">
                            <span class="title">复投人数</span>
                        </td>
                        <td class="td_content"><?= $reinvestUserCount ?></td>
                        <td class="td_title">
                            <span class="title">新增总额</span>
                        </td>
                        <td class="td_content"><?= $increaseInvestAmount ?></td>
                    </tr>
                    <tr>
                        <td class="td_title">
                            <span class="title">回款总额</span>
                        </td>
                        <td class="td_content"><?= $refundAmount ?></td>
                        <td class="td_title">
                            <span class="title">回款人数</span>
                        </td>
                        <td class="td_content"><?= $refundCount ?></td>
                        <td class="td_title">
                            <span class="title">复投率</span>
                        </td>
                        <td class="td_content"><?= $rate ?></td>
                    </tr>
                    <tr>
                        <td class="td_title">
                            <span class="title">提现金额</span>
                        </td>
                        <td class="td_content"><?= $drawAmount ?></td>
                        <td class="td_title">
                            <span class="title">提现人数</span>
                        </td>
                        <td class="td_content"><?= $drawCount ?></td>
                    </tr>
                </table>
        <?php
            }
        ?>
    </div>
</div>
<?php $this->endBlock(); ?>
