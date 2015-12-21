/**
 * Created by lcl on 2015/11/22.
 */
var csrf;
$(function(){
    function huadong(){
        var swiper = new Swiper('.swiper-container', {
            direction: "vertical",
            pagination: '.swiper-pagination',
            paginationClickable: true,
            //slidesPerView: 7,
            //spaceBetween: 50,
            breakpoints: {
                1800: {
                    slidesPerView: 4,
                    spaceBetween: 50
                },
                1024: {
                    slidesPerView: 6,
                    spaceBetween: 40
                },
                768: {
                    slidesPerView: 6,
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
    }

    huadong();
    csrf = $("meta[name=csrf-token]").attr('content');
    $('#editbankbtn').bind('click',function(){
        if($('#sub_bank_name').val()=='') {
            toast(this,'分支行名称不能为空');
            $(this).removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        if($('#province').val()=='') {
            toast(this,'分支行省份不能为空');
            $(this).removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        if($('#citys').val()=='') {
            toast(this,'分支行城市不能为空');
            $(this).removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        $(this).addClass("btn-press").removeClass("btn-normal");
        subForm("#form");
        $(this).removeClass("btn-press").addClass("btn-normal");
    });


    var cityid;
    $('.sheng').on('click',function(){
        var index= $('.sheng').index(this);
        cityid=$('.sheng').eq(index).attr('data-id');
        $.ajax({
            type: 'GET',
            url: "/system/system/city?pid="+cityid,
            dataType: 'json',
            success:function(data){
                if (data.code == 0) {
                    var html='';
                    $.each(data.city, function (i, item) {
                        var city = item.name;
                        html +='<div class="swiper-slide shi"><div><span>'+city+'</span></div></div>'
                    });
                    $('#city').html(html);

                    huadong();
                    $('.selecter-city').bind('click',function(){
                        $('.mask').css({display:'block'});
                        $('.citys').css({left:0});
                        $('.citys1').css({left:0});

                    });
                    $('.close img').bind('click',function(){
                        $('.mask').css({display:'none'});
                        $('.citys').css({left:'-10000px'});
                        $('.citys1').css({left:'-10000px'});
                    });
                    $('.mask').bind('click',function(){
                        $('.mask').css({display:'none'});
                        $('.citys').css({left:'-10000px'});
                        $('.citys1').css({left:'-10000px'});
                    });
                    $('.shi').click(function(){
                        var index=$('.swiper-slide').index(this);
                        var banks=$('.swiper-slide span').eq(index).html();
                        $('.kaihu').css({display:'none'});
                        $('.kaihu1').css({display:'block'});
                        $('.selecter-city')[0].innerHTML=banks;
                        $('#citys').val(banks)

                        $('.mask').css({display:'none'});
                        $('.citys').css({left:'-10000px'});
                        $('.citys1').css({left:'-10000px'});
                    });
                }
            }
        })
    });




    $('.selecter').bind('click',function(){
        $('.mask').css({display:'block'});
        $('.banks').css({left:'0'});
        $('.banks1').css({left:'0'});
    });
    $('.close img').bind('click',function(){
        $('.mask').css({display:'none'});
        $('.banks').css({left:'-10000px'});
        $('.banks1').css({left:'-10000px'});
    });
    $('.mask').bind('click',function(){
        $('.mask').css({display:'none'});
        $('.banks').css({left:'-10000px'});
        $('.banks1').css({left:'-10000px'});
    });
    $('.sheng').click(function(){
        var index=$('.swiper-slide').index(this);
        var banks=$('.swiper-slide span').eq(index).html();
        $('.kaihu').css({display:'none'});
        $('.kaihu1').css({display:'block'});
        $('.xian span')[0].innerHTML=banks;
        $('#province').val(banks);
        $('.selecter-city')[0].innerHTML='请选择所在城市';


        $('.mask').css({display:'none'});
        $('.banks').css({left:'-10000px'});
        $('.banks1').css({left:'-10000px'});
    });

});