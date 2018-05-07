/*
  @auhor songpeng
  @param {Object}一个对象{
             // 在某一个目标位置去绘画
             // dom:canvas,
             //素材路径
             src:'1.png',
             // 素材大小
             size:{width:'750',height:'440'},
              // 文本内容
             text:"三大饿哦大家饿哦的加偶外会发疯的佛阿文",
             textAlign:'left',
             textBaseline:'top',
             textPosition:{wx:'20',hx:'20',width:'200',maxWidth:"710"},
             colorPosition:{x0:'0',y0:'0',x1:'750',y1:'440'},
             color:[["0","blue"],["0.5","yellow"],["1.0","red"]],
             font:"50px microsoft yahei",
             initHeight:'0',
             hHeight:'80',
             // img:'',
            };
  @example var a = new Watermark(obj);
        a.init(function(){
            document.body.appendChild(a.img);
        });
  waterfall - v1.1 (2018-04-12T18:08:51+0800)
  @version 1.1
 */
function Watermark(obj){
    // 新建一个canvas元素
    this.canvas =document.createElement('canvas');
    // 宽 高
    this.canvas.width=obj.size.width||'750';
    this.canvas.height=obj.size.height||'440';
    // 图片路径
    this.src=obj.src||'';
    //绘制的文本
    this.text=obj.text||'bySongPeng';
    // X轴对齐方式
    this.textAlign=obj.textAlign||'left';
    // 文本位置 宽度 高度 最大宽度
    this.textPosition=obj.textPosition||{wx:'20',hx:'20',width:'200',maxWidth:"710"};
    // 文本渐变方位
    this.colorPosition=obj.colorPosition||{x0:'0',y0:'0',x1:'750',y1:'440'};
    //文本渐变的百分比及对应色值
    this.color=obj.color||[["0","blue"],["0.5","yellow"],["1.0","red"]];
    // 文本字体
    this.font=obj.font||"40px microsoft yahei";
    // 即将生成的图片
    this.img='';
    // 这个图片添加的类名
    this.class=obj.class||'targetClass';
    // 文本y轴对齐方式
    this.textBaseline=obj.textBaseline||'top';
    // 文本的行高
    this.hHeight=obj.hHeight||50;
    // 初始相对高度
    this.initHeight=obj.initHeight||0;
    // 文本标记 绘制会用到
    this.lastSubStrIndex=0;
    this.lineWidth=0;
}
/*添加水印操作*/
Watermark.prototype.addWatermark = function (callback) {
    var img = new Image();
    img.src = this.src;
    var that = this;
    img.onload = function () {
        var ctx = that.canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        ctx.font = that.font;
        ctx.textAlign=that.textAlign;
        ctx.textBaseline=that.textBaseline;
        var gradient=ctx.createLinearGradient(that.colorPosition.x0,that.colorPosition.y0,that.colorPosition.x1,that.colorPosition.y1);
        for(var j=0;j<that.color.length;j++){
            gradient.addColorStop(that.color[j][0],that.color[j][1]);
        }
        ctx.fillStyle=gradient;
        for(var i=0;i<that.text.length;i++){
            that.lineWidth+=ctx.measureText(that.text[i]).width;

            if(that.lineWidth>that.textPosition.width){
                ctx.fillText(that.text.substring(that.lastSubStrIndex,i+1),that.textPosition.wx,(Number(that.initHeight)+Number(that.textPosition.hx)));//绘制截取部分
                that.initHeight+=that.hHeight;//20为字体的高度
                that.lineWidth=0;
                that.lastSubStrIndex=i;
            }
            if(i==that.text.length-1){//绘制剩余部分
                ctx.fillText(that.text.substring(that.lastSubStrIndex,i+1),that.textPosition.wx,(Number(that.initHeight)+Number(that.textPosition.hx)));
            }

        }
        if(typeof callback === "function"){
            callback();
        }
    }
    return this;
}

/*canvas水印转化为图片*/
Watermark.prototype.convertCanvasToImage = function() {
    this.img = document.createElement('img');
    this.img.src = this.canvas.toDataURL("image/png");
    this.img.className=this.class;
    // let list=document.querySelectorAll("."+this.className);
}
Watermark.prototype.init=function(callback){
    var that=this;
    this.addWatermark(function(){
        that.convertCanvasToImage();
        if(typeof callback === "function"){
            callback();
        }
    });
}