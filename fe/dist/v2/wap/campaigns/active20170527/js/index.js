function roll(){if(lottery.times>lottery.cycle+5&&lottery.prize==lottery.index)clearTimeout(lottery.timer),lottery.prize=-1,lottery.times=0,click=!1,award();else{if(lottery.times<lottery.cycle)lottery.speed-=10;else if(lottery.times==lottery.cycle){var t=lottery.jiangpin;lottery.prize=t}else lottery.speed+=lottery.times>lottery.cycle+10&&(0==lottery.prize&&5==lottery.index||lottery.prize==lottery.index+4)?110:20;lottery.speed<65&&(lottery.speed=65),lottery.timer=setTimeout(roll,lottery.speed)}lottery.times+=1,lottery.roll()}var lottery={index:-1,count:0,timer:0,speed:100,times:0,cycle:30,prize:-1,jiangpin:5,init:function(t){$("#"+t).find(".lottery-unit").length>0&&($lottery=$("#"+t),$units=$lottery.find(".lottery-unit"),this.obj=$lottery,this.count=$units.length)},roll:function(){var t=this.index,e=this.count,i=this.obj;return $(i).find(".lottery-unit-"+t+" img").removeClass("active"),t+=1,t>e-1&&(t=0),$(i).find(".lottery-unit-"+t+" img").addClass("active"),this.index=t,!1},stop:function(t){return this.prize=t,!1}},click=!1;$(function(){FastClick.attach(document.body),weiXinShareTips($(".wap-share-btn")),$(".close-prize").on("click",function(){$(".myprize-list").hide(),$(".mask-list").hide(),$("body").css("overflow","scroll").off("touchmove")})});