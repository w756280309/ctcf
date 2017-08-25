/*此方法依赖jquery-1.11.1.min.js&&iscroll.js&&handlebars-v4.0.10.js*/
function giftsList(opt){
    var tpl = '<script  id="gifts-list-template" type="text/x-handlebars-template">\n' +
        '    <!--对应的奖品列表-->\n' +
        '    <div class="prizes-box">\n' +
        '        <div class="outer-box">\n' +
        '            <img class="pop_close" onclick="$(\'.prizes-box\').hide();" src="'+opt.closeImg+'" alt="">\n' +
        '\n' +
        '            <div class="prizes-pomp">\n' +
        '                <p class="prizes-title">我的奖品</p>\n' +
        '                <div id="wrapper">\n' +
        '                    <ul>\n' +
        '                        {{#if isGifts}}\n' +
        '                        {{#each list}}\n' +
        '                        <li class="clearfix">\n' +
        '                            <div class="lf"><img src="{{gifts_num}}" alt="礼品"></div>\n' +
        '                            <div class="lf">\n' +
        '                                <p>{{gifts_title}}</p>\n' +
        '                                <p>中奖来源:{{gifts_time}}</p>\n' +
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
}


