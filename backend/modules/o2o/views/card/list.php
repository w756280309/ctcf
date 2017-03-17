<?php

$this->title = '兑换码列表';
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

use common\utils\SecurityUtils;

?>
<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    运营管理 <small>O2O商家管理</small>
                    <a href="/o2o/card/supplement?affId=<?= $request['affId'] ?>" id="sample_editable_1_new" class="btn green" style="float: right;">
                        补充兑换码 <i class="icon-plus"></i>
                    </a>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/o2o/affiliator/list">商家列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/o2o/card/list?affId=<?= $request['affId'] ?>">兑换码列表</a>
                    </li>
                </ul>
            </div>

            <!-- 查询 start-->
            <div class="portlet-body">
                <form action="/o2o/card/list" method="get">
                    <input type="hidden" name="affId" value="<?= $request['affId'] ?>">
                    <table class="table search_form">
                        <tr>
                            <td>
                                <span class="title">商品名称</span>
                                <input type="text" class="m-wrap span8" name="goodsType_name" value="<?= $request['goodsType_name'] ?>" />
                            </td>
                            <td>
                                <span class="title">兑换码</span>
                                <input type="text" class="m-wrap span8" name="serial" value="<?= $request['serial'] ?>"/>
                            </td>
                            <td>
                                <span class="title">发放日期</span>
                                <input type="text" readonly class="m-wrap span8" name="pullDate" onclick="WdatePicker()" value="<?= $request['pullDate'] ?>" />
                            </td>
                            <td>
                                <span class="title">状态</span>
                                <select name="status" class="m-wrap span8">
                                    <option value="">请选择</option>
                                    <option value="0" <?php if ('0' === $request['status']) { ?> selected="selected"<?php } ?>>未发放</option>
                                    <option value="1" <?php if ('1' === $request['status']) { ?> selected="selected"<?php } ?>>已发放</option>
                                    <option value="2" <?php if ('2' === $request['status']) { ?> selected="selected"<?php } ?>>已使用</option>
                                    <option value="3" <?php if ('3' === $request['status']) { ?> selected="selected"<?php } ?>>已过期</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="left" class="search-btn">
                                    <button type='submit' class="btn blue btn-block button-search">查询
                                        <i class="m-icon-swapright m-icon-white"></i></button>
                                </div>
                            </td>
                            <td colspan="3">
                                <div align="right" class="search-btn">
                                    <a class="btn green btn-block" style="width: 140px;" href="/o2o/card/export?<?= http_build_query(Yii::$app->request->get())?>">导出筛选结果</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <!--根据时间查询数据 end-->

            <!--数据项包含 用户名称、用户手机号、单张代金券使用金额、单张代金券使用时间-->
            <div class="portlet-body">
                    <?= \yii\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                        'columns' => [
                            [
                                'contentOptions' => [
                                    'class' => 'text-center',
                                ],
                                'header' => '兑换码',
                                'value' => function ($model) {
                                    return $model->serial;
                                }
                            ],
                            [
                                'header' => '商品名称',
                                'value' => function ($model) {
                                    return $model->goods->name;
                                }
                            ],
                            [
                                'header' => '客户手机号',
                                'format' => 'html',
                                'value' => function ($model) {
                                    return isset($model->user_id) ? '<a href="/user/user/detail?id=' . $model->user->id . '">' . SecurityUtils::decrypt($model->user->safeMobile) . '</a>' : '--';
                                },
                            ],
                            [
                                'header' => '发放时间',
                                'value' => function ($model) {
                                    return null !== $model->pullTime ? $model->pullTime : '--';
                                }
                            ],
                            [
                                'header' => '使用时间',
                                'value' => function ($model) {
                                    return null !== $model->usedTime ? $model->usedTime : '--';
                                }
                            ],
                            [
                                'header' => '状态',
                                'value' => function ($model) {
                                    return $model->getStatusLabel();
                                }
                            ],
                        ]
                    ]) ?>
                </div>
        </div>
    </div>
<?php $this->endBlock(); ?>

