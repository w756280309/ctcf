/*此方法依赖jquery-1.11.1.min.js&&iscroll.js&&handlebars-v4.0.10.js*/
function giftsList(opt){
    var tpl = '<script  id="gifts-list-template" type="text/x-handlebars-template">\n' +
        '    <!--对应的奖品列表-->\n' +
        '    <div class="prizes-box">\n' +
        '        <div class="outer-box">\n' +
        '            <img class="pop_close" src="'+opt.closeImg+'" alt="">\n' +
        '\n' +
        '            <div class="prizes-pomp">\n' +
        '                <p class="prizes-title">奖品列表</p>\n' +
        '                <div id="wrapper">\n' +
        '                    <ul>\n' +
        '                        {{#if isGifts}}\n' +
        '                        {{#each list}}\n' +
        '                        <li class="clearfix">\n' +
        '                            <div class="lf"><img src="{{path}}" alt="礼品"></div>\n' +
        '                            <div class="lf">\n' +
        '                                <p>{{name}}</p>\n' +
        '                                <p>{{awardTime}}</p>\n' +
        '                            </div>\n' +
        '                        </li>\n' +
        '                        {{/each}}\n' +
        '                        {{else}}\n' +
        '                        <li class="no-prizes">您还没有获得奖品哦！</li>\n' +
        '                        {{/if}}\n' +
        '                    </ul>\n' +
        '                </div>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</script>';
    $('body').append(tpl);
    var defaults = {
        isGifts : false,
        list : []
    },
    options = $.extend(defaults, opt);
    var data = {
        isGifts : options.isGifts,
        list : options.list
    };
    var source = $("#gifts-list-template").html();
    var template = Handlebars.compile(source);
    var html = template(data);
    $('body').append(html);
    $(".prizes-box").on('touchmove', eventTarget, false);
    $(".pop_close").on("click",function(){
        $("#gifts-list-template,.prizes-box").remove();
        $(".prizes-box").off("touchmove",eventTarget,false);
    });
    setTimeout(function(){
        var myScroll = new iScroll('wrapper',{
            vScrollbar:false,
            hScrollbar:false
        });
    },500)
}
function eventTarget(event) {
    var event = event || window.event;
    event.preventDefault();
}

