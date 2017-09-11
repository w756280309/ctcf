<?php
$this->title = '周周乐结果';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/happy-week/css/record.css">
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>/libs/vue.min.js"></script>
<style>
    [v-cloak] { display: none }
</style>


<div class="flex-content" id="record">
    <div class="noRecord" style="padding: 3rem 0;text-align: center;font-size: .4rem;display: none;">周周乐活动本周是第一期，您暂无历史中奖数据哦~</div>
    <ul class="happyWeek-list">
        <li v-for="rewardInfo in rewardInfos" v-cloak>
            <p class="til">{{rewardInfo.qishu}}期</p>
            <div class="ctn">
                <div class="lucky-draw not-lottery-draw" v-if="rewardInfo.status==0">待开奖</div>
                <div class="lucky-draw" v-if="rewardInfo.status==1">已中奖</div>
                <div class="lucky-draw" v-if="rewardInfo.status==2">未中奖</div>
                <ul class="luck-Num clearfix">
                    <li class="lf mr8">
                        <img class="lucky-num" src="<?= FE_BASE_URI ?>wap/happy-week/images/lucky-num.png" alt="">
                        <img v-if="!!rewardInfo.userCard[0]" class="com-num" :src="baseUrlB+rewardInfo.userCard[0]+imgFormatUrl" alt="">
                        <img v-if="!!rewardInfo.userCard[0]" class="com-num-btm" :src="baseUrlB+rewardInfo.userCard[0]+imgFormatUrl" alt="">
                        <img v-if="!rewardInfo.userCard[0]" class="com-bg" src="<?= FE_BASE_URI ?>wap/happy-week/images/type-bg_01.png" alt="">
                    </li>
                    <li class="lf mr8">
                        <img class="login-num" src="<?= FE_BASE_URI ?>wap/happy-week/images/login-num.png" alt="">
                        <img v-if="!!rewardInfo.userCard[1]" class="com-num" :src="baseUrlR+rewardInfo.userCard[1]+imgFormatUrl" alt="">
                        <img v-if="!!rewardInfo.userCard[1]" class="com-num-btm" :src="baseUrlR+rewardInfo.userCard[1]+imgFormatUrl" alt="">
                        <img v-if="!rewardInfo.userCard[1]" class="com-bg" src="<?= FE_BASE_URI ?>wap/happy-week/images/type-bg_02.png" alt="">
                    </li>
                    <li class="lf mr8">
                        <img class="sigin-num" src="<?= FE_BASE_URI ?>wap/happy-week/images/sigin-num.png" alt="">
                        <img v-if="!!rewardInfo.userCard[2]" class="com-num" :src="baseUrlB+rewardInfo.userCard[2]+imgFormatUrl" alt="">
                        <img v-if="!!rewardInfo.userCard[2]" class="com-num-btm" :src="baseUrlB+rewardInfo.userCard[2]+imgFormatUrl" alt="">
                        <img v-if="!rewardInfo.userCard[2]" class="com-bg" src="<?= FE_BASE_URI ?>wap/happy-week/images/type-bg_03.png" alt="">
                    </li>
                    <li class="lf">
                        <img class="invest-num" src="<?= FE_BASE_URI ?>wap/happy-week/images/invest-num.png" alt="">
                        <img v-if="!!rewardInfo.userCard[3]" class="com-num" :src="baseUrlR+rewardInfo.userCard[3]+imgFormatUrl" alt="">
                        <img v-if="!!rewardInfo.userCard[3]" class="com-num-btm" :src="baseUrlR+rewardInfo.userCard[3]+imgFormatUrl" alt="">
                        <img v-if="!rewardInfo.userCard[3]" class="com-bg" src="<?= FE_BASE_URI ?>wap/happy-week/images/type-bg_04.png" alt="">
                    </li>
                </ul>
                <p v-if="rewardInfo.status==1||rewardInfo.status==2" class="win-num clearfix">
                    <span class="com-til lf">中奖号码：</span>
                    <img class="com-card lf" src="<?= FE_BASE_URI ?>wap/happy-week/images/type_04.png" alt=""><span class="com-fz lf grey mr8">{{rewardInfo.rewardCard[0]}}</span>
                    <img class="com-card lf mt3" src="<?= FE_BASE_URI ?>wap/happy-week/images/type_03.png" alt=""><span class="com-fz lf red mr8">{{rewardInfo.rewardCard[1]}}</span>
                    <img class="com-card lf mt3" src="<?= FE_BASE_URI ?>wap/happy-week/images/type_02.png" alt=""><span class="com-fz lf grey mr8">{{rewardInfo.rewardCard[2]}}</span>
                    <img class="com-card lf" src="<?= FE_BASE_URI ?>wap/happy-week/images/type_01.png" alt=""><span class="com-fz lf red mr8">{{rewardInfo.rewardCard[3]}}</span>
                </p>
                <p v-if="rewardInfo.status==1||rewardInfo.status==2" class="u-gifts">
                    <span class="com-til lf">您的奖品：</span>
                    <span class="gifts-detail red lf">{{rewardInfo.title}}</span>
                </p>
                <p v-if="rewardInfo.status==0" class="win-num clearfix">
                    <span class="com-til lf">中奖号码：</span>
                    <span class="not-lottery lh">待公布</span>
                </p>
                <p v-if="rewardInfo.status==0" class="u-gifts">
                    <span class="com-til lf">您的奖品：</span>
                    <span class="gifts-detail not-lottery lf">待开奖</span>
                </p>
            </div>
        </li>
    </ul>
</div>
<script>
    $(function(){
        var data = <?= $data ?>;
        if(data.length ==0) {
            $('.noRecord').show();
            return false;
        }
        var vm = new Vue({
            el: '#record',
            data:{
                rewardInfos:data,
                baseUrlB: '<?= FE_BASE_URI ?>wap/happy-week/images/',
                baseUrlR: '<?= FE_BASE_URI ?>wap/happy-week/images/r',
                imgFormatUrl: '.png',
                stop:true,
                currentPage:2,
                totalPage:<?= $totalPage ?>,
            },
            methods:{
                loadMore:function(){
                    if(this.stop && this.currentPage<=this.totalPage){
                        this.stop = false;
                        var _this = this;
                        $.ajax({
                        type: 'GET',
                        url: "/promotion/poker/record?page="+this.currentPage+"&limit=3",
                        dataType: 'json',
                        success: function(data){
                            var res = data.data;
                            for(var i=0 ,len=res.length;i<len;i++){
                                _this.rewardInfos.push(res[i]);
                            }
                            _this.currentPage++;
                        },
                        error: function(){},
                        complete: function() {
                            _this.stop = true;
                        }
                    })
                    }
                }
            }
        });

        $(window).scroll(function() {
            if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
                vm.loadMore();
            }
        });

    });

    Vue.config.devtools = true
</script>
