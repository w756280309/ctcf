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
		?>"><a href="<?= $self_url . "&tab=0" ?>" target="_self">我的融资</a></div>
		<div class="tab<?php
		if ($tab) {
			echo ' tabon';
		}
		?>"><a href="<?= $self_url . "&tab=1" ?>" target="_self">特殊资产</a></div>
	</div>
	<div class="page-right-detail">
		<div class="tender-myassets-choose">
			<ul>
				<li class="tender-myassets-choose-first">筛选：</li>
				<li><a href="<?= $self_url . "&status=2" ?>" class="<?php
					if (is_array($status)	) {
						echo "a-click";
					}
					?>">募集期</a></li>
				<li><a href="<?= $self_url . "&status=5" ?>" class="<?php
					if ($status == 5) {
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
				<th style="width:90px;">项目编号</th>
				<th style="width:90px;">项目名称</th>
				<th>融资总额</th>
				<th>年化收益</th>
				<th>项目期限</th>
				<?php if ($status == 6) { ?><th>实际还款日</th><?php } else { ?><th>到期兑付日</th><?php } ?>
				<?php if ($status == 2) { ?><th>状态</th><?php } ?>
				<?php if ($status == 6) { ?><th>到期本息</th><?php } ?>
				<th>操作</th>
				<?php if ($status == 5) { ?><th></th><?php } ?>
			</tr>

			<?php foreach ($model as $key => $val) { ?>
                        <tr class="data-index" data-index="<?= $val['id'] ?>_<?= $val['id'] ?>" data-status="<?php echo is_array($status)?2:$status; ?>">
					<td><?= $val['sn'] ?></td>
					<td><?= $val['title'] ?></td>
					<td><?= $pcd->toFormatMoney($val['money']) ?></td>
					<td><?= number_format($val['yield_rate']*100, 2) ?>%</td>
					<td class="thback-tianshukong"><?= $val['expires'] ?>天</td>
                                        <td class="refund_time_<?= $val['id'] ?>">
                                            <?php if($status==6||$status==5){echo "读取中……";}else{ echo $val['refund_time'];}?>
                                        
                                        </td>
					<?php if ($status == 2) { ?><td>募集期</td><?php } ?>
					<?php if ($status == 6) { ?><td><?php
							if ($val['refund_method'] == 1) {
								echo "到期本息";
							} else {
								echo "付息还本";
							}
							?></td><?php } ?>
					<td class="thback-click"><span class="thback-lan">查看详情</span>
						<div class="thback-rela">
							<div class="thback-abso">
								<div class="thback-top"></div>
								<div class="thback-center p<?= $val['id'] ?> o<?= $val['sn'] ?>">
									加载中，请稍后
								</div>
							</div>
						</div>
					</td>
			<?php if ($status == 5) { ?><td><a href="#" onclick="repayment('<?= $val['id'] ?>')" class="tendersuccess_form_success_a1">还款</a></td><?php } ?>
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
			status = $(tr).attr('data-status');
			sp = data.split('_');
			idx[i] = sp[1];
			pidx[i] = sp[0];
		});
		datastr = idx.join(',');
		pidstr = pidx.join(',');
		csrf = $("meta[name=csrf-token]").attr('content');
                status = <?=$status ?>
		/*获取产品*/
		$.ajax({
			type: "get",
			url: "/user/onlinetender/pro-info",
			data: {_csrf: csrf, pid: pidstr,status:status},
			dataType: "json",
			success: function (data) {
                            $.each(data.data, function (idx, obj) {
                            var html = '<p class="tender-myassets-thback tender-myassets-thback-top">项目状态：<font>'
                                                    + obj.status_title + '</font></p>';
                             if (status == 2) {
                                 html += '<p class="tender-myassets-thback">募集开始时间：<font>' + obj.refund_start_date + '</font></p>';
                                 html += '<p class="tender-myassets-thback">募集结束时间：<font>' + obj.refund_end_date + '</font></p>'
                             }else if (status == 5) { 
                                 html +=  '<p class="tender-myassets-thback">还款方式：<font>' + obj.refund_method_title + '</font></p>'
                             }else if (status == 6) {
                                 html +=  '<p class="tender-myassets-thback">还款方式：<font>' + obj.refund_method_title + '</font></p>';
                             }
                            html += '<p class="tender-myassets-thback-line"></p>';
                            
                            $('.p' + obj.id).html(html)
                            if(status==6||status==5){
                                $('.refund_time_'+obj.id).html(obj.refund_time)
                            }
                        })
				
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
	function getlay() {  //利用这个方法向子页面传递layer的index
            return laySum;
        }
	function repayment(pid){
			laySum = $.layer({
				type: 2,
				title: [
					'还款确认', 
					'background:#f7f8fa; height:40px; color:#black; border:none; font-weight:bold;' //自定义标题样式
				], 
				border:[0],
				area: ['400px','300px'],
				close: function(index){
					layer.close(index);
					location.reload();
				},
				iframe: {src: '/user/repayment/index?pid='+pid}
			})
	}

         function lout(){
            var index = window.parent.getlay();
            //parent.location.href="/user/login/logout";
            parent.layer.close(index);
        }

</script>