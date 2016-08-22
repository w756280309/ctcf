<?php
$this->title = '投资用户银行卡信息列表';

use common\models\user\QpayBinding;
use yii\grid\GridView;
use yii\helpers\Html;
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                会员管理 <small>会员银行卡信息模块【主要包含投资会员的银行卡信息】</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/listt">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/listt">投资会员</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/listt">会员列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/detail?id=<?= Html::encode($uid) ?>&type=1">会员详情</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">银行卡列表</a>
                </li>
            </ul>
        </div>

        <div class="portlet-body">
            <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items} <center><div class="pagination">{pager}</div></center>',
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
                    'columns' => [
                        [
                            'label' => '流水号',
                            'value' => function ($data) {
                                if ($data instanceof QpayBinding) {
                                    return $data->binding_sn;
                                } else {
                                    return $data->sn;
                                }
                            }
                        ],
                        [
                            'label' => '银行卡号',
                            'value' => function ($data) {
                                if ($data instanceof QpayBinding) {
                                    return substr_replace($data->card_number, '**** **** **** ', 0, -4);
                                } else {
                                    return substr_replace($data->cardNo, '**** **** **** ', 0, -4);
                                }
                            }
                        ],
                        [
                            'label' => '银行名称',
                            'value' => function ($data) {
                                if ($data instanceof QpayBinding) {
                                    return $data->bank_name;
                                } else {
                                    return $data->bankName;
                                }
                            }
                        ],
                        [
                            'label' => '创建时间',
                            'value' => function ($data) {
                                return date('Y-m-d H:i:s', $data->created_at);
                            }
                        ],
                        [
                            'label' => '状态',
                            'value' => function ($data) {
                                $arr = ['已申请', '成功', '失败', '处理中'];
                                return ($data instanceof QpayBinding ? '绑卡' : '换卡').$arr[$data->status];
                            }
                        ],
                        [
                            'label' => '联动状态',
                            'format' => 'html',
                            'value' => function ($data) {
                                return '<a class="btn btn-primary check-ump-info" href="/user/bank-card/ump-info?id='.$data->id.'&type='.($data instanceof QpayBinding ? 'b' : 'u').'">查询流水在联动状态</a>';
                            }
                        ],
                    ],
                ])
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        //点击获取流水状态
        var allowClick = true;
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
    })
</script>
<?php $this->endBlock(); ?>