$(function () {

    canvasToCircle();

    function canvasToCircle(){
        var canvasArray=document.getElementsByTagName('canvas');
        for(var i=0;i<canvasArray.length;i++){
            obj = $(canvasArray[i]);
            var per = obj.attr('data-per');
            $(canvasArray[i]).ClassyLoader({
                width: 60,
                height: 60,
                start: 'top',
                animate:false,
                percentage: per,//显示比例
                showText: false,//是否显示进度比例
                speed: 20,
                fontSize: '12px',
                diameter: 28,
                fontColor: '#fe0000',
                lineColor: '#fe0000',
                remainingLineColor: 'rgba(55, 55, 55, 0.1)',
                lineWidth: 3,
                textStr:'' //显示文字
            });
        }
    }

    //footer
    $('.footer-inner a').css({color: '#8c8c8c'});
    $('.footer-inner2 a').css({color: '#8c8c8c'});
    $('.footer-inner1 a').css({color: '#f44336'});
    $('.footer-inner1 .licai').css({background: 'url("/images/footer2.png") no-repeat -113px -57px',backgroundSize: '200px'});
    $('.footer-inner2 .zhanghu').css({background: 'url("/images/footer2.png") no-repeat -81px -3px',backgroundSize: '200px'});
    $('.footer-inner .shouye').css({background: 'url("/images/footer2.png") no-repeat -145px -3px',backgroundSize: '200px'});


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

    function getPageList() {
        if (stop === true && currentPage <= totalPage) {
            stop = false;
            $(".load").html("正在加载...");
            param = {page: currentPage};
            $.ajax({
                type: 'GET',
                url: "/deal/deal/loan",
                data: param,
                dataType: 'json',
                success: function (data) {
                    if (data.code === 0) {
                        //如果当前页和返回页码相同，则改变页面html,否则视为异常
                        if (currentPage === data.header.cp) {
                            if (data.html) {
                                $('#item-list').html($('#item-list').html() + data.html);
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
                        }
                    } else {
                        alert(data.message);
                    }

                    canvasToCircle();
                },
                complete: function () {
                    stop = true;
                    $(".load").html("加载更多");
                }
            });
        }
    }
});