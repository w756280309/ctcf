$(function() {
    $('.plD-btn').on('click',function(){
        var index=$('.plD-btn').index(this);
        $('.plD-btn').removeClass('plD-redBorder');
        $('.plD-btn div').removeClass('plD-redBorder');
        $('.plD-btn').eq(index).addClass('plD-redBorder');
        $('.plD-btn div').eq(index).addClass('plD-redBorder');
        $('.plD-content').hide();
        $('.plD-content').eq(index).show();
    });
    $('.dR-money').on('click',function(){
        $('.dR-tishi-error ').hide();
        $('.tishi').show();
    });
    $('.dR-money').on('blur',function(){
        $('.tishi').hide();
    });
    var dRprogress=$('.dR-progress span').attr('data-progress');
    $('.dR-progress span').css({width:dRprogress+'%'});
    $('.dR-down').on('click',function(){
        $('.dR-quan').stop(true,false).slideToggle();
    });
    $('.dR-quan li').on('click',function(){
        var index=$('.dR-quan li').index(this);
        $('.quan-true').hide();
        $('.quan-true').eq(index).show();
    });
    $('.dR-quan li').hover(function(){
        var index=$('.dR-quan li').index(this);
        $('.dR-quan li').stop(true,false).eq(index).css({top:'-2px'});
    },function(){
        var index=$('.dR-quan li').index(this);
        $('.dR-quan li').stop(true,false).eq(index).css({top:0});
    })
});