<?php
$this->title = '项目详情';

use common\models\product\RateSteps;
use common\view\LoanHelper;

$deal->money = rtrim(rtrim($deal->money, '0'), '.');
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/xiangqing.css?v=20160427">
        <!--xiangqing-->
        <div class="row column">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-12 col-sm-10 column-title"><span><?=$deal->title?></span></div>
            <div class="container">
                <ul class="row column-content <?= $deal->isFlexRate ? 'rate-steps' : '' ?>">
                    <li class="col-xs-6">
                        <div class="xian">
                            <?= LoanHelper::getDealRate($deal) ?>
                            <span class="column-lu">%</span>
                            <?php if (!empty($deal->jiaxi) && !$deal->isFlexRate) { ?><span class="bonus-badge">+<?=  doubleval($deal->jiaxi) ?>%</span><?php } ?>
                        </div>
                        <span class="qing">年化收益率</span>
                    </li>
                    <li class="col-xs-6">
                        <div>
                            <?php $ex = $deal->getDuration() ?><?= $ex['value'] ?><span class="column-lu"><?= $ex['unit']?></span>
                        </div>
                        <span class="qing">期限
                            <?php if (!empty($deal->kuanxianqi)) { ?>
                            <i>(包含<?= $deal->kuanxianqi ?>天宽限期)</i> <img src="<?= ASSETS_BASE_URI ?>images/dina.png" alt="">
                            <?php } ?>
                        </span>
                    </li>
                    <?php if (!empty($deal->kuanxianqi)) { ?>
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
                    <div class="progress-bar progress-bar-red" style="width:<?= number_format($deal->finish_rate * 100, 0)?>%"></div>
                </div>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row shuju">
            <div class="col-xs-1"></div>
            <div class="col-xs-8" style="padding: 0;padding-left: 15px">
                <span><?= ($deal->status == 1)?(Yii::$app->functions->toFormatMoney($deal->money)) : rtrim(rtrim(number_format($deal_balace, 2), '0'), '.').'元'?></span><i>/<?= Yii::$app->functions->toFormatMoney($deal->money); ?></i>
                <div>可投余额/项目总额</div>
            </div>
            <div class="col-xs-1" style="padding: 0;">
                <div class="shuju-bili"><?=  number_format($deal->finish_rate * 100, 0)?><em>%</em></div>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row message">
            <div class="col-xs-1"></div>
            <div class="col-xs-10 xian2">
                <div class="m1">起投金额：<span><?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>元</span></div>
                <div class="m5">递增金额：<span><?= rtrim(rtrim(number_format($deal->dizeng_money, 2), '0'), '.') ?>元</span></div>   <!-- 增加递增金额字段 -->
                <div class="m2">项目起息：<span><?= $deal->jixi_time > 0 ? date('Y-m-d',$deal->jixi_time) : '项目成立日次日';?></span></div>
                <?php if (0 === (int)$deal->finish_date) { ?>
                    <div class="m3">项目期限：<span><?php $ex = $deal->getDuration() ?><?= $ex['value'] ?></span><?= $ex['unit']?></div>
                <?php } else { ?>
                    <div class="m3">项目结束：<span><?= date('Y-m-d',$deal->finish_date) ?></span></div>
                <?php } ?>

                <div class="m4">还款方式：<span><?= Yii::$app->params['refund_method'][$deal->refund_method]?></span></div>
            </div>
            <div class="col-xs-1"></div>
        </div>

        <?php if ($deal->isFlexRate) { ?>
        <div class="row tab lee_title">
            <div class="col-xs-1"></div>
            <div class="col-xs-10 lee_shuoming">收益说明</div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row message"  style="padding-bottom: 0px;" >
            <div class="col-xs-1  col-sm-1" ></div>
            <div class="col-xs-6 money  col-sm-5" style="color:#9c9c9c">认购金额(元)</div>
            <div class="col-xs-4 shouyi right_num col-sm-5"  style="color:#9c9c9c;" >年化收益率(%)</div>
            <div class="col-xs-1 right_hide  col-sm-1" ></div>
        </div>
        <div class="row message" style="padding-bottom: 0px;" >
            <div class="col-xs-1  col-sm-1" ></div>
            <div class="col-xs-7 money  col-sm-5" >累计认购<?= rtrim(rtrim(number_format($deal->start_money, 2), '0'), '.') ?>起</div>
            <div class="col-xs-3 shouyi center_num  col-sm-5" ><?= rtrim(rtrim(number_format($deal->yield_rate * 100, 2), '0'), '.') ?></div>
            <div class="col-xs-1 col-sm-1" ></div>
        </div>
        <?php foreach (RateSteps::parse($deal->rateSteps) as $val) { ?>
        <div class="row message" style="padding-bottom: 0px;" >
            <div class="col-xs-1  col-sm-1" ></div>
            <div class="col-xs-7 money  col-sm-5" >累计认购<?= rtrim(rtrim(number_format($val['min'], 2), '0'), '.') ?>起</div>
            <div class="col-xs-3 shouyi center_num  col-sm-5" ><?= rtrim(rtrim(number_format($val['rate'], 2), '0'), '.') ?></div>
            <div class="col-xs-1 col-sm-1" ></div>
        </div>
        <?php } ?>

        <div class="row message" >
            <div class="col-xs-1" ></div>
            <div class="col-xs-10 txt_orange" >投资金额越多,年化收益率越高</div>
            <div class="col-xs-1" ></div>
        </div>
        <?php } ?>

        <div class="row tab">
            <div class="col-xs-1"></div>
            <div class="col-xs-10 tabs">
                <div class="tab1">项目详情</div>
                <div class="tab2">投资记录</div>
            </div>
            <div class="col-xs-1"></div>
        </div>
        <div class="row tab-conten">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">
                <?=  \yii\helpers\HtmlPurifier::process($deal->description)?>
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
                        <div class="col-xs-4" style="padding: 0px;">投资金额(元)</div>
                    </div>

                </div>
            <div class="col-xs-1"></div>
        </div>
    </div>
    <?php if($deal->status==2){?>
    <form action="/deal/deal/toorder?sn=<?=$deal->sn . (defined('IN_APP') ? "&token=" . \yii\helpers\Html::encode(Yii::$app->request->get("token")): "") ?>" method="post" id="toorderform" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken ?>">
        </form>
        <div id="x-purchase" class="row rengou" style="cursor: pointer">
            <div class="col-xs-1"></div>
            <div class="col-xs-10">立即认购</div>
            <div class="col-xs-1"></div>
        </div>
    <?php }else{ ?>
        <div class="row huankuang">
            <div class="col-xs-1"></div>
            <div class="col-xs-10"><?=  Yii::$app->params['deal_status'][$deal->status]?><?= $deal->status==1?"(".$deal->start_date."开始)":"" ?></div>
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
                pid = '<?=$deal->id;?>';
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


            function subForm2(form){
                //vals = $(form).serialize();
                to = $(form).attr("data-to");//设置如果返回错误，是否需要跳转界面

                var xhr = $.get($(form).attr("action"), function (data) {
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

                return xhr;
            }

            $('#x-purchase').on('click', function(e) {
                var $this = $(this);
                if ($this.data('x-purchase-clicked')) {
                    return;
                }
                $this.data('x-purchase-clicked', true);
                var xhr = subForm2('#toorderform');
                xhr.always(function() {
                    $this.data('x-purchase-clicked', false);
                });
            });

            // 新的
            $('.qing img').on('click',function(){
                $('#chart-box').stop(true,false).fadeToggle();
            })
        </script>
</body>
</html>
