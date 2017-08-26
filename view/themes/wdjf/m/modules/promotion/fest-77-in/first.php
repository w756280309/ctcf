<?php
use common\models\adv\Share;
$this->title = "七夕大作战";

$this->share = new Share([
    'title' => '我在这里玩答题闯关获得了大红包！快来一起玩吧！',
    'description' => '温都金服七夕献礼，海量红包、礼品送不停！',
    'imgUrl' => FE_BASE_URI.'wap/campaigns/active20170823/images/wx_share.png',
    'url' => Yii::$app->request->hostInfo.'/promotion/fest-77-in/index',
]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170823/css/question.css?v=1">

<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>
<div class="flex-content">
    <div class="part-top"></div>
    <div class="part-middle">
        <div class="page-ready">
            <p>纤云弄巧，飞星传恨，</p>
            <p>银汉迢迢暗度。</p>
            <p>又是一年七夕到，</p>
            <p>快来免费玩答题，</p>
            <p>还能100%领红包哦！</p>
            <div class="btn-begin"></div>
        </div>
        <div class="page-question" style="display: none;">
            <div class="question-title">
                <p>1、七夕源自哪一对情侣的故事？</p>
            </div>
            <ul class="question-options clearfix">
                <li><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/dot_1.png" alt=""><span>牛郎织女</span></li>
                <li><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/dot_1.png" alt=""><span>梁山伯祝英台</span></li>
                <li><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/dot_1.png" alt=""><span>张生莺莺</span></li>
                <li><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/dot_1.png" alt=""><span>焦仲卿刘兰芝</span></li>
            </ul>
            <p class="question-warning yincang">提示：答案错误</p>
            <div class="question-button"></div>
        </div>
        <div class="page-result" style="display: none">
            <p class="result-correct">您答对了 <span>5</span> 题</p>
            <p class="result-congra">恭喜您获得</p>
            <div class="result-coupon">
                <p class="coupon-jine"><span><?= !is_null($coupon) ? ceil($coupon->amount) : 10 ?></span>元代金券</p>
                <p class="coupon-qitou"><span><?= !is_null($coupon) ? ceil($coupon->minInvest) : 1000 ?></span>元起投</p>
            </div>
            <div class="result-link clearfix">
                <!--<a href="#" class="lf"></a>-->
                <!--此处是不可点击状态下的按钮-->
                <?php if (!is_null($status) && $status == 1) { ?>
                    <a href="javascript:;" class="lf share qwsz"></a>
                <?php } else if (!is_null($status) && $status == 2)  { ?>
                    <a href="/promotion/fest-77-in/first" class="lf link-disable"></a>
                <?php } else { ?>
                    <a href="javascript:;" class="lf link-disable"></a>
                <?php } ?>
                <a href="second" class="rg"></a>
            </div>
        </div>
    </div>
    <div class="part-bottom"></div>
</div>
<script>
    <?php if (!is_null($status) && ($status == 1 || $status ==3) ) { ?>
        $(".page-question").hide();
        $(".page-ready").hide();
        $(".page-result").show();
    <?php }?>
    //题库
    var quesLib = [
        {'title': '、七夕源自哪一对情侣的故事？', 'options': ['牛郎织女', '焦仲卿刘兰芝', '张生莺莺', '梁山伯祝英台'], 'answer': '1'},
        {'title': '、七夕七巧源于哪个朝代？', 'options': ['汉朝', '唐朝', '宋朝', '明朝'], 'answer': '1'},
        {'title': '、阻挡牛郎织女的银河是什么？', 'options': ['金箍棒', '宝莲灯', '玉净瓶', '王母的簪子'], 'answer': '4'},
        {'title': '、以下不属于七夕别称的是', 'options': ['乞巧节', '女儿节', '午日节', '穿针节'], 'answer': '3'},
        {'title': '、七夕又称乞巧节，乞巧指的是什么？', 'options': ['心灵手巧', '来年丰收', '生活富足', '子孙满堂'], 'answer': '1'},
        {'title': '、每年七夕，牛郎织女会在哪里相见？', 'options': ['网络', '月亮', '梦里', '鹊桥'], 'answer': '4'},
        {'title': '、七夕的应节小吃是什么？', 'options': ['月饼', '粽子', '饺子', '巧果'], 'answer': '4'},
        {'title': '、以下哪个不是七夕的习俗？', 'options': ['穿针乞巧', '赛龙舟', '喜蛛应巧', '投针验巧'], 'answer': '2'},
    ];
    //生成随机1-8的数组
    function createRandom(num, from, to) {
        var arr = [];
        var json = {};
        while (arr.length < num) {
            //产生单个随机数
            var ranNum = Math.ceil(Math.random() * (to - from)) + from;
            //通过判断json对象的索引值是否存在 来标记 是否重复
            if (!json[ranNum]) {
                json[ranNum] = 1;
                arr.push(ranNum);
            }
        }
        return arr;
    }

    var shareCallBack = function () {
        $.ajax({
            url: '/promotion/fest-77-in/share',
            type: "get",
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    $(".qwsz").attr('href', '/promotion/fest-77-in/first');
                    $(".qwsz").removeClass('share');
                    $(".qwsz").addClass('link-disable');
                }
            }
        });
    };

    $(function () {
        var quesArr = createRandom(5, 0, 7);
        var quesLibNow = [];
        //当前问题序号
        var quesIndex = 1;
        //当前问题的答案序号
        var answerSelect;
        for (var i = 0; i < 5; i++) {
            quesLibNow[i] = quesLib[quesArr[i]];
        }
        //渲染当前题目和答案
        $(".page-question .question-title p").text(quesIndex + quesLibNow[0].title);
        for (var j = 0; j < 4; j++) {
            $(".page-question .question-options li:eq(" + j + ") span").text(quesLibNow[0].options[j])
        }
        var answerNow = quesLibNow[0].answer;

        //点击准备页按钮开始答题
        $(".page-ready .btn-begin").click(function () {
            <?php if(is_null($status)) { ?>
            var module = poptpl.popComponent({
                popBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_bg_01.png) no-repeat',
                popBorder:0,
                closeUrl : "<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_close.png",
                btnMsg : "去登录",
                popTopColor:"#fb3f5a",
                title:'<p style="font-size:0.50666667rem; margin: 1rem 0 2.2rem;">您还未登录哦！</p>',
                popBtmBackground:'url(<?= FE_BASE_URI ?>wap/campaigns/active20170711/img/pop_btn_01.png) no-repeat',
                popBtmBorderRadius:0,
                popBtmFontSize : ".50666667rem",
                btnHref:'/site/login?next=<?= urlencode(Yii::$app->request->absoluteUrl) ?>'
            });
            <?php } else { ?>
                $(".page-ready").hide();
                $(".page-question").show();
            <?php } ?>
        });

        //点击选项
        $(".question-options li").click(function () {
            $(this).find('img').attr('src', '<?php echo FE_BASE_URI . "/wap/campaigns/active20170823"; ?>/images/dot_2.png').parent().siblings().find('img').attr('src', '<?php echo FE_BASE_URI . "/wap/campaigns/active20170823"; ?>/images/dot_1.png');
            answerSelect = $(this).index() + 1;
        });

        //点击提交按钮
        $(".page-question .question-button").click(function () {
            if (answerSelect == answerNow) {
                if (quesIndex < 5) {
                    $(".question-warning").addClass('yincang');
                    quesIndex++;
                    $(".page-question .question-title p").text(quesIndex + quesLibNow[quesIndex - 1].title);
                    for (var j = 0; j < 4; j++) {
                        $(".page-question .question-options li:eq(" + j + ") span").text(quesLibNow[quesIndex - 1].options[j]);
                        $(".page-question .question-options li:eq(" + j + ") img").attr('src', '<?php echo FE_BASE_URI . "/wap/campaigns/active20170823"; ?>/images/dot_1.png');
                    }
                    answerNow = quesLibNow[quesIndex - 1].answer;
                } else {
                    window.location.href = 'first?success=true';
                }
            } else {
                $(".question-warning").removeClass('yincang');
                return false;
            }
        });
    })
</script>
</body>
</html>