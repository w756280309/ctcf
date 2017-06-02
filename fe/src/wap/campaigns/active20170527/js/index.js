//抽奖配置代码
var lottery={
  index:-1,	//当前转动到哪个位置，起点位置
  count:0,	//总共有多少个位置
  timer:0,	//setTimeout的ID，用clearTimeout清除
  speed:100,	//初始转动速度
  times:0,	//转动次数
  cycle:30,	//转动基本次数：即至少需要转动多少次再进入抽奖环节
  prize:-1,	//中奖位置
  jiangpin: 5,
  init:function(id){
    if ($("#"+id).find(".lottery-unit").length>0) {
      $lottery = $("#"+id);
      $units = $lottery.find(".lottery-unit");
      this.obj = $lottery;
      this.count = $units.length;
//        $lottery.find(".lottery-unit-"+this.index).addClass("active");
    }
  },
  roll:function(){
    var index = this.index;
    var count = this.count;
    var lottery = this.obj;
    $(lottery).find(".lottery-unit-"+index+" img").removeClass("active");
    index += 1;
    if (index>count-1) {
      index = 0;
    }
    $(lottery).find(".lottery-unit-"+index+" img").addClass("active");
    this.index=index;
    return false;
  },
  stop:function(index){
    this.prize=index;
    return false;
  }
};
function roll(){
  if (lottery.times > lottery.cycle+5 && lottery.prize==lottery.index) {
    clearTimeout(lottery.timer);
    lottery.prize=-1;
    lottery.times=0;
    click=false;
    award();
  }else{
    if (lottery.times<lottery.cycle) {
      lottery.speed -= 10;
    }else if(lottery.times==lottery.cycle) {
      var index = lottery.jiangpin;
      lottery.prize = index; //此处定义最后是哪个奖品，可通过给lottery.jiangpin赋值改变
    }else{
      if (lottery.times > lottery.cycle+10 && ((lottery.prize==0 && lottery.index==5) || lottery.prize==lottery.index+4)) {
        lottery.speed += 110;
      }else{
        lottery.speed += 20;
      }
    }
    if (lottery.speed<65) {
      lottery.speed=65;
    }
    // console.log(lottery.times+'^^^^^^'+lottery.speed+'^^^^^^^'+lottery.prize);
    lottery.timer = setTimeout(roll,lottery.speed);
  }
  lottery.times += 1;
  lottery.roll();
  // return false;
}
var click=false;

$(function () {
  FastClick.attach(document.body);

  $('.close-prize').on('click',function () {
    $('.myprize-list').hide();
    $('.mask-list').hide();
    $('body').css('overflow','scroll').off('touchmove');
  })
});