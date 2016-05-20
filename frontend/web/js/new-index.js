$(function(){
    $('#box').fullpage({
        'verticalCentered': false,
        'anchors': ['page1', 'page2', 'page3', 'page4', 'page5'],
        'css3': true,
        'navigation': true,
        slideSelector: '.horizontal-scrolling',
        'navigationPosition': 'right',
        //以下代码为下一个阶段所需代码,前端已经开发完成,等到APP上线就可以启用
        //afterRender:function(){
        //    $('<div class="footer-mark"></div>').insertBefore($('#top-box'));
        //    $('<div class="footer-fixed" data-sign="0">'+
        //        '<div class="footer-fixed-content">'+
        //        '<div class="footer-fixed-left">随时<i>投</i>随时<i>赚</i>，APP投标<i>更方便</i></div>'+
        //        '<div class="footer-fixed-right">'+
        //        '<div class="footer-fixed-right-code">'+
        //        '<img src="/images/web_chat.jpg" alt="">'+
        //        '<p style="margin-top: 46px;">扫一扫</p>'+
        //        '<p>关注微信订阅号</p></div>'+
        //        '<div class="footer-fixed-right-code">'+
        //        '<img src="/images/web_app.jpg" alt="">'+
        //        '<p style="margin-top: 67px;">扫描下载APP</p>'+
        //        '<img src="/images/wen-close.png" alt="" class="wen-close"></div></div></div></div>').insertAfter($('.footer-mark'));
        //},
        //afterLoad:function(anchorLink, index){
        //    if(index==5){
        //        $('.four-title').addClass('four-title1');
        //        $('.four-content').addClass('four-content1');
        //        $('.four-left').addClass('four-left1');
        //        $('.four-right').addClass('four-right1');
        //        $('.four-bottom').addClass('four-bottom1');
        //        $('.footer-mark').fadeOut();
        //        $('.footer-fixed').fadeOut();
        //    }else {
        //        $('.four-left').removeClass('four-left1');
        //        $('.four-right').removeClass('four-right1');
        //        $('.four-bottom').removeClass('four-bottom1');
        //        $('.four-title').removeClass('four-title1');
        //        $('.four-content').removeClass('four-content1');
        //        var dataSign=$('.footer-fixed').attr('data-sign');
        //        if(dataSign==0){
        //            $('.footer-mark').fadeIn();
        //            $('.footer-fixed').fadeIn();
        //        }
        //    }
        //},
        onLeave: function(index, nextIndex,direction) {
            if(index == 4) {
                if(direction=='down'){
                    $('.four-title').addClass('four-title1');
                    $('.four-content').addClass('four-content1');
                    $('.four-left').addClass('four-left1');
                    $('.four-right').addClass('four-right1');
                    $('.four-bottom').addClass('four-bottom1');
                    $('.footer-mark').fadeOut();
                    $('.footer-fixed').fadeOut();
                }else if(direction=='up'){
                    $('.four-left').removeClass('four-left1');
                    $('.four-right').removeClass('four-right1');
                    $('.four-bottom').removeClass('four-bottom1');
                    $('.four-title').removeClass('four-title1');
                    $('.four-content').removeClass('four-content1');
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