$(function(){
    // 全局禁止滑动
    function removeMove(){
        $('.flex-content').on('touchmove',function(e){
            var e=e||window.event;
            e.preventDefault();
        });
    }
    function clearMove(){
        $('.flex-content').off('touchmove');
        $('.mask-no-invest').css('display','none');
        $('.mask-prize-hint').css('display','none');
        $('.mask-login-invest').css('display','none');
    }
   removeMove();
    $('i.close-box').on('click',function(){
        console.log(111);
        clearMove();
    })
    // 已登陆注册弹框
    var mySwiper= new Swiper('.urgrade-swiper-contain', {
        spaceBetween:100,
        initialSlide :0,
        speed: 500,
        loop:false,
        pagination : '.swiper-pagination',
        autoplayDisableOnInteraction: false,
        onSlideChangeEnd:function(){
            if(mySwiper.activeIndex==0){
                $('.mask-login-invest').find('.close-box').css('display','none');
            }else if(mySwiper.activeIndex==1){
                $('.mask-login-invest').find('.close-box').css('display','block');
            }
        }

    });
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