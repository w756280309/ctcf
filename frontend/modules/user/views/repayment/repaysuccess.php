<?php
$this->registerJs("var laySum = 0; ;", 1); //定义变量 介绍layer的index
$this->registerJsFile('/js/layer/layer.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/user.js', ['depends' => 'yii\web\YiiAsset']);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
	<style>
		.repaysuccess_div p{text-align:center;margin-top: 64px;}
		.repaysuccess_div span{display:block; width:122px; height:32px; }
		.repaysuccess_div span a{width:62px; height:25px; display:block; background-color:#ff7800; color:#fff; font-size:14px; text-align:center; font-family:'微软雅黑'; line-height:23px; border-radius:5px; margin-left:160px;margin-top: 53px;text-decoration:none;cursor:pointer;}
		.repaysuccess_div span a:hover {color: #fff; }
	</style>
    <body>
		<div class="repaysuccess_div">
			<p>还款成功</p>
			<span><a onclick="lout(1)">确定</a></span>
		</div>
	</body>
</html>
<script type="text/javascript">
	function lout(re) {
		if(re==1){
                                window.parent.location.reload()
                    }
		var index = window.parent.getlay();
		//parent.location.href="/user/login/logout";
		parent.layer.close(index);
	}
</script>