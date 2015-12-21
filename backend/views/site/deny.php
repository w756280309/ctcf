<?php
/* @var $this yii\web\View */
$this->title = '温都金服后台管理系统';
?>
<?= $this->blocks['block1eft'] ?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">

        <div class="span12">
            
                <h3 class="page-title">
                        您没有权限进行此项操作
                </h3>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="120">程序版本: </td>
                <td width="250">温都金服 正式版 1.0.0</td>
                <td width="120">  </td>
                <td> </td>
            </tr>
            <tr>
                <td width="120" colspan="4">  <p class="c_666 ml40">该页将在 <span id='setouttime'>3</span>秒后自动跳转到上一页!</p>
        <p class="c_666 ml40">若页面没有反应，<a href="javascript:window.history.back();">请点击返回上一页</a></p></td>
            </tr>
        </table>
    </div>
</div>
<script language=javascript>
var int=self.setInterval("countdown()",3000);
function countdown(){
	var t=document.getElementById("setouttime").innerHTML-1;
	document.getElementById("setouttime").innerHTML=t;
	if(t===1){
            window.history.back();
	}
}
</script>
<?php $this->endBlock(); ?>
