$(function(){
    var flags=true;
    $('.login-check').on('click',function(){
        if(!flags){
            $('.login-check img')[0].src='../../images/login/check-false.png';
            $('.agree').val('no');
            flags=true;
        }else if(flags){
            $('.login-check img')[0].src='../../images/login/check-true.png';
            $('.agree').val('yes');
            flags=false;
        }
    });
    $('.login-btn').hover(function(){
        $('.login-btn').css({background:'#f41c11'});
    },function(){
        $('.login-btn').css({background:'#f44336'});
    });


});

