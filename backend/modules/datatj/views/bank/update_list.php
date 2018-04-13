<?php

use common\utils\SecurityUtils;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = '异常换卡记录列表';
$this->registerJsFile('/js/clipboard.min.js', ['depends' => 'yii\web\YiiAsset']);

?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h4>
                    超过3天且还是处理中的换卡记录列表
                </h4>
                <ul class="breadcrumb" style="line-height: 1.5rem;">
                    <li class="span12">PS：</li>
                    <li>1. 联动优势对于换卡记录的处理，一般超过20天，系统会默认为过期，可以再次提交换卡申请，但是不会更新记录的状态，查询接口返回的依然为处理中； </li>
                    <li>2. 对于换卡记录在20天以内的且联动状态为处理中的记录，除了同步我方数据外，还需我方客服向联动提交取消换卡申请邮件，邮件标题格式为"7001209+平台名称+取消XXX换卡申请"、内容格式为"XXX+XXX手机号，此用户取消换卡申请"，发送给shenhe@umpay.com，抄送p2p-op@umpay.com； </li>
                </ul>
            </div>
        </div>

        <div class="portlet-body">
            <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{summary}{items}<div class="pagination" style="text-align:center; clear: both;">{pager}</div>',
                    'columns' => [
                        [
                            'attribute' => 'sn',
                            'label' => '换卡流水号',
                        ],
                        [
                            'attribute' => 'cardHolder',
                            'label' => '换卡用户名',
                        ],
                        [
                            'label' => '换卡手机号',
                            'value' => function ($data) {
                                return Html::encode(SecurityUtils::decrypt($data->user->safeMobile));
                            },
                        ],
                        [
                            'label' => '换卡时间',
                            'value' => function ($data) {
                                return Html::encode(date('Y-m-d H:i:s', $data->created_at));
                            },
                        ],
                        [
                            'label' => '换卡天数',
                            'value' => function ($data) {
                                return (strtotime('today') - strtotime(date('Y-m-d', $data->created_at))) / (60 * 60 * 24);
                            },
                        ],
                        [
                            'label' => '查询联动状态',
                            'format' => 'html',
                            'value' => function ($data) {
                                return '<a class="btn btn-primary check-ump-info" href="/user/bank-card/ump-info?id='.$data->id.'&type=u">查询联动状态</a>';
                            }
                        ],
                        [
                            'label' => '发申诉邮件',
                            'format' => 'html',
                            'value' => function ($data) use ($companyShort) {
                                $msg = Html::encode('mailto:shenhe@umpay.com?cc=p2p-op@umpay.com&subject=7001209+'.$companyShort.'+取消'.$data->cardHolder.'换卡申请&body='.$data->cardHolder.'+'.SecurityUtils::decrypt($data->user->safeMobile).'，此用户取消换卡申请');

                                return '<a href="'.$msg.'">发送取消申请</a>';
                            }
                        ],
                        [
                            'label' => '操作',
                            'format' => 'raw',
                            'value' => function ($data) use ($companyShort) {
                                $cardHolder = Html::encode($data->cardHolder);

                                $button = '<a href="Javascript:void(0)" class="btn mini purple copy-buttons" data-clipboard-text="7001209+'.$companyShort.'+取消'.$cardHolder.'换卡申请"><i class="icon-edit"></i> 复制邮件标题</a>';
                                $button .= '<a href="Javascript:void(0)" class="btn mini purple copy-buttons" data-clipboard-text="'.$cardHolder.'+'.SecurityUtils::decrypt(Html::encode($data->user->safeMobile)).'，此用户取消换卡申请"><i class="icon-edit"></i> 复制邮件内容</a>';

                                return $button;
                            }
                        ],
                    ],
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover', 'style' => 'background-color: #fff']
                ])
            ?>
        </div>
    </div>

    <script type="text/javascript">
        $(function () {
            var allowClick = true;

            //查询记录对应的联动状态
            $('.check-ump-info').on('click', function (e) {
                e.preventDefault();

                if (!allowClick) {
                    return;
                }

                var _this = $(this);
                var url = _this.attr('href');
                if (url) {
                    allowClick = false;
                    var xhr = $.get(url, function (data) {
                        _this.parent().html(data.message);
                        allowClick = true;
                    });

                    xhr.fail(function () {
                        allowClick = true;
                    });
                } else {
                    alert('获取查询链接失败');
                }
            });

            //复制
            if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE8.0" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE7.0" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE6.0" ){
                $('.copy-buttons').on('click',function(){
                    alert('请手动复制链接');
                    return false;
                })
            } else {
                try {
                    var btn = $('.copy-buttons');
                    btn.each(function() {
                        var clipboard = new Clipboard(this);
                        clipboard.on('success', function(e) {
                            alert('内容已复制到剪贴板');
                        });

                        clipboard.on('error', function(e) {
                            alert('请重新复制');
                        });
                    })
                } catch(error) {
                }
            }
        })
    </script>
<?php $this->endBlock(); ?>
