<?php
$this->title = '新手标统计';
use yii\grid\GridView;
use yii\web\YiiAsset;

$this->title = '新手标统计';
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
                新手标人数统计
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/datatj/datatj/huizongtj">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">新手标人数统计</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <form action="" method="get" target="_self">
            <table class="table">
                <tr>
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
        <h3>新手标人数统计</h3>
                <table class="table" id="platform_rate">
                    <tr>
                        <td class="td_title">
                            <span class="title">新注册用户购买新手标人数</span>
                        </td>
                        <td class="td_content"><?= $xsCount ?></td>
                        <td class="td_title">
                            <span class="title">新注册用户买了新手标还买了其他产品人数</span>
                        </td>
                        <td class="td_content"><?= $xsAndOtherCount ?></td>
                    </tr>
                    <tr>
                        <td class="td_title">
                            <span class="title">只购买新手标到期提现人数</span>
                        </td>
                        <td class="td_content"><?= $xsAndDrawCount ?></td>
                        <td class="td_title">
                            <span class="title">购买新手标到期复投人数</span>
                        </td>
                        <td class="td_content"><?= $reOrderCount ?></td>
                    </tr>
                </table>
    </div>
</div>
<?php $this->endBlock(); ?>
