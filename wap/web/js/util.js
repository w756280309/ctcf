/**
 * Created by yabusai on 11/20/15.
 */

/**
 *
 * @description: 返回字符串长度，汉字计数为2
 * @param: str->指定字符串
 *
 */
function strLength(str) {
    var a = 0;
    for (var i = 0; i < str.length; i++) {
        if (str.charCodeAt(i) > 255)
            a += 2;//按照预期计数增加2
        else
            a++;
    }
    return a;
}

/**
 *
 * @description: 清除空格
 *
 */
String.prototype.trim = function() {
    var reExtraSpace = /^\s*(.*?)\s+$/;
    return this.replace(reExtraSpace, "$1")
}

/**
 *
 * @description: 清除左空格/右空格
 * @param: s->指定字符串
 *
 */
function ltrim(s){ return s.replace( /^(\s*|　*)/, ""); }
function rtrim(s){ return s.replace( /(\s*|　*)$/, ""); }

/**
 *
 * @desccrition: 对String类型去除空格的拓展
 * @dir : 被去除空格所在的位置
 * @test: ie6-9 chrome firefox
 */
String.prototype.trim = function(dir){
    switch (dir) {
        case 0 : //去左边的空格
            return this.replace(/(^\s*)/g,'');
            break;
        case 1 : //去右边的空格
            return this.replace(/(\s*$)/g,'');
            break;
        case 2 : //去掉所有的空格
            return this.replace(/(\s*)/g,'');
            break;
        default : //去掉两边的空格
            return this.replace(/(^\s*)|(\s*$)/g,'');
    }
}

/**
 *
 * @descrition: 对字符串进行截取，包括普通字符和中文字符
 * @param : str ->待截取的字符串
 * @param : len ->要截取的长度
 *
 * 比如cutstr('hello',2)->he  cutstr("您好呀",4)->您好
 * 优先选择后台进行字符串截取，后css截取，最后js截取
 */
var cutstr = function(str, len) {
    var temp,
        icount = 0,
        patrn = /[^\x00-\xff]/g,    //中文字符匹配
        strre = "";

    for (var k = 0; k < str.length; k++) {
        if (icount < len ) {
            temp = str.substr(k, 1);
            if (temp.match(patrn) == null) {
                icount = icount + 1;
            } else {
                icount = icount + 2;
            }
            strre += temp;
        } else {
            break
        }
    }
    return strre;
}

/**
 *
 * @Dependence : https://gist.github.com/hehongwei44/3e167cfcda47d4c8051a#file-extendstringprototype-js
 * @description : 判断输入的参数是否为空
 * @return : true表示为输入参数为空
 *
 */
var isEmpty = function (str) {
    //空引用  空字符串  空输入
    return str == null || typeof str == "undefined" || str.trim() == "" ? true : false;
}

/**
 *
 * @description: 判断传入的参数的长度是否在给定的有效范围内
 * @param: minL->给定的最小的长度
 * @param: maxL->给定的最大的长度
 * @param: str->待验证的参数
 * @return : true表示合理，验证通过
 *
 */
var isAvaiableLength = function(minL,maxL,str){
    return (str.length >= minL && str.length <= maxL) ? true : false;
}

/**
 *
 * @description: 判断是否为数字类型
 * @param: value->指定字符串
 *
 */
function isDigit(value) {
    var patrn = /^[0-9]*$/;
    if (patrn.exec(value) == null || value == "") {
        return false
    } else {
        return true
    }
}

/**
 *
 * @description: 身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X
 * @param: str->指定字符串
 *
 */
function verifyCard(str){
    var reg= /(^\d{15}$)|(^\d{17}([0-9]|X)$)/   ;
    if( reg.test(str) ){
        return true;
    }else{
        return false;
    }
}

/**
 *
 * @description: 检验URL链接是否有效
 * @param: URL->指定URL
 *
 */
function getUrlState(URL){
    var xmlhttp = new ActiveXObject("microsoft.xmlhttp");
    xmlhttp.Open("GET",URL, false);
    try{
        xmlhttp.Send();
    }catch(e){
    }finally{
        var result = xmlhttp.responseText;
        if(result){
            if(xmlhttp.Status==200){
                return(true);
            }else{
                return(false);
            }
        }else{
            return(false);
        }
    }
}
