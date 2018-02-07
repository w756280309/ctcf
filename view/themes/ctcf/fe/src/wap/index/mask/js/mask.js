$(function(){
    // if($('.mask-box').css('display')=="block"){
    //     // $('body').on()
    //     console.log(11);
    // }
    // 全局禁止滑动
    function addMove(){
        $('.flex-content').on('touchmove',function(e){
            var e=e||window.event;
            e.preventDefault();
        });
    }
    function removeMove(){
        $('.flex-content').off('touchmove');
        $('.mask-no-invest').css('display','none');
    }
   addMove();
    $('i.close-box').on('click',function(){
        removeMove();
    })





})