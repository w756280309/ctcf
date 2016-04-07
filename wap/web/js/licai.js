/**
 * Created by lcl on 2015/11/22.
 */
$(function(){
    var num = $('.container-text').attr('data-title');
    if(num>=3){
        num=3;
    }
    var swiper = new Swiper('.swiper-container', {
        direction: "horizontal",
        pagination: '.swiper-pagination',
        paginationClickable: true,
        slidesPerView: num,
        spaceBetween: 50,
        breakpoints: {
            1024: {
                slidesPerView: num,
                spaceBetween: 40
            },
            768: {
                slidesPerView: num,
                spaceBetween: 30
            },
            640: {
                slidesPerView: num,
                spaceBetween: 20
            },
            320: {
                slidesPerView: num,
                spaceBetween: 10
            }
        }
    });
    //nav
    $('.swiper-wrapper div').on('click',function(){
        var index=$('.swiper-wrapper div').index(this);
        $('.swiper-wrapper div').removeClass('dian');
        $('.swiper-wrapper div').eq(index).addClass('dian');
    })

    //分页相关
    var currentPage = 2;
    var totalPage = tp;
    var stop = true;
    //自动加载分页数据
    $(window).scroll(function () {
        //当内容滚动到底部时加载新的内容
        if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
            //当前要加载的页码
            getPageList();
        }
    });
    //保存两位小数
    function changeTwoDecimal(x)
    {
        var f_x = parseFloat(x);
        if (isNaN(f_x))
        {
            return false;
        }
        f_x = Math.round(f_x *100)/100;
        return f_x;
    }

    //getPageList();
    function getPageList() {
        if (stop === true && currentPage <= totalPage) {
            stop = false;
            $(".load").html("正在加载...");
            $.ajax({
                type: 'GET',
                url: "/user/user/myorder",
                data: {page: currentPage},
                dataType: 'json',
                success: function (data) {
                    //console.log(data);
                    if (data.code === 0) {
                        //如果当前页和返回页码相同，则改变页面html,否则视为异常
                        if (currentPage === data.header.cp) {
                            if (data.data.length > 0) {
                                var html = "";
                                $.each(data.data, function(i, item) {
                                    var pstatus = item.pstatus;
                                    var profitDes = (pstatus == 6) ? "实际收益" : "预期收益";
                                    
                                    html += '<a class="loan-box block" href="/user/user/orderdetail?id='+ item.id +'">' +
                                            '<div class="loan-title"><div class="title-overflow">' + item.title +
                                            '</div><div class="loan-status '+ item.classname +'">'+ item.statusval +'</div></div>' +
                                            '<div class="row loan-info">' +
                                            '<div class="col-xs-8 loan-info1">' +
                                            '<p><span class="info-label">认购金额：</span><span class="info-val">'+ item.order_money +'元</span></p>';
                                    if (0 === parseInt(item.finish_date)) {
                                        html += '<p><span class="info-label">项目期限：</span><span class="info-val">'+ item.expiress + item.method +'</span></p>';
                                    } else {
                                        html += '<p><span class="info-label">到期时间：</span><span class="info-val">'+ item.returndate +'</span></p>';
                                    }
                                    html += '</div>';
                                    if ('--' !== item.profit) {
                                        html += '<div class="col-xs-4 loan-info2">' +
                                                '<p class="info-val">'+ item.profit +'元</p>' +
                                                '<p class="info-label">'+ profitDes +'</p></div>';
                                    } else {
                                        html += '<div class="col-xs-4 loan-info2">' +
                                                '<p class="info-val">'+ item.finish_rate +'%</p>' +
                                                '<p class="info-label">募集进度</p></div>';

                                    }
                                    html += '</div></a>';
                                });
                                $('.load').before(html);
                                //页码＋1
                                currentPage = parseInt(data.header.cp) + 1;
                            } else {
//                                alert("暂无数据");
                            }
                        } else {
                            //异常,不做处理
                        }
                        //当前页大于总页数,隐藏加载更多提示(暂无数据时,tp为0)
                        if (currentPage >= data.header.tp) {
                            $(".load").hide();
                        } else {
                            $(".load").show();
                        }
                        //没有数据列表
                        if (data.header.tp === 0) {
                            //显示暂无数据视图
                            $(".nodata").show();
                        } else {
                            $(".nodata").hide();
                            //$('.load').show();
                        }
                    } else {
                        alert(data.message);
                    }
                },
                complete: function () {
                    stop = true;
                    $(".load").html("加载更多");
                }
            });
        }

    }

});
