/**
 * Created by lcl on 2015/11/16.
 */
var csrf;
$(function(){
    checkLoginStatus();

    csrf = $("meta[name=csrf-token]").attr('content');

    if($.trim($("#slideBox ul").html())!==""){
        //轮播图
        TouchSlide({
            slideCell:"#slideBox",
            titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
            mainCell:".bd ul",
            effect:"leftLoop",
            autoPage:true,//自动分页
            autoPlay:true, //自动播放
            interTime:2500
        });
    }


//    //去往详情页
//    $('.dealdata').bind('click', function () {
//        location.href='/deal/deal/detail?sn='+$(this).attr('data-index');
//    })

    //圆圈进度
    var canvasArray=document.getElementsByTagName('canvas');
    for(var i=0;i<canvasArray.length;i++){
        obj = $(canvasArray[i]);
        var per = obj.attr('data-per');
        $(canvasArray[i]).ClassyLoader({
            width: 100,
            height: 100,
            start: 'top',
            animate:false,
            percentage: per,//显示比例
            showText: false,//是否显示进度比例
            speed: 20,
            fontSize: '20px',
            diameter: 40,
            fontColor: '#ff9836',
            lineColor: '#f7403a',
            remainingLineColor: 'rgba(55, 55, 55, 0.1)',
            lineWidth: 4,
            textStr:'' //显示文字
        });
    }

})

function checkLoginStatus()
{
    var xhr = $.get('/site/session');

    xhr.done(function(data) {
        if (!data.isLoggedin) {
            $('#isLoggedin').show();
        }
    });
}

