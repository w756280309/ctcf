<?php
use wap\assets\WapAsset;
$this->title = '数据安全';
$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/safeguard/major.min.css');
$this->registerJsFile(ASSETS_BASE_URI . 'ctcf/js/libs/lib.flexible3.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link href="<?= ASSETS_BASE_URI ?>ctcf/css/common/base.css" rel="stylesheet">
<!-- 主体 -->
<div class="padding-contain">
    <div class="major-title-msg">
        <h5>楚天财富网线上的电子合同是否有效？</h5>
    </div>
    <P class="data-not-retract">最新修正的《刑法诉讼法》、《民事诉讼法》均将电子数据列为证据的一种，电子数据保全中心提供的保全证书，可作为司法人员和律师分析、认定案件事实的有效证据，让受保者在司法纠纷中占据有利地位。电子数据保全中心还可以为受保者提供合作机构出具的公证书或司法鉴定书。楚天财富网联手易保全电子数据保全中心，为投资者提供交易凭证保全服务，交易凭证（担保函、担保合同等信息）一旦保全，其内容、生成时间等信息将被加密固定，且生成唯一的保全证书供下载。事后任何细微修改，都会导致保全证书函数值变化，有效防止人为篡改。如发生司法纠纷，保全证书持有人，可以通过易保全电子数据保全中心提供的认证证书向法院或仲裁机构提供有效、可靠的证据，从而获得举证的优势地位。电子数据保全中心打通保全存证、司法鉴定、法律服务等环节，以电子数据第三方保全为核心的平台，该平台目前已获得三项专利，6项国家CNAS资格认证，与司法鉴定中心、公证处实行对接，实时进行保全的信息同步，是国内最大的电子数据保全平台。</P>
</div>
