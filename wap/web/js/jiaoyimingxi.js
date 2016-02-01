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
                url: "/user/user/mingxi",
                data: {page: currentPage},
                dataType: 'json',
                success: function (data) {
                    if (data.code === 0) {
                        console.log(data);
                        //如果当前页和返回页码相同，则改变页面html,否则视为异常
                        if (currentPage === data.header.cp) {
                            if (data.data.length > 0 && data.type) {
                                var html = "";
                                $.each(data.data, function (i, item) {
                                    var money = item.in_money!=0?item.in_money:item.out_money;
                                    var createdAt = new Date(item.created_at*1000);
                                    html += '<div style="background: #fff">'+
                                        '<div class="clear"></div><div class="row md-height border-bottom"><div class="col-xs-3 data">'+
                                        '<span class="data1">'+createdAt.getFullYear()+'-'+createdAt.getMonth()+'-'+createdAt.getDay()+'</span>'+
                                        '<span class="data2">'+createdAt.getHours()+':'+createdAt.getMinutes()+':'+createdAt.getSeconds()+'</span>'+
                                        '</div>'+
                                        '<div class="col-xs-3 revenue">'+data.type[item.type]+'</div>'+
                                        '<div class="col-xs-3 money">'+money+'</div>'+
                                        '<div class="col-xs-3 revenue">'+item.balance+'</div></div></div>';
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
