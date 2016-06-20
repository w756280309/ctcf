$(document).ready(function () {
    $('.scrollClick').on('click', function () {
        $('html,body').animate({'scrollTop': $('.help-header').eq($(this).index('.scrollClick')).offset().top}, 1000);
    })
    var search = window.location.search;
    if (search.indexOf('type') != -1) {
        var scrollArr = search.split('=');
        $('html,body').animate({'scrollTop': $('.help-header').eq(scrollArr[1]).offset().top}, 1000);
    }
    // var top1, top2, top3, top4;
    // $(window).load(function () {
    //     top1 = $('.help-header').eq(0).offset().top;
    //     top2 = $('.help-header').eq(1).offset().top;
    //     top3 = $('.help-header').eq(2).offset().top;
    //     top4 = $('.help-header').eq(3).offset().top;
    // })
    // $(window).scroll(function () {
    //     $('.userAccount-left-nav').find('.scrollClick ').removeClass('selected');
    //     if ($(window).scrollTop() >= 0 && $(window).scrollTop() < top2 - 100) {
    //         $('.userAccount-left-nav').find('.top1').addClass('selected');
    //     } else if ($(window).scrollTop() >= top2 - 100 && $(window).scrollTop() < top3 - 100) {
    //         $('.userAccount-left-nav').find('.top2').addClass('selected');
    //     } else if ($(window).scrollTop() >= top3 - 100 && $(window).scrollTop() < top4 - 100) {
    //         $('.userAccount-left-nav').find('.top3').addClass('selected');
    //     } else if ($(window).scrollTop() >= top4 - 100) {
    //         $('.userAccount-left-nav').find('.top4').addClass('selected');
    //     }
    // })
});