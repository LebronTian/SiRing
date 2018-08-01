$(function () {
    note_tips($('.drp-global-setting .help-circle'), $('.drp-global-setting .help-circle').data('help'), 'bottom');
    note_tips($('.drp-custom-setting .help-circle'), $('.drp-custom-setting .help-circle').data('help'), 'bottom');
    // 日历
    $('input.timepicker').datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: "HH:mm:ss",
        minDate: new Date(+new Date + 6e4),
        showSecond: true
    });

    $('.zent-steps-item').click(function () {
        $('.zent-step-btn:visible').trigger('click');
    });

    // 商品类型选择
    $('.goods-type').click(function (e) {
        if (!$(this).hasClass('zent-radio-checked')) {
            $('.goods-type').removeClass('zent-radio-checked');
            $("input[name='type']").prop('checked', false);
            $(this).find("input[name='type']").prop('checked', true);
            $(this).addClass('zent-radio-checked');

            // 服务商品
            if ($("input[name='type']:checked").val() == 1) {
                // 隐藏运费设置
                $('.delivery-field').addClass('hide');
                // 隐藏重量设置
                $('.weight-field').addClass('hide');
                $('.th-weight').addClass('hide');
                $('.td-weight').addClass('hide');
                $('.a-weight').addClass('hide');
            } else {
                // 显示运费设置
                $('.delivery-field').removeClass('hide');
                // 显示重量设置
                $('.weight-field').removeClass('hide');
                $('.th-weight').removeClass('hide');
                $('.td-weight').removeClass('hide');
                $('.a-weight').removeClass('hide');
            }
        }
    });

    // 购买渠道
    $('.goods-buy-channel').click(function (e) {
        if (!$(this).hasClass('zent-checkbox-checked')) {
            $(this).addClass('zent-checkbox-checked');
            $(this).find('.buy_channel').prop('checked', true);
            $('.link-field').removeClass('hide');
        } else {
            $(this).removeClass('zent-checkbox-checked');
            $(this).find('.buy_channel').prop('checked', false);
            $('.link-field').addClass('hide');
        }
        return false
    });

    // 添加图片
    /*$('body').on('click', '.js-add_goods_pic', function () {
        wj.pickerImg(function (pic_list) {
            for (var key in pic_list) {
                var html = '<li class="image-item" draggable="true" style="opacity: 1;"><img src="' + pic_list[key].url + '" role="presentation" alt=""><a class="close-modal small js-picture_delete">×</a></li>';
                if ($('.js-picture-list .image-item').size() > 0) {
                    $('.js-picture-list .image-item:last').after(html);
                } else {
                    $('.js-picture-list').prepend(html);
                }

                if ($('.js-picture-list').find('.image-item').size() >= 8) {
                    $('.js-picture-list').find('.rc-upload').closest('li').addClass('hide');
                    break;
                }
            }
        }, {maxnum: 1})
    });*/

    // 删除图片
    $('body').on('click', '.js-picture_delete', function () {
        $(this).closest('li').remove();
        $('.js-picture-list').find('.rc-upload').closest('li').removeClass('hide');
    });


    $('body').on('click', '.zent-select-delete', function (event) {
        event.stopPropagation();

        $(this).parent('span').parent('span').remove();

        if ($('.zent-select-tags').html().trim().length == 0) {
            $('.zent-select-tags').html($('.js-goods_category').data('text'));
            $('.zent-select-tags').data('empty', true);
        }

        close_select_box();
    });

    $(document).on('click', '.js-goods_category_refresh', function () {
        $.post(category_json_url, function (result) {
            if (result.code == 0) {
                goods_category_list = result.message;
            }
        });
    })

    $(document).on('click', '.zent-select-tags', function (event) {
        select_box($(this), event, goods_category_list, function () {

        });
    });

    // 计价方式
    $(document).on('click', '.pricing-method-field .zent-radio-wrap', function (e) {
        $(this).addClass('zent-radio-checked');
        $(this).find("input[name='pricing_method']").attr('checked', true);
        $(this).siblings('.zent-radio-wrap').removeClass('zent-radio-checked');
        $(this).siblings('.zent-radio-wrap').find("input[name='pricing_method']").attr('checked', false);
    });

    // 添加关联商品
   /* $(document).on('click', '.js-add-goods', function (e) {
        wj.getGoods(function (goods_list) {
            for (var key in goods_list) {
                var html = '<li class="goods-item" draggable="true" style="opacity: 1;" data-goods-id="' + goods_list[key]['goods_id'] + '"><img src="' + goods_list[key]['image'] + '" title="' + goods_list[key]['name'] + '" role="presentation" alt=""><a class="close-modal small">×</a></li>';
                if ($('.js-goods-list .goods-item').size() > 0) {
                    $('.js-goods-list .goods-item:last').after(html);
                } else {
                    $('.js-goods-list').prepend(html);
                }

                if ($('.js-goods-list').find('.goods-item').size() >= 6) {
                    $('.js-goods-list').find('.rc-upload').closest('li').addClass('hide');
                    break;
                }
            }
        })
    });*/

    // 删除关联商品
    $('.js-goods-list').on('click', '.close-modal', function (e) {
        $(this).closest('li').remove();
        $('.js-goods-list').find('.rc-sku-group > h3 > .rc-upload').closest('li').removeClass('hide');
    });

    // 库存显示
    $(document).on('click', '.show-stock-checkbox', function (e) {
        if (!$(this).hasClass('zent-checkbox-checked')) {
            $(this).addClass('zent-checkbox-checked');
            $("input[name='hide_stock']").prop('checked', true);
        } else {
            $(this).removeClass('zent-checkbox-checked');
            $("input[name='hide_stock']").prop('checked', false);
        }
        return false
    });

    // 运费
    $(document).on('click', '.delivery-field .zent-radio-wrap', function (e) {
        $('.delivery-field .zent-radio-wrap').removeClass('zent-radio-checked');
        $(this).addClass('zent-radio-checked');
        $("input[name='postage_type']").prop('checked', false);
        $(this).find("input[name='postage_type']").prop('checked', true);
        if ($(this).find("input[name='postage_type']").val() == 1) {
            $('body > .zent-portal').remove();
            $('.delivery-field .zent-select-text').text('请选择运费模版');

            if ($('.delivery-field').hasClass('has-error')) {
                $('.delivery-field .zent-select').addClass('no-error');
                if ($("input[name='postage']").val() == '') {
                    $("input[name='postage']").closest('.zent-number-input-wrapper').removeClass('no-error');
                    $('.delivery-field .zent-form__error-desc').text('请输入运费');
                }
            }
        } else {
            $("input[name='postage']").val('');
            if ($('.delivery-field').hasClass('has-error')) {
                $("input[name='postage']").closest('.zent-number-input-wrapper').addClass('no-error');
                if ($("input[name='postage_type']:checked").data('tpl-id') == '' || $("input[name='postage_type']:checked").data('tpl-id') == undefined) {
                    $(this).find('.zent-select').removeClass('no-error');
                    $('.delivery-field .zent-form__error-desc').text('请选择运费模版');
                }
            }
        }
        return false;
    });

    // 选择运费模板
    $(document).on('click', '.delivery-field .zent-select', function (e) {
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        var html = '<div class="zent-portal zent-select auto-width zent-popover zent-popover-internal-id-delivery zent-popover-position-bottom-left" style="position: absolute;"><div data-reactroot="" class="zent-popover-content"><div class="zent-select-popup" tabindex="0"><div class="zent-select-search"><input type="text" placeholder="" class="zent-select-filter" value=""></div>{{data}}</div></div></div>';
        if (postage_tpl_list.length > 0) {
            var options = '';
            for (var i in postage_tpl_list) {
                var current = '';
                if (i == 0) {
                    current = ' current';
                }
                if (postage_tpl_list[i].type == 1) {
                    type = '按件';
                } else {
                    type = '按重';
                }
                options += '<span value="' + postage_tpl_list[i]['wid'] + '" class="zent-select-option' + current + '">' + type + '：' + postage_tpl_list[i]['title'] + '</span>';
            }
        } else {
            options = '<span class="zent-select-empty">没有找到匹配项</span>';
        }

        html = html.replace(/{{data}}/g, options);
        $('body > .zent-portal').remove();
        $('body').append(html);
        $('body > .zent-portal').css({'top': (top + $(this).height()), 'left': left});
    });

    // 运费模板下拉列表hover
    $(document).on('mouseover', '.zent-popover-internal-id-delivery .zent-select-option', function (e) {
        $(this).closest('.zent-select-popup').find('.zent-select-filter').focus();
    });
    // 运费模板选择
    $(document).on('click', '.zent-popover-internal-id-delivery .zent-select-option', function (e) {
        var text = $.trim($(this).text());
        var tpl_id = parseInt($(this).attr('value')) || 0;
        $('.delivery-field .zent-select-text').text(text);
        $("input[name='postage_type']:checked").data('tpl-id', tpl_id);
    });
    // 新建运费模板
    $(document).on('click', '.delivery-field .new-window', function (e) {
        window.open($(this).attr('href'));
    });
    // 刷新费模板
    $(document).on('click', '.delivery-field .refresh', function (e) {
        $.post('/index.php/goods/index/postage_tpls', {}, function(data) {
            postage_tpl_list = data;
        })
    });

    // 查找模板
    $(document).on('keyup', '.zent-popover-internal-id-delivery .zent-select-filter', function (e) {
        var keyword = $.trim($(this).val()) || '';
        if (postage_tpl_list.length > 0) {
            var options = '';
            for (var i in postage_tpl_list) {
                if (postage_tpl_list[i]['title'].indexOf(keyword) >= 0) {
                    var current = '';
                    if (i == 0) {
                        current = ' current';
                    }
                    if (postage_tpl_list[i].type == 1) {
                        type = '按件';
                    } else {
                        type = '按重';
                    }
                    options += '<span value="' + postage_tpl_list[i]['wid'] + '" class="zent-select-option' + current + '">' + type + '：' + postage_tpl_list[i]['title'] + '</span>';
                }
            }
        }
        if (options == '') {
            options = '<span class="zent-select-empty">没有找到匹配项</span>';
        }

        $('.zent-popover-internal-id-delivery .zent-select-search').nextAll('span').remove();
        $('.zent-popover-internal-id-delivery .zent-select-popup').append(options);
    });

    // 商品状态
    $(document).on('click', '.sold-time-field .zent-radio-wrap', function (e) {
        $('.sold-time-field .zent-radio-wrap').removeClass('zent-radio-checked');
        $('.sold-time-field').find("input[name='status']").prop('checked', false);
        $(this).addClass('zent-radio-checked');
        $(this).find("input[name='status']").prop('checked', true);
        if ($(this).find("input[name='status']").val() != 2) {
            $("input[name='sold_time']").val('');
        }
    });

    // 上架时间
    $(document).on('click', '.zent-datetime-picker .zenticon-calendar-o', function (e) {
        $('input.timepicker').focus();
    });

    // 商品限购
    $(document).on('click', '.quota-field .zent-checkbox-wrap', function (e) {
        if (!$(this).hasClass('zent-checkbox-checked')) {
            $(this).addClass('zent-checkbox-checked');
            $("input[name='is_buy_quota']").prop('checked', true);
            $(this).next('.quota-field__inner').removeClass('hide');
        } else {
            $(this).removeClass('zent-checkbox-checked');
            $("input[name='is_buy_quota']").prop('checked', false);
            $(this).next('.quota-field__inner').addClass('hide');
            $("input[name='buy_quota']").val('');
            $("input[name='buy_quota_stime']").val('');
            $("input[name='buy_quota_etime']").val('');
        }
        return false;
    });

    // 商家推荐
    $(document).on('click', '.recommend-field .zent-checkbox-wrap', function (e) {
        if (!$(this).hasClass('zent-checkbox-checked')) {
            $(this).addClass('zent-checkbox-checked');
            $("input[name='recommend']").attr('checked', true);
        } else {
            $(this).removeClass('zent-checkbox-checked');
            $("input[name='recommend']").attr('checked', false);
        }
        return false;
    });

    // 留言字段添加
    $(document).on('click', '.message-add .control-action', function (e) {
        var field_num = $('.message-container .message-item').length;
        var text = '留言' + (field_num + 1);
        var html = '<div class="message-item"><span class="input-mini" style="margin-right: 0px;"><div class="zent-input-wrapper no-error"><input type="text" class="zent-input" value="' + text + '"></div></span><div class="zent-popover-wrapper zent-select message-item__select no-error " style="display: inline-block;"><div class="zent-select-text">文本格式</div></div><label class="zent-checkbox-wrap"><span class="zent-checkbox"><span class="zent-checkbox-inner"></span><input type="checkbox" name="multi_line" value="1" /></span><span>多行</span></label><label class="zent-checkbox-wrap"><span class="zent-checkbox"><span class="zent-checkbox-inner"></span><input type="checkbox" name="required" value="1" /></span><span>必填</span></label><a href="javascript:;" class="remove-message">删除</a></div>';
        $(this).closest('.message-add').prev('.message-container').append(html);
        if (field_num + 1 >= 10) {
            $(this).closest('.message-add').remove();
        }
    });

    // 留言字段删除
    $(document).on('click', '.messages-field .remove-message', function (e) {
        $(this).closest('.message-item').remove();
        if ($('.messages-field .message-add').length == 0) {
            $('.messages-field .message-container').after('<div class="message-add"><a href="javascript:;" class="control-action">﹢添加字段</a></div>');
        }
    });

    // 留言字段格式选择
    $(document).on('click', '.messages-field .message-item__select', function (e) {
        $('.messages-field .message-item__select').removeClass('active');
        $(this).addClass('active');

        var left = $(this).offset().left;
        var top = $(this).offset().top;
        $(this).find('.zent-select-text').focus();
        var html = '<div class="zent-portal zent-select zent-popover zent-popover-internal-id-message zent-popover-position-top-left" style="position: absolute;"><div data-reactroot="" class="zent-popover-content"><div class="zent-select-popup" tabindex="0"><span value="text" class="zent-select-option active current">文本格式</span><span value="number" class="zent-select-option">数字格式</span><span value="email" class="zent-select-option">邮件</span><span value="date" class="zent-select-option">日期</span><span value="time" class="zent-select-option">时间</span><span value="id_no" class="zent-select-option">身份证号</span><!--<span value="image" class="zent-select-option">图片</span>--><span value="mobile" class="zent-select-option">手机号</span></div></div></div>';
        $('body > .zent-portal').remove();
        $('body').append(html);
        $('body > .zent-portal').css({'top': (top + $(this).height()), 'left': left});
        return false;
    });

    // 留言字段格式选择
    $(document).on('click', '.zent-popover-internal-id-message .zent-select-option', function (e) {
        var value = $(this).attr('value') || 'text';
        var text = $(this).text() || '文本格式';
        $('.messages-field .message-item__select.active').find('.zent-select-text').data('value', value).text(text);
        $('.messages-field .message-item__select').removeClass('active');
    });

    // 留言多行
    $(document).on('click', '.messages-field .zent-checkbox-wrap', function () {
        if (!$(this).hasClass('zent-checkbox-checked')) {
            $(this).addClass('zent-checkbox-checked');
            $(this).find("input[type='checkbox']").prop('checked', true);
        } else {
            $(this).removeClass('zent-checkbox-checked');
            $(this).find("input[type='checkbox']").prop('checked', false);
        }
        return false;
    });

    // 下一步
    $(document).on('click', '.js-content_item .zent-step-btn', function (e) {
        if ($(this).hasClass('next')) { // 下一步
            // 表单验证
            if (!formValidate()) {
                return false;
            }
            $('.zent-steps-item').eq(1).addClass('is-finish');
            $(this).closest('.js-content_item').addClass('hide');
            $(this).closest('.js-content_item').next('.js-content_item').removeClass('hide');
        } else if ($(this).hasClass('prev')) { // 上一步
            $('.zent-steps-item').eq(1).removeClass('is-finish');
            $('.zent-steps-item').eq(0).addClass('is-finish');
            $(this).closest('.js-content_item').addClass('hide');
            $(this).closest('.js-content_item').prev('.js-content_item').removeClass('hide');
        }
    });

    // 商品编码
    $(document).on('keypress', "input[name='sn']", function (e) {
        var pricing_method = parseInt($('.pricing-method-field .zent-radio-checked').find("input[name='pricing_method']").val() || 0);
        var numbers = [48,49,50,51,52,53,54,55,56,57];
        var key = parseInt(e.keyCode);
        if ($.inArray(key, numbers) == -1) {
            return false;
        } else if (pricing_method == 1 && $(this).val().length >= 5) {
            global_tips('计重商品编码长度只能为5位，否则不支持称重', 1);
            return false;
        }
    });
    $(document).on('blur', "input[name='sn']", function (e) {
        var obj = this;
        var code = $.trim($(this).val() || '');
        if (code == '') {
            return false;
        }
        if (typeof goods_id === 'undefined') {
            goods_id = 0;
        }

        $(obj).closest('.zent-form__control-group').removeClass('has-error');
        $(obj).closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        $.post(check_code_url, {'code': code, 'goods_id': goods_id, 'sku_id': 0}, function (data) {
            if (data.code != 0) {
                $(obj).closest('.zent-form__control-group').addClass('has-error');
                $(obj).closest('.zent-form__controls').append('<p class="zent-form__error-desc">' + data.message + '</p>');
            }
        });
    });

    // 划线价(原价)
    $("input[name='original_price']").closest('.zent-form__controls').on('mouseover mouseout', '.help-block a', function (e) {
        if (e.type == 'mouseover') {
            var obj = this;
            var func = function () {
                var html = '<div class="zent-portal zent-pop zent-popover zent-popover-internal-id-price zent-popover-position-top-center" style="position: absolute;"><div data-reactroot="" class="zent-popover-content"><div class="zent-pop-inner"><div class="help-pop"><p class="help-pop__info">划线价在商品详情中显示示例：</p><div class="help-pop__content" style="width: 375px; height: 412px;"><img width="375" style="height: 412px;" alt="" src="/static/images/original-price.png"></div></div></div><i class="zent-pop-arrow"></i></div></div>';
                var top = $(obj).offset().top;
                var left = $(obj).offset().left;

                $('body .zent-popover-internal-id-price').remove();
                $('body').append(html);
                $('body > .zent-portal').css({'top': (top - 467), 'left': (left - 196)});
            }
            timer = setTimeout(func, 200);
        } else {
            $('body .zent-popover-internal-id-price').remove();
            clearTimeout(timer);
        }
    });

    // 价格
    $(document).on('keypress', "input[name='price']", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
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
    $(document).on('blur', "input[name='price']", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });

    // 划线价
    $(document).on('keypress', "input[name='original_price']", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
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
    $(document).on('blur', "input[name='original_price']", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });

    // 成本价
    $(document).on('keypress', "input[name='cost_price']", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
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
    $(document).on('blur', "input[name='cost_price']", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });

    // 库存
    $(document).on('keypress', "input[name='total_stock']", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
        var key = parseInt(e.keyCode);
        if ($.inArray(key, numbers) == -1) {
            return false;
        }
    });
    $(document).on('blur', "input[name='total_stock']", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseInt($(this).val()));
    });

    // 邮费
    $(document).on('keypress', "input[name='postage']", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
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
    $(document).on('blur', "input[name='postage']", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });

    // 重量
    $(document).on('keypress', "input[name='weight']", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
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
    $(document).on('blur', "input[name='weight']", function (e) {
        if ($(this).val() == '') {
            return false;
        } else if (parseFloat($(this).val()) < 0.001) {
            $(this).closest('.zent-form__control-group').addClass('has-error');
            $(this).closest('.zent-form__controls').find('.zent-form__error-desc').remove();
            $(this).closest('.zent-form__controls').append('<p class="zent-form__error-desc">重量最小为0.001kg</p>');
        } else {
            $(this).closest('.zent-form__control-group').removeClass('has-error');
            $(this).closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        }
        $(this).val(parseFloat($(this).val()).toFixed(3));
    });

    // 限购
    $(document).on('keypress', "input[name='buy_quota']", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
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
    $(document).on('blur', "input[name='buy_quota']", function (e) {
        if ($(this).val() == '') {
            return false;
        } else {
            $(this).closest('.zent-form__control-group').removeClass('has-error');
            $(this).closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        }
        $(this).val(parseInt($(this).val()));
    });

    // 积分
    $(document).on('keypress', "input[name='point']", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
        var key = parseInt(e.keyCode);
        if ($.inArray(key, numbers) == -1) {
            return false;
        }
    });
    $(document).on('blur', "input[name='point']", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseInt($(this).val()));
    });

    // 留言字段名称
    $(document).on('blur', ".message-item .zent-input", function (e) {
        if ($(this).val() == '') {
            $(this).closest('.zent-form__control-group').addClass('has-error');
            $(this).closest('.zent-form__controls').find('.zent-form__error-desc').remove();
            $(this).closest('.zent-form__controls').append('<p class="zent-form__error-desc">留言字段名不能为空</p>');
        } else {
            $(this).closest('.zent-form__control-group').removeClass('has-error');
            $(this).closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        }
    });

    // 支持分销
    $(document).on('click', '.is-drp', function (e) {
        if (!$(this).hasClass('zent-checkbox-checked')) {
            $(this).addClass('zent-checkbox-checked');
            $(this).find("input[name='is_drp']").attr('checked', true);
            $('.drp-setting').removeClass('hide');
        } else {
            $(this).removeClass('zent-checkbox-checked');
            $(this).find("input[name='is_drp']").attr('checked', false);
            $('.drp-setting').addClass('hide');
        }

        // 检测是否设置全局分润
        if ($('.drp-global-setting').hasClass('zent-radio-checked')) {
            $('.global-profit-ratio').removeClass('hide');
            $('.custom-profit-field').addClass('hide');
            profitRatio();
        } else {
            $('.global-profit-ratio').addClass('hide');
            $('.custom-profit-field').removeClass('hide');
        }
        return false;
    });

    // 分销设置
    $(document).on('click', '.drp-setting .zent-radio-wrap', function (e) {
        $('.drp-setting .zent-radio-wrap').removeClass('zent-radio-checked');
        $('.drp-setting .zent-radio-wrap').find("input[name='drp_setting']").attr('checked', false);
        $(this).addClass('zent-radio-checked');
        $(this).find("input[name='drp_setting']").attr('checked', true);
        if ($(this).find("input[name='drp_setting']").val() == 0) {
            $('.global-profit-ratio').removeClass('hide');
            $('.custom-profit-field').addClass('hide');
            profitRatio();
        } else {
            $('.global-profit-ratio').addClass('hide');
            $('.custom-profit-field').removeClass('hide');
        }
    });

    // 自定义分润
    $(document).on('keypress', ".custom-profit-field .drp-profit", function (e) {
        var numbers = [48,49,50,51,52,53,54,55,56,57];
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
    $(document).on('blur', ".custom-profit-field .drp-profit", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });

    // 商品内容
    var ueditor = new window.UE.ui.Editor({
        toolbars: [["bold", "italic", "underline", "strikethrough", "forecolor", "backcolor", "justifyleft", "justifycenter", "justifyright", "|", "insertunorderedlist", "insertorderedlist", "blockquote"], ["emotion", "uploadimage", "insertvideo", "link", "removeformat", "|", "rowspacingtop", "rowspacingbottom", "lineheight", "paragraph", "fontsize"], ["inserttable", "deletetable", "insertparagraphbeforetable", "insertrow", "deleterow", "insertcol", "deletecol", "mergecells", "mergeright", "mergedown", "splittocells", "splittorows", "splittocols"]],
        autoClearinitialContent: false,
        autoFloatEnabled: false,
        contentEnabled: true,
        wordCount: true,
        elementPathEnabled: false,
        maximumWords: 10000,
        initialFrameWidth: '404',
        initialFrameHeight: 108,
        focus: false
    });
    ueditor.addListener("blur",function(){
        var ue_con = ueditor.getContent();
        if(ue_con != ''){
            $('.cap-richtext').html(ue_con);
        }else{
            $('.cap-richtext').html('');
        }
    });

    ueditor.addListener("contentChange",function(){
        var ue_con = ueditor.getContent();
        if(ue_con != ''){
            $('.cap-richtext').html(ue_con);
        }else{
            $('.cap-richtext').html('');
        }
    });
    ueditor.render($('.rc-richtext')[0]);
    ueditor.ready(function(e){
        if($('.cap-richtext').html() != '' && !$('.cap-richtext').hasClass('empty')){
            ueditor.setContent($('.cap-richtext').html());
        }
        $('.cap-richtext').removeClass('empty');
    });

    // 保存并查看
    $(document).on('click', '.zent-btn-save-preview', function (e) {
        if (typeof goods_id == 'undefined') {
            goods_id = 0;
        }
        save(this, goods_id, true);
    });
    // 保存
    $(document).on('click', '.zent-btn-save', function (e) {
        if (typeof goods_id == 'undefined') {
            goods_id = 0;
        }
        save(this, goods_id, false);
    });
})

// 表单项验证
var formValidate = function () {
    var flag = true;
    var buy_channel = $('.goods-buy-channel.zent-checkbox-checked').find("input[name='buy_channel']").val() || 0;
    var buy_url = $.trim($("input[name='buy_url']").val()) || '';
    var name = $.trim($("input[name='name']").val()) || '';
    var share_desc = $.trim($("input[name='share_desc']").val()) || '';
    var images = $('.js-picture-list .image-item').length || 0;
    var price = $("input[name='price']").val() || '';
    var total_stock = $("input[name='total_stock']").val() || '';
    var postage_type = $(".delivery-field .zent-radio-checked").find("input[name='postage_type']").val() || 1;
    var postage = $("input[name='postage']").val() || '';
    var postage_tpl = $("input[name='postage_type']:checked").data('tpl-id') || '';
    var weight = $("input[name='weight']").val() || '';
    var is_buy_quota = $('.quota-field .zent-checkbox-checked').find("input[name='is_buy_quota']").val() || 0;
    var buy_quota = $("input[name='buy_quota']").val() || '';

    // 外部购买地址
    var reg = /(http|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/;
    if (buy_channel == 1 && !reg.test(buy_url)) {
        $("input[name='buy_url']").closest('.zent-form__control-group').addClass('has-error');
        $("input[name='buy_url']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        $("input[name='buy_url']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">请输入格式正确的外部购买地址</p>');
    } else {
        $("input[name='buy_url']").closest('.zent-form__control-group').removeClass('has-error');
        $("input[name='buy_url']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
    }
    // 商品名称
    if (name == '' || name.length > 100) {
        $("input[name='name']").closest('.zent-form__control-group').addClass('has-error');
        $("input[name='name']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        $("input[name='name']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">商品名称必须填写，最多100个字</p>');
        flag = false;
    } else {
        $("input[name='name']").closest('.zent-form__control-group').removeClass('has-error');
        $("input[name='name']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
    }

    // 分享描述
    if (share_desc.length > 100) {
        $("input[name='share_desc']").closest('.zent-form__control-group').addClass('has-error');
        $("input[name='share_desc']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        $("input[name='share_desc']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">分享描述最多不能超过100个字</p>');
        flag = false;
    } else {
        $("input[name='share_desc']").closest('.zent-form__control-group').removeClass('has-error');
        $("input[name='share_desc']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
    }

    // 图片
    if (images == 0) {
        $('.js-picture-list').closest('.zent-form__control-group').addClass('has-error');
        $('.js-picture-list').closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        $('.js-picture-list').closest('.zent-form__controls').append('<p class="zent-form__error-desc">最少需要添加一张商品图</p>');
        flag = false;
    } else {
        $('.js-picture-list').closest('.zent-form__control-group').removeClass('has-error');
        $('.js-picture-list').closest('.zent-form__controls').find('.zent-form__error-desc').remove();
    }

    // 规格值填写检测
    var empty = false;
    $('.rc-sku-item .zent-select-input').each(function () {
        var value = $.trim($(this).val()) || '';
        if (value == '') {
            empty = true;
            return false;
        }
    });
    if (empty) {
        $('.sku-field').addClass('has-error');
        $('.sku-field').find('.zent-form__error-desc').remove();
        $('.sku-field').find('.zent-form__controls').append('<p class="zent-form__error-desc">请将商品规格的信息填写完整</p>');
        flag = false;
    } else {
        $('.sku-field').removeClass('has-error');
        $('.sku-field').find('.zent-form__error-desc').remove();
    }

    // 库存信息填写检测
    $(".table-sku-stock").find("input[name='sku_price']").each(function () {
        var value = $.trim($(this).val()) || '';
        if (value == '') {
            $(this).closest('.widget-form__group-row').find('.widget-form__error-desc').remove();
            $(this).closest('.widget-form__group-row').addClass('widget-form__group--error');
            $(this).closest('.widget-form__group-row').append('<p class="widget-form__error-desc">请输入价格</p>');
            flag = false;
        } else {
            $(this).closest('.widget-form__group-row').removeClass('widget-form__group--error');
            $(this).closest('.widget-form__group-row').find('.widget-form__error-desc').remove();
        }
    });
    $(".table-sku-stock").find("input[name='sku_stock']").each(function () {
        var value = $.trim($(this).val()) || '';
        if (value == '') {
            $(this).closest('.widget-form__group-row').find('.widget-form__error-desc').remove();
            $(this).closest('.widget-form__group-row').addClass('widget-form__group--error');
            $(this).closest('.widget-form__group-row').append('<p class="widget-form__error-desc">请输入库存</p>');
            flag = false;
        } else {
            $(this).closest('.widget-form__group-row').removeClass('widget-form__group--error');
            $(this).closest('.widget-form__group-row').find('.widget-form__error-desc').remove();
        }
    });

    // 价格
    if (price == '') {
        $("input[name='price']").closest('.zent-form__control-group').addClass('has-error');
        $("input[name='price']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        $("input[name='price']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">请输入价格</p>');
        flag = false;
    } else {
        $("input[name='price']").closest('.zent-form__control-group').removeClass('has-error');
        $("input[name='price']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
    }

    // 库存
    if (total_stock == '') {
        $("input[name='total_stock']").closest('.zent-form__control-group').addClass('has-error');
        $("input[name='total_stock']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        $("input[name='total_stock']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">请输入库存</p>');
        flag = false;
    } else {
        $("input[name='total_stock']").closest('.zent-form__control-group').removeClass('has-error');
        $("input[name='total_stock']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
    }

    // 邮费
    if ($("input[name='postage_type']:visible").length > 0) {
        if (postage_type == 2 && postage_tpl == '') {
            $("input[name='postage_type']").closest('.zent-form__control-group').addClass('has-error');
            $("input[name='postage_type']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
            $("input[name='postage_type']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">请选择运费模版</p>');
            $('.delivery-field .zent-select').removeClass('no-error');
            $("input[name='postage']").closest('.zent-number-input-wrapper').addClass('no-error');
            flag = false;
        } else {
            $("input[name='postage_type']").closest('.zent-form__control-group').removeClass('has-error');
            $("input[name='postage_type']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();

            $("input[name='postage']").closest('.zent-number-input-wrapper').addClass('no-error');
            $('.delivery-field .zent-select').addClass('no-error');
        }
    }

    // 重量
    if ($("input[name='weight']:visible").length > 0) {
        if (postage_type == 2 && weight == '') {
            $("input[name='weight']").closest('.zent-form__control-group').addClass('has-error');
            $("input[name='weight']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
            $("input[name='weight']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">请输入商品重量</p>');
            flag = false;
        } else if (postage_type == 2 && parseFloat(weight) < 0.001) {
            $("input[name='weight']").closest('.zent-form__control-group').addClass('has-error');
            $("input[name='weight']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
            $("input[name='weight']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">重量最小为0.001kg</p>');
            flag = false;
        } else {
            $("input[name='weight']").closest('.zent-form__control-group').removeClass('has-error');
            $("input[name='weight']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        }
    }

    // 限购
    if (is_buy_quota == 1 && buy_quota == '') {
        $("input[name='buy_quota']").closest('.zent-form__control-group').addClass('has-error');
        $("input[name='buy_quota']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
        $("input[name='buy_quota']").closest('.zent-form__controls').append('<p class="zent-form__error-desc">请输入限购件数</p>');
        flag = false;
    } else {
        $("input[name='buy_quota']").closest('.zent-form__control-group').removeClass('has-error');
        $("input[name='buy_quota']").closest('.zent-form__controls').find('.zent-form__error-desc').remove();
    }

    // 留言
    if ($('.message-item .zent-input').length > 0) {
        $('.message-item .zent-input').each(function (i) {
            var name = $.trim($(this).val()) || '';
            if (name == '') {
                flag = false;
                return false;
            }
        });
    }

    if ($('.has-error:visible').length > 0) {
        return false;
    }

    return flag;
}

// 保存数据
var save = function (obj, goods_id, preview) {
    if (!formValidate()) {
        return false
    }

    // 防止重复提交
    if ($(obj).hasClass('loading')) {
        golbal_tips('数据保存中...，请勿重复提交', 1);
        return false;
    } else {
        $(obj).addClass('loading')
    }

    var goods_id = goods_id || 0;
    var preview = preview ? 1 : 0; // 预览
    var type = $('.goods-type.zent-radio-checked').find("input[name='type']").val() || 0; // 商品类型
    var buy_channel = $('.goods-buy-channel.zent-checkbox-checked').find("input[name='buy_channel']").val() || 0; // 购买渠道
    var buy_url = $.trim($("input[name='buy_url']").val()) || '';
    if (buy_channel == 0) {
        buy_url = '';
    }
    var name = $.trim($("input[name='name']").val()) || '';
    var share_desc = $.trim($("input[name='share_desc']").val()) || '';
    var images = [];
    $('.js-picture-list .image-item').each(function () {
        var image = $(this).find('img').attr('src') || '';
        if (image != '') {
            images.push(image);
        }
    });
    var goods = []; // 关联商品
    if ($('.js-goods-list .goods-item').length > 0) {
        $('.js-goods-list .goods-item').each(function () {
            var goods_id = parseInt($(this).data('goods-id')) || 0;
            goods.push(goods_id);
        });
    }
    var groups = []; // 商品分组
    if ($('.js-goods_category .zent-select-tag').length) {
        $('.js-goods_category .zent-select-tag').each(function () {
            var group_id = parseInt($(this).attr('value')) || 0;
            groups.push(group_id);
        })
    }
    var pricing_method = $('.pricing-method-field .zent-radio-checked').find("input[name='pricing_method']").val() || 0; // 计价方式
    var is_drp = $(".drp-field .zent-checkbox-checked").find("input[name='is_drp']").val() || 0; // 是否支持分销
    var drp_setting = parseInt($('.drp-setting .zent-radio-checked').find("input[name='drp_setting']").val()) || 0; // 分润设置 0 全局设置，1 自定义设置
    var price = parseFloat($("input[name='price']").val()) || 0;
    var original_price = parseFloat($("input[name='original_price']").val()) || 0;
    var cost_price = parseFloat($("input[name='cost_price']").val()) || 0;
    var sku = parseInt($("input[name='total_stock']").val()) || 0;
    var is_sku = $('.show-stock-checkbox').hasClass('zent-checkbox-checked') ? 0 : 1;
    var sn = $.trim($("input[name='sn']").val()) || '';
    var postage_type = $(".delivery-field .zent-radio-checked").find("input[name='postage_type']").val() || 1;
    var postage = 0;
    var postage_template = 0;
    if (postage_type == 1) {
        postage = $("input[name='postage']").val() || 0;
    } else if (postage_type == 2) {
        postage_template = $(".delivery-field .zent-radio-checked").find("input[name='postage_type']").data('tpl-id') || 0;
    }
    var status = $("input[name='status']:checked").val() || 1;
    var sold_time = '';
    if (status == 0) {
        status = 2;
    } else if (status == 2) {
        status = 1;
        sold_time = $("input[name='sold_time']").val();
    }
    var weight = $("input[name='weight']").val() || '';
    if (weight != '') {
        weight = parseFloat(weight) * 1000; // 重量 千克换算为克
    }
    var point = $("input[name='point']").val() || 0; // 积分
    var drp_1_profit = $("input[name='drp_1_profit']").val() || 0;
    var drp_2_profit = $("input[name='drp_2_profit']").val() || 0;
    var drp_3_profit = $("input[name='drp_3_profit']").val() || 0;
    var is_buy_quota = $('.quota-field .zent-checkbox-checked').find("input[name='is_buy_quota']").val() || 0;
    var buy_quota = parseInt($("input[name='buy_quota']").val()) || 0;
    var buy_quota_stime = $("input[name='buy_quota_stime']").val() || 0; // 限购开始时间
    var buy_quota_etime = $("input[name='buy_quota_etime']").val() || 0; // 限购结束时间
    var recommend = $('.recommend-field .zent-checkbox-checked').find("input[name='recommend']").val() || 0; // 商家推荐
    var message_fields = []; // 留言字段
    if ($('.messages-field .message-item').length > 0) {
        $('.messages-field .message-item').each(function (i) {
            var name = $(this).find('.zent-input').val();
            var type = $(this).find('.zent-select-text').data('value') || 'text';
            var multi_line = $(this).find(".zent-checkbox-checked input[name='multi_line']").val() || 0;
            var required = $(this).find(".zent-checkbox-checked input[name='required']").val() || 0;
            message_fields.push({
                'name': $.trim(name),
                'type': type,
                'multi_line': multi_line,
                'required': required
            });
        });
    }
    var info = '';
    if ($('.cap-richtext').html() != '' && !$('.cap-richtext').hasClass('empty')) {
        info = $('.cap-richtext').html();
    }
    var skus = [];
    var sku_imgs = []; // 规格值的图片

    if ($('.rc-sku-group > .group-title').find('.zent-select-input').length > 0) {
        // pid集合
        var sku_pids = [];
        $('.rc-sku-group > .group-title').find('.zent-select-input').each(function () {
            sku_pids.push(parseInt($(this).data('pid')));
        });
        // vid图片集合
        if ($('.rc-sku-item img').length > 0) {
            $('.rc-sku-item img').each(function () {
                var pid = $(this).closest('.rc-sku-group').find('.group-title .zent-select-input').data('pid');
                var vid = $(this).closest('.rc-sku-item').find('.zent-select-input').data('vid') || 0;
                var img = $(this).data('src');
                sku_imgs.push({
                    'pid': pid,
                    'vid': vid,
                    'image': img
                });
            });
        }

        if ($('.table-sku-stock tr').length > 0) {

            $('.table-sku-stock > tbody > tr').each(function () {
                var sku_vids = $(this).data('sku-vids').toString();
                sku_vids = sku_vids.split('-');
                var properties = [];
                for (var i in sku_pids) {
                    var pid = sku_pids[i] || 0;
                    var vid = sku_vids[i] || 0;
                    properties.push(pid + ':' + vid);
                }
                properties = properties.join(';');
                var sku_price = parseFloat($(this).find("input[name='sku_price']").val()) || 0;
                var sku_stock = parseInt($(this).find("input[name='sku_stock']").val()) || 0;
                var sku_sn = $.trim($(this).find("input[name='sku_sn']").val()) || '';
                var sku_cost_price = $(this).find("input[name='sku_cost_price']").val() || '';
                if (sku_cost_price != '') {
                    sku_cost_price = parseFloat(sku_cost_price);
                }
                var sku_weight = $(this).find("input[name='sku_weight']").val() || '';
                if (sku_weight != '') {
                    sku_weight = parseFloat(sku_weight);
                }
                var sku_profit_1 = parseFloat($(this).find("input[name='sku_profit_1']").val() || 0);
                var sku_profit_2 = parseFloat($(this).find("input[name='sku_profit_2']").val() || 0);
                var sku_profit_3 = parseFloat($(this).find("input[name='sku_profit_3']").val() || 0);
                skus.push({
                    'properties': properties,
                    'price': sku_price,
                    'stock': sku_stock,
                    'cost_price': sku_cost_price,
                    'sn': sku_sn,
                    'weight': sku_weight * 1000,
                    'drp_1_profit': sku_profit_1,
                    'drp_2_profit': sku_profit_2,
                    'drp_3_profit': sku_profit_3
                });
            });
        }
    }

    $.post(save_url, {
        'goods_id': goods_id,
        'type': type,
        'buy_channel': buy_channel,
        'buy_url': buy_url,
        'name': name,
        'share_desc': share_desc,
        'images': images,
        'goods': goods,
        'groups': groups,
        'pricing_method': pricing_method,
        'price': price,
        'original_price': original_price,
        'cost_price': cost_price,
        'sku': sku,
        'is_sku': is_sku,
        'sn': sn,
        'postage_type': postage_type,
        'postage': postage,
        'postage_template': postage_template,
        'status': status,
        'weight': weight,
        'point': point,
        'sold_time': sold_time,
        'is_buy_quota': is_buy_quota,
        'buy_quota': buy_quota,
        'buy_quota_stime': buy_quota_stime,
        'buy_quota_etime': buy_quota_etime,
        'recommend': recommend,
        'message_fields': message_fields,
        'info': info,
        'skus': skus,
        'sku_imgs': sku_imgs,
        'is_drp': is_drp,
        'drp_setting': drp_setting,
        'drp_1_profit': drp_1_profit,
        'drp_2_profit': drp_2_profit,
        'drp_3_profit': drp_3_profit,
        'preview': preview
    }, function (data) {
        if (data.err_code == 0) {
            global_tips(data.err_msg, 0);
            var func = function () {
                window.location.href = data.redirect_url;
            }
            var timer = setTimeout(func, 1000);
        } else {
            $(obj).removeClass('loading');

            global_tips(data.err_msg, 1);
            return false;
        }
    });
}

// 全局分润比率
var profitRatio = function () {
    $.post(profit_ratio_url, {}, function (data) {
        if (data.err_code != 0) {
            golbal_tips('抱歉，您还未设置全局分润，暂时不能选择此项。', 1);
            $('.drp-global-setting').removeClass('zent-radio-checked');
            $('.drp-global-setting').find("input[name='drp_setting']").attr('checked', false);
            $('.drp-custom-setting').addClass('zent-radio-checked');
            $('.drp-custom-setting').find("input[name='drp_setting']").attr('checked', true);
        } else {
            if (typeof data.err_msg.drp_1_profit_ratio != 'undefined') {
                drp_1_profit_ratio = parseFloat(data.err_msg.drp_1_profit_ratio).toFixed(1);
                $('.drp-1-profit-ratio').text(drp_1_profit_ratio);
            }
            if (typeof data.err_msg.drp_2_profit_ratio != 'undefined') {
                drp_2_profit_ratio = parseFloat(data.err_msg.drp_2_profit_ratio).toFixed(1);
                $('.drp-2-profit-ratio').text(drp_2_profit_ratio);
            }
            if (typeof data.err_msg.drp_3_profit_ratio != 'undefined') {
                drp_3_profit_ratio = parseFloat(data.err_msg.drp_3_profit_ratio).toFixed(1);
                $('.drp-3-profit-ratio').text(drp_3_profit_ratio);
            }
        }
    });
}