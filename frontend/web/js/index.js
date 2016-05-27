$(function(){
    $('#box').fullpage({
        'verticalCentered': false,
        'anchors': ['page1', 'page2', 'page3', 'page4', 'page5', 'page6'],
        'css3': true,
        'navigation': true,
        slideSelector: '.horizontal-scrolling',
        'navigationPosition': 'right',
        //以下代码为下一个阶段所需代码,前端已经开发完成,等到APP上线就可以启用
        afterRender:function(){
            $('<div class="footer-mark"></div>').insertBefore($('#top-box'));
            $('<div class="footer-fixed" data-sign="0">'+
                '<div class="footer-fixed-content">'+
                '<div class="footer-fixed-left">随时<i>投</i>随时<i>赚</i>，APP投标<i>更方便</i></div>'+
                '<div class="footer-fixed-right">'+
                '<div class="footer-fixed-right-code">'+
                '<img src="/images/web_chat.jpg" alt="">'+
                '<p style="margin-top: 46px;">扫一扫</p>'+
                '<p>关注微信订阅号</p></div>'+
                '<div class="footer-fixed-right-code">'+
                '<img src="/images/web_app.jpg" alt="">'+
                '<p style="margin-top: 67px;">扫描下载APP</p>'+
                '<img src="/images/wen-close.png" alt="" class="wen-close"></div></div></div></div>').insertAfter($('.footer-mark'));
        },
        afterLoad:function(anchorLink, index){
            if(index==6){
                $('.six-logo img').addClass('six-logo1');
                $('.six-tilt').addClass('six-tilt1');
                $('.six-tilts').addClass('sixbxx-tilts1');
                $('.yitiao').addClass('yitiao1');
                $('.six-inner').addClass('six-inner1');
                $('.bxx').addClass('bxx1');
                $('.yi1').addClass('yi11');
                $('.yi2').addClass('yi22');
                $('.yi3').addClass('yi33');
                $('.yi4').addClass('yi44');
                $('.yi5').addClass('yi55');
                $('.footer-mark').fadeOut();
                $('.footer-fixed').fadeOut();
            }else {
                $('.six-logo img').removeClass('six-logo1');
                $('.six-tilt').removeClass('six-tilt1');
                $('.six-tilts').removeClass('six-tilts1');
                $('.yitiao').removeClass('yitiao1');
                $('.six-inner').removeClass('six-inner1');
                $('.bxx').removeClass('bxx1');
                $('.yi1').removeClass('yi11');
                $('.yi2').removeClass('yi22');
                $('.yi3').removeClass('yi33');
                $('.yi4').removeClass('yi44');
                $('.yi5').removeClass('yi55');
                var dataSign=$('.footer-fixed').attr('data-sign');
                if(dataSign==0){
                    $('.footer-mark').fadeIn();
                    $('.footer-fixed').fadeIn();
                }
            }
        },
        onLeave: function(index, nextIndex,direction) {
            if(index == 5) {
                if(direction=='down'){
                    $('.six-logo img').addClass('six-logo1');
                    $('.six-tilt').addClass('six-tilt1');
                    $('.six-tilts').addClass('six-tilts1');
                    $('.yitiao').addClass('yitiao1');
                    $('.six-inner').addClass('six-inner1');
                    $('.bxx').addClass('bxx1');
                    $('.yi1').addClass('yi11');
                    $('.yi2').addClass('yi22');
                    $('.yi3').addClass('yi33');
                    $('.yi4').addClass('yi44');
                    $('.yi5').addClass('yi55');
                    $('.footer-mark').fadeOut();
                    $('.footer-fixed').fadeOut();
                }else if(direction=='up'){
                    $('.six-logo img').removeClass('six-logo1');
                    $('.six-tilt').removeClass('six-tilt1');
                    $('.six-tilts').removeClass('six-tilts1');
                    $('.yitiao').removeClass('yitiao1');
                    $('.six-inner').removeClass('six-inner1');
                    $('.bxx').removeClass('bxx1');
                    $('.yi1').removeClass('yi11');
                    $('.yi2').removeClass('yi22');
                    $('.yi3').removeClass('yi33');
                    $('.yi4').removeClass('yi44');
                    $('.yi5').removeClass('yi55');
                }
            }
        }
    });

    $('.wen-close').on('click',function(){
        $('.footer-mark').fadeOut();
        $('.footer-fixed').fadeOut();
        $('.footer-fixed').attr({'data-sign':'1'});
    })
});