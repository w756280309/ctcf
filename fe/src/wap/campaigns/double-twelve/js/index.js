/**
 * Created by lw on 2016/12/7.
 */
$(function(){
    FastClick.attach(document.body);
    var len =$('.people ul li').length;
    if(len > 3){
        lunBo();
    }
    $('.btest').on('click',function () {
        $('.mask').show();
        var classNmae = $(this).data('value');
        $('.'+classNmae).show();
        document.body.addEventListener('touchmove',eventTarget, false);
        $('html').attr('ontouchmove', 'event.preventDefault()');

    })

    document.getElementById("giftBox").addEventListener("touchmove",function(e){
        e.stopPropagation();
        //return false;
    },false);
    $('.mask,.closepop').on('click',function () {
        $('.pop').hide();
        $('.mask').hide();
        document.body.removeEventListener('touchmove',eventTarget, false);
        $('html').removeAttr('ontouchmove');
    });
    $('.pop').on('click',function(event){
        event.stopPropagation();
    })
})

function lunBo(){
    var liH = 0.693;
    var ts = setInterval(function(){
        $('.people ul li').eq(0).animate({"margin-top":"-"+liH+"rem"},1000,function(){
            $(this).css({"margin-top":0});
            $(this).appendTo($('.people ul'));
        });
    },2000);
}

function eventTarget(event){
    event.stopPropagation();
    event.preventDefault();
}