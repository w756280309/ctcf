$(function(){

    // 登陆状态
    var isLoggedin = $('input[name=isLoggedin]').val();
    // 是否投资
    var isInvest = $('input[name=isInvest]').val();
    // 未登录弹窗
    if(isLoggedin=='false'){
        $('.mask-prize-hint').css('display','block').find('i.close-box').on('click',function(){
            $('.mask-prize-hint').css('display','none');
        });
    }
    // 已登陆未投资的弹窗
    if(isLoggedin=='true'&&isInvest=='false'){
        $('.mask-no-invest').css('display','block').find('i.close-box').on('click',function(){
            $('.mask-no-invest').css('display','none');
        });
    }
    // 已登陆已投资
    if(isLoggedin=='true'&&isInvest=='true'){
        $('.mask-login-invest').css('display','block').find('i.close-box').on('click',function(){
            $('.mask-login-invest').css('display','none');
        });
    }
    // 升级奖励
    $('.check-login-invest').on('click',function(){
        $('.swiper-wrapper').stop().animate({left:-350},500,'swing',swiperCallback1);
     });
    function swiperCallback1(){
        $('.mask-login-invest').find('.close-box').css('display','block');
        $('.swiper-pagination').find('span').toggleClass('swiper-span-active');
    }
    // 活动规则
    $('u.get-prize-rules').on('click',function(){
        $('.swiper-wrapper').stop().animate({left:0},500,'swing',swiperCallback2);
    });
    function swiperCallback2(){
        $('.mask-login-invest').find('.close-box').css('display','none');
        $('.swiper-pagination').find('span').toggleClass('swiper-span-active');
    }
    // // 点击补偿规则返回上一个页面
    // $('u.get-prize-rules').on('click',function(){
    //     mySwiper.slideTo(0, 500);
    //     // $('.mask-login-invest').find('.close-box').css('display','block');
    // });

    // 已登陆注册弹框
    // var mySwiper= new Swiper('.urgrade-swiper-contain', {
    //     spaceBetween:100,
    //     initialSlide :0,
    //     speed: 500,
    //     loop:false,
    //     pagination : '.swiper-pagination',
    //     autoplayDisableOnInteraction: false,
    //     onSlideChangeEnd:function(){
    //         if(mySwiper.activeIndex==0){
    //             $('.mask-login-invest').find('.close-box').css('display','none');
    //         }else if(mySwiper.activeIndex==1){
    //             $('.mask-login-invest').find('.close-box').css('display','block');
    //         }
    //     }
    //
    // });
    // // 点击查看升级红包显示下一个页面
    // $('.check-login-invest').on('click',function(){
    //     mySwiper.slideTo(1, 500);
    //
    // });
    // // 点击补偿规则返回上一个页面
    // $('u.get-prize-rules').on('click',function(){
    //     mySwiper.slideTo(0, 500);
    //     // $('.mask-login-invest').find('.close-box').css('display','block');
    // });
})