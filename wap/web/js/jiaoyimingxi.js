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
                        //如果当前页和返回页码相同，则改变页面html,否则视为异常
                        if (currentPage === data.header.cp) {
                            if (data.data.length > 0) {
                                var html = "";
                                $.each(data.data, function (i, item) {
                                    var in_money = WDJF.numberFormat(item.in_money, 1);
                                    var out_money = WDJF.numberFormat(item.out_money, 1);
                                    var money = (item.in_money > item.out_money) ? ('+' + in_money) : ('-' + out_money);
                                    var className = (item.in_money > item.out_money) ? 'red' : 'green';
                                    var createdAt = moment(item.created_at*1000);
                                    html += '<div class="clear"></div>'+
                                        '<div  class="row jiaoyi"><div class="col-xs-1"></div><div class="col-xs-10"><div class="col-xs-6 lf">'+
                                        '<p  class="way">'+mingxitype[item.type]+'</p>'+
                                        '<p class="revenue">余额：<span>'+ WDJF.numberFormat(item.balance, 1) +'元</span></p></div>'+
                                        '<div class="col-xs-6 rg"><p  class="money '+ className +'" >'+ money +'</p>'+
                                        '<p  class="date" ><span class="data1">'+createdAt.format('YYYY-MM-DD')+'</span> '+
                                        '<span class="data2">'+createdAt.format('HH:mm:ss')+'</span></p></div>'+
                                        '</div><div class="col-xs-1"></div></div>';
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