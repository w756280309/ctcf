/**
 * Created by lcl on 2015/11/16.
 */
var csrf;
$(function(){
    csrf = $("meta[name=csrf-token]").attr('content');
    //轮播图
    function swiper(){
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            centeredSlides: true,
            speed:500,
            autoplay: 5000,
            loop:true,
            autoHeight:false,
            direction: "horizontal",
            updateOnImagesReady : true,
            onAutoplayStop:function(swiper){
                function IsPC() {
                    var userAgentInfo = navigator.userAgent;
                    var Agents = ["Android", "iPhone",
                        "SymbianOS", "Windows Phone",
                        "iPad", "iPod"];
                    var flag = true;
                    for (var v = 0; v < Agents.length; v++) {
                        if (userAgentInfo.indexOf(Agents[v]) > 0) {
                            flag = false;
                            break;
                        }
                    }
                    return flag;
                }
                if (!IsPC()) {
                    setTimeout(function(){
                        swiper.startAutoplay();
                    },100);
                }
            }
        });
        $('.swiper-container').hover(function(){
            swiper.stopAutoplay();
        },function(){
            swiper.startAutoplay();
        });
    }
    $(window).load(function(){
        swiper();
    });

    //圆圈进度
    var canvasArray=document.getElementsByTagName('canvas');
    for(var i=0;i<canvasArray.length;i++){
        obj = $(canvasArray[i]);
        var per = obj.attr('data-per');
        $(canvasArray[i]).ClassyLoader({
            width: 100,
            height: 100,
            start: 'top',
            animate:false,
            percentage: per,//显示比例
            showText: false,//是否显示进度比例
            speed: 20,
            fontSize: '20px',
            diameter: 40,
            fontColor: '#ff9836',
            lineColor: '#f7403a',
            remainingLineColor: 'rgba(55, 55, 55, 0.1)',
            lineWidth: 4,
            textStr:'' //显示文字
        });
    }
});

function myConfirm() {
    var chongzhi = $('<div class="mask" style="display: block;height: 1000px;"></div><div class="bing-info show" style="position: fixed;"> <p class="tishi-p" style="line-height: 20px;">根据合规性要求，私募基金产品，定向发行，请先登录后方可了解相关内容</p > <div class="bind-btn"> <span class="no" style="border-right: 1px solid #ccc;">取消</span> <span class="yes"  style="border-left: 1px solid #ccc;">确定</span></div> </div>');
    $(chongzhi).insertAfter($('body'));
    $('.bing-info .yes').on('click', function () {
        $(chongzhi).remove();
        window.location.href = '/site/login';
    });
    $('.bing-info .no').on('click', function () {
        $(chongzhi).remove();
    });
}
function checkLoginStatus()
{
    var xhr = $.get('/site/session');
    xhr.done(function(data) {
        if (!data.isLoggedin) {
            $('#isLoggedin').show();
            //未登录状态点击温股投弹出提示框
            var wgt = $('#wgt');
            wgt.find('a').removeAttr('href');
            wgt.css('cursor', 'pointer');
            wgt.click(function () {
                myConfirm();
            });
        }
    });
}

