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
                    'value' => function ($data) {
                        return '<a href="/user/user/detail?id=' . $data['user_id'] . '&type=1">' . $data['user']->real_name . '</a>';
                    },
                ],
                [
                    'label' => '手机号',
                    'value' => function ($data) {
                        return $data['user']->mobile;
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
                    'format' => 'html',
                    'value' => function ($data) {
                        $baoquanData = $data['baoquan'];
                        $str = '';
                        foreach ($baoquanData as $baoQuan) {
                            /**
                             * @var EbaoQuan $baoQuan
                             */
                            $url = \EBaoQuan\Client::contractFileDownload($baoQuan);
                            if ($baoQuan->type === EbaoQuan::TYPE_LOAN) {
                                $title = '标的合同';
                            } elseif ($baoQuan->type === EbaoQuan::TYPE_CREDIT) {
                                $title = '转让合同';
                            }
                            if (empty($url) || empty($title)) {
                                continue;
                            }

                            $str .= ' <a href="' . $url . '">' . $title . '</a> |';
                        }
                        if (empty($str)) {
                            $str = '等待生成保全合同';
                        }
                        return rtrim($str, '|');
                    }
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
    </div>
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
<?php $this->endBlock(); ?>