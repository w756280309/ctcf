$(function(){

    $.ajax({
        type: "GET",
        url: "oldUserRewardPop/pop-type",
        success: function(data){
            if (data == 0) {

            } else if (data == 1) {
                removeMove();
                $('.mask-prize-hint').css('display','block').find('i.close-box').on('click',function(){
                    $('.flex-content').off('touchmove');
                    $('.mask-prize-hint').css('display','none');
                });
            } else if (data == 2) {
                removeMove();
                $('.mask-no-invest').css('display','block').find('i.close-box').on('click',function(){
                    $('.flex-content').off('touchmove');
                    $('.mask-no-invest').css('display','none');
                });
            } else if (data == 3) {
                $('.updata-top-part').html("<span>8元</span><span>30元</span><span>80元</span>");
                $('.updata-mid-part').html('共得<span>118元</span>红包');
                $('.updata-bottom-rg').html("<p>188积分</p><p>可以兑换超多礼品</p>");
                removeMove();
                $('.mask-login-invest').css('display','block').find('i.close-box').on('click',function(){
                    $('.flex-content').off('touchmove');
                    $('.mask-login-invest').css('display','none');
                });

            } else if (data == 4) {
                $('.updata-top-part').html("<span>8元</span><span>30元</span><span>80元</span><span>150元</span>");
                $('.updata-mid-part').html('共得<span>268元</span>红包');
                $('.updata-bottom-rg').html("<p>388积分</p><p>可以兑换超多礼品</p>");
                removeMove();
                $('.mask-login-invest').css('display','block').find('i.close-box').on('click',function(){
                    $('.flex-content').off('touchmove');
                    $('.mask-login-invest').css('display','none');
                });
            } else if (data == 5) {
                $('.updata-top-part').html("<span>30元</span><span>80元</span><span>150元</span><span>220元</span>");
                $('.updata-mid-part').html('共得<span>480元</span>红包');
                $('.updata-bottom-rg').html("<p>588积分</p><p>可以兑换超多礼品</p>");
                removeMove();
                $('.mask-login-invest').css('display','block').find('i.close-box').on('click',function(){
                    $('.flex-content').off('touchmove');
                    $('.mask-login-invest').css('display','none');
                });
            }
            $.ajax({
                type:'get',
                url:'oldUserRewardPop/after-pop',
            });
        },
        error:function(){

        }
    });
    // 全局禁止滑动
    function removeMove(){
        $('.flex-content').on('touchmove',function(e){
            var e=e||window.event;
            e.preventDefault();
        });
    }
    var mySwiper;
    var timer = setTimeout(function(){
        // 已登陆注册弹框
        mySwiper = new Swiper('.urgrade-swiper-contain', {
            spaceBetween:100,
            initialSlide :0,
            speed: 500,
            loop:false,
            pagination:'.swiper-pagination',
            autoplayDisableOnInteraction: false,
            onSlideChangeEnd:function(){
                if(mySwiper.activeIndex==0){
                    $('.mask-login-invest').find('.close-box').css('display','none');
                }else if(mySwiper.activeIndex==1){
                    $('.mask-login-invest').find('.close-box').css('display','block');
                }
            }

        });
        clearTimeout(timer);
    }, 200);
    // 点击查看升级红包显示下一个页面
    $('.check-login-invest').on('click',function(){
        mySwiper.slideTo(1, 500);

    });
    // 点击补偿规则返回上一个页面
    $('u.get-prize-rules').on('click',function(){
        mySwiper.slideTo(0, 500);
        // $('.mask-login-invest').find('.close-box').css('display','block');
    });

})