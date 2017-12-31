<?php

$this->title = '新人红包升级';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/layer_mobile/layer.js"></script>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171229/css/index.css?v=0.1">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<div class="flex-content" id="app">
	<div class="part-top"></div>
	<div class="part-middle">
		<div id="btn-one" class="btn btn-red" style="padding: 0;left: 1.09333333rem;">补领180元红包</div>
		<div id="btn-two" class="btn btn-red" style="padding: 0;right: 1.09333333rem;">补领230元红包</div>
	</div>
	<div class="part-bottom">
		<a href="/user/invite" class="go-invest"></a>
	</div>
</div>
<script>
  $(function () {
    FastClick.attach(document.body);
    //获取当前两个按钮的状态 初始化按钮
    var c1 = "<?= $flags['c1'] ?>";
    var c2 = "<?= $flags['c2'] ?>";
    if(!!c1){
      $("#btn-one").removeClass("btn-red").addClass("btn-grey").html("已领180元红包");
    }
    if(!!c2){
      $("#btn-two").removeClass("btn-red").addClass("btn-grey").html("已领230元红包");
    }
    function eventTarget(event) {
      event.preventDefault();
    }

    //获取当前登录状态
    var isLogin = $("input[name=isLoggedin]").val();
    //点击红色按钮事件
    $(".btn-red").on("click", function () {
      //未登录，跳转登录
      if (isLogin == "false") {
        location.href = "/site/login?next=/promotion/p698/";
        return false;
      } else {
        //已登录，弹出确认弹窗
        if ($(this).index() == 0) {
          openPopup(1);
        } else {
          openPopup(2)
        }
      }
    });
    function openPopup(index) {
      $('body').on('touchmove', eventTarget, false);
      var ind = index;
      var message;
      var type;
      if (index == 1) {
        type = "c1";
        message = '代金券在领取前会一直保留，一旦领取将进入有效期，建议您需要使用的时候再领取。<br><br>是否现在就领取180元代金券（100万起投，有效期90天）？';
      } else {
        type = "c2";
        message = '代金券在领取前会一直保留，一旦领取将进入有效期，建议您需要使用的时候再领取。<br><br>是否现在就领取230元代金券（200万起投，有效期90天）？';
      }
      layer.open({
        title: [
          '温馨提示',
          'background-color: #ff6058; color:#fff;'
        ]
        , content: message
        , shadeClose: false
        , className: 'customer-layer-popuo'
        , btn: ['现在就用', '我再想想']
        , no: function (index) {
          layer.closeAll();
          $('body').unbind('touchmove');
        }
        , yes: function (index) {
          $.ajax({
            url: "/promotion/p698/pull",
            dataType: "json",
            type: "get",
            data: {type: type},
            success: function (res) {
	            if(res.code == 0){
                if(ind == 1){
                  $("#btn-one").removeClass("btn-red").addClass("btn-grey").html("已领180元红包");
                }else{
                  $("#btn-two").removeClass("btn-red").addClass("btn-grey").html("已领230元红包");
                }
	            }
	            else if(res.code == 2){
                location.href = "/site/login?next=/promotion/p698/";
	            }
	            else if(res.code == 3){
                layer.closeAll();
                layer.open({
                  content: '您已领取'
                  ,skin: 'msg'
                  ,time: 2
                });
                if(ind == 1){
                  $("#btn-one").removeClass("btn-red").addClass("btn-grey").html("已领180元红包");
                }else{
                  $("#btn-two").removeClass("btn-red").addClass("btn-grey").html("已领230元红包");
                }
	            }
	            else if(res.code == 4){
                layer.closeAll();
                layer.open({
                  content: '系统异常，请刷新后重试'
                  ,skin: 'msg'
                  ,time: 2
                });
	            }

              $('body').unbind('touchmove');
            }
          })
        }
      });
    }
  })
</script>
