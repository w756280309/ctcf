<?php
$this->title = '项目详情';
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\JqueryAsset', 'position' => 1]);

$deal['money'] = doubleval($deal['money']);
?>
<link rel="stylesheet" href="/css/xiangqing.css">
        <!--xiangqing-->
        <div class="row column">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-12 col-sm-10 column-title"><span><?=$deal['title']?></span></div>
            <div class="container">
                <ul class="row column-content">
                    <li class="col-xs-6">
                        <div class="xian">
                            <?=  doubleval(number_format($deal->baseRate*100, 2)) ?><span class="column-lu">%</span>
                            <?php if (!empty($deal['jiaxi'])) { ?><span class="bonus-badge">+<?=  doubleval($deal['jiaxi']) ?>%</span><?php } ?>
                        </div>
                        <span class="qing">年化收益率</span>
                    </li>
                    <li class="col-xs-6">
                        <div>
                            <?=$deal->expires?>
                            <span class="column-lu">
                                <?php if (1 === (int)$deal['refund_method']) { ?>
                                天
                                <?php } else { ?>
                                个月
                                <?php } ?>
                            </span>
                        </div>
                        <span class="qing">期限
                            <?php if (!empty($deal['kuanxianqi'])) { ?>
                            <i>(包含<?= $deal['kuanxianqi'] ?>天宽限期)</i> <img src="/images/dina.png" alt="">
                            <?php } ?>
                        </span>
                    </li>
                    <?php if (!empty($deal['kuanxianqi'])) { ?>
                    <div class="row" id='chart-box' hidden="true">
                        <div class="col-xs-12">
                            <div>宽限期：应收账款的付款方因内部财务审核,结算流程或结算日遇银行非工作日等因素，账款的实际结算日可能有几天的延后</div>
                        </div>
                    </div>
                    <?php } ?>
                </ul>
            </div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <div class="row bili">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">
                <div class="per">
                    <div class="progress-bar progress-bar-red" style="width:<?= number_format($deal['finish_rate']*100,0)?>%"></div>
                </div>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row shuju">
            <div class="col-xs-1"></div>
            <div class="col-xs-8" style="padding: 0;padding-left: 15px">
                <span><?= ($deal['status']==1)?(Yii::$app->functions->toFormatMoney($deal['money'])) : doubleval($deal_balace).'元'?></span><i>/<?= Yii::$app->functions->toFormatMoney($deal['money']); ?></i>
                <div>可投余额/项目总额</div>
            </div>
            <div class="col-xs-1" style="padding: 0;">
                <div class="shuju-bili"><?=  number_format($deal['finish_rate']*100,0)?><em>%</em></div>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row message">
            <div class="col-xs-1"></div>
            <div class="col-xs-10 xian2">
                <div class="m1">起投金额：<span><?= doubleval($deal['start_money']) ?>元</span></div>
                <div class="m2">项目起息：<span><?= $deal['jixi_time']>0 ? date('Y-m-d',$deal['jixi_time']) : '项目成立日次日';?></span></div>
                <?php if (0 === (int)$deal['finish_date']) { ?>
                    <div class="m3">项目期限：<span><?=$deal->expires?></span>
                    <?php if (1 === (int)$deal['refund_method']) { ?>
                    天
                    <?php } else { ?>
                    个月
                    <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="m3">项目结束：<span><?= date('Y-m-d',$deal['finish_date']) ?></span></div>
                <?php } ?>

                <div class="m4">还款方式：<span><?= Yii::$app->params['refund_method'][$deal['refund_method']]?></span></div>
            </div>
            <div class="col-xs-1"></div>
        </div>

        <div class="row tab">
            <div class="col-xs-1"></div>
            <div class="col-xs-10 tabs">
                <div class="tab1">项目详情</div>
                <div class="tab2">投资记录(元)</div>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row tab-conten">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">
                <?=  \yii\helpers\HtmlPurifier::process($deal['description'])?>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <!--投资记录-->
        <div class="row touzi-box">
            <div class="col-xs-1"></div>
            <div class="col-xs-10 col">
                    <div class="row touzi datafirst">
                        <div class="col-xs-3 col">投资人</div>
                        <div class="col-xs-5">投资时间</div>
                        <div class="col-xs-4">投资金额</div>
                    </div>

                </div>
            <div class="col-xs-1"></div>
        </div>
    </div>
    <?php if($deal['status']==2){?>
        <form action="/deal/deal/toorder?sn=<?=  Yii::$app->request->get('sn');?>" method="post" id="toorderform" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken ?>">
        </form>
        <div class="row rengou" style="cursor: pointer" h="location.href='/order/order?sn=<?=$deal['sn']?>'" onclick="subForm('#toorderform')">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">立即认购</div>
            <div class="col-xs-1"></div>
        </div>
    <?php }else{ ?>
        <div class="row huankuang">
            <div class="col-xs-1"></div>
            <div class="col-xs-10"><?=  Yii::$app->params['deal_status'][$deal['status']]?><?= $deal['status']==1?"(".$deal['start_date']."开始)":"" ?></div>
            <div class="col-xs-1"></div>
        </div>
    <?php } ?>
   <script>
            $(function(){
                $('.tabs div').click(function(){
                    var index=$('.tabs div').index(this);
                    $('.tabs div').css({background:'#ffffff',color:'#f34334'}); //303030
                    $('.tabs div').eq(index).css({background:'#f34334',color:'#ffffff'});
                    if(index==1){
                        $('.tab-conten').css({display:'none'});
                        $('.touzi-box').css({display:'block'});
                    }else{
                        $('.tab-conten').css({display:'block'});
                        $('.touzi-box').css({display:'none'});
                    }
                })
                pid = '<?=$deal['id'];?>';
                $.get('/deal/deal/orderlist',{pid:pid},function(data){
                    html = "";
                    for(var i=0;i<data.orders.length;i++){
                            html+='<div class="row touzi-content border-bottom1">';
                            html+='    <div class="col-xs-3 col">'+data.orders[i]['mobile']+'</div>';
                            html+='    <div class="col-xs-5 data"><span class="data1">'+data.orders[i]['time']+'</span><span class="data2">'+data.orders[i]['his']+'</span></div>';
                            html+='    <div class="col-xs-4">'+data.orders[i]['money']+'</div>';
                            html+='</div>';
                    }
                    $('.datafirst').after(html)
                })

            });

            <?php if($deal['status']==2){?>
            function subForm(form){
                vals = $(form).serialize();
                to = $(form).attr("data-to");//设置如果返回错误，是否需要跳转界面

                $.post($(form).attr("action"), vals, function (data) {
                    if(data.code!=0&&to==1&&data.tourl!=undefined){
                        if(data.message=='请登录'){
                            toast(data.message, function() {
                                location.href = data.tourl;
                            });
                        }else{
                            alertTrueVal(data.message,function(){
                                location.href=data.tourl;
                            });
                        }
                    }else{
                        if(data.code!=0){
                            toast(data.message);
                        }

                        if(to==1&&data.tourl!=undefined){
                           location.href=data.tourl;
                        }
                    }
                });
            }
            <?php } ?>
            // 新的
            $('.qing img').on('click',function(){
                $('#chart-box').stop(true,false).fadeToggle();
            })
        </script>
</body>
</html>
