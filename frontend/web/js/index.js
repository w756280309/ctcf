$(function(){
    $('#box').fullpage({
        'verticalCentered': false,
        'anchors': ['page1', 'page2', 'page3', 'page4', 'page5'],
        'css3': true,
        'navigation': true,
        slideSelector: '.horizontal-scrolling',
        'navigationPosition': 'right',
        afterLoad:function(anchorLink, index){
            if(index==5){
                $('.four-title').addClass('four-title1');
                $('.four-content').addClass('four-content1');
                $('.four-left').addClass('four-left1');
                $('.four-right').addClass('four-right1');
                $('.four-bottom').addClass('four-bottom1');
            }else {
                $('.four-left').removeClass('four-left1');
                $('.four-right').removeClass('four-right1');
                $('.four-bottom').removeClass('four-bottom1');
                $('.four-title').removeClass('four-title1');
                $('.four-content').removeClass('four-content1');
            }
        },
        onLeave: function(index, nextIndex,direction) {
            if(index == 4) {
                if(direction=='down'){
                    $('.four-title').addClass('four-title1');
                    $('.four-content').addClass('four-content1');
                    $('.four-left').addClass('four-left1');
                    $('.four-right').addClass('four-right1');
                    $('.four-bottom').addClass('four-bottom1');
                }else if(direction=='up'){
                    $('.four-left').removeClass('four-left1');
                    $('.four-right').removeClass('four-right1');
                    $('.four-bottom').removeClass('four-bottom1');
                    $('.four-title').removeClass('four-title1');
                    $('.four-content').removeClass('four-content1');
                }
            }
        }
    })

})