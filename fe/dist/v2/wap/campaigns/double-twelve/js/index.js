function lunBo(){{var o=.693;setInterval(function(){$(".people ul li").eq(0).animate({"margin-top":"-"+o+"rem"},1e3,function(){$(this).css({"margin-top":0}),$(this).appendTo($(".people ul"))})},2e3)}}function eventTarget(o){o.preventDefault()}$(function(){FastClick.attach(document.body),initScroll();var o=$(".people ul li").length;o>3&&lunBo(),$(".btest").on("click",function(){$(".mask").show();var o=$(this).data("value");$("."+o).show(),"drawgift"==o&&(flag=!0),$("body").on("touchmove",eventTarget,!1)}),$(".mask,.closepop").on("click",function(){$(".pop").hide(),$(".mask").hide(),flag&&(window.location.href=window.location.href+"?v="+10*Math.random()),$("body").unbind("touchmove")});var n=$("header").height()+$("#activereg").height()+5;$(".step1 a,.step2 a").on("click",function(){$("body").animate({scrollTop:n+"px"},1e3)}),$(".drawgift a").on("click",function(){window.location.href=window.location.href+"?v="+10*Math.random()})});var initScroll=function(){var o;intervalTime=setInterval(function(){var n=$("#giftBox ul").height();n>0&&(clearInterval(intervalTime),o=new iScroll("giftBox",{vScrollbar:!1}))},1)};