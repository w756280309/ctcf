
window.onload = function(){
    FastClick.attach(document.body);
    // 页面加载完成之后要做的事情
    var promoStatus = $('input[name=promoStatus]').val();
    if(promoStatus == 1){
        toastCenter('活动未开始');
    } else if (promoStatus == 2) {
        toastCenter('活动已结束');
    }
    // 点击眼睛变化
    imgChange();
    getTicket();
    initScroll();
    closeDoor();

}

//关闭弹窗的事件
function closeDoor(){
    $('.cue-close').click(function(){
        $('.box-top').css('display','none');
        refreshPage();
    })
}

// 中间iscroll
function initScroll(){
    var myScroll;
    intervalTime = setInterval(function () {
        var resultContentH = $("#wrapper ul").height();
        if (resultContentH > 0) {  //判断数据加载完成的条件
            clearInterval(intervalTime);
            myScroll = new iScroll('wrapper',{
                vScrollbar:false,
            });
        }
    }, 1);
};

//活动状态的弹框
function toastCenter(val, active) {
    var $alert = $('<div class="error-info" style="display: block; position: fixed;font-size: .4rem;"><div>' + val + '</div></div>');
    $('body').append($alert);
    $alert.find('div').width($alert.width());
    setTimeout(function () {
        $alert.fadeOut();
        setTimeout(function () {
            $alert.remove();
        }, 200);
        if (active) {
            active();
        }
    }, 2000);}


//  点击眼睛变化 松开复原
function imgChange(){
    var money=document.querySelector('.money-amounts');
    $('.ice-thing').attr('mark','hide');
    $('.ice-thing').click(function(){
        if($('.ice-thing').attr('mark')=='show'){
            this.src=feBaseUrl+'wap/campaigns/active20171212/images/bg-ice2.png';
            $('.ice-thing').attr('mark','hide');
            $(money).html('*****');
        }else if($('.ice-thing').attr('mark')=='hide'){
            this.src=feBaseUrl+'wap/campaigns/active20171212/images/bg-ice1.png';
            $('.ice-thing').attr('mark','show');
            $(money).html(accountBalance);
        }
    });
}

//加息券内容展示
// 点击领走加息券部分事件
function getTicket(){
    // 页面数据
    var contain=document.querySelector('.contain');
    // 点击跳转的A标签
    var atecket=document.querySelector('.get-tecket');
    //模态框背景盒子
    var boxTop=document.querySelector('.box-top');
    // 模态框盒子
    var boxTopMid=document.querySelector('.box-top-mid');
    // 模态框内券盒子
    var boxContain=document.querySelector('.box-contain');

    //事件部分后台写好了直接用这块
    atecket.addEventListener('touchstart',function(e){
        var e=e||window.event;
        e.preventDefault();
        // 接收到隐藏域的值判断活动有没有开始
        var promoStatus = $('input[name=promoStatus]').val();
        if (promoStatus == 0){
            //进行中
            // 实际操作
            $.ajax({
                type:'get',
                dataType:'json',
                url:'/promotion/p171212/pull',
                data:'',
                success:function(data){
                    if(data.num==0){
                        $('.box-top-mid').css('height','7.2rem');
                        $('.box-contain').find('.p-hint').html('您暂时还未获得加息券哦，余额达到5万即可领取，快去充值吧!').css('margin',"0.7rem auto 0.3rem").css('fontSize','0.53rem');
                        $('.box-contain>.a-div').remove();
                        $('.box-contain').find('.mid-wire').remove();
                        $('.box-contain').find('.p-mid').remove();
                        $('.box-contain').find('.contain-quan').html('<a class="go-pay" href="/user/userbank/recharge">去充值</a>');
                    }else if(data.num>0&&data.num<5){
                        $('.box-contain').find('.p-hint').find('span').html(data.num);
                    }else if(data.num==5){
                        $('.box-contain').find('.p-hint').html('您已获得全部加息券，仅限活动 期间使用哦！快去开启加息吧！');
                        $('.box-contain>.a-div').css('width','3.30666667rem');
                        $('.box-contain>.a-div').html('<a href="/deal/deal/index?t=1">使用加息券</a>');
                    }
                    // 模板引擎渲染
                    var source   = document.getElementById("addTicket").innerHTML;
                    var template = Handlebars.compile(source);
                    var context=data.award;
                    var htmlTpl=template(context);
                    $('.contain-quan').find('ul').html(htmlTpl);
                },
                complete:function () {
                    boxTop.style.display = "block";
                    // 去除页面的touchmove事件
                    boxTop.addEventListener('touchmove', function (e) {
                        var e = e || window.event;
                        e.preventDefault();
                    }, false);
                    contain.addEventListener('touchmove', function (e) {
                        var e = e || window.event;
                        e.preventDefault();
                    }, false);

                }
            });
            return false;
        } else if (promoStatus == 1) {
            toastCenter('活动未开始');
        } else if (promoStatus == 2) {
            toastCenter('活动已结束');
        }
    });
}

//刷新页面
function refreshPage() {
    var isweixin = isWeiXin();
    if (isweixin) {
        var paramsStr = getParamsString();
        if (paramsStr === '') {
            paramsStr = '?t=' + Math.random();
        } else {
            paramsStr = '&t=' + Math.random();
        }
        location.href = location.href + paramsStr;
    } else {
        location.reload();
    }
}

//微信浏览器location.reload() 写法,无法实现刷新
//判断是否是微信浏览器
function isWeiXin() {
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
}
function getParamsString() {
    var url = location.search; //获取url中"?"符后的字串
    var paramsStr = '';
    if (url.indexOf("?") != -1) {
        //判断是否有参数
        paramsStr = url.substr(1);
    }
    return paramsStr;
}
