$(function(){
    $('.bottom-box .inv-title span').on('click',function(){
        var index=$('.bottom-box .inv-title span').index(this);
        $('.bottom-box .inv-title span').removeClass('selected');
        $('.invite-list').hide();
        $('.rule-box').hide();
        if(index==0){
            $('.bottom-box .inv-title span').eq(0).addClass('selected');
            $('.invite-list').show();
        }else if(index==1){
            $('.bottom-box .inv-title span').eq(1).addClass('selected');
            $('.rule-box').show();
        }
    });
    $('.invite-btn').on('click',function(){
        $('.mark-box').show();
        $('.share-box').show();
    });
    $('.share-box').on('click',function(){
        $('.mark-box').hide();
        $('.share-box').hide();
    })
})