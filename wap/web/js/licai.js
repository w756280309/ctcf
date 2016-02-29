/**
 * Created by lcl on 2015/11/22.
 */
$(function(){
    var swiper = new Swiper('.swiper-container', {
        direction: "horizontal",
        pagination: '.swiper-pagination',
        paginationClickable: true,
        slidesPerView: 5,
        spaceBetween: 50,
        breakpoints: {
            1024: {
                slidesPerView: 5,
                spaceBetween: 40
            },
            768: {
                slidesPerView: 5,
                spaceBetween: 30
            },
            640: {
                slidesPerView: 5,
                spaceBetween: 20
            },
            320: {
                slidesPerView: 5,
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
                    console.log(data);
                    if (data.code === 0) {
                        //如果当前页和返回页码相同，则改变页面html,否则视为异常
                        if (currentPage === data.header.cp) {
                            if (data.data.length > 0) {
                                var html = "";
                                $.each(data.data, function(i, item) {
                                    var title = item.title;
                                    var order_time = item.order_time;
                                    var yield_rate = changeTwoDecimal(item.yield_rate * 100) + '%';
                                    var statusval = item.statusval; //已还清
                                    var profit = (item.profit == null) ? '--' : item.profit; //预期收益
                                    var pstatus = item.pstatus;
                                    var className = (pstatus == 1 || pstatus == 2) ? "column-title-rg" : "column-title-rg1";
                                    var profitDes = (pstatus == 6) ? "实际收益" : "预期收益";
                                    var expiress = item.expiress;
                                    html += '<div class="row times">'+
                                                    '<div class="col-xs-4">'+order_time+'</div>'+
                                                    '<div class="col-xs-8"></div>'+
                                                '</div>'+
                                                '<div class="row column">'+
                                                    '<div class="hidden-xs col-sm-1" style="height:50px;"></div>'+
                                                    '<div class="col-xs-12 col-sm-10 column-title"><span>'+title+'</span></div>'+
                                                    '<div class="'+className+'">'+statusval+'</div>'+
                                                    '<div class="container" id=\'project-box\'>'+
                                                        '<div class="row">'+
                                                            '<div class="col-xs-4">'+
                                                                '<div>'+yield_rate+'</div>'+
                                                                '<p>年化收益</p>'+
                                                            '</div>'+
                                                            '<div class="col-xs-4">'+
                                                                '<div>'+expiress+'天</div>'+
                                                                '<p>期限</p>'+
                                                            '</div>'+
                                                            '<div class="col-xs-4">'+
                                                                '<div>'+profit+'</div>'+
                                                                '<p>'+profitDes+'</p>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>'+
                                                '</div>';
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
