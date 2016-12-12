<?php
use yii\helpers\Html;

$this->title = '获奖列表';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理
                <small>获奖列表：<?= Html::encode($promo->title)?></small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/ranking/index">活动管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">奖励列表</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="portlet-body">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'header' => '姓名',
                            'format' => 'html',
                            'value' => function ($model) {
                                return '<a href="/user/user/detail?id='.$model->user->id.'">'.$model->user->real_name ?: '---'.'</a>';
                            }
                        ],
                        [
                            'header' => '手机号',
                            'value' => function ($model) {
                                return $model->user->mobile;
                            }
                        ],
                        [
                            'header' => '奖品名称',
                            'content' => function ($model) use($promo) {
                                $class = $promo->promoClass;
                                if (method_exists($class, 'getAward')) {
                                    $award = $class::getAward($model->reward_id);
                                    if ($award && isset($award['name'])) {
                                        return Html::encode($award['name']);
                                    }
                                }
                                return '---';
                            },
                        ],
                        [
                            'header' => '抽奖时间',
                            'value' => function ($model) {
                                return date('Y-m-d H:i:s', $model->drawAt);
                            }
                        ],
                        [
                            'header' => '发奖时间',
                            'value' => function ($model) {
                            return $model->rewardedAt ? date('Y-m-d H:i:s', $model->rewardedAt) : '未发奖';
                            }
                        ],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>
