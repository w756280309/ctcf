/**
 * Created by lcl on 2015/12/24.
 */
$(function(){
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

    function getPageList() {
        if (stop === true && currentPage <= totalPage) {
            stop = false;
            $(".load").html("正在加载...");
            $.ajax({
                type: 'GET',
                url: "/news/index",
                data: {page: currentPage},
                dataType: 'json',
                success: function (data) {
                    if (data.code === 0) {
                        //如果当前页和返回页码相同，则改变页面html,否则视为异常
                        if (currentPage === data.header.cp) {
                            if (data.data.length > 0) {
                                var html = "";
                                $.each(data.data, function (i, item) {
                                    var news_time = $.format.toBrowserTimeZone(parseInt(item.news_time) * 1000, 'yyyy-MM-dd');   //时间戳转换日期格式

                                    html += '<a href="/news/detail?id='+ item.id +'" class="single">' +
                                            '<p class="single-title">【资讯标题】'+ item.title +'</p>' +
                                            '<p class="single-description">'+ item.summary +'</p>' +
                                            '<p class="single-time">'+ news_time +'</p>' +
                                            '</a>';
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