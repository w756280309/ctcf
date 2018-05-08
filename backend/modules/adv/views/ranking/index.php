<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = '活动列表';

?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理
                <small>活动管理</small>
                <a href="/adv/ranking/create" id="sample_editable_1_new" class="btn green" style="float: right;">
                    添加活动 <i class="icon-plus"></i>
                </a>
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
                    <a href="javascript:void(0);">活动列表</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <form action="/adv/ranking/index" method="get" target="_self">
            <table class="table">
                <tbody>
                <tr>
                    <td><span class="title">标题</span></td>
                    <td>
                        <input type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='title' value="<?= Yii::$app->request->get('title') ?>" />
                    </td>


                    <td><div align="right" style="margin-right: 20px">
                            <button type='submit' class="btn blue btn-block" style="width: 100px;">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                        </div></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="portlet-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}<div class="pagination"><center>{pager}</center></div>',
                    'columns' => [
                        'id',
                        [
                            'header' => 'html',
                            'content' => function ($model) {
                                return Html::encode($model->title);
                            },
                            'contentOptions' => ['style' => 'width:40%;']
                        ],
                        [
                            'attribute' => 'startTime',
                        ],
                        [
                            'attribute' => 'endTime',
                            'value' => function ($model) {
                                return $model->endTime ? $model->endTime : '长期';
                            }
                        ],
                        [
                            'attribute' => 'sortValue',
                            'value' => function ($model) {
                                return $model->sortValue ? $model->sortValue : '';
                            }
                        ],
                        [
                            'attribute' => 'advSn',
                            'value' => function ($model) {
                                return $model->advSn ? $model->advSn : '';
                            }
                        ],
                        [
                            'label' => '操作',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $html = '<a href="/adv/ranking/update?id=' . $model->id . '" class="btn mini green ajax_op"><i class="icon-edit"></i>编辑</a> |';
                                $html .= " <button onclick=\"if(confirm('确认删除')){window.location.href='/adv/ranking/delete?id=" . $model->id . "'}\" class=\"btn mini red ajax_op\">删除</button> ";

                                if (!empty($model->promoClass) && class_exists($model->promoClass)) {
                                    $html .= '| <button class="btn mini green ajax_op online_btn" id="'.$model->id.'">'.($model->isOnline ? '下线' : '上线').'</button>';
                                    $html .= '| <a href="/adv/ranking/award-list?id='.$model->id.'" class="btn mini green ajax_op"></i>查看获奖情况</a>';
                                }

                                return $html;
                            }
                        ],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('.online_btn').on('click', function () {
            var _this = $(this);
            _this.attr('disabled', true);
            var id = _this.attr('id');
            var xhr = $.get('online?id='+id);

            xhr.done(function (data) {
                newalert(!data.code, data.message, 1);
            })

            xhr.always(function() {
                _this.attr('disabled', false);
            });
        });
    })
</script>
<?php $this->endBlock(); ?>
