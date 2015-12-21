<?php
use yii\helpers\Html;
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>
        <link href="/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
        <link href="/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <script src="/js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript" src="/js/layer/layer.min.js"></script>
        <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
        <script type="text/javascript" src="/js/showres.js"></script>
        <style type="text/css">
            .portlet-body .title{
                height: 34px;
                line-height: 34px;
                vertical-align: middle;
                font-size: 14px;
            }
            .has-error .help-block{
                color: red;
            } 
            .form-group div.radio label{
                width:400px;
            }
        </style>

    </head>
    <body class="page-header-fixed page-full-width" style="background-color:white !important">
        <div class="page-container row-fluid" style="margin-top:0px">		
            <div class="page-content">
                <div class="form-horizontal form-view">
                    <div class="control-group">
                        <?php if ($model->status == 1) { ?>
                            <label class="control-label">放款</label>
                        <?php } elseif ($model->status == 2) { ?>
                            <label class="control-label">提现详情</label>
                        <?php } else { ?>
                            <label class="control-label">提现申请</label>
                        <?php } ?>
                    </div>
                    <?php if ($model->status != 1) { ?>
                        <div class="row-fluid">
                            <div class="span6 ">
                                <div class="control-group">
                                    <label class="control-label" for="">开户行:</label>
                                    <div class="controls">
                                        <span class="text"><?= $userBank->bank_name ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">卡号:</label>
                                    <div class="controls">
                                        <span class="text"><?= $userBank->card_number ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">分支行:</label>
                                    <div class="controls">
                                        <span class="text"><?= $userBank->sub_bank_name ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">户名:</label>
                                    <div class="controls">
                                        <span class="text"><?= $userBank->account ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">手机号:</label>
                                    <div class="controls">
                                        <span class="text"><?= $tixianSq->mobile ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">提现金额（元）:</label>
                                    <div class="controls">
                                        <span class="text"><?= $model->money ?></span>
                                    </div>
                                </div>

                                <?php if ($model->status == 2) { ?>
                                    <div class="control-group">
                                        <label class="control-label" for="">状态:</label>
                                        <div class="controls">
                                            <span class="text">提现成功</span>
                                        </div>
                                    </div>                        
                                    <div class="control-group">
                                        <label class="control-label" for="">放款时间：</label>
                                        <div class="controls">
                                            <span class="text"><?= date('Y-m-d H:i:s', $model->updated_at) ?></span>
                                        </div>
                                    </div>                        
                                <?php } ?>
                            </div>
                        </div>
     
                    <?php } else { ?>
                    
                        <div class="row-fluid">
                            <div class="span6 ">
                                <div class="control-group">
                                    <label class="control-label" for="">开户行:</label>
                                    <div class="controls">
                                        <span class="text"><?= $userBank->bank_name ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">卡号:</label>
                                    <div class="controls">
                                        <span class="text"><?= $userBank->card_number ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">分支行:</label>
                                    <div class="controls">
                                        <span class="text"><?= $userBank->sub_bank_name ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">户名:</label>
                                    <div class="controls">
                                        <span class="text"><?= $userBank->account ?></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="">手机号:</label>
                                    <div class="controls">
                                        <span class="text"><?= $tixianSq->mobile ?></span>
                                    </div>
                                </div>
                                <hr>
                                <p class="alert-msg"><center><span class="text">确定给用户 <?= $tixianSq['real_name'] ?> 放款 <?= $model['money'] ?> 元？</span></center></p>
                            </div>

                        </div>
                    <?php } ?>

                    <?php if ($model->status == 1) { ?>
                        <div class="form-actions">
                            <button type="button" class="btn green fkbutton" data-index="fk">确定</button>
                            <button type="button" class="btn red fkbutton" data-index="close">取消</button>
                        </div>          
                    <?php } ?>

                    <?php if (isset($model->status) && $model->status == 0 || $model->status == 11) { ?>
                        <div class="form-actions">
                            <button type="button" class="btn green fkbutton" data-index="sh" data-type="1">通过</button>
                            <button type="button" class="btn red fkbutton" data-index="sh" data-type="11">不通过</button>
                        </div>           
                    <?php } elseif ($model->status == 2) { ?>
                        <div class="form-actions">
                            <button type="button" class="btn green fkbutton" data-index="close">确定</button>                     
                        </div>           
                    <?php } ?>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                $('.fkbutton').bind('click', function() {
                    var key = $(this).attr('data-index');
                    var id = <?= Yii::$app->request->get('id') ?>;
                    var uid = <?= Yii::$app->request->get('pid') ?>;
                    var csrftoken = '<?= Yii::$app->request->getCsrfToken(); ?>';
                    if (key == 'sh') {
                        var type = $(this).attr("data-type");
                        $(this).attr('disabled', 'disabled');
                        $(this).html('正在处理……');
                        //审核通过按钮
                        $.post('/user/drawrecord/checksq', {id: id, type: type, _csrf: csrftoken}, function(data)
                        {
                            if (data) {
                                parent.location.reload();
                            }
                        });

                    } else if (key == 'fk') {
                        //审核通过按钮
                        $.post('/user/drawrecord/checksqfangkuan',{id: id, uid: uid, _csrf:csrftoken},function(data)
                        {
                            if(data){
                                parent.location.reload();
                            }
                        });
                    } else if (key == 'close') {
                        closewin();
                    }

                })
            })
        </script>
    </body>
</html>

