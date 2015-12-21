<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
$this->title = '操作提示';
error_reporting(E_ALL^E_NOTICE);
?>
<?php $this->beginBlock('blockhead'); ?>
    <meta name="copyright" content="金交中心" />
<?php $this->endBlock(); ?>

<?php $this->beginBlock('blockmain'); ?>

<!--操作成功 start--> 
<div class="change_done bd_db tipinfo">
	<p style="margin: 5px 0;">
		<span class="ico_big ico_done">
                    <?php if($res==1){ ?>
                    <img src="/images/success.png" class="tip-icon" />
                    <?php }else{ ?>
                    <img src="/images/error.png" class="tip-icon" />
                    <?php } ?>
                 </span>
		<span class="fw_b fs_14"><?php echo isset($message)?$message:'操作成功' ?></span>
	</p>
	<p class="line_30">现在去，
		<?php 
			foreach($links as $link){
                            echo Html::a($link[0], $link[1], array('class'=>'c_06c mr10'));
			}
		?>
	</p>
	<p class="c_666 ml40">该页将在 <span id='setouttime'>3</span>秒后自动跳转!</p>
</div> 
<!--操作成功 end-->        

<script language=javascript>
var int=self.setInterval("countdown()",3000);
function countdown(){
	var t=document.getElementById("setouttime").innerHTML-1;
	document.getElementById("setouttime").innerHTML=t;
	if(t===0){
		location='<?php echo $links[0][1]?>';
	}
}
</script>
    
<?php $this->endBlock(); ?>