/**
 * Created by lcl on 2015/11/16.
 */
var csrf;
$(function() {
    csrf = $("meta[name=csrf-token]").attr('content');
    //轮播图
    function swiper() {
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            centeredSlides: true,
            speed: 500,
            autoplay: 5000,
            loop: true,
            autoHeight: false,
            direction: "horizontal",
            updateOnImagesReady: true,
            onAutoplayStop: function (swiper) {
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
                    setTimeout(function () {
                        swiper.startAutoplay();
                    }, 100);
                }
            }
        });
        $('.swiper-container').hover(function () {
            swiper.stopAutoplay();
        }, function () {
            swiper.startAutoplay();
        });
    }

    $(window).load(function() {
        swiper();
        //文字轮播
        setInterval(move1,2000)
        function move1() {
            $('.index-notices div').eq(0).animate({marginTop: '-0.5rem'}, 1000, function () {
                $('.index-notices div').eq(0).css({marginTop: 0});
                $('.index-notices div').eq(0).appendTo($('.index-notices'));
            });
        };

        //底部图片轮播
        //通过window.orientation来判断设备横竖屏
        window.onorientationchange = function (){
            location.reload();
        };
        var offX = document.documentElement.clientWidth*0.15;
        var mySwiper = new Swiper('.index-bottom .swiper-container1', {
            loop: true,
            slidesOffsetAfter : -offX,
            slidesOffsetBefore : -offX,
            slidesPerView : "auto",
            spaceBetween: 20,
        });
    });
});