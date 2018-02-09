<?php

$this->title = '玩填字 赢红包';
?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180205/css/index.css?v=1.3">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/layer_mobile/layer.js"></script>
<script>
    var bwAccessToken = false;
    var match = window.location.search.match(new RegExp('[?&]token=([^&]+)(&|$)'));
    if (match) {
        var val = decodeURIComponent(match[1].replace(/\+/g, " "));
        bwAccessToken = encodeURIComponent(val);
    }
</script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js?v=20180208-1"></script>
<style>
    .flex-content .bottom-part .bottom-play .play-choice-list li {
        width: 13.995%;
    }
    .flex-content .bottom-part .bottom-play .play-choice-list li.btn-submit {
        width: 2.63rem;
        background-size: 100% 100%;
    }
</style>
<div class="flex-content">
    <div class="top-part">
        <div class="top-text top-ready">
            <p class="line-one">活动时间：2018.2.8~2.12，每天都能来玩！</p>
            <p class="line-two">查看奖励：完成游戏即可抽取红包，奖励即时发放到账，请到“账户”-“<a href="/user/coupon/list">优惠券</a>”中查看。</p>
        </div>
        <div class="top-text top-questions" style="display: none">
            <p class="question-text">问题：<span></span></p>
            <span class="question-process"><span>1</span>/3</span>
        </div>
    </div>
    <div class="bottom-part">
        <div class="bottom-ready">
            <ul class="letter-list clearfix" style="margin-bottom: 0.32rem;">
                <li>每</li>
                <li>日</li>
                <li>填</li>
                <li>字</li>
            </ul>
            <ul class="letter-list clearfix">
                <li>狂</li>
                <li>撒</li>
                <li>红</li>
                <li>包</li>
            </ul>
            <div class="btn-start"></div>
            <p class="show-rules">活动规则</p>
        </div>
        <div class="bottom-play" style="display: none;">
            <ul class="play-answer-list">

            </ul>
            <ul class="play-choice-list clearfix">
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="letter-li"><span class="single-letter"></span></li>
                <li class="btn-submit"></li>
            </ul>
        </div>
        <div class="bottom-result" style="display:none;">
            <p class="result-answer">正确答案：<span></span></p>
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180205/images/pic_result_wrong.png" alt=""
                 class="result-picture result-picture-wrong" style="display: none">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180205/images/pic_result_true.png" alt=""
                 class="result-picture result-picture-true" style="display: none;">
            <div class="result-btn">下一题(<span>3</span>)</div>
        </div>
    </div>
</div>
<div class="mask" style="display: none"></div>
<!--规则弹窗-->
<div class="popup pop-rules " style="display: none">
    <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20180205/images/pic_close.png" alt="">
    <p class="rules-title">活动规则</p>
    <ul class="rules-list">
        <li>活动时间：2018.2.8至2.12；</li>
        <li>填字游戏玩法：<br>在页面下方文字区点击相应文字，填满上方答案区空格，再点击“提交”，即可提交答案进入下一题；<br>如果填错文字，可以点击答案区填错的文字，即可完成删除。</li>
        <li>每轮填字游戏有3道题，答对越多，获得奖励的几率越高哦！欢迎您邀请亲朋好友协力参与，抢走大红包！</li>
        <li>活动期间每位用户每天最多有2次免费游戏机会，其中第2次游戏机会必须分享本活动到朋友圈才能获得；</li>
        <li>活动期间每天的游戏机会将在次日0点重置，请当日使用完；</li>
        <li>活动期间，每天2次游戏机会用完后，可以次日至本活动页继续参与游戏；</li>
        <li>本次活动虚拟奖品将立即发放到账，实物奖品将在7个工作日内与您联系。</li>
    </ul>
</div>
<!--玩法弹窗-->
<div class="popup pop-method" style="display: none">
    <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20180205/images/pic_close.png" alt="">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180205/images/pic_method.png" alt="" class="method-pic">
    <div class="method-close"></div>
</div>
<!--没机会提示弹窗-->
<div class="popup-small pop-nochance" style="display: none">
    <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20180205/images/pic_close.png" alt="">
    <div class="popup-small-btn no-change-btn"></div>
</div>
<!--分享弹窗-->
<div class="popup-small pop-share" style="display: none">
    <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20180205/images/pic_close.png" alt="">
    <div class="popup-small-btn share-btn" style="cursor: pointer;"></div>
</div>
<!--结果弹窗-->
<div class="popup-small pop-result" style="display: none">
    <img class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20180205/images/pic_close.png" alt="">
    <img src="" alt="" class="result-pic">
    <span class="result-number"></span>
    <div class="result-btns clearfix">
        <div class="lf share-btn" style="cursor: pointer;"></div>
        <div class="rg"></div>
    </div>
</div>

<script>
    $(function () {

        var boolClick = true;
        //题库
        var quesCase = []; //dataJson.questions;
        //进度
        var quesIndex = 0;
        //当前答案
        var answerIndex = '';
        //当前答案长度
        var answerLen = 0;
        //答对题数量
        var correct = 0;
        var boolclk = true;

        for (var i in dataJson.questions) {
            quesCase.push(dataJson.questions[i])
        }

        if(dataJson.isPopGameGuide){
            $(".mask,.pop-method").show();
        }
        //layer提示
        function showMsg(msg) {
            layer.open({
                content: msg
                , skin: 'msg'
                , time: 2 //2秒后自动关闭
            });
        }

        var t;
        //倒计时
        function downTime() {
            var time = 2;
            t = setInterval(function () {
                $(".result-btn span").html(time);
                time--;
                if (time < 0) {
                    clearInterval(t);
                    $(".result-btn").click();
                }
            }, 1000)
        }

        //初始化题目
        function initQuestion(i) {
            $(".play-answer-list li").remove();
            $.each($(".letter-li"), function (index, value) {
                if ($(this).has("span").length == 0) {
                    $(this).append("<span class='single-letter'></span>")
                }
            })
            $(".single-letter").html("");
            for (var j = 0; j < quesCase[i].answerLength; j++) {
                $(".play-answer-list").append("<li></li>")
            }
            $.each($(".single-letter"), function (index, value) {
                $(this).html(quesCase[i].dictionary[index])
            })
            $(".question-text span").html(quesCase[i].question);
            $(".question-process span").html(quesIndex + 1);
            $(".result-answer span").html(quesCase[i].answer);
            $(".result-btn span").html("3");
            answerIndex = quesCase[i].answer;
            answerLen = quesCase[i].answerLength;
        }

        //显示下一题
        function nextQuestion(result) {
            if (result) {
                $(".result-picture-true").show()
            } else {
                $(".result-picture-wrong").show()
            }
            quesIndex++;
            $(".bottom-play").hide();
            $(".bottom-result").show();
            boolclk = true;
            downTime();
            $(".result-btn").on('click', function () {
                initQuestion(quesIndex);
                clearInterval(t);
                $(".result-picture,.bottom-result").hide();
                $(".bottom-play").show();
            })
        }
        function eventTarget(event) {
            event.preventDefault();
        }


        //点击显示活动规则
        $(".bottom-ready .show-rules").on('click', function () {
            if (dataJson.promoStatus == 2) {
                showMsg("活动已结束");
                return false;
            }
            $(".mask,.pop-rules").show()
            //$('body').on('touchmove', eventTarget, false);
        })
        //点击"开始挑战"按钮
        $(".bottom-ready .btn-start").on('click', function () {
            if (dataJson.promoStatus == 1) {
                showMsg("活动未开始");
                return false
            } else if (dataJson.promoStatus == 2) {
                showMsg("活动已结束");
                return false
            } else if (dataJson.isLoggedIn == false) {
                location.href = "/site/login"
            } else {
              $.ajax({
                url:"/promotion/p180207/waste?token="+bwAccessToken,
                type:"get",
                dataType:"json",
                success:function(res){
	                if(res.code == 0){
                    initQuestion(quesIndex);
                    $(".top-ready,.bottom-ready").hide();
                    $(".top-questions,.bottom-play").show()
	                }else{
                    showMsg(res.data.message);
                    return false;
	                }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  var res = jqXHR.responseJSON
                  if(res.code == 5){
                    $(".pop-nochance,.mask").show();
                  }else if(res.code == 4){
                    $(".pop-share,.mask").show();
                  }else if(res.code == 3){
                    location.href = "/site/login"
                  }
                }
              })
            }
        })
        //点击每道题的提交按钮
        $(".btn-submit").on("click", function () {
            if(boolclk){
                boolclk = false;
                var answerNowArr = [];
                $.each($(".play-answer-list li span"), function (index, value) {
                    answerNowArr.push($(value).html())
                })
                var answerNow = answerNowArr.join("");
                if (answerNow.length != answerLen) {
                    showMsg("请完成当前题目");
                    boolclk = true;
                    return false;
                }
                if (quesIndex != 2) {
                    if (answerNow == answerIndex) {
                        correct++;
                        nextQuestion(true)
                    } else {
                        nextQuestion(false)
                    }
                } else {
                    if (answerNow == answerIndex) {
                        correct++;
                    }
                    $.ajax({
                      url:"/promotion/p180207/reply?token="+bwAccessToken,
                      type:"post",
                      dataType:"json",
                      data:{
                        sn:quesCase[2].batchSn,
                        correctNum:correct,
                        _csrf:'<?= Yii::$app->request->getCsrfToken()?>'
                      },
                      success:function(res){
                        $(".pop-result .result-pic").attr('src','<?= FE_BASE_URI ?>'+res.path);
                        $(".pop-result .result-number").html(correct);
                        $(".mask,.pop-result").show();
                        boolclk = true;
                      },
                      error: function (jqXHR, textStatus, errorThrown) {
	                      var res = jqXHR.responseJSON
	                      if(res.code == 5){
                          $(".pop-nochance,.mask").show();
	                      }else if(res.code == 4){
                          $(".pop-share,.mask").show();
	                      }
                      }
                    })
                    //post请求时使用
//                    var params = new URLSearchParams();
//                    params.append('sn', quesCase[2].batchSn);
//                    params.append('correctNum', correct);
//                    params.append('_csrf', '//');
//                    axios({
//                        method: "post",
//                        url: "/promotion/p180207/reply?token="+bwAccessToken,
//                        data: params
//                    })
//                        .then(function (response) {
//                            $(".pop-result .result-pic").attr('src','//'+response.data.path);
//                            $(".pop-result .result-number").html(correct);
//                            $(".mask,.pop-result").show();
//                            boolclk = true;
//                        })
//                        .catch(function (error) {
//                            var res = error.response.data;
//                            if(res.code == 5){
//                                $(".pop-nochance,.mask").show();
//                                //$('body').on('touchmove', eventTarget, false);
//                            }else if (res.code == 4){
//                                $(".pop-share,.mask").show();
//                                //$('body').on('touchmove', eventTarget, false);
//                            }
//                        });
                }
            }
        })
        //关闭弹窗
        $("img.close,.method-close,.no-change-btn").on("click", function () {
            $(this).parent().hide();
            $(".mask").hide();
            //$('body').unbind('touchmove');
        })
        $(".pop-share .close").on('click',function () {
            location.reload();
        })
        //重新开始
        $(".pop-result img.close,.pop-result .rg").on("click", function () {
            location.reload();
        });

        //点击备选字
        $(".flex-content").on("click", ".single-letter", function () {
            if (boolClick) {
                boolClick = false
                var _this = $(this);
                //索引值
                var _index = _this.parent().index();
                //内容
                var letterNow = $(this).html();
                $.each($(".play-answer-list li"), function (index, value) {
                    if (value.innerHTML == "") {
                        //赋内容值 索引值 清除原位置内容
                        var position = $(this).offset();
                        var positionOld = _this.offset();
                        _this.css({"position": "fixed", "top": positionOld.top, "left": positionOld.left})
                        _this.animate({'top': (position.top + 5) + 'px', 'left': (position.left + 5) + 'px'}, 'normal', function () {
                            _this.remove()
                            value.innerHTML = "<span data-xulie='" + _index + "'>" + letterNow + "</span>"
                            setTimeout(function () {
                                boolClick = true
                            }, 400)
                            return false
                        })
                    }
                })
            }
        })
        //清除答案
        $(".flex-content").on("click",".play-answer-list li", function () {
            //获取索引
            var xulie = $(this).children().data("xulie");
            var content = $(this).children().html();
            $(".play-choice-list li:eq(" + xulie + ")").append("<span class='single-letter'>" + content + "</span>")
            $(this).children().remove();
            boolClick = true;
        })

        var appId = '<?= \Yii::$app->params['weixin']['appId'] ?>';
        var shareData = {
            title: "我正在玩【答题抢红包】小游戏，百万元红包正在派发，你也快来玩吧！",
            des: "点击链接，马上参与~",
            link: "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/promotion/p180207/",
            imgUrl: "https://static.wenjf.com/upload/link/link1517922530613507.png",
            appId: appId
        };

        wxShare.TimelineSuccessCallBack = function () {
            $.get('/promotion/p180207/add-share?scene=timeline&shareUrl='+encodeURIComponent(location.href), function (data) {
            });
        }
        wxShare.setParams(shareData.title, shareData.des, shareData.link, shareData.imgUrl, shareData.appId);
    })
</script>