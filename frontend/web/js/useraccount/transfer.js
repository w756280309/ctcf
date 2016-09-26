$(function () {
    $('#current_interest').on('mouseover', function () {
        $('.contentCenter_tip').show();
    })
    $('#current_interest').on('mouseout', function () {
        $('.contentCenter_tip').hide();
    })

    $('.discountRate_tip_icon').on('mouseover', function () {
        $('.discountRate_tip').show();
    })
    $('.discountRate_tip_icon').on('mouseout', function () {
        $('.discountRate_tip').hide();
    })

    $('.poundage_tip_icon').on('mouseover', function () {
        $('.poundage_tip').show();
    })
    $('.poundage_tip_icon').on('mouseout', function () {
        $('.poundage_tip').hide();
    })
});

//弹框的关闭按钮和确定按钮
function subClose() {
    $('.mask').hide();
    $('.confirmBox').hide();
}
