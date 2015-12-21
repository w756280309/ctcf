/**
 * Created by lcl on 2015/11/22.
 */
$(function(){
    var swiper = new Swiper('.swiper-container', {
        direction: "vertical",
        pagination: '.swiper-pagination',
        paginationClickable: true,
        slidesPerView: 7,
        spaceBetween: 50,
        breakpoints: {
            1800: {
                slidesPerView: 4,
                spaceBetween: 50
            },
            1024: {
                slidesPerView: 5,
                spaceBetween: 40
            },
            768: {
                slidesPerView: 4,
                spaceBetween: 30
            },
            640: {
                slidesPerView: 6,
                spaceBetween: 20
            },
            320: {
                slidesPerView: 6,
                spaceBetween: 10
            }
        }
    });
    $('.selecter').bind('click',function(){
        $('.mask').css({display:'block'});
        $('.banks').css({left:0});
        $('.banks1').css({left:0});
    });
    $('.you').bind('click',function(){
        $('.mask').css({display:'block'});
        $('.banks').css({left:0});
        $('.banks1').css({left:0});
    });
    $('.close img').bind('click',function(){
        $('.mask').css({display:'none'});
        $('.banks').css({left:-10000});
        $('.banks1').css({left:-10000});
    });
    $('.mask').bind('click',function(){
        $('.mask').css({display:'none'});
        $('.banks').css({left:-10000});
        $('.banks1').css({left:-10000});
    });
    $('.swiper-slide').click(function(){
        var index=$('.swiper-slide').index(this);
        var imgs=$('.swiper-slide img').eq(index).attr('src');
        var banks=$('.swiper-slide span').eq(index).html();
        var bankid=$('.swiper-slide').eq(index).attr('data-id');
        $('.kaihu').css({display:'none'});
        $('.kaihu1').css({display:'block'});
        $('.xian img')[0].src=imgs;
        $('.xian span')[0].innerHTML=banks;
        $('#bank_name').val(banks);
        $('#bank_id').val(bankid);

        $('.mask').css({display:'none'});
        $('.banks').css({left:-10000});
        $('.banks1').css({left:-10000});
    })
});