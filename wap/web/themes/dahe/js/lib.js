var WDJF = {};
/**
 * 为金额添加千分位显示
 * @param double|string amount 金额的数字或字符串
 * @param boolean stripTrailingZeros 末尾是否保留0标志位,为真时,去掉小数点右边多余的零,否则保留两位小数
 * @returns string 金额的千分位显示字符串
 */
WDJF.numberFormat = function(amount, stripTrailingZeros) {
    if (stripTrailingZeros) {
        return (new Number(amount) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
    } else {
        return (new Number(amount).toFixed(2) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
    }
}

