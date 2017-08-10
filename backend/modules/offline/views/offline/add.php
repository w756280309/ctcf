<?php

$this->title = '导入新数据';
use yii\widgets\ActiveForm;

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                线下数据
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/offline/offline/add">导入新数据</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <div class="col-md-12">
            <table class="table">
                <tr>
                    <th>网点</th>
                    <th>产品名称</th>
                    <th>标的序号</th>
                    <th>产品期限</th>
                    <th>客户姓名</th>
                    <th>证件号</th>
                    <th>联系电话</th>
                    <th>开户行名称</th>
                    <th>银行卡账号</th>
                    <th>认购金额（万）</th>
                    <th>认购日</th>
                    <th>起息日</th>
                    <th>利率</th>
                </tr>
                <tr>
                    <td>长兴传媒</td>
                    <td>宁富37号青州第二期</td>
                    <td>OF201758f9db7ab7836</td>
                    <td>24个月</td>
                    <td>吴伯秋</td>
                    <td>330**********4101X</td>
                    <td>135******18</td>
                    <td>中信银行湖州长兴支行</td>
                    <td>62177*******4304</td>
                    <td>5.0</td>
                    <td>2017-08-04</td>
                    <td>2017-08-04</td>
                    <td>8.50%</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="portlet-body">
        <?php $form = ActiveForm::begin(['action'=>'/offline/offline/add', 'options'=>['class'=>'form-horizontal form-bordered form-label-stripped', 'enctype'=>'multipart/form-data']]); ?>
            <table class="table">
                <tr>
                    <td width="10%">
                        <span class="title" style="display: inline-block;line-height:27px;">上传excel表格</span>
                    </td>
                    <td>
                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken(); ?>">
                        <div>
                            <?= $form->field($model, 'excel', ['template' => '{input}'])->fileInput() ?>
                            <?= $form->field($model, 'excel', ['template' => '{error}']) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="flag" value="1" style="display: none;">
                    </td>
                    <td>
                        <button id="post-excel" class="btn blue" style="width: 60px;display: inline-block;">提交</button>
                        <a href="list" class="btn">取消</a>
                    </td>
                </tr>
            </table>
        <?php $form->end(); ?>
    </div>
</div>
<script>
    $('#post-excel').bind('click', function() {
        if ($(this).hasClass('clicked-btn')) {
            return false;
        }
        $(this).addClass('clicked-btn');
    });
</script>
<?php $this->endBlock(); ?>
