$(function() {
    FastClick.attach(document.body);
    //将页面下部导航首页、理财、账户的理财置红
    $('.footer-inner a').css({color: '#8c8c8c'});
    $('.footer-inner2 a').css({color: '#8c8c8c'});
    $('.footer-inner1 a').css({color: '#f44336'});
    $('.footer-inner1 .licai').css({background: 'url("/images/footer2.png") no-repeat -113px -57px',backgroundSize: '200px'});
    $('.footer-inner2 .zhanghu').css({background: 'url("/images/footer2.png") no-repeat -81px -3px',backgroundSize: '200px'});
    $('.footer-inner .shouye').css({background: 'url("/images/footer2.png") no-repeat -145px -3px',backgroundSize: '200px'});

    //去除默认事件
    function delDefaultEvent(e){
        var e=e||window.event();
        e.preventDefault();
    }
    // 搜索 和 列表
    var $allListItem=$('.all-list-item');
    var $select=$(".select-box").children('div');
    var $listDiv=$('.list-content').children('div');
    // 3个选项
    var $select0=$('#select0');
    var $select1=$('#select1');
    var $select2=$('#select2');
    // 选项值
    var $span0=$select0.find('span');
    var $span1=$select1.find('span');
    var $span2=$select2.find('span');

    // scroll实例
    var scroll1=null;
    var scroll2=null;
    // 3个选项选中某一个让其显示，再次选中同一个隐藏
    $select.on('click',function(e){
        var e=e||windwow.event;
        if($(e.target).parent().hasClass('select-div')||$(e.target).hasClass('select-div')){
            $select.each(function(i,item){
                item.className='';
            });
            $listDiv.find('ul').css('display','none');
            $listDiv.css('display','none');
            $('.container').off('touchmove',delDefaultEvent);
            // $listDiv.fadeOut(400);
            // setTimeout(function(){
            //     //$listDiv.css('display','none');
            //     $listDiv.fadeOut(200);
            //     $('.container').off('touchmove',delDefaultEvent);
            // },400);
        }else{
            // 框
            $select.each(function(i,item){
                item.className='';
            });
            this.className='select-div';
            $('.container').on('touchmove',delDefaultEvent)

            // 列表
            $listDiv.css('display','block');
            $listDiv.find('ul').stop().slideUp();
            $listDiv.find('ul').eq($(this).index()).stop().slideDown(400);
            if($(this).index()==0){
                if($(".select0").find('li').length>8){
                    setTimeout(function(){
                        scroll1 = new BScroll('.scroll1');
                    },430);
                }
            }else if($(this).index()==1){
                if($(".select1").find('li').length>8){
                    setTimeout(function(){
                        scroll2 = new BScroll('.scroll2');
                    },430);
                }
            }
        }
    });
    // 列表赋值，点击外面区域关闭
    $listDiv.on('click',function (e) {
        var e=e||window.event;
        if($(e.target).parent().hasClass('select-ul')){
            $(e.target).siblings().removeClass('check-li');
            if(!$(e.target).hasClass('check-li')){
                $(e.target).addClass('check-li');
            };
            if($(e.target).parent().hasClass('select0')){
                $span0.text($(e.target).text());
                $span0.attr('expireStart',$(e.target).attr('k1'));
                $span0.attr('expireEnd',$(e.target).attr('k2'));
            }else if($(e.target).parent().hasClass('select1')){
                $span1.text($(e.target).text());
                $span1.attr('projectRateStart',$(e.target).attr('k3'));
                $span1.attr('projectRateEnd',$(e.target).attr('k4'));
            }else if($(e.target).parent().hasClass('select2')){
                $span2.text($(e.target).text());
                $span2.attr('isDiscount',$(e.target).attr('k5'));
            }
        }else{
            // $listDiv.find('ul').css('display','none');
            // $listDiv.css('display','none');
            toastCenter("请点击搜索");
        }
    })
    // 点击搜索 参数
    var selectType=3;
    var searchReqUrl = url;
    var searchCurrentPage = 1;
    var searchTotalPage = 1;
    var searchStop = true;
    //点击条件的搜索
    var nums=null;
    var k1=null;
    var k2=null;
    var k3=null;
    var k4=null;
    var k5=null;
    $('.now-to-search').on('click',function(){
        selectType=3;
        if($span0.text()=="剩余期限"||$span1.text()=="项目利率"||$span2.text()=="折让率"){
            toastCenter("请选择所有筛选条件");
        }else{
            $select.each(function(i,item){
                item.className='';
            });
            $listDiv.find('ul').css('display','none');
            $listDiv.css('display','none');
            $("#credititem-list").html('');
            $("#credititem-list").siblings('a.col').remove();
            $('.container').off('touchmove',delDefaultEvent);
            // 重置
            searchCurrentPage = 1;
            searchTotalPage = 1;
            searchStop = true;
            k1=$span0.attr('expireStart');
            k2=$span0.attr('expireEnd');
            k3=$span1.attr('projectRateStart');
            k4=$span1.attr('projectRateEnd');
            k5=$span2.attr('isDiscount');
            window.onscroll='';
            getSearchPageList(selectType,nums,k1,k2,k3,k4,k5);
            window.onscroll=function()  {
                //当内容滚动到底部时加载新的内容
                if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
                    //当前要加载的页码
                    getSearchPageList(selectType,nums,k1,k2,k3,k4,k5);
                }
            };
        }
    })
    // 关闭下面的搜索出来的弹窗
    function closeSearchBox(){
        $listDiv.find('ul').css('display','none');
        $listDiv.css('display','none');
        $('.container').off('touchmove',delDefaultEvent);
    }
    //点击上面的搜索框
    $(".search-input").on('click',searchEvent);
    function searchEvent(){
        // 关闭下面的搜索出来的弹窗
        $listDiv.find('ul').css('display','none');
        $listDiv.css('display','none');
        //禁止滑动
        $('.container').on('touchmove',delDefaultEvent);
        //清除这个事件
        $(".search-input").off('click',searchEvent);
        // 隐藏条件搜索
        if($('.select-lists').css('display')=='block'){
            $('.select-lists').css('display','none');
        }
        $allListItem.animate({"left":"-100%"},300,'linear',function(){
            $allListItem.children('.col').remove().end().children('#credititem-list').html('');
            $(".load").html('');
            $allListItem.css('left',"0");
            // 让这个页面可滑动
            $('.container').off('touchmove',delDefaultEvent);
        });
        $(".search-input-box").css('width','78%');
        $('.go-back-search').css('display','inline-block');
        $('.search-box-btn').css('display','inline-block');
        // 色值调整
        $('body').css('background','#fff');
    }
    // 底部隐藏
    $(".search-input").on('focus',function(){
        $('.footer').css('display','none');
    });
    // 底部显示
    // $(".search-input").on('blur',function(){
    //     $('.footer').css('display','block');
    // })
    // 定义一个参数，取值上面的搜索框，用于请求做参数
    var inputValue='';
    // 上边框右面的搜索
    $('.search-box-btn').on('click',function(){
        if(!$('.search-input').val()){
            return false;
        }
        inputValue=$('.search-input').val();
        $('body').css('background','#e7eaf1');
        $("#credititem-list").html('');
        $("#credititem-list").siblings('a.col').remove();
        // inputValue=$('.search-input').val();
        selectType=2;
        // 重置
        searchCurrentPage = 1;
        searchTotalPage = 1;
        searchStop = true;
        window.onscroll='';
        getSearchPageList(selectType,inputValue);
        window.onscroll=function()  {
            //当内容滚动到底部时加载新的内容
            if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
                //当前要加载的页码
                getSearchPageList(selectType,inputValue);
            }
        };
    });


    canvasToCircle();
    function canvasToCircle(){
        var canvasArray=document.getElementsByTagName('canvas');
        for(var i=0;i<canvasArray.length;i++){
            alert(1);
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
    // 原来转让列表的分页
    //分页相关
    var req_url = url;
    var currentPage = 2;
    var totalPage = tp;
    var stop = true;
    //自动加载分页数据
    window.onscroll=function() {
        //当内容滚动到底部时加载新的内容
        if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
            //当前要加载的页码
            getPageList();
        }
    };
    function getPageList() {
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
                        if (currentPage == data.header.cp) {
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

                    canvasToCircle();
                },
                complete: function()
                {
                    stop = true;
                    $(".load").html("加载更多");
                }
            });
        }
    }

    // 条件搜索功能的分页
    function getSearchPageList(type,num,k1,k2,k3,k4,k5) {
        if(type==2){
            if (searchStop === true && searchCurrentPage <= searchTotalPage) {
                searchStop = false;
                $(".load").html("正在加载...");
                $.ajax({
                    type: 'GET',
                    url: searchReqUrl,
                    data: {page: searchCurrentPage,'multiName':num,'selectType':2},
                    dataType: 'json',
                    success: function(data)
                    {
                        if(data.isShowPop){
                            toastCenter("没有符合条件的转让",function(){
                                location.href='/licai/notes';
                            });
                            return false;
                        }else{
                            searchTotalPage=data.header.tp;
                            if (data.code === 0) {
                                //如果当前页和返回页码相同，则改变页面html,否则视为异常
                                if (searchCurrentPage == data.header.cp) {
                                    if (data.html) {
                                        $('.load').before(data.html);
                                        // $("#credititem-list").html($("#credititem-list").html()+data.html);
                                        //页码＋1
                                        searchCurrentPage=parseInt(data.header.cp) + 1;;
                                    }
                                }
                                //当前页大于总页数,隐藏加载更多提示(暂无数据时,tp为0)
                                if (searchCurrentPage >= data.header.tp) {
                                    $(".load").hide();
                                } else {
                                    $(".load").show();
                                }
                            } else {
                                alert(data.message);
                            }
                            canvasToCircle();
                        }
                    },
                    complete: function()
                    {
                        searchStop = true;
                        $(".load").html("加载更多");
                    }
                });
            }
        }else{
            if (searchStop === true && searchCurrentPage <= searchTotalPage) {
                searchStop = false;
                $(".load").html("正在加载...");
                $.ajax({
                    type: 'GET',
                    url: searchReqUrl,
                    data: {page: searchCurrentPage,'expireStart':k1*30,'expireEnd':k2*30,'projectRateStart':k3,'projectRateEnd':k4,'isDiscount':k5,'selectType':3},
                    dataType: 'json',
                    success: function(data)
                    {
                        if(data.isShowPop){
                            toastCenter("没有符合条件的转让",function(){
                                // location.href='/licai/notes';
                                $('#credititem-list').html('<div class="mark-top" style="position:relative;bottom: -10px;background: #fff;height: 41px;line-height: 41px;color:#131313;font-size:14px;padding-left: 4.5%;border-bottom: 1px solid #ddd;">推荐转让<img style="width:7.5%;margin-left: 1%;" src="/images/licaiSelect/tuijian.png" alt=""></div>\n');
                                window.onscroll='';
                                req_url = url;
                                currentPage = 1;
                                totalPage = tp;
                                stop = true;
                                getPageList();
                                $span0.text('剩余期限');
                                $span1.text('项目利率');
                                $span2.text('折让率');
                                $(".select-ul").children().removeClass('check-li');
                                //自动加载分页数据
                                window.onscroll=function() {
                                    //当内容滚动到底部时加载新的内容
                                    if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
                                        //当前要加载的页码
                                        getPageList();
                                    }
                                };
                            });
                            return false;
                        }else{
                            searchTotalPage=data.header.tp;
                            if (data.code === 0) {
                                //如果当前页和返回页码相同，则改变页面html,否则视为异常
                                if (searchCurrentPage == data.header.cp) {
                                    if (data.html) {
                                        $('.load').before(data.html);
                                        // $("#credititem-list").html($("#credititem-list").html()+data.html);
                                        //页码＋1
                                        searchCurrentPage=parseInt(data.header.cp) + 1;;
                                    }
                                }
                                //当前页大于总页数,隐藏加载更多提示(暂无数据时,tp为0)
                                if (searchCurrentPage >= data.header.tp) {
                                    $(".load").hide();
                                } else {
                                    $(".load").show();
                                }
                            } else {
                                alert(data.message);
                            }
                            canvasToCircle();
                        }
                    },
                    complete: function()
                    {
                        searchStop = true;
                        $(".load").html("加载更多");
                    }
                });
            }
        }
    }
    // toast 弹窗
    function toastCenter(val, active) {
        var $alert = $('<div class="error-info" style="display: block; position: fixed;font-size: 12px;"><div>' + val + '</div></div>');
        $('body').append($alert);
        $alert.find('div').width($alert.width());
        setTimeout(function () {
            $alert.fadeOut();
            setTimeout(function () {
                $alert.remove();
            }, 200);
            if (active) {
                active();
            }
        }, 2500);
    }
});
