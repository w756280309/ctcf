<?php

$this->title = '答题开宝箱';
?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/T1591/css/layout.min.css?v=6">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/layer_mobile/layer.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script>
  var bwAccessToken = false;
  var match = window.location.search.match(new RegExp('[?&]token=([^&]+)(&|$)'));
  if (match) {
    var val = decodeURIComponent(match[1].replace(/\+/g, " "));
    bwAccessToken = encodeURIComponent(val);
  }
</script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js?v=3"></script>

<style>
	[v-cloak] {
		display: none
	}
</style>
<input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
<div id="app" class="flex-content">
	<div v-cloak class="part-ready" v-show="!ifGame">
		<div class="ready-btn btn-rules" @click="toggleRules"></div>
		<div class="ready-btn btn-list" @click="toggleAward"></div>
		<div class="btn-begin" @click="begin"></div>
	</div>
	<div v-cloak class="part-play" v-show="ifGame">
		<div class="play-countdown">倒计时：<span>20</span></div>
		<ul class="play-question">
			<li class="question" style="list-style-type: none">{{questionIndex + 1}}、{{questionNow.content}}请选择：</li>
		</ul>
		<ul class="play-answer-list">
			<li v-for="(value,key) in questionNow.options" @click="flag && selected($event,key)" :data-id="key"><span>{{value}}</span>
			</li>
		</ul>
		<div class="play-btn submit" @click="showResult" v-show="ifshowSubmit">查看结果</div>
		<div class="play-btn next" @click="showNext" v-show="ifshowNext">下一题</div>
		<div class="play-btn answer" @click="showAnswer" v-show="ifshowAnswer">提交答案</div>
		<div class="play-btn static" v-show="ifshowStatic">提交答案</div>
	</div>
	<!--规则弹窗-->
	<div v-cloak class="pops pop-rules" v-show="ifshowRules">
		<div class="btn-close" @click="toggleRules"></div>
		<p class="rules-title">活动规则</p>
		<ul class="rules-list">
			<li>活动时间：2018.2.22至2.26；</li>
			<li>每轮答题游戏有5道题，答对越多，获得奖励的几率越高；</li>
			<li>活动期间每位用户每天最多有2次免费游戏机会，其中第2次游戏机会必须分享本活动到朋友圈才能获得；</li>
			<li>活动期间每天的游戏机会将在次日0点重置，请当日使用完；</li>
			<li>活动期间，每天2次游戏机会用完后，可以次日进入本活动页继续参与游戏；</li>
			<li>本次活动虚拟奖品将立即发放到账户，实物奖品将在7个工作日内与您联系；</li>
			<li>本活动最终解释权归温都金服所有。</li>
		</ul>
	</div>
	<!--结果弹窗-->
	<div v-cloak class="pops pop-result" v-show="ifshowResults">
		<div class="btn-close" @click="reload"></div>
		<p class="result-title">本轮答题完成,<br> 共答对 <span class="result-num"></span>道题!</p>
		<img src="<?= FE_BASE_URI ?>wap/campaigns/T1591/images/pic_award_big.png" alt=""
		     class="result-pic result-pic-one" style="display: none">
		<img src="<?= FE_BASE_URI ?>wap/campaigns/T1591/images/pic_award_no.png" alt=""
		     class="result-pic result-pic-two" style="display: none">
		<!--<p class="result-content">哎呀，红包与您擦肩而过~</p>-->
		<p class="result-content"></p>
		<div class="result-btns clearfix">
			<div class="lf share-btn" style="cursor: pointer">去分享</div>
			<div class="rg" @click="reload">再玩一次</div>
		</div>
	</div>
	<!--分享弹窗-->
	<div v-cloak class="pops pop-share" v-show="ifshowShare">
		<div class="btn-close" @click="toggleShare"></div>
		<p class="share-content">您没有答题次数了!<br>分享到朋友圈,<br>还能再玩一次哦!</p>
		<div class="share-btn">立即分享</div>
		<p class="share-remind">提示:必须分享到朋友圈才有效哦！</p>
	</div>
	<!--没有机会弹窗-->
	<div v-cloak class="pops pop-nochance" v-show="ifshowNochance">
		<div class="btn-close" @click="toggleNochance"></div>
		<p class="share-content">您今天已经用完<br>全部答题次数了哦!<br>明天再来玩吧!</p>
		<div class="nochance-btn" @click="toggleNochance">我知道了</div>
		<p class="share-remind">提示:活动期间每天都能来答题哦！</p>
	</div>
	<!--获奖列表弹窗-->
	<div v-cloak class="pops pop-awards" v-show="ifshowAward">
		<div class="btn-close" @click="closeAward"></div>
		<div class="awards-title">奖品列表</div>
		<ul class="awards-list">
			<li class="clearfix" v-for="list in awardList">
				<div class="lf"></div>
				<div class="rg">
					<p class="award-name">{{list.name}}</p>
					<p class="award-date">中奖时间{{list.awardTime}}</p>
				</div>
			</li>
		</ul>
	</div>
	<!--无奖品弹窗-->
	<div v-cloak class="pops pop-noawards" v-show="ifshowNoaward" style="padding-bottom: 2.093333rem;">
		<div class="btn-close" @click="closeNoaward"></div>
		<p class="noawards-title" style="margin-bottom: 1.653333rem;">奖品列表</p>
		<p class="noawards-content" style="margin-bottom: 0.4rem;">您还没有中奖哦!</p>
	</div>
	<div v-cloak class="mask" v-show="ifshowMask"></div>
</div>
<script>

  $(function () {
    //安卓微信防缩放代码
    (function () {
      if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
        handleFontSize();
      } else {
        document.addEventListener("WeixinJSBridgeReady", handleFontSize, false);
      }
      function handleFontSize() {
        // 设置网页字体为默认大小
        WeixinJSBridge.invoke('setFontSizeCallback', {'fontSize': 0});
        // 重写设置网页字体大小的事件
        WeixinJSBridge.on('menu:setfont', function () {
          WeixinJSBridge.invoke('setFontSizeCallback', {'fontSize': 0});
        });
      }
    })();

    function showMsg(msg) {
      layer.open({
        content: msg
        , skin: 'msg'
        , time: 2 //2秒后自动关闭
      });
    }

    var model = new Vue({
      el: '#app',
      data: {
        promoStatus: dataJson.promoStatus, //活动状态
        isLoggedIn: dataJson.isLoggedIn, //登录状态
        activeTicketCount: 1, //游戏机会
        questionCase: [], //本轮题库
        questionIndex: 0, //当前题目的索引值
        questionNow: { //当前的题目

        },
        answerArr: {},//答案数组
        answerNow: "",//当前答案
        awardList: [ //获奖列表

        ],
        ifGame: false,
        ifshowMask: false, //蒙层显示与否
        ifshowRules: false, //规则弹窗显示与否
        ifshowResults: false, //结果弹窗显示与否
        ifshowShare: false, //分享弹窗显示与否
        ifshowNochance: false, //没机会弹窗显示与否
        ifshowAward: false, //奖品列表弹窗显示与否
        ifshowNoaward: false,//无奖品弹窗显示与否
        ifshowSubmit: false,//各个按钮的显隐
        ifshowNext: false,
        ifshowAnswer: false,
        ifshowStatic: true,
        flag: true, //选项点击事件与否的开关
        batchSn: '',
        correct: 0
      },
      methods: {
        //开关奖品弹窗的方法
        toggleAward: function () {
          //此处先判断登录状态和活动类型，然后请求列表接口，根据结果显示不同显示
          var that = this;
          if (this.isLoggedIn == false) {
            location.href = "/site/login"
          } else {
            //请求接口
            $.ajax({
              url: "/promotion/p180222/award-list?key=promo_180222",
              type: "get",
              dataType: "json",
              success: function (res) {
                if (res.length == 0) {
                  //列表为空 直接显示为中奖弹窗
                  that.ifshowMask = !that.ifshowMask;
                  that.ifshowNoaward = !that.ifshowNoaward;
                } else {
                  that.awardList = [];
                  for (var i = 0; i < res.length; i++) {
                    if (res[i].sn != "180222_ZW") {
                      that.awardList.unshift(res[i])
                    }
                  }
                  if (that.awardList.length == 0) {
                    that.ifshowMask = !that.ifshowMask;
                    that.ifshowNoaward = !that.ifshowNoaward;
                  } else {
                    that.ifshowMask = !that.ifshowMask;
                    that.ifshowAward = !that.ifshowAward;
                  }
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
								/*错误信息处理*/
                layer.open({
                  content: ''
                  , skin: 'msg'
                  , time: 2 //2秒后自动关闭
                });
              }
            })
          }
        },
        //关闭奖品弹窗
        closeAward: function () {
          this.ifshowMask = !this.ifshowMask;
          this.ifshowAward = !this.ifshowAward;
        },
        //关闭无奖品弹窗
        closeNoaward: function () {
          this.ifshowMask = !this.ifshowMask;
          this.ifshowNoaward = !this.ifshowNoaward;
        },
        //开关规则弹窗的方法
        toggleRules: function () {
          this.ifshowMask = !this.ifshowMask;
          this.ifshowRules = !this.ifshowRules;
        },
        //开关无奖品弹窗的方法
        toggleNoaward: function () {
          this.ifshowMask = !this.ifshowMask;
          this.ifshowNoaward = !this.ifshowNoaward;
        },
        //开关分享的方法
        toggleShare: function () {
          this.ifshowMask = !this.ifshowMask;
          this.ifshowShare = !this.ifshowShare;
        },
        //开关没机会的方法
        toggleNochance: function () {
          this.ifshowMask = !this.ifshowMask;
          this.ifshowNochance = !this.ifshowNochance;
        },
        //开始答题按钮
        begin: function () {
          //判断各种状态，都ok后开始
          var that = this;
          if (that.isLoggedIn == false) { //登录状态判断
            location.href = "/site/login"
          } else if (that.promoStatus == 1) { //活动状态判断
            showMsg("活动还未开始")
          } else if (that.promoStatus == 2) {
            showMsg("活动已经结束")
          } else { //可以请求开始接口
            $.ajax({
              url: "/promotion/p180222/begin",
              type: "get",
              dataType: "json",
              success: function (res) {
                if (res.code == 0) {
                  //渲染题目
                  that.batchSn = dataJson.questions[0].batchSn;
                  that.questionCase = dataJson.questions;
                  that.questionNow = that.questionCase[that.questionIndex];
                  that.ifGame = !that.ifGame;
                  countDown();
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
								/*错误信息处理*/
                var res = JSON.parse(jqXHR.responseText);
                if (res.code == 1) {
                  showMsg("活动还未开始")
                } else if (res.code == 2) {
                  showMsg("活动已经结束")
                } else if (res.code == 5) {
                  that.ifshowMask = !that.ifshowMask;
                  that.ifshowNochance = !that.ifshowNochance;
                } else if (res.code == 4) {
                  that.ifshowMask = !that.ifshowMask;
                  that.ifshowShare = !that.ifshowShare;
                }
              }
            })
          }
        },
        //选择一个选项
        selected: function (event, key) {
          $(".play-answer-list li").removeClass("bg-selected");
          $(event.target).closest("li").addClass("bg-selected");
          this.answerNow = key;
          if (!!this.answerNow) {
            this.ifshowStatic = false;
            this.ifshowAnswer = true;
          }
        },
        //提交答案按钮
        showAnswer: function () {
          var that = this;
          clearInterval(t);
          if (this.questionIndex < that.questionCase.length - 1) {
            //只请求submit接口
            $.ajax({
              url: "/promotion/p180222/submit",
              type: "post",
              dataType: "json",
              data: {
                qid: that.questionNow.id,
                opt: that.answerNow,
                _csrf: "<?= Yii::$app->request->csrfToken ?>"
              },
              success: function (res) {
                if (res.code == 0) {
                  if (that.answerNow == res.ticket) { //答对了
                    $.each($(".play-answer-list li"), function () {
                      if (this.getAttribute("data-id") == that.answerNow) {
                        $(this).addClass("bg-correct-answer")
                      }
                    })
                    that.flag = false;
                    that.ifshowAnswer = false;
                    that.ifshowStatic = false;
                    that.ifshowNext = true;
                    that.correct++;
                  } else if (that.answerNow == "") { //未选择
                    $(".play-answer-list li").removeClass();
                    $.each($(".play-answer-list li"), function () {
                      if (this.getAttribute("data-id") == res.ticket) {
                        $(this).addClass("bg-correct")
                      } else {
                        $(this).addClass("bg-error")
                      }
                    });
                    that.flag = false;
                    that.ifshowAnswer = false;
                    that.ifshowStatic = false;
                    that.ifshowNext = true;
                  } else { //错误
                    $(".play-answer-list li").removeClass();
                    $.each($(".play-answer-list li"), function () {
                      if (this.getAttribute("data-id") == that.answerNow) {
                        $(this).addClass("bg-error-answer")
                      } else if (this.getAttribute("data-id") == res.ticket) {
                        $(this).addClass("bg-correct")
                      }
                    });
                    that.flag = false;
                    that.ifshowAnswer = false;
                    that.ifshowStatic = false;
                    that.ifshowNext = true;
                  }
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
								/*错误信息处理*/
              }
            })
          } else {
            //先请求submit 后回调
            $.ajax({
              url: "/promotion/p180222/submit",
              type: "post",
              dataType: "json",
              data: {
                qid: that.questionNow.id,
                opt: that.answerNow,
                _csrf: "<?= Yii::$app->request->csrfToken ?>"
              },
              success: function (res) {
                if (res.code == 0) {
                  //把最后一题的答案存入结果对象
                  that.answerArr[that.questionNow.id] = that.answerNow;
                  $.ajax({
                    url: "/promotion/p180222/finish",
                    type: "post",
                    dataType: "json",
                    data: {
                      sn: that.batchSn,
                      res: JSON.stringify(that.answerArr),
                      _csrf: "<?= Yii::$app->request->csrfToken ?>"
                    },
                    success: function (data) {
                      if (data.code == 0) {
                        //成功 先显示结果
                        if (that.answerNow == res.ticket) { //答对了
                          $.each($(".play-answer-list li"), function () {
                            if (this.getAttribute("data-id") == that.answerNow) {
                              $(this).addClass("bg-correct-answer")
                            }
                          });
                          that.flag = false;
                          that.ifshowAnswer = false;
                          that.ifshowStatic = false;
                          that.ifshowSubmit = true;
                          that.correct++
                        } else if (that.answerNow == "") { //未选择
                          $(".play-answer-list li").removeClass();
                          $.each($(".play-answer-list li"), function () {
                            if (this.getAttribute("data-id") == res.ticket) {
                              $(this).addClass("bg-correct")
                            } else {
                              $(this).addClass("bg-error")
                            }
                          });
                          that.flag = false;
                          that.ifshowAnswer = false;
                          that.ifshowStatic = false;
                          that.ifshowSubmit = true;
                        } else { //错误
                          $(".play-answer-list li").removeClass();
                          $.each($(".play-answer-list li"), function () {
                            if (this.getAttribute("data-id") == that.answerNow) {
                              $(this).addClass("bg-error-answer")
                            } else if (this.getAttribute("data-id") == res.ticket) {
                              $(this).addClass("bg-correct")
                            }
                          });
                          that.flag = false;
                          that.ifshowAnswer = false;
                          that.ifshowStatic = false;
                          that.ifshowSubmit = true;
                        }
                        //将中奖结果存入
                        $(".result-num").html(that.correct);
                        if (data.ticket.ref_amount == 0) {
                          //未中奖
                          $(".result-pic-two").show();
                          $(".result-content").html("哎呀，红包与您擦肩而过~");
                        } else {
                          //中奖
                          $(".result-pic-one").show();
                          $(".result-content").html("恭喜您获得" + parseInt(data.ticket.ref_amount) + "元代金券~");
                        }
                      }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
											/*错误信息处理*/
                    }
                  })
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
								/*错误信息处理*/
              }
            })
          }
        },
        //进入下一题
        showNext: function () {
          //存入结果对象
          this.answerArr[this.questionNow.id] = this.answerNow;
          //题目序列号++
          this.questionIndex++;
          //渲染题目和按钮
          this.questionNow = this.questionCase[this.questionIndex];
          $(".play-answer-list li").removeClass();
          this.ifshowStatic = true;
          this.ifshowNext = false;
          this.ifshowAnswer = false;
          //清空答案
          this.answerNow = "";
          //放开点击事件 开始新的倒计时
          this.flag = true;
          countDown();
        },
        //查看结果按钮
        showResult: function () {
          this.ifshowResults = !this.ifshowResults;
          this.ifshowMask = !this.ifshowMask;
        },
        //刷新页面
        reload: function () {
          location.reload();
        }
      }
    })

    var t;
    //倒计时函数
    function countDown() {
      var second = 20;
      t = setInterval(function () {
        $(".play-countdown span").html(second);
        second--;
        if (second < 0) {
          //自动提交
          model.showAnswer()
          clearInterval(t);
          second = 20
        }
      }, 1000)
    }

    var appId = '<?= \Yii::$app->params['weixin']['appId'] ?>';
    var shareData = {
      title: "我正在玩【答题开宝箱】小游戏，百万红包正在狂撒，你也快来玩吧！",
      des: "点击链接，立即参与~",
      link: "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/promotion/p180222/",
      imgUrl: "https://static.wenjf.com/upload/link/link1518419706309630.png",
      appId: appId
    };

    wxShare.TimelineSuccessCallBack = function () {
      $.get('/promotion/p180222/add-share?scene=timeline&shareUrl=' + location.href, function (data) {
      });
    };
    wxShare.setParams(shareData.title, shareData.des, shareData.link, shareData.imgUrl, shareData.appId);

  })
  Vue.config.devtools = true


</script>
