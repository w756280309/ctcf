<?php
use common\models\order\EbaoQuan;
use common\utils\StringUtils;
use yii\grid\GridView;
use yii\widgets\LinkPager;

?>
    <style>
        .left {
            text-align: left !important;
        }
    </style>
<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
    <div>
        <h3 class="page-title">
            贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
        </h3>

        <ul class="breadcrumb">
            <li>
                <i class="icon-home"></i>
                <a href="/product/productonline/list">贷款管理</a>
                <i class="icon-angle-right"></i>
            </li>
            <li>
                <a href="javascript:void(0);">转让列表</a>
            </li>
        </ul>
    </div>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}',
            'columns' => [
                [
                    'label' => '姓名',
                    'format' => 'html',
                    'value' => function ($data) use ($users){
                        return '<a href="/user/user/detail?id=' . $data['user_id'] . '&type=1">' . $users[$data['user_id']]->real_name . '</a>';
                    },
                ],
                [
                    'label' => '手机号',
                    'value' => function ($data) use ($users){
                        return $users[$data['user_id']]->mobile;
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '投资金额',
                    'value' => function ($data) {
                        return StringUtils::amountFormat3(bcdiv($data['amount'], 100));
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '投资时间',
                    'value' => function ($data) {
                        return $data['settleTime'];
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '保全合同',
                    'format' => 'raw',
                    'value' => function ($data) {
                            $str .= ' <a href="javascript:;" class="btn mini green" onclick="miitbaoquan('. $data['id'] . ')">' . 标的合同 . '</a> | ';
                            $str .= ' <a href="javascript:;" class="btn mini green" onclick="miitbaoquan('. $data['id'] . ','. 3 .')">' . 转让合同 . '</a> | ';
                            $str .= '<a href="/product/growth/transfer-cert?orderId='.$data['id'].'" target="_blank" class="btn mini" style="background-color:#36A9CE;color: white;">交易凭证</a>';
                        return $str;
                    }
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
    </div>
    <script>
        //重新保全（国家电子合同）
        function miitbaoquan(id, type = 2) {
            if (id !== null && id !== undefined && id !== '') {
                $.ajax({
                    type: "GET",
                    url: "/product/productonline/miit-baoquan",
                    data: {id : id, type : type},
                    dataType: "json",
                    success: function (data) {
                        if (data.code == 0) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            window.open(data.url);
                        }
                    }
                });
            }
        }
    </script>
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
<?php $this->endBlock(); ?>