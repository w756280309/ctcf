var WDJF = {};
/**
 * 转换格式方法,做了如下处理:
 * 1. 添加千分位显示
 * 2. 当type传入时,保留两位小数;当type没有传入时,去掉小数点右边多余的零
 * @param {type} amount
 * @param {type} type
 * @returns {String}
 */
WDJF.numberFormat = function(amount, type) {
    if ('undefined' === typeof type) {
        return (new Number(amount) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
    } else {
        return (new Number(amount).toFixed(2) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
    }
}

