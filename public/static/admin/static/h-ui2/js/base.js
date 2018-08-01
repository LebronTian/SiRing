$(function () {
    if (navigator.userAgent.indexOf("Chrome") == -1) {
        //location.href = 'user.php?c=chrome';
    }
    $('.usertips').hover(function () {
        $(this).addClass('current').find('.downmenu1').show();
    }, function () {
        $(this).removeClass('current').find('.downmenu1').hide();
    });

    /* 回到顶部 */
    function goTop() {
        $("html,body").animate({
            scrollTop: 0
        }, 500);
    }

    $('body').on('click','#kfSysBox',function () {
      layer.open({
          type: 1,
          title: false,
          closeBtn: 0,
          shadeClose: true, //开启遮罩关闭
          area: ['300px', 'auto'],
       //   time: 2000, //2秒后自动关闭
          content: $("#kfSysList").html(),
          anim: 2,
      });
    });

    $(".js-common-msg").on('click', function () {
        var flag = $('.sys_msg').attr('flag');
        if (flag == 'showing' || flag == undefined) {
            $('.sys_msg').slideDown(300);
            $('.sys_msg').attr('flag', 'hideing');
        } else {
            /*$('.sys_msg').slideUp(300);
			$('.sys_msg').attr('flag','showing');*/
        }
    });

    /*播放音乐*/
    var play_voice_timer = null;
    $('.voice-player').on('click', function () {
        if ($("#audio_wrapper").size() != 0) {
            var old_dom = $('.js-audio-playnow');
            old_dom.find('.play').hide(),
                old_dom.find('.stop').text('点击播放').show(),
                old_dom.find('.second').empty().hide(),
                old_dom.removeClass("js-audio-playnow"),
            play_voice_timer && clearInterval(play_voice_timer),
                $('#audio_wrapper').remove();
        }
        var now_dom = $(this);
        now_dom.addClass('js-audio-playnow').find('.stop').html('loading...');
        var dom = $('<div id="audio_wrapper"><audio id="audio_player" preload="" type="audio/mpeg" src=""></audio></div>').hide();
        0 === $("#audio_wrapper").size() && $("body").append(dom);
        $("#audio_player").attr("src", now_dom.parent().attr('data-voice-src'));
        var player_dom = document.getElementById("audio_player");
        var o = function () {
            now_dom.find('.play').hide(),
                now_dom.find('.stop').text('点击播放').show(),
                now_dom.find('.second').empty().hide(),
                now_dom.removeClass("js-audio-playnow"),
            play_voice_timer && clearInterval(play_voice_timer),
                $('#audio_wrapper').remove();
        };
        var s = !!navigator.userAgent.match(/AppleWebKit.*Mobile./);
        s && player_dom.play();

        player_dom.addEventListener("canplay", function () {
            player_dom.play();
            play_voice_timer && clearInterval(play_voice_timer),
                now_dom.find('.stop').empty().hide().siblings('.play').show();
            now_dom.find('.second').empty().show().html("0/" + Math.floor(player_dom.duration));
            play_voice_timer = setInterval(function () {
                now_dom.find('.second').html(Math.round(player_dom.currentTime) + "/" + Math.floor(player_dom.duration))
            }, 1000);
            return false;
        });
        player_dom.addEventListener("ended", function () {
            o();
        });
        player_dom.addEventListener("error", function () {
            now_dom.find('.stop').text("加载失败！")
        }),
            $(".js-audio-playnow").one('click', function (e) {
                player_dom.pause(),
                    o(),
                    e.stopPropagation();
            });
    });
});

function layer_tips(msg_type, msg_content) {
    golbal_tips(msg_content, msg_type);
    return;
    layer.closeAll();
    var time = msg_type == 0 ? 3 : 4;
    var type = msg_type == 0 ? 1 : (msg_type != -1 ? 0 : -1);
    if (type == 0) {
        msg_content = '<font color="red">' + msg_content + '</font>';
    }
    layer.msg(msg_content);
}

function global_tips(msg, status, callback, delay) {
    //status 1:error, 0:success
    var type = typeof status == 'undefined' || status == 0 ? 'success' : 'error';
    var delay = delay || 1000;
    if ($("#infotips").length > 0) $("#infotips").remove();
    var html = '<div class="js-notifications notifications" id="infotips"><div class="alert in fade alert-' + type + '"><a href="javascript:;" class="close pull-right hide" onclick="$(\'#infotips\').remove();">×</a>' + msg + '</div></div>';
    $('body').append(html);
    $('#infotips').delay(delay).fadeOut(0, callback);
}

function golbal_tips(msg, status) {
    global_tips(msg, status)
}


var load_page_cache = [];

function load_page(dom, url, param, cache, obj) {
    loadProgress();
    if (cache != '' && load_page_cache[cache]) {
        $(dom).html(load_page_cache[cache]);
        loadProgress();
        if (obj) obj();
    } else {
        $(dom).html('<div data-reactroot="" class="ui-page-loading"><div style="height: 420px;"></div></div>');
        $(dom).load(url + '?load_page=1&t=' + Math.random(), param, function (response, status, xhr) {
            if (cache != '') {
                load_page_cache[cache] = response;
            }
            loadProgress();
            if (obj) {
                obj();
            }
        });
    }
}


/*
 * 小的弹出层
 *
 * param dom	  弹出层的ID 				使用 $(this);
 * param e	      弹出层的ID点击返回事件 	使用 event;
 * param position 方向  					left,top,right,bottom
 * param type     弹出层的类别  			copy,edit_txt,edit_txt_2delete,confirm,multi_txt,radio,input,url,module, te
 * param content  内容 						如果type为qrcode，可以配置显示选项 参数示例{qrcode:qrcode_url, title:'活动二维码', content:'扫一扫参加活动', copy_link:'', wx_download:''}
 * param ok_obj   点击确认键的回调方法
 * param placeholder 点位符
 */
function button_box(dom, event, position, type, content, ok_obj, placeholder) {
    var cancel_obj = arguments[7];
    event.stopPropagation();
    var left = 0, top = 0, width = 0, height = 0;
    var dom_offset = dom.offset();
    var content = content ? content : '';
    $('.popover').remove();
    if (type == 'copy') {
        $.getScript('/static/lib/jquery.zclip.min.js', function () {
            var html = $('<div class="popover ' + position + '" style="display: block; left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + ($(window).height() / 2) + 'px;"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><div class="form-inline"><div class="input-append"><input type="text" class="txt js-url-placeholder url-placeholder" readonly="" value="' + content + '"/><button type="button" class="btn js-btn-copy">复制</button></div></div></div></div></div>');
            $('body').append(html);
            var clip = new Clipboard(html.find('.js-btn-copy').get(0), {
                target: function () {
                    return html.find('.js-url-placeholder').get(0);
                }
            });

            clip.on('success', function (e) {
                e.clearSelection();
                $('.popover').remove();
                layer_tips(0, '复制成功');
            });

            button_box_after();
        });
    } else if (type == 'edit_txt') {
        $('body').append('<div class="popover ' + position + '" style="left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-rename"><div class="popover-content"><div class="form-horizontal"><div class="control-group"><div class="controls"><input type="text" class="js-rename-placeholder" maxlength="256"/> <button type="button" class="btn btn-primary js-btn-confirm">确定</button> <button type="reset" class="btn js-btn-cancel">取消</button></div></div></div></div></div></div>');
        $('.js-rename-placeholder').attr('placeholder', content).focus();
        button_box_after();
    } else if (type == 'edit_txt_2') {
        $('body').append('<div class="popover ' + position + '" style="left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-rename"><div class="popover-content"><div class="form-horizontal"><div class="control-group"><div class="controls">' + content.title_1 + ':<input type="text" class="js-rename-placeholder" maxlength="256"/> <br /><br />' + content.title_2 + ':<input type="text" class="js-keyword-placeholder" maxlength="100" style="width: 100px;" /> <button type="button" class="btn btn-primary js-btn-confirm">确定</button> <button type="reset" class="btn js-btn-cancel">取消</button></div></div></div></div></div></div>');
        $('.js-rename-placeholder').attr('placeholder', content.input_1).focus();
        $('.js-keyword-placeholder').attr('placeholder', content.input_2).focus();
        button_box_after();
    } else if (type == 'input') {
        $('body').append('<div class="popover ' + position + '" style="left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-rename"><div class="popover-content"><div class="form-horizontal"><div class="control-group"><div class="controls"><input type="text" class="js-rename-placeholder" maxlength="256"/> <button type="button" class="btn btn-primary js-btn-confirm">确定</button> <button type="reset" class="btn js-btn-cancel">取消</button></div></div></div></div></div></div>');
        if (placeholder) {
            $('.js-rename-placeholder').attr('placeholder', placeholder);
        }
        $('.js-rename-placeholder').val(content).focus();
        button_box_after();
    } else if (type == 'multi_txt') {
        $('body').append('<div class="popover ' + position + '" style="left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-chosen"><div class="popover-content"><div class="select2-container select2-container-multi js-select2 select2-dropdown-open" style="width:242px;display:inline-block;"><ul class="select2-choices"><li class="select2-search-field">    <input type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" id="s2id_autogen26" tabindex="-1" style="width:192px;"></li></ul></div> <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button> <button type="reset" class="btn js-btn-cancel">取消</button></div></div></div>');
        $('.popover-chosen .select2-input').attr('placeholder', content).focus();
        multi_choose_obj();
        button_box_after();
    } else if (type == 'multi_txt2') {
        var cccat_id = content.cats_id;
        $('body').append('<div class="popover ' + position + '" style="left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-chosen"><div class="popover-content"><div class="select2-container select2-container-multi js-select2 select2-dropdown-open" style="width:242px;display:inline-block;"><ul class="select2-choices"><li class="select2-search-field">    <input type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" id="s2id_autogen26" tabindex="-1" style="width:192px;"></li></ul></div> <button type="button" data-button-cat-id="' + cccat_id + '"  class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button> <button type="reset" class="btn js-btn-cancel">取消</button></div></div></div>');
        $('.popover-chosen .select2-input').attr('placeholder', content.contents).focus();
        // multi_choose_obj();
        multi_choose_obj2(content.arr, content.has_atom_id);
        button_box_after();
    } else if (type == 'radio') {
        $('body').append('<div class="popover ' + position + '" style="top: ' + $(window).scrollTop() + 'px; left: -' + ($(window).width() * 5) + 'px;"><div class="arrow"></div><div class="popover-inner popover-change"><div class="popover-content text-center"><form class="form-inline"><label class="radio"><input type="radio" name="discount" value="1" checked="">参与</label><label class="radio"><input type="radio" name="discount" value="0">不参与</label><button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button><button type="reset" class="btn js-btn-cancel">取消</button></form></div></div></div>');
        button_box_after();
    } else if (type == 'url') {
        var yinxiao_btn = '';

        var button_h = $('<div class="popover ' + position + '" style="left:-' + ($(window).width() * 5) + 'px; top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-rename"><div class="popover-content"><div class="form-horizontal"><div class="control-group"><div class="controls"><input type="text" class="link-placeholder js-link-placeholder" placeholder="' + content + '" /> ' + yinxiao_btn + '  <button type="button" class="btn btn-primary js-btn-confirm">确定</button> <button type="reset" class="btn js-btn-cancel">取消</button></div></div></div></div></div></div>');

        button_h.find('.js-btn-link').click(function () {
            $.layer({
                type: 2,
                title: '插入功能库链接',
                shadeClose: true,
                maxmin: true,
                fix: false,
                area: ['600px', '450px'],
                iframe: {
                    src: '?c=link&a=index'
                }
            });
        });

        //新活动弹窗
        button_h.find('.js-btn-link-new').click(function () {
            $.layer({
                type: 2,
                title: '插入其他新活动链接',
                shadeClose: true,
                maxmin: true,
                fix: false,
                area: ['600px', '450px'],
                iframe: {
                    src: '?c=link&a=new_activity'
                }
            });
        });


        $('body').append(button_h);
        button_h.show();
        $('.js-rename-placeholder').val(content).focus();
        button_box_after();
    } else if (type == 'module') {
        $('body').append('<div class="popover ' + position + '" style="left:' + (dom_offset.left - 178) + 'px;top:' + (dom_offset.top - 500) + 'px;"><div class="arrow"></div><div class="popover-inner popover-text"><div class="popover-content"><form class="form-horizontal"><div class="control-group"><label class="control-label">请设置模块名称：</label><div class="controls"><input type="text" class="text-placeholder js-text-placeholder"></div></div><div class="form-actions"><button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定"> 确定</button><button type="reset" class="btn js-btn-cancel">取消</button></div></form></div></div></div>');
        $('.js-text-placeholder').focus();
        $('.js-text-placeholder').val(content);
        button_box_after();
        $('.popover').css({
            top: (dom_offset.top - dom.height() - 115),
            left: dom_offset.left - ($('.popover').width() / 2) + 20
        });
    } else if (type == 'tips') {
        $('body').append('<div class="popover ' + position + '" style="display:block;left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-' + type + '"><div class="popover-content text-center"><div class="form-inline"><span class="help-inline item-delete">' + content + '</span><button type="button" class="btn btn-primary js-btn-confirm">确定</button> </div></div></div></div>');
        button_box_after();
    } else if (type == 'qrcode') { //二维码
        var title_html = '';
        var copy_html = '';
        var content_html = '';
        var download_html = '';
        var wx_download_html = '';

        //标题
        if (content.title && content.title != undefined) {
            var title_html = '<div class="popover-qrcode-header"><button type="button" class="close js-btn-cancel">×</button><div class="popover-qrcode-title">' + content.title + '</div></div>';
        }
        //复制链接
        if (content.copy_link && content.copy_link != undefined) {
            var copy_html = '<div class="popover-copy-link"><input type="text" class="txt js-url-placeholder url-placeholder" readonly="" value="' + content.copy_link + '"/><button type="button" class="btn js-btn-copy">复制</button></div>';
            $.getScript('/static/lib/jquery.zclip.min.js', function () {
                var clip = new Clipboard($('.js-btn-copy').get(0), {
                    target: function () {
                        return $('.js-url-placeholder').get(0);
                    }
                });

                clip.on('success', function (e) {
                    e.clearSelection();
                    $('.popover').remove();
                    global_tips(0, '复制成功');
                });
            });
        }
        //内容提示
        if (content.content && content.content != undefined) {
            var content_html = '<p class="scan-info">' + content.content + '</p>';
        }
        //下载二维码
        if (content.qrcode && content.qrcode != undefined) {
            var download_html = '<a href="' + content.qrcode + '">下载二维码</a>';
        }
        //下载微信二维码暂时不用
        if (content.wx_download && content.wx_download != undefined) {
            var wx_download_html = '<a href="' + content.wx_download + '">微信二维码</a>';
        }

        $('body').append('<div class="popover ' + position + '" style="display:block;left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-' + type + '"><div class="popover-content">' + title_html + copy_html + '<div class="form-inline"><div class="qrcode-wrap loading"><img class="qrcode" src="' + content.qrcode + '">' + content_html + '</div><div class="popover-qrcode-footer">' + download_html + wx_download_html + '</div></div></div></div></div>');
        button_box_after();
    } else {
        $('body').append('<div class="popover ' + position + '" style="display:block;left:-' + ($(window).width() * 5) + 'px;top:' + $(window).scrollTop() + 'px;"><div class="arrow"></div><div class="popover-inner popover-' + type + '"><div class="popover-content text-center"><div class="form-inline"><span class="help-inline item-delete" style="display:block;">' + content + '</span><button type="button" class="btn btn-primary js-btn-confirm">确定</button> <button type="reset" class="btn js-btn-cancel">取消</button></div></div></div></div>');
        button_box_after();
    }

    function button_box_after() {
        $('.popover .js-btn-cancel').one('click', function () {
            if (cancel_obj != undefined) {
                cancel_obj();
            } else {
                close_button_box();
            }
        });
        $('.popover .js-btn-confirm').bind('click', function () {
            if (ok_obj) {
                ok_obj();
            } else {
                close_button_box();
            }
        });
        $('.popover').click(function (e) {
            e.stopPropagation();
        });
        if (cancel_obj == undefined) {
            $('body').bind('click', function () {
                close_button_box();
            });
        }

        var popover_height = $('.popover').height();
        var popover_width = $('.popover').width();
        $('.popover').css('display', 'block');
        switch (position) {
            case 'left':
                $('.popover').css({
                    top: dom_offset.top - (popover_height + 10 - dom.height()) / 2,
                    left: dom_offset.left - popover_width - 14
                });
                break;
            case 'right':
                $('.popover').css({
                    top: dom_offset.top - (popover_height + 10 - dom.height()) / 2,
                    left: dom_offset.left + dom.width() + 27
                });
                $('.popover-confirm').css('margin-left', '0');
                break;
            case 'top':
                $('.popover').css({
                    top: (dom_offset.top - dom.height() - 40),
                    left: dom_offset.left - (popover_width / 2) + (dom.width() / 2)
                });
                break;
            case 'bottom':
                $('.popover').css({
                    top: dom_offset.top + dom.height() - 3,
                    left: dom_offset.left - (popover_width / 2) + (dom.width() / 2)
                });
                break;
        }
    }

    //添加商品添加规格专用方法
    function multi_choose_obj() {
        $('.popover-chosen .select2-input').keyup(function (event) {
            var input_select2 = $.trim($(this).val());
            if (event.keyCode == 13 && input_select2.length != 0) {
                var html = $('<li class="select2-search-choice"><div>' + input_select2 + '</div><a href="#" class="select2-search-choice-close" tabindex="-1" onclick="$(this).closest(\'li\').remove();$(\'.popover-chosen .select2-input\').focus();"></a></li>');
                if ($('.popover-chosen .select2-choices .select2-search-choice').size() > 0) {
                    var has_li = false;
                    $.each($('.popover-chosen .select2-choices .select2-search-choice'), function (i, item) {
                        if ($(item).find('div').html() == input_select2) {
                            has_li = true;
                            return false;
                        }
                    });
                    if (has_li === false) {
                        $('.popover-chosen .select2-choices .select2-search-choice:last').after(html);
                    } else {
                        layer_tips(1, '已经存在相同的规格');
                        $(this).val('').focus();
                        return;
                    }
                } else {
                    $('.popover-chosen .select2-choices').prepend(html);
                }

                var r = getRandNumber();
                html.attr('data-vid', r);
                html.attr('check-data-vid', r);

                $.post(get_property_value_url, {
                    pid: dom.closest('.sku-sub-group').find('.js-sku-name').attr('data-id'),
                    txt: input_select2
                }, function (result) {
                    if (result.err_code == 0) {
                        html.attr('data-vid', result.err_msg);

                        if ($("#r_" + r).size() > 0) {
                            $("#r_" + r).attr("atom-id", result.err_msg);
                        }
                    } else {
                        layer_tips(result.err_msg);
                        html.remove();
                    }
                });
                $(this).removeAttr('placeholder').val('').focus();
            }
        });
    }

    //查询商品属性规格专用方法  array(1,2,3)
    function multi_choose_obj2(strss, arr_has_atom_id) {

        var html;
        $('.popover-chosen .select2-choices .select2-search-choice').detach('');
        for (var i in strss) {
            // html +=  '<li class="select2-search-choice"  onclick="$(this).addClass(\'choice\');"  data-vid='+strss[i].pid+'"><div>'+strss[i].value+'</div><a href="#" class="select2-search-choice-select" tabindex="-1"  onclick="$(\'.popover-chosen .select2-input\').focus();"></a></li>';
            if (jQuery.inArray(strss[i].vid, arr_has_atom_id) == '-1') {
                html += '<li class="select2-search-choice cursor"  onclick="javascript:if($(this).attr(\'idd\')==\'choice\'){ $(this).removeClass(\'choice\').attr(\'idd\',\'\'); } else{$(this).addClass(\'choice\').attr(\'idd\',\'choice\');}"  data-vid=' + strss[i].vid + '"><div>' + strss[i].value + '</div><a href="javascript:" class="select2-search-choice-select" tabindex="-1"  onclick="$(\'.popover-chosen .select2-input\').focus();"></a></li>';
            }
        }
        var htmls = $(html);

        $('.popover-chosen .select2-choices').prepend(htmls);
        //包所有属性值 放入 容器中
        $('.popover-chosen .select2-input').keyup(function (event) {


        })
    }
}


function close_button_box() {
    $('.popover').remove();
}

/**
 * 链接弹出层
 */
var link_save_box = {};

function link_box(dom, typeArr, after_obj) {
    var domHtml;
    dom.hover(function () {
        if (dom.find('.dropdown-menu').size() == 0) {
            if (typeArr.length == 0) {
                var domHtmlString = '<ul class="dropdown-menu" style="display:block; z-index: 99999999;">';
                domHtmlString += '<li><a data-type="page" href="javascript:;">微页面及分类</a></li>';
                domHtmlString += '<li><a data-type="all_goods" href="javascript:;">商品列表</a></li>';
                domHtmlString += '<li><a data-type="goods" href="javascript:;">商品及分组</a></li>';
                domHtmlString += '<li><a data-type="checkin" href="javascript:;">我要签到</a></li>';
                domHtmlString += '<li><a data-type="home" href="javascript:;">店铺主页</a></li>';
                domHtmlString += '<li><a data-type="cart" href="javascript:;">购物车</a></li>';
                domHtmlString += '<li><a data-type="ucenter" href="javascript:;">会员主页</a></li>';
                domHtmlString += '<li><a data-type="udrp" href="javascript:;">我的分销</a></li>';
                domHtmlString += '<li><a data-type="card" href="javascript:;">会员卡</a></li>';
                domHtmlString += '<li><a data-type="coupon_list" href="javascript:;">优惠券</a></li>';
                domHtmlString += '<li><a data-type="tuan_only" href="javascript:;">拼团活动</a></li>';
                domHtmlString += '<li><a data-type="order" href="javascript:;">我的订单</a></li>';
                domHtmlString += '<li><a data-type="link" href="javascript:;">自定义外链</a></li>';
                domHtmlString += "</ul>";
                domHtml = $(domHtmlString);
            } else {
                var domContent = '<ul class="dropdown-menu" style="display:block; z-index: 99999999;">';
                for (var i in typeArr) {
                    domContent += '<li><a data-type="' + typeArr[i] + '" href="javascript:;">';
                    switch (typeArr[i]) {
                        case 'page':
                        case 'pagecat':
                            domContent += '微页面及分类';
                            break;
                        case 'page_only':
                            domContent += '微页面';
                            break;
                        case 'pagecat_only':
                            domContent += '微页面分类';
                            break;
                        case 'good':
                        case 'goodcat':
                            domContent += '商品及分组';
                            break;
                        case 'good_only':
                            domContent += '商品';
                            break;
                        case 'good_only_pic':
                            domContent += '商品及图片';
                            break;
                        case 'goodcat_only':
                            domContent += '商品分组';
                            break;
                        case 'home':
                            domContent += '店铺主页';
                            break;
                        case 'card':
                            domContent += '会员卡';
                            break;
                        case 'cart':
                            domContent += '购物车';
                            break;
                        case 'subject_type':
                            domContent += '专题分类展示';
                            break;
                        case 'ucenter':
                            domContent += '会员主页';
                            break;
                        case 'udrp':
                            domContent += '我的分销';
                            break;
                        case 'link':
                            domContent += '自定义外链';
                            break;
                        case 'checkin':
                            domContent += '我要签到';
                            break;
                        case 'order':
                            domContent += '我的订单';
                            break;
                        case 'user_activity':
                            domContent += '会员活动';
                            break;
                        case 'all_goods':
                            domContent += '商品列表';
                            break;
                    }
                    domContent += '</a></li>';
                }
                domContent += '</ul>';
                domHtml = $(domContent);
            }
            dom.append(domHtml);
        } else {
            domHtml = dom.find('.dropdown-menu');
            domHtml.show();
        }
        var modalDom = {};
        domHtml.find('a').bind('click', function () {
            var type = $(this).data('type');
            if (type == 'home') {
                after_obj('home', '店铺主页', '店铺主页', wap_store_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'cart') {
                after_obj('cart', '购物车', '购物车', wap_cart_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'ucenter') {
                after_obj('home', '会员主页', '会员主页', wap_ucenter_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'udrp') {
                after_obj('home', '我的分销', '我的分销', wap_udrp_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'card') {
                after_obj('card', '会员卡', '会员卡', wap_card_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'checkin') {
                after_obj('home', '我要签到', '我要签到', wap_checkin_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'all_goods') {
                after_obj('all_goods', '商品列表', '商品列表', all_goods_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'order') {
                after_obj('order', '我的订单', '我的订单', wap_order_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'coupon_list') {
                after_obj('order', '优惠券', '优惠券', wap_coupon_url);
                domHtml.trigger('mouseleave');
            } else if (type == 'link') {
                button_box(dom, event, 'bottom', 'url', '链接地址：http://example.com', function () {
                    var url = $('.js-link-placeholder').val();
                    if (url != '') {
                        if (!check_url(url)) {
                            url = 'http://' + url;
                        }
                        after_obj('link', '外链', url, url);
                        close_button_box();
                    } else {
                        return false;
                    }
                });
                domHtml.trigger('mouseleave');
            } else {
                $('.modal-backdrop,.modal').remove();
                $('body').append('<div class="modal-backdrop fade in widget_link_back"></div>');
                var randNum = getRandNumber();
                if (type.substr(-4, 4) == 'only') {
                    var load_url = widget_url + '?a=' + type.replace('_only', '') + '&only=1&number=' + randNum;
                } else {
                    var load_url = widget_url + '?a=' + type + '&number=' + randNum;
                    if (getQueryString('type') != null) {
                        load_url += '&target=' + getQueryString('type');
                    } else if (getEditId()) {
                        load_url += '&widget_id=' + getEditId();
                    }
                }
                load_url += '&link=1';
                link_save_box[randNum] = after_obj;
                modalDom = $('<div class="modal fade hide js-modal in widget_link_box" aria-hidden="false" style="margin-top:0px;display:block;"><iframe src="' + load_url + '" style="width:100%;height:200px;border:0;-webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;"></iframe></div>');
                $('body').append(modalDom);
                modalDom.animate({'top': '10%'}, "slow");
                $('.modal-backdrop').click(function () {
                    login_box_close();
                });
            }
        });
    }, function (e) {
        domHtml.hide().find('a').unbind('click');
    });
}

function login_box_after(number, type, title, url, data) {
    var prefix = '';
    switch (type) {
        case 'page':
            prefix = '微页面';
            break;
        case 'pagecat':
            prefix = '页面分类';
            break;
        case 'goods_category':
            prefix = '商品分组';
            break;
        case 'goods':
            prefix = '商品';
            break;
        case 'tuan':
            prefix = '拼团活动';
            break;
        case 'coupon_details':
            prefix = '优惠券';
            break;
    }

    link_save_box[number](type, prefix, title, url, data);
    login_box_close();
}

/**
 *
 */
function login_box_close() {
    $('.widget_link_box').animate({'margin-top': '-' + ($(window).scrollTop() + $(window).height()) + 'px'}, "slow", function () {
        $('.widget_link_back,.widget_link_box').remove();
    });
}

/**
 * 挂件选择弹出层
 */
var widget_link_save_box = {};

function widget_link_box(dom, type, after_obj, items) {
    var radio = arguments[4] || 0; //是否单选
    //点击事件
    dom.click(function () {
        //移除
        $('.modal-backdrop,.modal').remove();

        //增加
        $('body').append('<div class="modal-backdrop fade in widget_link_back"></div>');

        //赋值
        var randNum = getRandNumber();
        var load_url = widget_url + '?a=' + type + '&type=more&number=' + randNum + '&radio=' + radio;
        if ((type == 'goods' || type == 'goods_cat&only=1' || type == 'coupon' || type == 'coupon&coupon_type=1' || type == 'coupon&coupon_type=2') && items != undefined) {		// 已选取的商品置灰
            if (type == 'game_module') {
                var itemArr = {};
                $.each(items, function (i, v) {
                    if (v != undefined) {
                        if (itemArr[v.atype] == undefined) {
                            itemArr[v.atype] = [];
                        }
                        if (v != undefined) {
                            itemArr[v.atype].push(v.id);
                        }
                    }
                });
                var itemStr = "";
                for (var k in itemArr) {
                    itemStr += k + ":" + itemArr[k].join(",") + ";";
                }
                itemArr = itemStr;
            } else {
                var itemArr = [];
                $.each(items, function (i, v) {
                    if (v != undefined) {
                        itemArr[i] = v.wid;
                    }
                });
            }
            load_url += '&selected_item_str=' + itemArr;
        } else if (type.indexOf('goods&only=1&link=1') == 0) { // 商品选择
            // 已选中
            items = [];
            var itemArr = [];
            if ($('.js-product-list .sort').length > 0) {
                $('.js-product-list .sort').each(function (i) {
                    items.push($(this).data("product_id"));
                });
                $.each(items, function (i) {
                    itemArr[i] = items[i];
                });
                load_url += '&selected_item_str=' + itemArr;
            }
        }

        widget_link_save_box[randNum] = after_obj;
        link_save_box[randNum] = after_obj;

        modalDom = $('<div class="modal fade hide js-modal in widget_link_box" aria-hidden="false" style="margin-top:0px;display:block;"><iframe src="' + load_url + '" style="width:100%;height:200px;border:0;-webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;"></iframe></div>');

        //增加
        $('body').append(modalDom);

        //动画
        modalDom.animate({'top': '10%'}, "slow");

        //点击关闭
        $('.modal-backdrop').click(function () {
            login_box_close();
        });
    });
}

// 微页面模板
function widget_page_template_box(dom, type, after_obj) {
    //点击事件
    dom.click(function () {
        //移除
        $('.modal-backdrop,.modal').remove();

        //增加
        $('body').append('<div class="modal-backdrop fade in widget_link_back"></div>');

        //赋值
        var randNum = getRandNumber();
        var load_url = widget_url + '?a=' + type + '&number=' + randNum;

        widget_link_save_box[randNum] = after_obj;
        link_save_box[randNum] = after_obj;

        modalDom = $('<div class="modal fade hide js-modal in widget_link_box" aria-hidden="false" style="margin-top:0px;display:block; width: 880px; margin-left: -440px;"><iframe src="' + load_url + '" style="width:100%;height:200px;border:0;-webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;"></iframe></div>');

        //增加
        $('body').append(modalDom);

        //动画
        modalDom.animate({'top': '10%'}, "slow");

        //点击关闭
        $('.modal-backdrop').click(function () {
            login_box_close();
        });
    });
}


/**
 * 挂件选择弹出层（vue使用，不再传递dom触发click事件）
 */
function vue_widget_link_box(dom, type, after_obj, items) {
    var radio = arguments[4] || 0; //是否单选
    //点击事件

    //移除
    $('.modal-backdrop,.modal').remove();

    //增加
    $('body').append('<div class="modal-backdrop fade in widget_link_back"></div>');

    //赋值
    var randNum = getRandNumber();
    var load_url = widget_url + '?a=' + type + '&type=more&number=' + randNum + '&radio=' + radio;
    if ((type == 'goods' || type == 'goods_cat&only=1' || type == 'coupon' || type == 'coupon&coupon_type=1' || type == 'coupon&coupon_type=2') && items != undefined) {		// 已选取的商品置灰
        if (type == 'game_module') {
            var itemArr = {};
            $.each(items, function (i, v) {
                if (v != undefined) {
                    if (itemArr[v.atype] == undefined) {
                        itemArr[v.atype] = [];
                    }
                    if (v != undefined) {
                        itemArr[v.atype].push(v.id);
                    }
                }
            });
            var itemStr = "";
            for (var k in itemArr) {
                itemStr += k + ":" + itemArr[k].join(",") + ";";
            }
            itemArr = itemStr;
        } else {
            var itemArr = [];
            $.each(items, function (i, v) {
                if (v != undefined) {
                    itemArr[i] = v.wid;
                }
            });
        }
        load_url += '&selected_item_str=' + itemArr;
    } else if (type.indexOf('goods&only=1&link=1&nopro=1') == 0) {
		// 已选中
        items = [];
        var itemArr = [];
        if ($('.js-product-list .sort').length > 0) {
            $('.js-product-list .sort').each(function (i) {
                items.push($(this).data("product_id"));
            });
            $.each(items, function (i) {
                itemArr[i] = items[i];
            });
            load_url += '&selected_item_str=' + itemArr;
        }
	} else if (type.indexOf('goods&only=1&link=1') == 0) { // 商品选择
        // 已选中
        items = [];
        var itemArr = [];
        if ($('.js-product-list .sort').length > 0) {
            $('.js-product-list .sort').each(function (i) {
                items.push($(this).data("product_id"));
            });
            $.each(items, function (i) {
                itemArr[i] = items[i];
            });
            load_url += '&selected_item_str=' + itemArr;
        }
    }

    widget_link_save_box[randNum] = after_obj;
    link_save_box[randNum] = after_obj;

    modalDom = $('<div class="modal fade hide js-modal in widget_link_box" aria-hidden="false" style="margin-top:0px;display:block;"><iframe src="' + load_url + '" style="width:100%;height:200px;border:0;-webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;"></iframe></div>');

    //增加
    $('body').append(modalDom);

    //动画
    modalDom.animate({'top': '10%'}, "slow");

    //点击关闭
    $('.modal-backdrop').click(function () {
        login_box_close();
    });
}

/**
 *
 * @param number
 * @param data
 */
function widget_box_after(number, data) {
    widget_link_save_box[number](data);
    login_box_close();
}

/**
 *
 * @param url
 * @returns {boolean}
 */
function check_url(url) {
    var reg = new RegExp();
    reg.compile("^(http|https)://.*?$");
    if (!reg.test(url)) {
        return false;
    }
    return true;
}

/**
 * 得到对象的长度
 */
function getObjLength(obj) {
    var number = 0;
    for (var i in obj) {
        number++;
    }
    return number;
}

/**
 * 得到文件的大小
 */
function getSize(size) {
    var kb = 1024;
    var mb = 1024 * kb;
    var gb = 1024 * mb;
    var tb = 1024 * gb;
    if (size < mb) {
        return (size / kb).toFixed(2) + " KB";
    } else if (size < gb) {
        return (size / mb).toFixed(2) + " MB";
    } else if (size < tb) {
        return (size / gb).toFixed(2) + " GB";
    } else {
        return (size / tb).toFixed(2) + " TB";
    }
}

/**
 * 生成一个唯一数
 */
function getRandNumber() {
    var myDate = new Date();
    return myDate.getTime() + '' + Math.floor(Math.random() * 10000);
}

var obj2String = function (_obj) {
    var t = typeof(_obj);
    if (t != 'object' || _obj === null) {
        // simple data type
        if (t == 'string') {
            _obj = '"' + _obj + '"';
        }
        return String(_obj);
    } else {
        if (_obj instanceof Date) {
            return _obj.toLocaleString();
        }
        // recurse array or object
        var n, v, json = [],
            arr = (_obj && _obj.constructor == Array);
        for (n in _obj) {
            v = _obj[n];
            t = typeof(v);
            if (t == 'string') {
                v = '"' + v + '"';
            } else if (t == "object" && v !== null) {
                v = this.obj2String(v);
            }
            json.push((arr ? '' : '"' + n + '":') + String(v));
        }
        return (arr ? '[' : '{') + String(json) + (arr ? ']' : '}');
    }
};

//解决js toFixed四舍五入问题
Number.prototype.toFixed = function (d) {
    var s = this + "";
    if (!d) d = 0;
    if (s.indexOf(".") == -1) s += ".";
    s += new Array(d + 1).join("0");
    if (new RegExp("^(-|\\+)?(\\d+(\\.\\d{0," + (d + 1) + "})?)\\d*$").test(s)) {
        var s = "0" + RegExp.$2, pm = RegExp.$1, a = RegExp.$3.length, b = true;
        if (a == d + 2) {
            a = s.match(/\d/g);
            if (parseInt(a[a.length - 1]) > 4) {
                for (var i = a.length - 2; i >= 0; i--) {
                    a[i] = parseInt(a[i]) + 1;
                    if (a[i] == 10) {
                        a[i] = 0;
                        b = i != 1;
                    } else break;
                }
            }
            s = a.join("").replace(new RegExp("(\\d+)(\\d{" + d + "})\\d$"), "$1.$2");

        }
        if (b) s = s.substr(1);
        return (pm + s).replace(/\.$/, "");
    }
    return this + "";
};

function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

function getEditId() {
    var str = window.location.href;
    var i = str.indexOf('#edit/');
    var num = str.substring(i + 6);
    var r = /^[0-9]+.?[0-9]*$/;
    if (r.test(num)) {
        return num;
    } else {
        return null;
    }
}


// ==================================
$(function () {
    $('.app-help-icon-close').click(function () {
        $('#app-help-container').removeClass('show-help');
    });

    $('#app-help-button').click(function () {
        $('#app-help-container').addClass('show-help');
    });
});

function loadProgress() {
    var dom = $('#nprogress');
    if (dom.html()) {
        dom.html('');
    } else {
        dom.html('<div class="bar" role="bar"><div class="peg"></div></div><div class="spinner" role="spinner"><div class="spinner-icon"></div></div>');
    }
}


// 通用select下拉列表
function select_box(dom, event, data, obj) {
    var cancel_obj = arguments[7];
    event.stopPropagation();
    var dom_offset = dom.offset();
	$('.select-large__popup').remove();

    var html = $('<div class="zent-portal zent-select select-large__popup zent-popover zent-popover-internal-id-5 zent-popover-position-bottom-left" style="position: absolute;">' +
        '    <div data-reactroot="" class="zent-popover-content">\n' +
        '        <div class="zent-select-popup" tabindex="0">\n' +
        '            <div class="zent-select-search">\n' +
        '                <input type="text" placeholder="" class="zent-select-filter" value="">' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>');

    var select_html = '';
    for (var i in data) {
        select_html += '<span value="' + data[i].wid + '" class="zent-select-option">' + data[i].title + '</span>';
    }

    html.find('.zent-select-popup').append(select_html);

    // 点击每一个下拉
    html.find('span').click(function (event) {
        event.stopPropagation();
        var value = $(this).attr('value');
        var title = $(this).html();
        var has_value = false;

        dom.find('.zent-select-tag').each(function () {
            if ($(this).attr('value') == value) {
                has_value = true;
                return false;
            }
        });

        if (!has_value) {
            if (dom.find('span').size() == 0) {
                dom.html('');
            }

            var select_tag_html = '<span><span class="zent-select-tag" value="' + value + '">' + title + '<i class="zent-select-delete"></i></span></span>';
            dom.append(select_tag_html);
        }

        button_box_after();
        close_select_box();
    });

    html.find('.zent-select-filter').keyup(function () {
        var val = $(this).val();

        var select_html = '';
        for (var i in data) {
            if (data[i].title.indexOf(val) != -1) {
                select_html += '<span value="' + data[i].wid + '" class="zent-select-option">' + data[i].title + '</span>';
            }
        }

        html.find('.zent-select-popup').find('span').remove();
        html.find('.zent-select-popup').append(select_html);
    });

    $('body').append(html);
    $('.zent-select-popup').css({opacity: 1, height: 'auto'});

    $(document).click(function () {
        if ($('.zent-select-filter').is(':focus')) {
            return;
        }
        close_select_box();
    });

    button_box_after();

    function button_box_after() {
        $('.popover .js-btn-cancel').one('click', function () {
            if (cancel_obj != undefined) {
                cancel_obj();
            } else {
                close_button_box();
            }
        });
        $('.popover .js-btn-confirm').bind('click', function () {
            if (ok_obj) {
                ok_obj();
            } else {
                close_button_box();
            }
        });
        $('.popover').click(function (e) {
            e.stopPropagation();
        });
        if (cancel_obj == undefined) {
            $('body').bind('click', function () {
                close_button_box();
            });
        }

        var popover_height = $('.select-large__popup').height();
        var popover_width = $('.select-large__popup').width();

        $('.select-large__popup').css({top: dom_offset.top + dom.height() + 10, left: dom_offset.left});
    }
}

function close_select_box() {
    $('.select-large__popup').remove();
}


// 通用vue select下拉列表
function vue_select_box(dom, event, data, after_obj) {
    event.stopPropagation();
    var dom_offset = dom.offset();
    $('.select-large__popup').remove();

    var html = $('<div class="zent-portal zent-select select-large__popup zent-popover zent-popover-internal-id-5 zent-popover-position-bottom-left" style="position: absolute;">' +
        '    <div data-reactroot="" class="zent-popover-content">\n' +
        '        <div class="zent-select-popup" tabindex="0">\n' +
        '            <div class="zent-select-search">\n' +
        '                <input type="text" placeholder="" class="zent-select-filter" value="">' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>');

    var select_html = '';
    for (var i in data) {
        select_html += '<span value="' + data[i].wid + '" class="zent-select-option">' + data[i].title + '</span>';
    }

    html.find('.zent-select-popup').append(select_html);

    // 点击每一个下拉
    html.find('.zent-select-option').click(function (event) {
        event.stopPropagation();
        var value = $(this).attr('value');
        var title = $(this).html();
        var has_value = false;

        dom.find('.zent-select-tag').each(function () {
            if ($(this).attr('value') == value) {
                has_value = true;
                return false;
            }
        });

        if (!has_value) {
            if (dom.find('span').size() == 0) {
                dom.html('');
            }

            var returnData = {wid:value, title: title};
            after_obj(value, returnData, 'add');
        }

        close_select_box();
    });

    html.find('.zent-select-filter').keyup(function () {
        var val = $(this).val();

        var select_html = '';
        for (var i in data) {
            if (data[i].title.indexOf(val) != -1) {
                select_html += '<span value="' + data[i].wid + '" class="zent-select-option">' + data[i].title + '</span>';
            }
        }

        html.find('.zent-select-popup').find('span').remove();
        html.find('.zent-select-popup').append(select_html);
    });

    $('body').append(html);
    $('.zent-select-popup').css({opacity: 1, height: 'auto'});

    $(document).click(function () {
        if ($('.zent-select-filter').is(':focus')) {
            return;
        }
        close_select_box();
    });

    $('.select-large__popup').css({top: dom_offset.top + dom.height() + 10, left: dom_offset.left});
}

// 提示tips
var note_tips = function (dom, msg, position) {
    var _func = function () {
        $('body > .zent-portal').remove();
    }
    dom.on('mouseover mouseout', function (e) {
        if (e.type == 'mouseover') {
            var class_name = 'zent-popover-position-bottom-left';
            var top = dom.offset().top;
            var left = dom.offset().left;
            switch (position) {
                case 'top':
                    class_name = 'zent-popover-position-top-left';
                    break;
                case 'right':
                    class_name = 'zent-popover-position-top-left';
                    break;
                case 'bottom':
                    class_name = 'zent-popover-position-bottom-left';
                    top = dom.offset().top + dom.height() + 10;
                    left = dom.offset().left - (dom.width() / 2) - 3;
                    break;
                case 'left':
                    class_name = 'zent-popover-position-right-top';
                    break;
            }
            var id = id || new Date().getTime();
            var html = '<div class="zent-portal zent-pop zent-popover zent-popover-internal-id-tips ' + class_name + ' active" style="position: absolute;"><div data-reactroot="" class="zent-popover-content"><div class="zent-pop-inner">' + msg + '</div><i class="zent-pop-arrow"></i></div></div>';
            $('body').append(html);

            $('body > .zent-portal').css({'top': top, 'left': left});
        } else if (e.type == 'mouseout') {
            _func();
        }
    });
}


// 添加商品规格
var SKU = function (ext) {
    this.defaultOption = {
        baseUrl: '/index.php/goods/index/',
        skuMax: 3, // 规格支持最大数量
        defaultHtml: '<h3 class="group-title"><button type="button" class="zent-btn">添加规格项目</button></h3>',
        skuHtml: '<h3 class="group-title"><span class="group-title__label">规格名：</span><div class="zent-popover-wrapper zent-select" style="display: inline-block;"><input type="text" class="zent-select-input" placeholder="请选择" value="" title="" maxlength="4"></div>{{skuImg}}<span class="group-remove">×</span></h3><div class="group-container"><span class="sku-list__label">规格值：</span><div class="sku-list"></div></div><div class="sku-group-cont"></div>',
        skuValHtml: '<div class="rc-sku-item"><div class="zent-popover-wrapper zent-select" style="display: inline-block;"><input type="text" class="zent-select-input" placeholder="请选择" value="" title="" maxlength="20"></div><span class="item-remove small">×</span></div>',
        skuImgHtml: '<span class="zent-checkbox-wrap"><span class="zent-checkbox"><span class="zent-checkbox-inner"></span><input type="checkbox" value="on"></span><span>添加规格图片</span></span>',
        imgTips: '<p class="help-block">仅支持为第一组规格设置规格图片（最多40张图），买家选择不同规格会看到对应规格图片，建议尺寸：800 x 800像素</p>',
        sysTpl: '<div class="zent-portal zent-select auto-width zent-popover zent-popover-internal-id-14 zent-popover-position-bottom-left" style="position: absolute;"><div data-reactroot="" class="zent-popover-content"><div class="zent-select-popup" tabindex="0">{{data}}</div></div></div>',
        stockHtml: '<label class="zent-form__control-label">规格明细：</label><div class="zent-form__controls"><div class="table-sku-wrap"><table class="table-sku-stock"><thead><tr>{{name}}<th class="th-price"><em class="zent-form__required">*</em>价格（元）</th><th class="th-stock"><em class="zent-form__required">*</em>库存</th><th class="th-code">规格编码<div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;"><span class="help-circle" data-help="为方便管理，可以自定义规格编码，比如货号"><i class="zenticon zenticon-help-circle"></i></span></div></th><th class="text-cost-price">成本价<div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;"><span class="help-circle" data-help="成本价未来会用于营销建议，利润分析等"><i class="zenticon zenticon-help-circle"></i></span></div></th><th class="th-weight {{hide}}">重量（Kg）<div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;"><span class="help-circle" data-help="运费模版按物流重量（含包装）计费，需要输入重量"><i class="zenticon zenticon-help-circle"></i></span></div></th>{{drp}}</tr></thead><tbody>{{data}}</tbody><tfoot><tr><td class="batch-opts" colspan="6"><span>批量设置：</span><span>{{batch}}</span></td></tr></tfoot></table></div></div>',
        uploadImgHtml: '<div class="upload-img-wrap"><div class="arrow"></div><div class="rc-upload"><div class="rc-upload-trigger"><i>+</i></div><p class="rc-upload-tips"></p></div></div>',
        batchOptsHtml: '<a href="javascript:;" style="margin-right: 10px;">价格</a><a href="javascript:;" style="margin-right: 10px;">库存</a><a href="javascript:;" style="margin-right: 10px;">成本</a><a href="javascript:;" style="margin-right: 10px;" class="a-weight {{hide}}">重量</a>{{drpBatch}}',
    }
    this.option = $.extend({}, this.defaultOption, ext || {});
    this.activeSku = ''; // 当前操作规格
    this.activeSkuVal = '' // 当前操作规格值
    this.skuData = [];
}
SKU.prototype = {
    init: function (skuData) { // 初始化数据
        this.skuData = skuData || [];

        // 绑定数据
        this.render(goods_property_list);

        // 绑定事件
        this.bind(this);
    },
    search: function (obj, type) { // 查找规格名称|值
        if (type == 'sku') {
            //var self = this;
            var name = $.trim($(obj).val()) || '';
            $.post(this.option.baseUrl + 'property_name', {'name': name}, function (data) {
                var tpl = '';
                if (data.length == 0) {
                    $('body > .zent-select').find('.zent-select-popup').html('<span class="zent-select-empty">没有找到匹配项</span>');
                } else {
                    for (var i in data) {
                        if (i == 0) {
                            tpl += '<span value="' + data[i].wid + '" class="zent-select-option current">' + data[i].name + '</span>';
                        } else {
                            tpl += '<span value="' + data[i].wid + '" class="zent-select-option">' + data[i].name + '</span>';
                        }
                    }
                    $('body > .zent-select').find('.zent-select-popup').html(tpl);
                }
            });

        } else if (type == 'skuVal') {
            var pid = $(obj).closest('.rc-sku-group').find('.group-title .zent-select-input').data('pid');
            var value = $.trim($(obj).val()) || '';
            $.post(this.option.baseUrl + 'property_value', {'pid': pid, 'value': value}, function (data) {
                var tpl = '';
                if (data.length == 0) {
                    $('body > .zent-select').find('.zent-select-popup').html('<span class="zent-select-empty">没有找到匹配项</span>');
                } else {
                    for (var i in data) {
                        if (i == 0) {
                            tpl += '<span value="' + data[i].wid + '" class="zent-select-option current">' + data[i].value + '</span>';
                        } else {
                            tpl += '<span value="' + data[i].wid + '" class="zent-select-option">' + data[i].value + '</span>';
                        }
                    }
                    $('body > .zent-select').find('.zent-select-popup').html(tpl);
                }
            });
        }
    },
    add: function (obj, type) { // 添加规格/值
        if (type == 'sku') {
            var skuHtml = this.option.skuHtml;
            var _obj = $(obj).closest('.rc-sku-group');
            if ($('.rc-sku-group').length == 1) {
                skuHtml = this.option.skuHtml.replace(/{{skuImg}}/g, this.option.skuImgHtml);
            } else {
                skuHtml = this.option.skuHtml.replace(/{{skuImg}}/g, '');
            }
            $(obj).closest('.rc-sku-group').html(skuHtml);
            if ($('.rc-sku-group').length < this.option.skuMax) {
                $('.rc-sku > div').append('<div class="rc-sku-group">' + this.option.defaultHtml + '</div>');
            } else {
                $('.rc-sku > div').append('<div class="rc-sku-group"><h3 class="group-title"><div class="zent-popover-wrapper zent-pop-wrapper" style="display: inline-block;"><button type="button" class="zent-btn-disabled zent-btn">添加规格项目</button></div></h3></div>');
            }

            // 默认显示规格下拉列表
            this.list(_obj.find(".zent-select-input").focus());

        } else if (type == 'skuVal') {
            $('body > .zent-select').remove();
            $(obj).before(this.option.skuValHtml);
            $(obj).prev('.rc-sku-item').find(".zent-select-input").focus();
        } else if (type == 'AjaxSkuVal') { // 添加规格值，如果已存在返回规格值vid
            var exists = false;
            var self = this;
            var pid = $(obj).closest('.rc-sku-group').find('.group-title .zent-select-input').data('pid');
            var value = $.trim($(obj).val()) || '';
            var old_value = $.trim($(obj).data('value')) || '';
            $(obj).closest('.rc-sku-item').siblings('.rc-sku-item').each(function () {
                if (value != '' && $.trim($(this).find('.zent-select-input').val()) == value) {
                    $(obj).val('');
                    $(obj).data('vid', '');
                    golbal_tips('您已经添加了相同的规格值', 1);
                    exists = true;
                    return false;
                }
            });

            // 添加了相同的规格值
            if (exists) {
                return false;
            }

            if (value != '' && value != old_value) {
                $.post(self.option.baseUrl + 'add_property_value', {'pid': pid, 'value': value}, function (data) {
                    $(obj).data('vid', data.vid);
                    self.change($(obj));
                });
            } else if (value == '' && old_value != '') {
                $(obj).val(old_value);
            }
            if (value != '') {
                $(obj).data('value', value);
            }
        }
        return;
    },
    del: function (obj, type) { // 删除规格/值
        if (type == 'sku') {
            $(obj).closest('.rc-sku-group').remove();
            $('.zent-btn-disabled').closest('.rc-sku-group').remove();
            if ($('.rc-sku-group .zent-btn').length == 0) {
                $('.rc-sku > div').append('<div class="rc-sku-group">' + this.option.defaultHtml + '</div>');
            }


            // 删除规格图片支持,重新添加规格图片支持
            if ($('.rc-sku-group').find('.zent-checkbox-wrap').length == 0) {
                $('.rc-sku-group .group-title').eq(0).find('.zent-select').after(this.option.skuImgHtml);
            }

            // 生成html
            this.html();
        } else if (type == 'skuVal') {
            $(obj).closest('.rc-sku-item').remove();
            // 生成html
            this.html();

        } else if (type == 'img') { // 规格图片
            var img_wrap = $(obj).closest('.upload-img-wrap');
            img_wrap.find('.upload-img').remove();
            if (img_wrap.children('.rc-upload').length == 0) {
                img_wrap.append('<div class="rc-upload"><div class="rc-upload-trigger"><i>+</i></div><p class="rc-upload-tips"></p></div>');
            }
        }
        return;
    },
    addImg: function (obj) { // 添加规格图片
        if (!$(obj).hasClass('zent-checkbox-checked')) {
            $(obj).addClass('zent-checkbox-checked');
            $(obj).closest('.rc-sku-group').find('.sku-group-cont').html(this.option.imgTips);

            $(obj).closest('.rc-sku-group').find('.rc-sku-item').addClass('active');
            $(obj).closest('.rc-sku-group').find('.rc-sku-item').append(this.defaultOption.uploadImgHtml);
        } else {
            $(obj).removeClass('zent-checkbox-checked');
            $(obj).closest('.rc-sku-group').find('.sku-group-cont').html('');

            $(obj).closest('.rc-sku-group').find('.rc-sku-item').removeClass('active');
            $(obj).closest('.rc-sku-group').find('.rc-sku-item .upload-img-wrap').remove();
        }
        return;
    },
    upload: function (obj) { // 上传图片
        wj.pickerImg(function (pic_list) { // 图片上传插件
            var image = pic_list[0].url.toString() || '';
            if (image != '') {
                var img_wrap = $(obj).closest('.upload-img-wrap');
                if (img_wrap.find('img').length == 0) {
                    img_wrap.find('.rc-upload').remove();
                    img_wrap.append('<div class="upload-img"><span class="item-remove small" title="删除">×</span><img src="' + image + '" role="presentation" alt="" data-src="' + image + '"><div class="rc-upload"><div class="img-edit"><span>替换</span></div><p class="rc-upload-tips"></p></div></div>');
                } else {
                    img_wrap.find('img').attr({'src': image, 'data-src': image});
                }
            }
        }, {maxnum: 1})
    },
    list: function (obj, type) { // 规格下拉列表
        var self = this;
        if (type == 'sku') {
            this.activeSku = obj.closest('.rc-sku-group');

            // 解决规格重复选择
            var selected = [];
            $('.rc-sku-group .zent-select-input').each(function () {
                var pid = parseInt($(this).data('pid') || 0);
                if (pid > 0 && parseInt(self.activeSku.find('.zent-select-input').data('pid') || 0) != pid) {
                    selected.push(pid)
                }
            });
            // 绑定数据
            this.render(goods_property_list, '', selected);

            var top = obj.offset().top;
            var left = obj.offset().left;
            $('body > .zent-select').remove();
            $('body').append(this.option.sysTpl);
            $('body > .zent-select').css({'top': (top + obj.height() + 12), 'left': left});
            $('body > .zent-select').find('.zent-select-option').eq(0).addClass('current');

        } else if (type == 'skuVal') {
            this.activeSkuVal = obj.closest('.rc-sku-item');

            var self = this;
            var pid = obj.closest('.rc-sku-group').find('.group-title .zent-select-input').data('pid');
            var top = obj.offset().top;
            var left = obj.offset().left;
            $.post(self.option.baseUrl + 'property_value', {'pid': pid}, function (data) {
                if (data != undefined && data != null && data.length > 0) {
                    // 解析模板
                    var tpl = self.render(data, self.defaultOption.sysTpl);
                    $('body').append(tpl);
                } else {
                    // 未找到规格值的模板
                    $('body').append(self.defaultOption.sysTpl.replace(/{{data}}/g, '<span class="zent-select-empty">没有找到匹配项</span>'));
                }

                // 上传规格图片
                if (obj.closest('.rc-sku-group').find('.zent-checkbox-checked').length > 0 && obj.closest('.rc-sku-item').find('.upload-img-wrap').length == 0) {
                    obj.closest('.rc-sku-item').addClass('active');
                    obj.closest('.rc-sku-item').append(self.defaultOption.uploadImgHtml);
                    if ($('.upload-img-wrap').length > 1) {
                        top -= 48;
                    }
                }

                $('body > .zent-select').css({'top': (top + obj.height() + 12), 'left': left});
                $('body > .zent-select').find('.zent-select-option').eq(0).addClass('current');
            });
        }
    },
    selected: function (obj) { // 规格下拉列表选择
        if (this.activeSku != '') {
            $(obj).addClass('active').siblings('.zent-select-option').removeClass('active');
            this.activeSku.find('.zent-select-input').data('pid', $(obj).attr('value')).val($(obj).text());
            this.activeSku.find('.sku-list').html(this.option.skuValHtml + '<span class="sku-add">添加规格值</span>');

            // 规格值下拉列表
            this.activeSku.find('.rc-sku-item .zent-select-input').focus();
            this.activeSku.find('.zent-select-input').blur();
            this.activeSku = '';
        } else if (this.activeSkuVal != '') {
            var exists = false;
            var vid = $(obj).attr('value');
            var value = $(obj).text();
            this.activeSkuVal.siblings('.rc-sku-item').each(function () {
                if (value != '' && $.trim($(this).find('.zent-select-input').val()) == $(obj).text()) {
                    exists = true;
                    golbal_tips('您已经添加了相同的规格值', 1);
                    return false;
                }
            });
            if (!exists) {
                this.activeSkuVal.find('.zent-select-input').data('vid', vid).val(value);
                this.activeSkuVal.find('.zent-select-input').data('value', value);
                this.change(this.activeSkuVal);
            }
            this.activeSkuVal = '';
        }
    },
    hover: function (obj) { // 规格下拉列表hover
        $(obj).addClass('current').siblings('.zent-select-option').removeClass('current');
    },
    change: function (obj) {
        // 生成html
        this.html();
    },
    batch: function (obj, type) { // 批发设置 价格|库存|成本价|重量|一级分润|二级分销|直销分润
        var type = $('.batch-opts a').index($(obj));
        var item = 0;
        if ($(obj).prevAll('.input-mini').length > 0) {
            item = parseInt($(obj).data('item') || 0);
            if (type == 0) {
                type = 4;
            } else if (type == 1) {
                type = 5;
            }
        }
        // 分润设置
        if (parseInt($(obj).data('level')) > 0) {
            type = parseInt($(obj).data('level')) + 10;
        }
        var html = '<span class="input-mini"><div class="zent-input-wrapper"><input type="text" class="zent-input" /></div></span><a href="javascript:;" style="margin-right: 10px;">保存</a><a href="javascript:;">取消</a>';
        switch (type) {
            case 0: // 价格
                $(obj).closest('.batch-opts').find('span:last').html(html);
                $('.batch-opts').find('a:first').data('item', type);
                break;
            case 1: // 库存
                $(obj).closest('.batch-opts').find('span:last').html(html);
                $('.batch-opts').find('a:first').data('item', type);
                break;
            case 2: // 成本价
                $(obj).closest('.batch-opts').find('span:last').html(html);
                $('.batch-opts').find('a:first').data('item', type);
                break;
            case 3: // 重量
                $(obj).closest('.batch-opts').find('span:last').html(html);
                $('.batch-opts').find('a:first').data('item', type);
                break;
            case 4: // 保存
                var value = $.trim($('.batch-opts .zent-input').val()) || '';
                if (item == 0) {
                    if (value == '') {
                        return false;
                    } else if (isNaN(value)) {
                        global_tips('请输入一个正确的价格', 1);
                        return false;
                    }

                    $("input[name='sku_price']").val(parseFloat(value).toFixed(2));
                    $("input[name='price']").val(parseFloat(value).toFixed(2)); // 同步商品价格
                } else if (item == 1) {
                    var reg = /^\+?[1-9][0-9]*$/;
                    if (value == '') {
                        return false;
                    } else if (!reg.test(value)) {
                        global_tips('请输入一个正确的库存', 1);
                        return false;
                    }

                    $("input[name='sku_stock']").val(parseInt(value));
                    var stock = 0;
                    $("input[name='sku_stock']").each(function () {
                        stock += parseInt($(this).val());
                    });
                    $("input[name='total_stock']").val(stock);
                } else if (item == 2) {
                    if (value == '') {
                        return false;
                    } else if (isNaN(value)) {
                        global_tips('请输入一个正确的成本价', 1);
                        return false;
                    }

                    $("input[name='sku_cost_price']").val(parseFloat(value).toFixed(2));
                    $("input[name='cost_price']").val(parseFloat(value).toFixed(2));
                } else if (item == 3) {
                    if (value == '') {
                        return false;
                    } else if (isNaN(value)) {
                        global_tips('请输入一个正确的重量', 1);
                        return false;
                    } else if (parseFloat(value) < 0.001) {
                        global_tips('重量最小为0.001kg', 1);
                        return false;
                    }

                    $("input[name='sku_weight']").val(parseFloat(value).toFixed(3));
                    $("input[name='weight']").val(parseFloat(value).toFixed(3));
                } else if (item == 11 || item == 12 || item == 13) {
                    if (value == '') {
                        return false;
                    } else if (isNaN(value)) {
                        global_tips('请输入一个正确的分润', 1);
                        return false;
                    }
                    // 分销级别
                    var drp_level = item - 10;
                    $("input[name='sku_profit_" + drp_level + "']").val(parseInt(value).toFixed(2));
                    $("input[name='drp_" + drp_level + "_profit']").val(parseInt(value).toFixed(2));
                }

                var html = this.defaultOption.batchOptsHtml;
                var is_drp = parseInt($(".is-drp.zent-checkbox-checked").find("input[name='is_drp']").val()) || 0; // 是否支持分销
                var drp_setting = parseInt($('.drp-setting .zent-radio-checked').find("input[name='drp_setting']").val()) || 0;
                var drpBatchHtml = '';
                if (is_drp == 1 && drp_setting == 1) {
                    var level_text = ['一级', '二级', '直销']
                    for (var i = 0; i < drp_max_level; i++) {
                        if (drp_max_level == 1) {
                            level_text[0] = '直销';
                        } else if (drp_max_level == 2) {
                            level_text[1] = '直销';
                        }
                        drpBatchHtml += '<a href="javascript:;" style="margin-right: 10px;" class="a-proft" data-level="' + (i + 1) + '">' + level_text[i] + '分润</a>';
                    }
                    html = html.replace(/{{drpBatch}}/g, drpBatchHtml);
                } else {
                    html = html.replace(/{{drpBatch}}/g, drpBatchHtml);
                }
                $('.batch-opts > span:last').html(html);
                break;
            case 5: // 取消
                var html = this.defaultOption.batchOptsHtml;
                var is_drp = parseInt($(".is-drp.zent-checkbox-checked").find("input[name='is_drp']").val()) || 0; // 是否支持分销
                var drp_setting = parseInt($('.drp-setting .zent-radio-checked').find("input[name='drp_setting']").val()) || 0;
                var drpBatchHtml = '';
                if (is_drp == 1 && drp_setting == 1) {
                    var level_text = ['一级', '二级', '直销']
                    for (var i = 0; i < drp_max_level; i++) {
                        if (drp_max_level == 1) {
                            level_text[0] = '直销';
                        } else if (drp_max_level == 2) {
                            level_text[1] = '直销';
                        }
                        drpBatchHtml += '<a href="javascript:;" style="margin-right: 10px;" class="a-proft" data-level="' + (i + 1) + '">' + level_text[i] + '分润</a>';
                    }
                    html = html.replace(/{{drpBatch}}/g, drpBatchHtml);
                } else {
                    html = html.replace(/{{drpBatch}}/g, drpBatchHtml);
                }
                $('.batch-opts > span:last').html(html);
                break;
            case 11: // 一级分润设置
                $(obj).closest('.batch-opts').find('span:last').html(html);
                $('.batch-opts').find('a:first').data('item', type);
                break;
            case 12: // 二级分润设置
                $(obj).closest('.batch-opts').find('span:last').html(html);
                $('.batch-opts').find('a:first').data('item', type);
                break;
            case 13: // 三级分润设置
                $(obj).closest('.batch-opts').find('span:last').html(html);
                $('.batch-opts').find('a:first').data('item', type);
                break;
        }
    },
    html: function () { // 生成html
        var self = this;
        $('.stock-field').removeClass('hide');
        var type = $(".goods-type.zent-radio-checked").find("input[name='type']").val(); // 商品类型 0 普通 1 服务（不填重量）
        var is_drp = parseInt($(".is-drp.zent-checkbox-checked").find("input[name='is_drp']").val()) || 0; // 是否支持分销
        var drp_setting = parseInt($('.drp-setting .zent-radio-checked').find("input[name='drp_setting']").val()) || 0;
        var hide = '';
        if (type == 1) {
            hide = 'hide';
        }
        var th = '';
        var sku_dom_num = 0; // 有效的规格数量
        $('.rc-sku-group').each(function (i) {
            var name = $(this).find('.group-title .zent-select-input').val() || '';
            if (name == '') {
                return false;
            }

            if ($(this).find('.rc-sku-item').length > 0) { // 有规格值
                sku_dom_num++;
                th += '<th>' + name + '</th>';
            }
        });

        var tr_html = '';
        switch (sku_dom_num) { // 一个规格
            case 1:
                $('.sku-list .rc-sku-item').each(function () {
                    var pid = parseInt($(this).closest('.rc-sku-group').find('.group-title').find('.zent-select-input').data('pid') || 0);
                    var vid = parseInt($(this).find('.zent-select-input').data('vid') || 0);
                    if (vid == 0) {
                        return true;
                    }
                    var label = $.trim($(this).find('.zent-select-input').val());
                    var sku = self.skuData[pid + ':' + vid] || [];

                    tr_html += '<tr data-sku-vids="' + vid + '">';
                    tr_html += '	<td rowspan="1">' + label + '</td>';
                    tr_html += '	<td>';
                    tr_html += '		<div class="widget-form__group-row">';
                    tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                    tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_price" value="' + (sku['price'] || '') + '" autocomplete="off" /></div>';
                    tr_html += '			</div>';
                    tr_html += '		</div>';
                    tr_html += '	</td>';
                    tr_html += '	<td>';
                    tr_html += '		<div class="widget-form__group-row">';
                    tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                    tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_stock" value="' + (sku['sku'] || '') + '" autocomplete="off" maxlength="8" /></div>';
                    tr_html += '			</div>';
                    tr_html += '		</div>';
                    tr_html += '	</td>';
                    tr_html += '	<td>';
                    tr_html += '		<div class="widget-form__group-row">';
                    tr_html += '			<div class="zent-input-wrapper input-mini2"><input type="text" class="zent-input" name="sku_sn" value="' + (sku['sn'] || '') + '" autocomplete="off" placeholder="输入数字商品编码" maxlength="32" /></div>';
                    tr_html += '		</div>';
                    tr_html += '	</td>';
                    tr_html += '	<td>';
                    tr_html += '		<div class="widget-form__group-row">';
                    tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                    tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_cost_price" value="' + (sku['cost_price'] || '') + '" autocomplete="off" /></div>';
                    tr_html += '			</div>';
                    tr_html += '		</div>';
                    tr_html += '	</td>';
                    tr_html += '	<td class="td-weight ' + hide + '">';
                    tr_html += '		<div class="zent-number-input-wrapper input-mini">';
                    tr_html += '			<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_weight" value="' + (sku['weight'] || '') + '" autocomplete="off" /></div>';
                    tr_html += '		</div>';
                    tr_html += '	</td>';
                    if (is_drp == 1 && drp_max_level >= 1) {
                        tr_html += '<td class="td-profit">';
                        tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                        if (drp_setting == 1) {
                            tr_html += '	<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_1" autocomplete="off" value="' + (sku['drp_1_profit'] || '') + '" /></div>';
                        } else {
                            tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_1_profit_ratio + '</div>';
                        }
                        tr_html += '	</div>';
                        tr_html += '</td>';
                    }
                    if (is_drp == 1 && drp_max_level >= 2) {
                        tr_html += '<td class="td-profit">';
                        tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                        if (drp_setting == 1) {
                            tr_html += '	<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_2" autocomplete="off" value="' + (sku['drp_2_profit'] || '') + '" /></div>';
                        } else {
                            tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_2_profit_ratio + '</div>';
                        }
                        tr_html += '	</div>';
                        tr_html += '</td>';
                    }
                    if (is_drp == 1 && drp_max_level == 3) {
                        tr_html += '<td class="td-profit">';
                        tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                        if (drp_setting == 1) {
                            tr_html += '		<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_3" autocomplete="off" value="' + (sku['drp_3_profit'] || '') + '" /></div>';
                        } else {
                            tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_3_profit_ratio + '</div>';
                        }
                        tr_html += '	</div>';
                        tr_html += '</td>';
                    }
                    tr_html += '</tr>';
                });
                break;
            case 2: // 两个规格
                var sku_dom1 = 0;
                var sku_dom2 = 1;
                var sku_dom1_flag = false;

                $.each($('.rc-sku-group'), function (i, item) {
                    if ($(item).find('.rc-sku-item').size() > 0 && sku_dom1_flag == false) {
                        sku_dom1 = i;
                        sku_dom1_flag = true;
                    } else if ($(item).find('.rc-sku-item').size() > 0) {
                        sku_dom2 = i;
                    }
                });

                $('.rc-sku-group').eq(0).find('.rc-sku-item').each(function (i, item) { // 规格一
                    $('.rc-sku-group').eq(1).find('.rc-sku-item').each(function (j, item2) { // 规格二
                        var pid1 = parseInt($(item).closest('.rc-sku-group').find('.group-title').find('.zent-select-input').data('pid') || 0);
                        var vid1 = parseInt($(item).find('.zent-select-input').data('vid') || 0);
                        if (vid1 == 0) {
                            return true;
                        }
                        var pid2 = parseInt($(item2).closest('.rc-sku-group').find('.group-title').find('.zent-select-input').data('pid') || 0);
                        var vid2 = parseInt($(item2).find('.zent-select-input').data('vid') || 0);
                        if (vid2 == 0) {
                            return true;
                        }
                        var sku = self.skuData[pid1 + ':' + vid1 + ';' + pid2 + ':' + vid2] || [];

                        tr_html += '<tr data-sku-vids="' + vid1 + '-' + vid2 + '">';
                        if (j == 0) {
                            tr_html += '<td rowspan="' + $('.rc-sku-group').eq(sku_dom2).find('.rc-sku-item').size() + '" class="text-center">' + $(item).find('.zent-select-input').val() + '</td>';
                        }
                        tr_html += '<td class="text-center">' + $(item2).find('.zent-select-input').val() + '</td>';
                        tr_html += '	<td>';
                        tr_html += '		<div class="widget-form__group-row">';
                        tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                        tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_price" value="' + (sku['price'] || '') + '" autocomplete="off" /></div>';
                        tr_html += '			</div>';
                        tr_html += '		</div>';
                        tr_html += '	</td>';
                        tr_html += '	<td>';
                        tr_html += '		<div class="widget-form__group-row">';
                        tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                        tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_stock" value="' + (sku['sku'] || '') + '" autocomplete="off" maxlength="8" /></div>';
                        tr_html += '			</div>';
                        tr_html += '		</div>';
                        tr_html += '	</td>';
                        tr_html += '	<td>';
                        tr_html += '		<div class="widget-form__group-row">';
                        tr_html += '			<div class="zent-input-wrapper input-mini2"><input type="text" class="zent-input" name="sku_sn" value="' + (sku['sn'] || '') + '" autocomplete="off" placeholder="输入数字商品编码" maxlength="32" /></div>';
                        tr_html += '		</div>';
                        tr_html += '	</td>';
                        tr_html += '	<td>';
                        tr_html += '		<div class="widget-form__group-row">';
                        tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                        tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_cost_price" value="' + (sku['cost_price'] || '') + '" autocomplete="off" /></div>';
                        tr_html += '			</div>';
                        tr_html += '		</div>';
                        tr_html += '	</td>';
                        tr_html += '	<td class="td-weight ' + hide + '">';
                        tr_html += '		<div class="zent-number-input-wrapper input-mini">';
                        tr_html += '			<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_weight" autocomplete="off" value="' + (sku['weight'] || '') + '" /></div>';
                        tr_html += '		</div>';
                        tr_html += '	</td>';
                        if (is_drp == 1 && drp_max_level >= 1) {
                            tr_html += '<td class="td-profit">';
                            tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                            if (drp_setting == 1) {
                                tr_html += '	<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_1" value="' + (sku['drp_1_profit'] || '') + '" autocomplete="off" /></div>';
                            } else {
                                tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_1_profit_ratio + '</div>';
                            }
                            tr_html += '	</div>';
                            tr_html += '</td>';
                        }
                        if (is_drp == 1 && drp_max_level >= 2) {
                            tr_html += '<td class="td-profit">';
                            tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                            if (drp_setting == 1) {
                                tr_html += '	<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_2" value="' + (sku['drp_2_profit'] || '') + '" autocomplete="off" /></div>';
                            } else {
                                tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_2_profit_ratio + '</div>';
                            }
                            tr_html += '	</div>';
                            tr_html += '</td>';
                        }
                        if (is_drp == 1 && drp_max_level == 3) {
                            tr_html += '<td class="td-profit">';
                            tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                            if (drp_setting == 1) {
                                tr_html += '		<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_3" value="' + (sku['drp_3_profit'] || '') + '" autocomplete="off" /></div>';
                            } else {
                                tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_3_profit_ratio + '</div>';
                            }
                            tr_html += '	</div>';
                            tr_html += '</td>';
                        }
                        tr_html += '</tr>';
                    });
                });
                break;
            case 3: // 三个规格
                $('.rc-sku-group').eq(0).find('.rc-sku-item').each(function (i, item) { // 规格一
                    $('.rc-sku-group').eq(1).find('.rc-sku-item').each(function (j, item2) { // 规格二
                        $('.rc-sku-group').eq(2).find('.rc-sku-item').each(function (k, item3) { // 规格三
                            var pid1 = parseInt($(item).closest('.rc-sku-group').find('.group-title').find('.zent-select-input').data('pid') || 0);
                            var vid1 = parseInt($(item).find('.zent-select-input').data('vid') || 0);
                            if (vid1 == 0) {
                                return true;
                            }
                            var pid2 = parseInt($(item2).closest('.rc-sku-group').find('.group-title').find('.zent-select-input').data('pid') || 0);
                            var vid2 = parseInt($(item2).find('.zent-select-input').data('vid') || 0);
                            if (vid2 == 0) {
                                return true;
                            }
                            var pid3 = parseInt($(item3).closest('.rc-sku-group').find('.group-title').find('.zent-select-input').data('pid') || 0);
                            var vid3 = parseInt($(item3).find('.zent-select-input').data('vid') || 0);
                            if (vid3 == 0) {
                                return true;
                            }
                            var sku = self.skuData[pid1 + ':' + vid1 + ';' + pid2 + ':' + vid2 + ';' + pid3 + ':' + vid3] || [];

                            tr_html += '<tr data-sku-vids="' + vid1 + '-' + vid2 + '-' + vid3 + '">';
                            if (j == 0 && k == 0) {
                                tr_html += '<td rowspan="' + ($('.rc-sku-group').eq(1).find('.rc-sku-item').size() * $('.rc-sku-group').eq(2).find('.rc-sku-item').size()) + '" class="text-center">' + $(item).find('.zent-select-input').val() + '</td>';
                            }
                            if (k == 0) {
                                tr_html += '<td rowspan="' + $('.rc-sku-group').eq(2).find('.rc-sku-item').size() + '" class="text-center">' + $(item2).find('.zent-select-input').val() + '</td>';
                            }
                            tr_html += '	<td class="text-center">' + $(item3).find('.zent-select-input').val() + '</td>';
                            tr_html += '	<td>';
                            tr_html += '		<div class="widget-form__group-row">';
                            tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                            tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_price" value="' + (sku['price'] || '') + '" autocomplete="off" /></div>';
                            tr_html += '			</div>';
                            tr_html += '		</div>';
                            tr_html += '	</td>';
                            tr_html += '	<td>';
                            tr_html += '		<div class="widget-form__group-row">';
                            tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                            tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_stock" value="' + (sku['sku'] || '') + '" autocomplete="off" maxlength="8" /></div>';
                            tr_html += '			</div>';
                            tr_html += '		</div>';
                            tr_html += '	</td>';
                            tr_html += '	<td>';
                            tr_html += '		<div class="widget-form__group-row">';
                            tr_html += '			<div class="zent-input-wrapper input-mini2"><input type="text" class="zent-input" name="sku_sn" value="' + (sku['sn'] || '') + '" autocomplete="off" placeholder="输入数字商品编码" maxlength="32" /></div>';
                            tr_html += '		</div>';
                            tr_html += '	</td>';
                            tr_html += '	<td>';
                            tr_html += '		<div class="widget-form__group-row">';
                            tr_html += '			<div class="zent-number-input-wrapper input-mini">';
                            tr_html += '				<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_cost_price" value="' + (sku['cost_price'] || '') + '" autocomplete="off" /></div>';
                            tr_html += '			</div>';
                            tr_html += '		</div>';
                            tr_html += '	</td>';
                            tr_html += '	<td class="td-weight ' + hide + '">';
                            tr_html += '		<div class="zent-number-input-wrapper input-mini">';
                            tr_html += '			<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_weight" value="' + (sku['weight'] || '') + '" autocomplete="off" /></div>';
                            tr_html += '		</div>';
                            tr_html += '	</td>';
                            if (is_drp == 1 && drp_max_level >= 1) {
                                tr_html += '<td class="td-profit">';
                                tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                                if (drp_setting == 1) {
                                    tr_html += '	<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_1" value="' + (sku['drp_1_profit'] || '') + '" autocomplete="off" /></div>';
                                } else {
                                    tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_1_profit_ratio + '</div>';
                                }
                                tr_html += '	</div>';
                                tr_html += '</td>';
                            }
                            if (is_drp == 1 && drp_max_level >= 2) {
                                tr_html += '<td class="td-profit">';
                                tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                                if (drp_setting == 1) {
                                    tr_html += '	<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_2" value="' + (sku['drp_2_profit'] || '') + '" autocomplete="off" /></div>';
                                } else {
                                    tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_2_profit_ratio + '</div>';
                                }
                                tr_html += '	</div>';
                                tr_html += '</td>';
                            }
                            if (is_drp == 1 && drp_max_level == 3) {
                                tr_html += '<td class="td-profit">';
                                tr_html += '	<div class="zent-number-input-wrapper input-mini">';
                                if (drp_setting == 1) {
                                    tr_html += '		<div class="zent-input-wrapper input-mini"><input type="text" class="zent-input" name="sku_profit_3" value="' + (sku['drp_3_profit'] || '') + '" autocomplete="off" /></div>';
                                } else {
                                    tr_html += '	<div class="zent-input-wrapper input-mini">' + drp_3_profit_ratio + '</div>';
                                }
                                tr_html += '	</div>';
                                tr_html += '</td>';
                            }
                            tr_html += '</tr>';
                        });
                    });
                });
                break;
        }

        if ($('.sku-list .rc-sku-item').length > 0) {
            var drp_th = '';
            if (is_drp == 1) {
                if (drp_max_level == 1) {
                    drp_th += '<th class="th-profit"><em class="zent-form__required">*</em>直销分润（' + (drp_setting == 1 ? '元' : '%') + '）</th>';
                } else {
                    drp_th += '<th class="th-profit"><em class="zent-form__required">*</em>一级分润（' + (drp_setting == 1 ? '元' : '%') + '）</th>';
                }
                if (drp_max_level == 2) {
                    drp_th += '<th class="th-profit"><em class="zent-form__required">*</em>直销分润（' + (drp_setting == 1 ? '元' : '%') + '）</th>';
                } else {
                    drp_th += '<th class="th-profit"><em class="zent-form__required">*</em>二级分润（' + (drp_setting == 1 ? '元' : '%') + '）</th>';
                }
                if (drp_max_level == 3) {
                    drp_th += '<th class="th-profit"><em class="zent-form__required">*</em>直销分润（' + (drp_setting == 1 ? '元' : '%') + '）</th>';
                }
            }
            var html = this.defaultOption.stockHtml;
            html = html.replace(/{{name}}/g, th);
            html = html.replace(/{{drp}}/g, drp_th);
            html = html.replace(/{{data}}/g, tr_html);
            html = html.replace(/{{batch}}/g, this.defaultOption.batchOptsHtml);
            var drpBatchHtml = '';
            if (is_drp == 1 && drp_setting == 1) {
                var level_text = ['一级', '二级', '直销']
                for (var i = 0; i < drp_max_level; i++) {
                    if (drp_max_level == 1) {
                        level_text[0] = '直销';
                    } else if (drp_max_level == 2) {
                        level_text[1] = '直销';
                    }
                    drpBatchHtml += '<a href="javascript:;" style="margin-right: 10px;" class="a-proft" data-level="' + (i + 1) + '">' + level_text[i] + '分润</a>';
                }
                html = html.replace(/{{drpBatch}}/g, drpBatchHtml);
            } else {
                html = html.replace(/{{drpBatch}}/g, drpBatchHtml);
            }
            html = html.replace(/{{hide}}/g, hide);
            $('.stock-field').html(html);

            // 禁止修改
            $("input[name='price']").attr("readonly", true);
            $("input[name='price']").closest('.zent-input-wrapper').addClass('zent-input-wrapper__not-editable');
            $("input[name='total_stock']").attr("readonly", true);
            $("input[name='total_stock']").closest('.zent-input-wrapper').addClass('zent-input-wrapper__not-editable');
            $("input[name='cost_price']").attr("readonly", true);
            $("input[name='cost_price']").closest('.zent-input-wrapper').addClass('zent-input-wrapper__not-editable');
            $("input[name='weight']").attr("readonly", true);
            $("input[name='weight']").closest('.zent-input-wrapper').addClass('zent-input-wrapper__not-editable');
            $(".drp-profit").attr('readonly', true);
            $(".drp-profit").closest('.zent-input-wrapper').addClass('zent-input-wrapper__not-editable');

            note_tips($('.th-code .help-circle'), $('.th-code .help-circle').data('help'), 'bottom');
            note_tips($('.text-cost-price .help-circle'), $('.text-cost-price .help-circle').data('help'), 'bottom');
            note_tips($('.th-weight .help-circle'), $('.th-weight .help-circle').data('help'), 'bottom');
        } else {
            $('.stock-field').addClass('hide');
            $('.stock-field .table-sku-wrap').html('');

            $("input[name='price']").attr("readonly", false);
            $("input[name='price']").closest('.zent-input-wrapper').removeClass('zent-input-wrapper__not-editable');
            $("input[name='total_stock']").attr("readonly", false);
            $("input[name='total_stock']").closest('.zent-input-wrapper').removeClass('zent-input-wrapper__not-editable');
            $("input[name='cost_price']").attr("readonly", false);
            $("input[name='cost_price']").closest('.zent-input-wrapper').removeClass('zent-input-wrapper__not-editable');
            $("input[name='weight']").attr("readonly", false);
            $("input[name='weight']").closest('.zent-input-wrapper').removeClass('zent-input-wrapper__not-editable');
            $(".drp-profit").attr('readonly', false);
            $(".drp-profit").closest('.zent-input-wrapper').removeClass('zent-input-wrapper__not-editable');
        }
    },
    bind: function (self) { // 绑定事件
        note_tips($('.th-code .help-circle'), $('.th-code .help-circle').data('help'), 'bottom');
        note_tips($('.text-cost-price .help-circle'), $('.text-cost-price .help-circle').data('help'), 'bottom');
        note_tips($('.th-weight .help-circle'), $('.th-weight .help-circle').data('help'), 'bottom');

        // 规格搜索
        $(document).on('keyup', '.group-title .zent-select-input', function (e) {
            self.search(this, 'sku');
        });
        // 规格值搜索
        $(document).on('keyup', '.rc-sku-item .zent-select-input', function (e) {
            self.search(this, 'skuVal');
        });
        // 添加规格
        $(document).on('click', '.rc-sku-group .zent-btn', function (e) {
            if ($(this).hasClass('zent-btn-disabled')) {
                return false;
            }
            self.add(this, 'sku');
        });
        // 删除规格
        $(document).on('click', '.rc-sku-group .group-remove', function (e) {
            self.del(this, 'sku');
        });
        // 添加规格图片
        $(document).on('click', '.rc-sku-group .zent-checkbox-wrap', function (e) {
            self.addImg(this);

        });
        // 系统规格
        $(document).on('focus', '.rc-sku-group .group-title .zent-select-input', function (e) {
            self.list($(this), 'sku');
        });
        // 规格选择
        $(document).on('click', '.zent-select-popup .zent-select-option', function (e) {
            self.selected(this);
        });
        // 规格下拉列表hover
        $(document).on('mouseover', '.zent-select-popup .zent-select-option', function (e) {
            self.hover(this);
        });
        // 删除规格下拉列表
        $(document).click(function () {
            if (self.activeSku != '' || self.activeSku == undefined) {
                if (self.activeSku.find('.zent-select-input').is(':focus')) {
                    return false;
                }
            }
            if (self.activeSkuVal != '' || self.activeSkuVal == undefined) {
                if (self.activeSkuVal.find('.zent-select-input').is(':focus')) {
                    return false;
                }
            }
            if ($('.zent-select-filter').is(':focus')) {
                return false;
            }

            $('body > .zent-select').remove();
        });
        // 删除规格值
        $(document).on('click', '.rc-sku-item > .item-remove', function (e) {
            self.del(this, 'skuVal');
        });
        // 规格值添加
        $(document).on('click', '.sku-list .sku-add', function (e) {
            self.add(this, 'skuVal');
        });
        // 规格值
        $(document).on('focus', '.rc-sku-item .zent-select-input', function (e) {
            self.list($(this), 'skuVal');
        });
        // 规格值确认
        $(document).on('change', '.rc-sku-item .zent-select-input', function (e) {
            //self.change($(this));
        });
        // 上传规格图片
        $(document).on('click', '.rc-sku-item .rc-upload', function (e) {
            self.upload(this);
        });
        // 删除规格图片
        $(document).on('click', '.upload-img > .item-remove', function (e) {
            self.del(this, 'img');
        });
        // 添加新规格值
        $(document).on('blur', '.rc-sku-item .zent-select-input', function (e) {
            self.add(this, 'AjaxSkuVal');
        });
        // 最大规格数提醒
        $(document).on('mouseover mouseout', '.rc-sku-group .zent-pop-wrapper', function (e) {
            if (e.type == 'mouseover') {
                var top = $(this).offset().top;
                var left = $(this).offset().left;
                $('body > .zent-portal').remove();
                $('body').append('<div class="zent-portal zent-pop zent-popover zent-popover-internal-id-30 zent-popover-position-top-left" style="position: absolute;"><div data-reactroot="" class="zent-popover-content"><div class="zent-pop-inner">最多支持 3 组规格</div><i class="zent-pop-arrow"></i></div></div>');
                $('body > .zent-portal').css({'top': (top - $(this).height() - 18), 'left': left});
            } else {
                $('body > .zent-portal').remove();
            }
        });
        // 批量设置
        $(document).on('click', '.batch-opts a', function (e) {
            self.batch(this);
        });
        // 规格价格检测
        $(document).on('keypress', "input[name='sku_price']", function (e) {
            var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
            var key = parseInt(e.keyCode);
            if ($.inArray(key, numbers) == -1 || ($(this).val() == '' && key == 46)) {
                if (key == 46) {
                    if ($(this).val() != '' && $(this).val().indexOf('.') == -1) {
                        return true;
                    }
                }
                return false;
            }
        });
        $(document).on('blur', "input[name='sku_price']", function (e) {
            if ($(this).val() == '') {
                return false;
            }
            $(this).val(parseFloat($(this).val()).toFixed(2));

            var sku_price = [];
            $("input[name='sku_price']").each(function () {
                if ($(this).val() != '') {
                    sku_price.push(parseFloat($(this).val()));
                }
            });
            $("input[name='price']").val(Math.min.apply(null, sku_price).toFixed(2));

            $(this).closest('.widget-form__group-row').removeClass('widget-form__group--error');
            $(this).closest('.widget-form__group-row').find('.widget-form__error-desc').remove();

            $("input[name='price']").closest('.zent-form__control-group').removeClass('has-error');
            $("input[name='price']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        });
        // 库存检测
        $(document).on('keypress', "input[name='sku_stock']", function (e) {
            var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
            var key = parseInt(e.keyCode);
            if ($.inArray(key, numbers) == -1) {
                return false;
            }
        });
        $(document).on('blur', "input[name='sku_stock']", function (e) {
            if ($(this).val() == '') {
                return false;
            }
            $(this).val(parseInt($(this).val()));

            var stock = 0;
            $("input[name='sku_stock']").each(function () {
                if ($(this).val() != '') {
                    stock += parseInt($(this).val());
                }
            });
            $("input[name='total_stock']").val(stock);

            $(this).closest('.widget-form__group-row').removeClass('widget-form__group--error');
            $(this).closest('.widget-form__group-row').find('.widget-form__error-desc').remove();

            $("input[name='total_stock']").closest('.zent-form__control-group').removeClass('has-error');
            $("input[name='total_stock']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        });
        // 规格编码
        $(document).on('keypress', "input[name='sku_sn']", function (e) {
            var pricing_method = parseInt($('.pricing-method-field .zent-radio-checked').find("input[name='pricing_method']").val() || 0);
            var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
            var key = parseInt(e.keyCode);
            if ($.inArray(key, numbers) == -1) {
                return false;
            } else if (pricing_method == 1 && $(this).val().length >= 5) {
                global_tips('计重商品编码长度只能为5位，否则不支持称重', 1);
                return false;
            }
        });
        $(document).on('blur', "input[name='sku_sn']", function (e) {
            var obj = this;
            var code = $.trim($(this).val() || '');
            if (code == '') {
                return false;
            }
            if (typeof goods_id === 'undefined') {
                goods_id = 0;
            }

            $(obj).closest('.widget-form__group-row').removeClass('widget-form__group--error');
            $(obj).closest('.widget-form__group-row').find('.widget-form__error-desc').remove();
            $.post(check_code_url, {'code': code, 'goods_id': goods_id, 'sku_id': 0}, function (data) {
                if (data.code != 0) {
                    $(obj).closest('.widget-form__group-row').addClass('widget-form__group--error');
                    $(obj).closest('.widget-form__group-row').append('<p class="widget-form__error-desc">' + data.message + '</p>');
                }
            });
        });
        // 成本价检测
        $(document).on('keypress', "input[name='sku_cost_price']", function (e) {
            var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
            var key = parseInt(e.keyCode);
            if ($.inArray(key, numbers) == -1 || ($(this).val() == '' && key == 46)) {
                if (key == 46) {
                    if ($(this).val() != '' && $(this).val().indexOf('.') == -1) {
                        return true;
                    }
                }
                return false;
            }
        });
        $(document).on('blur', "input[name='sku_cost_price']", function (e) {
            if ($(this).val() == '') {
                return false;
            }
            $(this).val(parseFloat($(this).val()).toFixed(2));

            var sku_cost_price = [];
            $("input[name='sku_cost_price']").each(function () {
                if ($(this).val() != '') {
                    sku_cost_price.push(parseFloat($(this).val()));
                }
            });
            $("input[name='cost_price']").val(Math.min.apply(null, sku_cost_price).toFixed(2));
        });
        // 重量检测
        $(document).on('keypress', "input[name='sku_weight']", function (e) {
            var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
            var key = parseInt(e.keyCode);
            if ($.inArray(key, numbers) == -1 || ($(this).val() == '' && key == 46)) {
                if (key == 46) {
                    if ($(this).val() != '' && $(this).val().indexOf('.') == -1) {
                        return true;
                    }
                }
                return false;
            }
        });
        $(document).on('blur', "input[name='sku_weight']", function (e) {
            if ($(this).val() == '') {
                return false;
            } else if (parseFloat($(this).val()) < 0.001) {
                $(this).val(0.001);
            }
            $(this).val(parseFloat($(this).val()).toFixed(3));

            var sku_weight = [];
            $("input[name='sku_weight']").each(function () {
                if ($(this).val() != '') {
                    sku_weight.push(parseFloat($(this).val()));
                }
            });
            $("input[name='weight']").val(Math.min.apply(null, sku_weight).toFixed(3));
        });
        // 一级分润检测
        $(document).on('keypress', "input[name='sku_profit_1']", function (e) {
            var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
            var key = parseInt(e.keyCode);
            if ($.inArray(key, numbers) == -1 || ($(this).val() == '' && key == 46)) {
                if (key == 46) {
                    if ($(this).val() != '' && $(this).val().indexOf('.') == -1) {
                        return true;
                    }
                }
                return false;
            }
        });
        $(document).on('blur', "input[name='sku_profit_1']", function (e) {
            if ($(this).val() == '') {
                return false;
            }
            $(this).val(parseFloat($(this).val()).toFixed(2));

            var sku_profit_1 = [];
            $("input[name='sku_profit_1']").each(function () {
                if ($(this).val() != '') {
                    sku_profit_1.push(parseFloat($(this).val()));
                }
            });
            $("input[name='drp_1_profit']").val(Math.min.apply(null, sku_profit_1).toFixed(2));
        });
        // 二级分润检测
        $(document).on('keypress', "input[name='sku_profit_2']", function (e) {
            var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
            var key = parseInt(e.keyCode);
            if ($.inArray(key, numbers) == -1 || ($(this).val() == '' && key == 46)) {
                if (key == 46) {
                    if ($(this).val() != '' && $(this).val().indexOf('.') == -1) {
                        return true;
                    }
                }
                return false;
            }
        });
        $(document).on('blur', "input[name='sku_profit_2']", function (e) {
            if ($(this).val() == '') {
                return false;
            }
            $(this).val(parseFloat($(this).val()).toFixed(2));

            var sku_profit_2 = [];
            $("input[name='sku_profit_2']").each(function () {
                if ($(this).val() != '') {
                    sku_profit_2.push(parseFloat($(this).val()));
                }
            });
            $("input[name='drp_2_profit']").val(Math.min.apply(null, sku_profit_2).toFixed(2));
        });
        // 直销分润检测
        $(document).on('keypress', "input[name='sku_profit_3']", function (e) {
            var numbers = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
            var key = parseInt(e.keyCode);
            if ($.inArray(key, numbers) == -1 || ($(this).val() == '' && key == 46)) {
                if (key == 46) {
                    if ($(this).val() != '' && $(this).val().indexOf('.') == -1) {
                        return true;
                    }
                }
                return false;
            }
        });
        $(document).on('blur', "input[name='sku_profit_3']", function (e) {
            if ($(this).val() == '') {
                return false;
            }
            $(this).val(parseFloat($(this).val()).toFixed(2));

            var sku_profit_3 = [];
            $("input[name='sku_profit_3']").each(function () {
                if ($(this).val() != '') {
                    sku_profit_3.push(parseFloat($(this).val()));
                }
            });
            $("input[name='drp_3_profit']").val(Math.min.apply(null, sku_profit_3).toFixed(2));
        });
        // 分润设置切换
        $(document).on('click', '.is-drp', function (e) {
            if ($('.table-sku-wrap').html() != '') {
                self.html();
            }
        });
        $(document).on('click', '.drp-setting .zent-radio-wrap', function (e) {
            if ($('.table-sku-wrap').html() != '') {
                self.html();
            }
        });
    },
    render: function (data, tpl, pids) { // 绑定数据
        var html = '';
        var pids = pids || [];
        if (tpl == undefined || tpl == '') {
            for (var i in data) {
                if ($.inArray(data[i].wid, pids) == -1) {
                    html += '<span value="' + data[i].wid + '" class="zent-select-option">' + (data[i].name || data[i].value) + '</span>';
                }
            }
            return this.option.sysTpl = this.defaultOption.sysTpl.replace(/{{data}}/g, html);
        } else {
            for (var i in data) {
                html += '<span value="' + data[i].wid + '" class="zent-select-option">' + (data[i].name || data[i].value) + '</span>';
            }
            return tpl.replace(/{{data}}/g, html);
        }
    }
}

window.wj = {
    linkBox: function (args, callback) {
        var params = $.extend({type: 'more', number: getRandNumber(), radio: 0}, args);
        var load_url = widget_url + '?' + $.param(params);

        link_save_box[params.number] = callback;

        //增加
        var $backdrop = $('<div class="modal-backdrop fade in widget_link_back"></div>');
        modalDom = $('<div class="modal fade hide js-modal in widget_link_box" aria-hidden="false"><iframe src="' + load_url + '"></iframe></div>');
        $('.modal-backdrop,.modal').remove();
        $(document.body).append($backdrop).append(modalDom);
        modalDom.animate({'top': '10%'}, "slow");
        // 点击关闭
        $backdrop.one('click', login_box_close);
    },
    frameLayer: function (url, ext) {
        var option = {
            type: 2,
            title: false,
            resize: false,
            scrollbar: false,
            area: ['860px', '700px'],
            content: url
        };

        option = $.extend(option, ext || {});
        delete option.recall;
        delete option.optionData;

        // 打开弹窗
        var layerId = layer.open(option);

        window.iframeParentOption = {
            action: function (res) {
                layer.close(layerId);
                if (typeof ext.recall === 'function') {
                    ext.recall(res);
                }
            },
            data: ext.optionData || {}
        };

        return layerId;
    },
    pickerFile: function (recall, type, optionData) {
        return wj.frameLayer([_boot.baseHttp + '/store/file/picker#/' + type, 'no'], {
            title: '选择素材',
            recall: recall,
            optionData: optionData
        });
    },
    pickerImg: function (recall, optionData) {
        wj.pickerFile(recall, 'image', optionData);
    },
    getGoods: function (recall, optionData) {
        wj.frameLayer(_boot.baseHttp + '/goods/index/related', {
            area: ['700px', '550px'],
            recall: recall,
            optionData: optionData
        });
    },
    cutImg: function (dom, para, op, callback) {
        var ele = dom.ele || $('.app'),
            img = para.path || '',
            option = op;
        option.onChange = function (c) {
            para.x = c.x;
            para.y = c.y;
            para.w = c.w;
            para.h = c.h;
            para.iw = c.x2;
            para.ih = c.y2;
        };
        var cut_tpl = '  <div class="imgcut-r">' +
            '<span class="top-san"></span>' +
            '<div class="imgcut-box">' +
            '<img src="' + img + '" id="imgcutEle">' +
            '<div class="imgcut-box-btn" id="imgcutSure">确定</div>' +
            '</div>' +
            '</div>';
        ele.parent().append(cut_tpl);
        $('#imgcutEle').Jcrop(option);
        $("body").on("click", "#imgcutSure", function () {
            $.ajax({
                type: "POST",
                url: _boot.baseHttp + '/store/file/cutImg',
                dataType: "json",
                data: para,
                success: function (rs) {
                    if (rs.code == 0) {
                        $(".imgcut-r").remove();
                    }
                    callback.call(this, rs);
                }
            });
        });
    }
};


// 帮助说明
$(function () {
    var help_t = '';
    $(document).on('mouseover mouseout', '.js-help-notes', function (e) {
        if (e.type == 'mouseout') {
            help_t = setTimeout('hidePopoverHelp()', 200);
            return;
        }

        var content = $(this).next('.js-notes-cont').html();
        $('.popover-help-notes').remove();

        var html = '<div class="js-intro-popover popover popover-help-notes bottom" style="display: none; top: ' + ($(this).offset().top + 16) + 'px; left: ' + ($(this).offset().left - 18) + 'px;"><div class="arrow"></div><div class="popover-inner"><div class="popover-content">' + content + '</div></div></div>';
        $('body').append(html);
        $('.popover-help-notes').show();
    })
});

function hidePopoverHelp() {
    $('.popover-help-notes').hide();
}

//数组的删除
var removeArr = function (arr, from, to) {
    var rest = arr.slice((to || from) + 1 || this.length);
    arr.length = from < 0 ? arr.length + from : from;
    return arr.push.apply(arr, rest);
};

// 通用推广，有复制链接地址，有二维码
function promotionQr(dom, url, e) {
    var top = dom.offset().top;
	var left = dom.offset().left;
	var url_encode = '/index.php/widget/index/qrcode?url=' + encodeURIComponent(url);
	var html = '<div class="ui-popover top-center">';
	html += '	<div class="ui-popover-inner promotion-popover">';
	html += '		<div class="input-append url-content">';
	html += '			<input type="text" class="txt js-url-placeholder url-placeholder" readonly="" value="' + url + '">';
	html += '			<button type="button" class="btn js-btn-copy" data-clipboard-text="' + url + '">复制</button>';
	html += '		</div>';
	html += '		<div class="qrcode-content">';
	html += '			<p class="team-code loading">';
	html += '				<img src="' + url_encode + '" alt="">';
	html += '			</p>';
	html += '			<p class="download-code">';
	html += '				<a href="' + url_encode + '" download="">下载二维码</a>';
	html += '			</p>';
	html += '		</div>';
	html += '	</div>';
	html += '	<div class="arrow"></div>';
	html += '</div>';
	
	var line_height = 15;
	var css_line_height = dom.css('line-height');
	try {
		line_height = parseInt(css_line_height) * 0.8;
	} catch (e) {
		line_height = 15;
	}
	

	$('body > .ui-popover').remove();
	$('body').append(html);
	$('body > .ui-popover').css({'top': top + line_height, 'left': left - $('body > .ui-popover').width() / 2 + 10});

	$.getScript('/static/lib/jquery.zclip.min.js', function () {
		var clip = new Clipboard($('.js-btn-copy').get(0), {
			target: function () {
				return $('.js-url-placeholder').get(0);
			}
		});
		clip.on('success', function (e) {
			e.clearSelection();
			global_tips('复制成功', 0);
		});
	})
	
	
	$('body').on('click', '.ui-popover', function (e) {
		e.stopPropagation();
	})

	$('body').click(function () {
		$('.ui-popover').remove();
	});
}

// 获取url后参数列表
function getParam(url, name) {
    if (url.indexOf('?') != -1) {
        url_arr = url.split('?');
        if (typeof url_arr[1] != 'undefined') {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
            var r = url_arr[1].match(reg);
            if (r != null) {
                return unescape(r[2]);
            }
            return '';
        }
    }

    return '';
}
