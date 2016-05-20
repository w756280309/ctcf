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
                url: "/user/coupon/list",
                data: {page: currentPage},
                dataType: 'json',
                success: function (data) {
                    if (data.code === 0) {
                        //如果当前页和返回页码相同，则改变页面html,否则视为异常
                        if (currentPage === data.header.cp) {
                            if (data.data.length > 0) {
                                var html = "";
                                $.each(data.data, function (i, item) {
                                    var $desc = '未使用';
                                    var $div = '';
                                    var $image = 'ok_ticket';

                                    if (item.isUsed) {
                                        $desc = '已使用';
                                        $div = '<div class="row over_img over_user_img"></div>';
                                        $image = 'over_ticket';
                                    } else {
                                        if (date('Y-m-d') > item.useEndDate) {
                                            $desc = '已过期';
                                            $div = '<div class="row over_img over_time_img"></div>';
                                            $image = 'over_ticket';
                                        }
                                    }

                                    html += '<div class="box">' +
                                            '<div class="row coupon_num">' +
                                            '<img src="/images/'+ $image +'.png" alt="券">' +
                                            '<div class="row pos_box">' +
                                            '<div class="col-xs-2"></div>' +
                                            '<div class="col-xs-4 numbers">¥<span>'+ WDJF.numberFormat(item.amount, true) +'</span></div>' +
                                            '<div class="col-xs-6 right_tip">' +
                                            '<div class="a_height"></div>' +
                                            '<div class="b_height">' +
                                            '<p class="b_h4">'+ item.name +'</p>' +
                                            '</div>' +
                                            '<div class="c_height">' +
                                            '<p class="condition1">单笔投资满'+ item.minInvestDesc +'可用</p>' +
                                            '</div>' +
                                            '<div class="d_height"></div>' +
                                            '<div class="c_height">' +
                                            '<p  class="condition1">所有项目可用</p>' +
                                            '</div></div></div> <div class="clear"></div>'+ $div +
                                            '</div>' +
                                            '<div class="row gray_time">' +
                                            '<img src="/images/coupon_img.png" alt="底图">' +
                                            '<div class="row pos_box">' +
                                            '<div class="col-xs-8 ticket_time">有效期至'+ item.useEndDate +'</div>' +
                                            '<div class="col-xs-4 no-use">'+ $desc +'</div>' +
                                            '</div></div></div>';
                                });

                                $('.load').before(html);
                                //页码＋1
                                currentPage = parseInt(data.header.cp) + 1;
                            }
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