<?php

use yii\widgets\LinkPager;
use frontend\models\ProductCategoryData;

$pcd = new ProductCategoryData();
$self_url = "/user/onlinetender/means?current=2";
$this->registerCssFile('/css/tender.css', ['depends' => 'yii\web\YiiAsset']);
?>
<div class="fr page-right tender-myassets">
    <div class="page-rigth-title">
        <div class="tab<?php
	if (empty($tab)) {
	    echo ' tabon';
	}
	?>"><a href="<?= $self_url . "&tab=0" ?>" target="_self">我的资产</a></div>
        <div class="tab<?php
	if ($tab) {
	    echo ' tabon';
	}
	?>"><a href="<?= $self_url . "&tab=1" ?>" target="_self">特殊资产</a></div>
    </div>
    <div class="page-right-detail">
        <div class="tender-myassets-choose">
            <ul>
                <li class="tender-myassets-choose-first">筛选：</td>
                <li><a href="<?= $self_url . "&status=2" ?>" class="<?php
		    if (($status)==2) {
			echo "a-click";
		    }
		    ?>">募集期</a></li>
                <li><a href="<?= $self_url . "&status=5" ?>" class="<?php
		    if (is_array($status) == 5) {
			echo "a-click";
		    }
		    ?>">还款中</a></li>
                <li><a href="<?= $self_url . "&status=6" ?>" class="<?php
		    if ($status == 6) {
			echo "a-click";
		    }
		    ?>">已还清</a></li>
                <li class="tender-myassets-choose-right">总计：<span><?php echo "$count"; ?></span> 笔</li>
            </ul>
        </div>
	<table>
	    <tr class="th">
		<?php if ($status == 6) { ?><th>项目编号</th><?php } ?>
		<th>项目名称</th>
		<th>年化收益</th>
		<th>项目期限</th>
		<?php if ($status == 2) { ?><th>认购时间</th><?php } ?>
		<th>认购金额</th>
		<?php if ($status == 6) { ?><th>实际还款日</th><?php } else { ?><th>到期兑付日</th><?php } ?>
		<?php if ($status == 5) { ?><th>预期收益</th><?php } ?>
		<?php if ($status == 6) { ?><th>实际收益</th><?php } ?>
		<th>操作</th>
	    </tr>

	    <?php foreach ($model as $key => $val) { ?>
    	    <tr class="data-index" data-index="<?= $val['online_pid'] ?>_<?= $val['id'] ?>">
		    <?php if ($status == 6) { ?><td><?= $val['sn'] ?></td><?php } ?>
    		<td style="max-width:120px;"><?= $val['title'] ?></td>
    		<td><?= number_format($val['yield_rate']*100, 2) ?>%</td>
    		<td class="thback-tianshukong"><?= $val['expires'] ?>天</td>
		    <?php if ($status == 2) { ?><td><?= date("Y-m-d", $val['order_time']) ?></td><?php } ?>
    		<td><?= $pcd->toFormatMoney($val['order_money']) ?></td>
		    <?php if ($status == 6) { ?><td><?= $val['refund_time'] ?></td><?php } else { ?><td><?= $val['refund_time'] ?></td><?php } ?>
		    <?php if ($status == 5) { ?><td><?= $val['lixi'] ?>元</td><?php } ?>
		    <?php if ($status == 6) { ?><td><?= $val['lixi'] ?>元</td><?php } ?>
    		<td class="thback-click"><span class="thback-lan">查看详情</span>
    		    <div class="thback-rela">
    			<div class="thback-abso">
    			    <div class="thback-top"></div>
    			    <div class="thback-center p<?= $val['online_pid'] ?> o<?= $val['sn'] ?>">
    				加载中，请稍后 
    			    </div>
    			</div>
    		    </div>
    		</td>    
    	    </tr>
	    <?php } ?>

	    <tr><td colspan="8"><?= LinkPager::widget(['pagination' => $pages]); ?></td></tr>
	</table>

    </div>
</div>
<script>
    $(function () {
        idx = new Array();
        pidx = new Array();
        $('.data-index').each(function (i, tr) {
            data = $(tr).attr('data-index');
            sp = data.split('_');
            idx[i] = sp[1];
            pidx[i] = sp[0];
        });
        datastr = idx.join(',');
        pidstr = pidx.join(',');
        csrf = $("meta[name=csrf-token]").attr('content');
        /*获取产品*/
        $.ajax({
            type: "get",
            url: "/user/onlinetender/pro-info",
            data: {_csrf: csrf, pid: pidstr},
            dataType: "json",
            success: function (data) {
                $.each(data.data, function (idx, obj) {
                    $('.p' + obj.id).html('<p class="tender-myassets-thback tender-myassets-thback-top">项目状态：<font>'
                            + obj.status_title + '</font></p>'
                            + '<p class="tender-myassets-thback">还款方式：<font>' + obj.refund_method_title + '</font></p>'
                            + '<p class="tender-myassets-thback-line"></p>');
                })
                
                /*获取合同列表 */
                $.ajax({
                    type: "get",
                    url: "/user/onlinetender/contract-list",
                    data: {_csrf: csrf, data: datastr, pid: pidx},
                    dataType: "json",
                    success: function (data) {
                        $.each(data.data, function (idx, obj) {

                            var html = '<p class="tender-myassets-thback-center">项目合同</p>';
                            $.each(obj, function (key, val) {
                                var path = "";
                                if (val.contract_content != '') {
                                    html += '<p><span>' + val.contract_name + '</span><a href="/user/onlinetender/contract?order_id='+idx+'" target="_blank" class="thback-abso-a">查看</a><a href="/user/onlinetender/contract?order_id='+idx+'&op=D" target="_blank">下载</a></p>'
                                } else if (val.path != null) {
                                    html += '<p><span>' + val.contract_name + '</span><a href="' + path + '" target="_blank" class="thback-abso-a">查看</a><a href=/download.php?file=' + path + '" target="_blank">下载</a></p>'
                                }

                            })
                            len = $('.o' + idx).length;
                            $('.o' + idx).append(html);
                        })

                    }
                });


            }
        });
        


        $('.thback-click').hover(function () {
            $(this).children('.thback-rela').css("display", "block");
        },
                function () {
                    $(this).children('.thback-rela').css("display", "none");
                }
        );
    });
</script>
