<script src="/js/jquery-1.11.1.min.js"></script>
<link href="/css/common.css" rel="stylesheet">
<link href="/css/popup.css" rel="stylesheet">
<div class="clear"></div>
<?php if (!empty($r)) { ?>
<style>
   .tender-login-deng{text-align: center;line-height: 154px;}	
</style>
    <script type="text/javascript">
		parent.location.reload();
	</script>
	<div class="main tender-login-deng">
		请稍后……
	</div>
<?php } else { ?>
<div class="body tender-login">
	<div class="register_center register_select">
		<ul>
			<li>
				<a id="regsel_top" class="regclass" href="/user/login/popup-account?r=buy">
					<span></span>
					<p>投资账户登录</p>
				</a>
			</li> 
			<li>
				<a id="regsel_bot" class="regclass" href="/user/login/popup-account?r=raise">
					<span></span>
					<p>融资账户登录</p>
				</a>
			</li> 
		</ul>
		<div class="clear"></div>
	</div>
</div>
<script>
	$(function () {
		$('#regsel_top').hover(function () {
			$(this).find('span').css("background-position", "0 -104px");
			$(this).find('p').css("color", "#ff7800");
		}, function () {
			$(this).find('span').css("background-position", "0 0");
			$(this).find('p').css("color", "#5d5d5d");
		});
		$('#regsel_bot').hover(function () {
			$(this).find('span').css("background-position", "-135px -104px");
			$(this).find('p').css("color", "#ff7800");
		}, function () {
			$(this).find('span').css("background-position", "-135px 0");
			$(this).find('p').css("color", "#5d5d5d");
		});
	})
</script>

<?php } ?>