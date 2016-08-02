$(function() {
    //分页相关
    var req_url = url;
    var currentPage = 2;
    var totalPage = tp;
    var stop = true;

    //自动加载分页数据
    $(window).scroll(function() {
        //当内容滚动到底部时加载新的内容
        if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
            //当前要加载的页码
            getPageList();
        }
    });

    function getPageList()
    {
        if (stop === true && currentPage <= totalPage) {
            stop = false;
            $(".load").html("正在加载...");
            $.ajax({
                type: 'GET',
                url: req_url,
                data: {page: currentPage},
                dataType: 'json',
                success: function(data)
                {
                    if (data.code === 0) {
                        //如果当前页和返回页码相同，则改变页面html,否则视为异常
                        if (currentPage === data.header.cp) {
                            if (data.html) {
                                $('.load').before(data.html);
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
                    } else {
                        alert(data.message);
                    }
                },
                complete: function()
                {
                    stop = true;
                    $(".load").html("加载更多");
                }
            });
        }
    }
});