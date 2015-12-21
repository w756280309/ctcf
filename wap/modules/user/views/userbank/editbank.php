<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title="完善银行卡信息";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/base.css"/>
<link rel="stylesheet" href="/css/wansanxinxi.css"/>
<script src="/js/jquery.js"></script>
<script src="/js/swiper.min.js"></script>
<script src="/js/wansanxinxi.js"></script>
    <div class="row tishi">
        <div class="col-xs-12" style="padding-right: 0">*请注意核对信息,填写错误会影响您的正常取现</div>
    </div>
    <!--省份选择-->
    <div class="mask"></div>
    <div class="row">
        <div class="col-xs-2"></div>
        <div class="col-xs-8">
            <div class="banks"></div>
            <div class="banks1">
                <img src="/images/mask.png" alt="" class="mask-bank"/>
                <div class="close"><img src="/images/close.png" alt=""/></div>
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <?php foreach($province as $val): ?>
                        <div class="swiper-slide sheng" data-id="<?= $val['id'] ?>">
                            <div>
                                <span><?= $val['name'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Add Pagination -->
                    <!--<div class="swiper-pagination"></div>-->
                </div>
            </div>
        </div>
        <div class="col-xs-2"></div>
    </div>

    <!--城市选择-->
    <div class="row">
        <div class="col-xs-2"></div>
        <div class="col-xs-8">
            <div class="citys"></div>
            <div class="citys1">
                <img src="/images/mask.png" alt="" class="mask-bank"/>
                <div class="close"><img src="/images/close.png" alt=""/></div>
                <div class="swiper-container">
                    <div class="swiper-wrapper" id='city'>
                        
                    </div>
                    <!-- Add Pagination -->
                    <!--<div class="swiper-pagination"></div>-->
                </div>
            </div>
        </div>
        <div class="col-xs-2"></div>
    </div>

    <form method="post" class="cmxform" id="form" action="/user/userbank/editbank" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="row kahao">
            <div class="col-xs-4 xian">分支行名称</div>
            <div class="col-xs-8 xian" style="padding: 0"><input id="sub_bank_name" type="text" name="UserBanks[sub_bank_name]" placeholder="请输入分支行名称" value="<?= $model->sub_bank_name ?>"/></div>
            <input type="text" name="" style="display:none"/>
        </div>


        <div class="row kahao">
            <div class="col-xs-4 xian">分支行省份</div>
            <div class="col-xs-6 xian" style="padding: 0">
                <input type="hidden" id='province' name="UserBanks[province]" placeholder="请选择所在省份" value="<?= $model->province ?>"/>
            	<span class="selecter ss"><?= $model->province?$model->province:"请选择所在省份" ?></span>
            </div>
            <div class="col-xs-2 xian"><img src="/images/you.png" alt=""/></div>
        </div>

        <div class="row kahao">
            <div class="col-xs-4 xian">分支行城市</div>
            <div class="col-xs-6 xian" style="padding: 0">
                <input type="hidden" id='citys' name="UserBanks[city]" placeholder="请选择所在城市" value="<?= $model->city ?>"/>
            	<span class="selecter-city ss"><?= $model->city?$model->city:"请选择所在城市" ?></span>
            </div>
            <div class="col-xs-2 xian"><img class="selecter" src="/images/you.png" alt=""/></div>
        </div>
        <!--提交按钮-->
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6 login-sign-btn">
                <input id="editbankbtn" class="btn-common btn-normal" name="signUp" type="button" value="下一步">
            </div>
            <div class="col-xs-3"></div>
        </div>
    </form>
    <script type="text/javascript">
        $(function(){
           var err = '<?= $data['code'] ?>';
           var mess = '<?= $data['message'] ?>';
           var tourl = '<?= $data['tourl'] ?>';
           if(err == '1') {
               toasturl(tourl,mess);
           }
       });
     </script>
    
   
  