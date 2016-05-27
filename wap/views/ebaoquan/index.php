<?php
use yii\helpers\Html;
use common\view\AnalyticsHelper;

wap\assets\WapAsset::register($this);
AnalyticsHelper::registerTo($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no"/>
    <title>温都金服 - 数据保全</title>
    <?= Html::csrfMetaTags() ?>
    <meta name="csrf-param" content="_csrf">
    <meta name="csrf-token" content="bUJodzlZTEpVO1pDDzs2OwYxKyQIbSg.AjcEOQhhAysVNwcFQ209Ig==">
    <link href="/css/bootstrap.min.css?v=20160407" rel="stylesheet">
    <link href="/css/base.css?v=20160407" rel="stylesheet">
    <link href="/promo/1605/yibaoquan/css/index.css?v=&lt;?php echo time() ?&gt;" rel="stylesheet">
    <?php $this->head() ?>
    <script src="/assets/f7150be8/jquery.min.js"></script>
    <script src="/js/common.js"></script>
    <script src="/js/lib.js"></script>
    <script src="/js/jquery.cookie.js"></script>
    <script src="/js/hmsr.js?v=20160428"></script>
    <script src="/promo/1605/choujiang/js/520.js"></script>    <script>
        $(function() {
            $(document).ajaxSend(function(event, jqXHR, settings) {
                var match = window.location.search.match(new RegExp('[?&]token=([^&]+)(&|$)'));
                if (match) {
                    var val = decodeURIComponent(match[1].replace(/\+/g, " "));
                    settings.url = settings.url+(settings.url.indexOf('?') >= 0 ? '&' : '?')+'token='+encodeURIComponent(val);
                }
            });
        });
    </script>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container bg_color">
    <!-- banner-->
    <div class="icon">
        <img src="/promo/1605/yibaoquan/images/banner_1.png?v=1.0" alt="广告图">
        <img src="/promo/1605/yibaoquan/images/banner_2.png?v=1.0" alt="广告图">
        <img src="/promo/1605/yibaoquan/images/banner_3.png?v=1.0" alt="广告图">
        <img src="/promo/1605/yibaoquan/images/banner_4.png?v=1.0" alt="广告图">
        <p class="clear"></p>
    </div>
    <!--  title-->
    <div class="title_h3">
        <h3>温都金服交易电子数据保全</h3>
        <h3 style="margin-top: 10px;">将交易合同锁进保险箱</h3>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <p class="content">
                温都金服联手易保全电子数据保全中心，为投资者提供交易凭证保全服务，交易凭证（担保函、担保合同等信息）一旦保全，其内容、生成时间等信息将被加密固定，且生成唯一的保全证书供下载。事后任何细微修改，都会导致保全证书函数值变化，有效防止人为篡改。如发生司法纠纷，保全证书持有人，可以通过易保全电子数据保全中心提供的认证证书向法院或仲裁机构提供有效、可靠的证据，从而获得举证的优势地位。
            </p>
        </div>
    </div>
    <!-- 1、title-->
    <div class="row">
        <div class="col-xs-12">
            <div class="title_h4">
                <h4>一、电子数据保全是什么？</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <p class="content">
                电子数据在线保全是指对以电子数据形式（包括文字、图形、字母、数字、三维标志、颜色组合声音以及上述要素的组合等下同）存在的各类电子数据信息，运用专利技术进行运算、加密固定，载明保全生成的标准时间、运算值、档案编号等，以防止被人篡改，确保电子数据的原始性、客观性的程序及方法。
            </p>
        </div>
    </div>
    <!-- 2、title-->
    <div class="row">
        <div class="col-xs-12">
            <div class="title_h4">
                <h4>二、易保全电子数据保全中心提供什么？</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <p class="content content-subtitle">
                1.提供受法律认可的电子数据认证证书服务
            </p>
            <p class="content">
                国家专利局、CNAS中国国家实验室资格认证，让易保全电子数据保全中心出具的电子认证证书，受法律认可！
            </p>
            <p class="content content-subtitle">
                2.CFCA身份认证、电子签章服务
            </p>
            <p class="content">
                提供CFCA身份认证、电子签章，让身份认证更可靠
            </p>
            <p class="content content-subtitle">
                3.一站式存取证服务
            </p>
            <p class="content">
                电子数据保全中心打通保全存证、司法鉴定、法律服务等环节，真正做到一站式服务。
            </p>
        </div>
        <!-- 证书-->
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-6 content-img">
                    <img style="width: 96%;" src="/promo/1605/yibaoquan/images/sfjd.png?v=1.0" alt="广告图">

                </div>
                <div class="col-xs-6 content-img">
                    <img style="width: 96%;" src="/promo/1605/yibaoquan/images/gzjg.png?v=1.0" alt="广告图">
                </div>
            </div>
        </div>
        <!-- 证书-->
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-4 content-img">
                    <img style="width: 87%;" src="/promo/1605/yibaoquan/images/dzsjbq.png?v=1.0" alt="广告图">
                </div>
                <div class="col-xs-4 content-img">
                    <img style="width: 96%;" src="/promo/1605/yibaoquan/images/zl_1.png?v=1.0" alt="广告图">
                </div>
                <div class="col-xs-4 content-img">
                    <img style="width: 96%;" src="/promo/1605/yibaoquan/images/zl_2.png?v=1.0" alt="广告图">
                </div>
            </div>
        </div>
    </div>

    <!-- 3、title-->
    <div class="row">
        <div class="col-xs-12">
            <div class="title_h4">
                <h4>三、常见问题</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <p class="content content-subtitle">
                Q：投资者如何检验保全证书真伪？
            </p>
            <p class="content">
                A：投资者拿到保全证书后，可以登录www.ebaoquan.org，录入证书上的备案号及上传被保全文件（如电子合同）进行真伪验证，或者从收到证书生成的邮件中，点击“查看我的保全证书”，进入证书页面使用验证功能。
            </p>
            <p class="content content-subtitle">
                Q：电子数据保全是否具有法律效力？
            </p>
            <p class="content">
                A：最新修正的《刑法诉讼法》、《民事诉讼法》均将电子数据列为证据的一种，电子数据保全中心提供的保全证书，可作为司法人员和律师分析、认定案件事实的有效证据，让受保者在司法纠纷中占据有利地位，根据受保护者需要，电子数据保全中心还可以为受保者需要，电子数据保全中心还可以为受保者提供合作机构出具的公证书或司法鉴定书。
            </p>
            <p class="content content-subtitle">
                Q：为什么选择电子数据保全？
            </p>
            <p class="content">
                A：相较传统取证手段，电子数据保全具有低成本、高效率、保密（系统仅仅在本地生成数据的数字摘要，绝无泄露隐私、商业秘密、内容的风险！）合法、权威等优势，并且可以通过事先存证来预防和化解纠纷，是互联网投资者保护交易证据安全的首选。
            </p>
            <p class="content">
                易保全电子数据中心是以电子数据第三方保全为核心的平台，面向金融、电商、医疗、通讯等行业提供专业定制化的保全服务。该平台目前已获得三项专利，6项国家CNAS资格认证，与司法鉴定中心、公证处实行对接，实时进行保全的信息同步，是国
                内最大的电子数据保全平台。
            </p>
        </div>
    </div>


</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
