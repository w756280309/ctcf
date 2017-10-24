<?php

$this->title = '产品列表';

use common\models\order\OnlineFangkuan;
use common\models\product\RateSteps;
use common\utils\StringUtils;
use yii\widgets\LinkPager;
use yii\helpers\Html;

$pc_cat = Yii::$app->params['pc_cat'];
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);

/**
 * @var \backend\modules\product\models\LoanSearch $loanSearch
 */

?>


<?php $this->beginBlock('blockmain'); ?>
    <style>
        div.item_label, div.item_content {
            display: inline-block;
        }

        div.item_label span, div.item_label select {
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
            width: 70px;
            text-align: left;
            margin-right: 5px;
        }

        div.item_content {
            width: 180px;
        }
        div.item_label{
            float: left;
        }
        div.item_label span{
            height: 34px;
            line-height: 34px;
        }
        div.item {
            margin: 5px 0;
            padding: 0;
            min-width: 230px;
        }

        div.item_content input.m-wrap {
            diplay: inline-block;
            width: 84px;
            height: 30px;
        }

        div.item_content input.m-wrap.span4 {
            width: 100%;
        }

        div.item_content select.m-wrap {
            width: 100%
        }

        div.item_content input.m-wrap.span6 {
            width: 100%;
        }

        div.item a {
            width: 160px;
        }

        #loanFilter .group_buttons {
            margin-left: 0;
            padding-left: 0;
        }

        @media (max-width: 768px) {
            #loanFilter .group_buttons div {
                display: block;
                margin: 10px 0;
            }
        }

        @media (min-width: 768px) {
            #loanFilter .group_buttons div.button_right {
                float: right;
                margin: 15px 5px;
            }

            #loanFilter .group_buttons div.button_left {
                float: left;
                margin: 15px 5px;
            }
        }
    </style>
    <div class="container-fluid" style="background-color: white;min-width: 1300px;min-height: 800px">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
    <div class="span12">
        <h3 class="page-title">
            <a href="add" class="btn green float-right">
                <i class="icon-plus"></i> 新增项目
            </a>
        </h3>

        <ul class="breadcrumb">
            <li>
                <i class="icon-home"></i>
                <a href="/product/productonline/list">贷款管理</a>
                <i class="icon-angle-right"></i>
            </li>
            <li>
                <a href="javascript:void(0);">项目列表</a>
            </li>
        </ul>
    </div>

    <!--search start-->
    <div class="portlet-body" style="padding:0 20px">
        <form action="/product/productonline/list" method="get" target="_self" id="loanFilter">
            <input type="hidden" name="days" value="<?= $loanSearch->days ?>">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-sx-12">
                    <div class="item">
                        <div class="item_label">
                            <span class="title">项目名称</span>
                        </div>
                        <div class="item_content">
                            <input id="name" type="text" class="m-wrap span6" name='title'
                                   value="<?= $loanSearch->title ?>" placeholder="请输入项目名称"/>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item_label">
                            <span class="title">项目副标题</span>
                        </div>
                        <div class="item_content">
                            <input id="internalTitle" type="text" class="m-wrap span6" name='internalTitle'
                                   value="<?= $loanSearch->internalTitle ?>" placeholder="请输入项目副标题"/>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item_label">
                            <span class="title">到期日</span>
                        </div>
                        <div class="item_content">
                            <input type="text" name="finishDateStart" value="<?= $loanSearch->finishDateStart ?>"
                                   class="m-wrap"
                                   onclick="WdatePicker()"> -
                            <input type="text" name="finishDateEnd" value="<?= $loanSearch->finishDateEnd ?>"
                                   class="m-wrap"
                                   onclick="WdatePicker()">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="item">
                        <div class="item_label">
                            <span class="title">状态</span>
                        </div>
                        <div class="item_content">
                            <select id="type" class="m-wrap" name='status'>
                                <option value="">--请选择--</option>
                                <option value="0" <?= Yii::$app->request->get('status') == '0' ? 'selected' : '' ?>>
                                    未上线
                                </option>
                                <?php foreach ($loanStatus as $key => $val): ?>
                                    <option value="<?= $key ?>"
                                        <?php
                                        if ($loanSearch->status == $key) {
                                            echo 'selected';
                                        }
                                        ?> >
                                        <?= $val ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item_label">
                            <span class="title">序号</span>
                        </div>
                        <div class="item_content">
                            <input id="sn" type="text" class="m-wrap span4" name='sn' value="<?= $loanSearch->sn ?>"
                                   placeholder="请输入序号"/>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item_label">
                            <span class="title">投资时间</span>
                        </div>
                        <div class="item_content">
                            <input type="text" name="investDateStart" value="<?= $loanSearch->investDateStart ?>"
                                   class="m-wrap"
                                   onclick="WdatePicker()"> -
                            <input type="text" name="investDateEnd" value="<?= $loanSearch->investDateEnd ?>"
                                   class="m-wrap"
                                   onclick="WdatePicker()">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="item">
                        <div class="item_label">
                            <span class="title">标的类别</span>
                        </div>
                        <div class="item_content">
                            <select class="m-wrap" name="isTest" id="isTest">
                                <option value="0" selected>正式标</option>
                                <option value="1">测试标</option>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item_label">
                            <span class="title">发行方编号</span>
                        </div>
                        <div class="item_content">
                            <input id="sn" type="text" class="m-wrap span4" name='issuerSn'
                                   value="<?= $loanSearch->issuerSn ?>" placeholder="请输入发行方编号"/>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item_label">
                            <span class="title">类型</span>
                        </div>
                        <div class="item_content">
                            <select class="m-wrap" name="cid">
                                <option value="" >全部</option>
                                <option value="1" <?= $loanSearch->cid === 1 ? 'selected' : '' ?>><?= $pc_cat['1'] ?></option>
                                <option value="2" <?= $loanSearch->cid === 2 ? 'selected' : '' ?>><?= $pc_cat['2'] ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="item">
                        <div class="item_label">
                            <span class="title">是否隐藏</span>
                        </div>
                        <div class="item_content">
                            <select class="m-wrap" name="isHide">
                                <option value="0" <?= $loanSearch->isHide ? '' : 'selected' ?>>显示可见标的</option>
                                <option value="1" <?= $loanSearch->isHide ? 'selected' : '' ?>>显示隐藏标的</option>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item_label">
                            <span class="title">新手标</span>
                        </div>
                        <div class="item_content">
                            <select class="m-wrap" name="isXs" id="isTest">
                                <option value="" <?= is_null($loanSearch->isXs) ? 'selected' : '' ?>>--请选择--</option>
                                <option value="0" <?= !is_null($loanSearch->isXs) && !$loanSearch->isXs ? 'selected' : '' ?>>
                                    非新手标
                                </option>
                                <option value="1" <?= !is_null($loanSearch->isXs) && $loanSearch->isXs ? 'selected' : '' ?>>
                                    新手标
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="group_buttons">
                <div class="button_right">
                    <button type='submit' class="btn blue" style="width: 100px;">查询 <i
                                class="m-icon-swapright m-icon-white"></i></button>
                </div>
                <div class="button_right">
                    <input type="button" class="btn" value="重置" onclick="formReset()"/>
                </div>
                <div class="button_left">
                    <a href="/product/productonline/export?exportType=user_invest_data&
                       <?= parse_url(Yii::$app->request->getAbsoluteUrl(), PHP_URL_QUERY) ?>" class="btn"
                       target="_blank">导出用户投资记录</a>
                </div>
                <div class="button_left">
                    <a href="/product/productonline/export?exportType=loan_invest_data&<?= parse_url(Yii::$app->request->getAbsoluteUrl(),
                        PHP_URL_QUERY) ?>" class="btn" target="_blank">导出标的投资信息</a>
                </div>
            </div>
        </form>
    </div>
    <!--search end -->

    <div class="portlet-body">
        <table class="table table-striped table-bordered table-advance table-hover">
            <thead>
            <tr>
                <th><input class="chooseall" type='checkbox'></th>
                <th>序号</th>
                <th width="80">名称</th>
                <th width="50">类型</th>
                <th width="50">期限</th>
                <th width="100">利率（%）</th>
                <th width="90" class="money">募集(元)</th>
                <th width="120" class="money">实际募集(元)</th>
                <th width="80">满标时间</th>
                <th width="80">起息时间</th>
                <th width="80">放款时间</th>
                <th width="60">状态</th>
                <th width="60">到期日</th>
                <th class="action">
                    <center>操作</center>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            /**
             * @var \common\models\product\OnlineProduct $val
             */
            ?>
            <?php if (count($models) < 1) echo '<tr><td><font color="red">暂无数据</font></td></tr>'; ?>
            <?php foreach ($models as $key => $val) : ?>
                <tr>
                    <td>
                        <input class="choice" type='checkbox' name='choose[]' value='<?= $val['id'] ?>'>
                    </td>
                    <td><?= $val['sn'] ?></td>
                    <td>
                        <?= $val['title'] ?>
                        <?php if (!empty($val['internalTitle'])) { ?>
                            <span style="color: #949494;">
                                        （<?= $val['internalTitle'] ?>）
                                    </span>
                        <?php } ?>
                    </td>
                    <td><?= $pc_cat[$val['cid']] ?></td>
                    <td>
                        <?php $ex = $val->getDuration() ?>
                        <?= $ex['value'] ?>
                        <?= $ex['unit'] ?>
                    </td>
                    <td>
                        <?php
                        echo StringUtils::amountFormat2(bcmul($val['yield_rate'], 100, 2));
                        if ($val->isFlexRate) {
                            echo '~' . StringUtils::amountFormat2(RateSteps::getTopRate(RateSteps::parse($val['rateSteps'])));
                        }
                        ?>
                    </td>
                    <td class="money"><?= number_format($val['money'], 2) ?></td>
                    <td class="money"><?= number_format($val['funded_money'], 2) ?></td>
                    <td>
                        <?= ($val['status'] > 2 && $val['status'] != 4) ? date('Y-m-d H:i:s', $val['full_time']) : '--' ?>
                    </td>
                    <td>
                        <?= !empty($val['jixi_time']) ? date('Y-m-d', $val['jixi_time']) : '--' ?>
                    </td>
                    <td>
                        <?= !empty($val['fk_examin_time']) ? date('Y-m-d', $val['fk_examin_time']) : '--' ?>
                    </td>
                    <td><?= $val['online_status'] ? $loanStatus[$val['status']] : '未上线' ?></td>
                    <td><?= $val['finish_date'] ? date('Y-m-d', $val['finish_date']) : '' ?></td>
                    <td>
                        <a href="/product/productonline/show?id=<?= $val['id'] ?>" class="btn mini green"><i
                                    class="icon-edit"></i> 查看</a>
                        | <a href="/product/productonline/quote?id=<?= $val['id'] ?>" class="btn mini green"><i
                                    class="icon-edit"></i> 引用</a>
                        | <a href="/product/productonline/edit?id=<?= $val['id'] ?>" class="btn mini green"><i
                                    class="icon-edit"></i> 编辑</a>
                        <?php if ($isHide) : ?>
                            | <a href="javascript:hideLoan('<?= $val->id ?>', '<?= $val->title ?>')"
                                 class="btn mini red"><i class="icon-minus-sign"></i> 还原</a>
                        <?php else : ?>
                            <?php if ($val->online_status && $val->funded_money <= 0) { ?>
                                | <a href="javascript:hideLoan('<?= $val->id ?>', '<?= $val->title ?>')"
                                     class="btn mini red"><i class="icon-minus-sign"></i> 隐藏</a>
                            <?php } ?>
                            <?php if ($val['status'] < 2) { ?>
                                | <a href="javascript:del('/product/productonline/del','<?= $val['id'] ?>')"
                                     class="btn mini red ajax_op" op="status" data-index="<?= $val['status'] ?>"
                                     index="<?= $val['id'] ?>"><i class="icon-minus-sign"></i>删除</a>
                            <?php } ?>
                            <?php if ($val['online_status'] == 1 && ($val['status'] == 3 || $val['status'] == 7) && empty($val['fk_examin_time']) && 1 === (int)$val['is_jixi']) { ?>
                                |
                                <a href="javascript:openwin('/order/onlinefangkuan/examinfk?pid=<?= $val['id'] ?>',800,400)"
                                   class="btn mini green"><i class="icon-edit"></i> 放款审核</a>
                            <?php } ?>
                            <?php if ($val['online_status'] == 1 && $val['status'] > 1) { ?>
                                | <a href="/order/onlineorder/list?id=<?= $val['id'] ?>" class="btn mini green"><i
                                            class="icon-edit"></i> 投标记录</a>
                            <?php } ?>
                            <?php if ($val->allowRepayment()) { ?>
                                | <a href="/repayment/repayment?pid=<?= $val['id'] ?>" class="btn mini green"><i
                                            class="icon-edit"></i> 还款</a>
                            <?php } ?>
                            <?php if ($val['fk_examin_time'] && in_array($val->fangkuan->status, [OnlineFangkuan::STATUS_EXAMINED, OnlineFangkuan::STATUS_FANGKUAN, OnlineFangkuan::STATUS_TIXIAN_FAIL])) { ?>
                                | <a href="javascript:fk('<?= $val['id'] ?>');" class="btn mini green"><i
                                            class="icon-edit"></i> 放款</a>
                            <?php } ?>
                            <?php if ($val['online_status'] == 1 && (in_array($val['status'], [3, 5, 7])) && $val['is_jixi'] == 0) { ?>
                                | <a href="javascript:void(0)"
                                     onclick="openwin('/product/productonline/jixi?product_id=<?= $val['id'] ?>',500,300)"
                                     class="btn mini green"><i class="icon-edit"></i> 计息</a>
                                <?php if ($val->isCustomRepayment && !empty($val->jixi_time) && !$val->isJixiExamined) { ?>
                                    | <a href="/product/productonline/jixi-examined?id=<?= $val->id ?>"
                                         class="btn mini green">计息审核</a>
                                <?php } ?>
                                <?php if ($val->isJixiExamined) { ?>
                                    | <a href="javascript:corfirmJixi('<?= $val['id'] ?>');" class="btn mini green"><i
                                                class="icon-edit"></i> 确认计息</a>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($val['online_status'] == 1 && $val['status'] == 2) { ?>
                                | <a href="javascript:endproduct('<?= $val['id'] ?>')" class="btn mini green"><i
                                            class="icon-edit"></i> 结束项目</a>
                            <?php } ?>
                            <?php if (!empty($val['online_status']) && empty($val['isPrivate'])) {
                                if (empty($val['recommendTime'])) {
                                    ?>
                                    <a href="javascript:recommend('<?= $val['id'] ?>')" class="btn mini green"><i
                                                class="icon-edit"></i> 推荐</a>
                                <?php } else { ?>
                                    <a href="javascript:recommend('<?= $val['id'] ?>')" class="btn mini red"><i
                                                class="icon-minus-sign"></i> 取消推荐</a>
                                <?php }
                            } ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn green btn-block btn-block-line-on" style="width: 100px;float:left;"><i class="icon-edit"></i>上线
        </button>
    </div>
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?>
    </div>

    <script type="text/javascript">
        $(function () {
            $('.chooseall').click(function () {
                var isChecked = $(this).parent().hasClass('checked');
                if (!isChecked) {
                    $("input[name='choose[]']").parent().addClass('checked');
                } else {
                    $("input[name='choose[]']").parent().removeClass('checked');
                }
            });

            $('.btn-block-line-on').click(function () {
                var objs = $("input[name='choose[]']").parent();
                var ids = new Array();
                var ckidkey = 0;

                for (var i = 0; i < objs.length; i++) {
                    if ($(objs[i]).hasClass('checked')) {
                        ids[ckidkey] = $($("input[name='choose[]']").get(i)).val();
                        ckidkey++;
                    }
                }

                if (ids.length == 0) {
                    alert('请选择上线记录');
                    return false;
                }

                var csrftoken = '<?= Yii::$app->request->getCsrfToken(); ?>';
                if (confirm('确认将选择的记录上线吗？')) {
                    openLoading();
                    $.post('/product/productonline/lineon', {pids: ids.join(','), _csrf: csrftoken}, function (data) {
                        alert(data.message);
                        if (data.result == 1) {
                            location.reload();
                        }
                        cloaseLoading();
                    });
                }
            });
        })

        /**
         * 请求后台放款逻辑.
         */
        function fk(pid) {
            var csrftoken = '<?= Yii::$app->request->getCsrfToken() ?>';
            if (confirm('确认放款吗？')) {
                openLoading();
                var xhr = $.post('/repayment/repayment/fk', {pid: pid, _csrf: csrftoken}, function (data) {
                    alert(data.msg);
                    if (1 === data.res) {
                        location.reload();
                    }
                    cloaseLoading();
                });

                xhr.error(function () {
                    alert('系统繁忙,请稍后重试!');
                    location.reload();
                    cloaseLoading();
                });
            }
        }

        function del(url, id) {
            if (confirm('确认删除？')) {
                var csrftoken = '<?= Yii::$app->request->getCsrfToken(); ?>';
                var xhr = $.post(url, {id: id, _csrf: csrftoken}, function (data) {
                    newalert(data.code, data.message, 1);
                });
            }
        }

        function corfirmJixi(pid) {
            var csrftoken = '<?= Yii::$app->request->getCsrfToken(); ?>';
            if (confirm('确认计息吗？')) {
                openLoading();
                $.post('/product/productonline/jixicorfirm', {id: pid, _csrf: csrftoken}, function (data) {
                    cloaseLoading();
                    alert(data.message);
                    if (data.result == 1) {
                        location.reload();
                    }
                });
            }
        }

        function endproduct(pid) {
            var csrf = '<?= Yii::$app->request->getCsrfToken(); ?>';
            layer.confirm('是否要提前结束此项目的募集？', {title: '结束项目', btn: ['确定', '取消']}, function () {
                openLoading();//打开loading
                $.post("/product/productonline/found", {id: pid, _csrf: csrf}, function (result) {
                    cloaseLoading();//关闭loading
                    newalert(parseInt(result['result']), result['message'], 1);
                });
            }, function () {
                layer.closeAll();
            })
        }

        function hideLoan(pid, name) {
            var type = '<?= $isHide ? '还原' : '隐藏' ?>';
            layer.confirm('是否要' + type + '<font style="color: red;">' + name + '</font>项目？', {
                title: type + '项目',
                btn: ['确定', '取消']
            }, function () {
                openLoading();  //打开loading
                $.get('/product/productonline/hide-loan', {id: pid}, function (data) {
                    cloaseLoading();    //关闭loading
                    newalert(!data.code, data.message, 1);
                });
            }, function () {
                layer.closeAll();
            });
        }

        function recommend(pid) {
            $.get("/product/productonline/recommend", {id: pid}, function (data) {
                alert(data.message);
                if (data.code === 1) {
                    location.reload();
                }
            });
        }

        function formReset() {
            $.removeCookie('loanListFilterIsTest', {path: '/'});
            window.location.href = '/product/productonline/list';
        }
    </script>
<?php $this->endBlock(); ?>