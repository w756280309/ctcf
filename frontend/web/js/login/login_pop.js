$(function(){
    var lH=$('.loginUp-box').height()/2;
    $('.loginUp-box').css({marginTop:-lH});
    $('.loginUp-btn').hover(function(){
        $('.loginUp-btn').css({background:'#f41c11'});
    },function(){
        $('.loginUp-btn').css({background:'#f44336'});
    });
    $('.close').on('click',function(){
        $('.login-mark').fadeOut();
        $('.loginUp-box').fadeOut();
    })
})