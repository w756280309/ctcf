var loadindex = 0 ;
function getLoadindex(){
    return loadindex();
}
function openLoading(){
    loadindex = layer.load(1,{shade:[0.4, 'gray']});
    return loadindex;
}
function cloaseLoading(){
    layer.close(loadindex);
    loadindex=0;
    return loadindex;
}

function newalert(res, msg, reload)
{
    if (res) {
        layer.msg('操作成功', {icon: 1});
        try {
            if (reload == 1) {
                location.reload();
            }
        } catch(err) {
            location.reload();
        }
    } else {
        if (msg == '') {
            msg = '操作失败';
        }
        layer.msg(msg, {icon: 2});
    }
}

var playSum;
function getLayer() {
    return playSum;
}
function openwin(url,width,height){
    playSum = layer.open({
        type: 2,
        area: [width+'px', height+'px'],
        fix: false, //不固定
        maxmin: false,
        content: url,
        success: function (index) {
            layer.close(index); //一般设定yes回调，必须进行手工关闭
        },
        end: function (jsonStr) {

        }
    });
}
function closewin(){
    parent.layer.close(window.parent.playSum); //一般设定yes回调，必须进行手工关闭
}

isJson = function(obj){
    var isjson = typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;
    return isjson;
}