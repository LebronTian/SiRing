$(function(){
        $(window).bind('hashchange', function() {
            location_page(location.hash);
        });
	var return_keyword = getQueryString('return_keyword');
	var return_group_id = getQueryString('return_group_id');
	var return_p = getQueryString('return_p');

    location_page(location.hash);
    function location_page(mark, dom) {
        var type = 1;
        $('.ui-nav').find('li').removeClass('active');
        switch (mark) {
            case '#3':
                type = 3;
                $('.ui-nav').find('li').eq(1).addClass('active');
                break;
            case '#2':
                $('.ui-nav').find('li').eq(2).addClass('active');
                type = 2;
                break;
            default:
                type = 1;
                $('.ui-nav').find('li').eq(0).addClass('active');
                break;
        }

        load_page('.app__content', goods_url, {type: type, keyword: return_keyword, group_id: return_group_id, page: return_p}, '', function() {
            // 商品排序
            $('.app__content').on('click', '.sort', function (e) {
                var goods_id = $(this).data('goods-id');
                var sort = parseInt($(this).text()) || '';
                var html = '<div class="zent-input-wrapper serial-input"><input type="number" name="sort" class="zent-select-input" maxlength="20" min="0" data-goods-id="' + goods_id + '" value="' + sort + '" /></div>';
                $(this).closest('.cell__child-container').html(html);
                $("input[name='sort']").focus();
            });
            $('.app__content').on('blur', "input[name='sort']", function (e) {
                var obj = this;
                var goods_id = $(this).data('goods-id');
                var sort = parseInt($(this).val()) || 0;
                $.post(sort_url, {'goods_id': goods_id, 'sort': sort}, function (data) {
                    $(obj).closest('.cell__child-container').html('<a class="sort" data-goods-id="' + goods_id + '">' + sort + '</a>');
                });
            });

            // 分组搜索
            $('.app__content').on('click', '.filter-group .zent-select', function (e) {
                var obj = this;
                var top = $(this).offset().top;
                var left = $(this).offset().left;
                var html = '<div class="zent-portal zent-select auto-width zent-popover zent-popover-internal-id-group zent-popover-position-bottom-left" style="position: absolute;">';
                    html += '   <div data-reactroot="" class="zent-popover-content">';
                    html += '       <div class="zent-select-popup" tabindex="0" style="height: auto;opacity: 1">';
                    html += '           <div class="zent-select-search">';
                    html += '               <input type="text" placeholder="" class="zent-select-filter" />';
                    html += '           </div>';

                $.post(group_url, {}, function (data) {
                    var data = data.message || [];
                    if (data.length > 0) {
                        html += '<span value="0" class="zent-select-option active current">所有分组</span>';
                        for (var i in data) {
                            html += '<span value="' + data[i].wid + '" class="zent-select-option">' + data[i].title + '</span>';
                        }
                    } else {
                        html += '<span class="zent-select-empty">没有找到匹配项</span>';
                    }

                    html += '       </div>';
                    html += '   </div>';
                    html += '</div>';

                    $('body > .zent-portal').remove();
                    $('body').append(html);
                    $('body > .zent-portal').css({'top': top + $(obj).height(), 'left': left});

                    $(document).on('keyup', '.zent-popover-internal-id-group .zent-select-filter', function (e) {
                        var html = '';
                        $('.zent-select-popup span').remove();
                        var keyword = $.trim($(this).val()) || '';
                        if (keyword == '') {
                            if (data.length > 0) {
                                html += '<span value="0" class="zent-select-option active current">所有分组</span>';
                                for (var i in data) {
                                    html += '<span value="' + data[i].wid + '" class="zent-select-option">' + data[i].title + '</span>';
                                }
                            } else {
                                html += '<span class="zent-select-empty">没有找到匹配项</span>';
                            }
                        } else {
                            if (data.length > 0) {
                                var empty = true;
                                var _i = 0;
                                for (var i in data) {
                                    if (data[i].title.indexOf(keyword) >= 0) {
                                        empty = false;
                                        var current = '';
                                        if (_i == 0) {
                                            current = 'current';
                                        }
                                        html += '<span value="' + data[i].wid + '" class="zent-select-option ' + current + '">' + data[i].title + '</span>';
                                        _i++;
                                    }
                                }
                                if (empty) {
                                    html += '<span class="zent-select-empty">没有找到匹配项</span>';
                                }
                            } else {
                                html += '<span class="zent-select-empty">没有找到匹配项</span>';
                            }
                        }
                        $('.zent-popover-internal-id-group .zent-select-popup').append(html);
                    });
                });
            });
            $(document).on('mouseover', '.zent-popover-internal-id-group .zent-select-option', function (e) {
                $(this).siblings('.zent-select-option').removeClass('current');
            });
            $(document).on('click', '.zent-popover-internal-id-group .zent-select-option', function (e) {
                var type = $('.ui-nav li.active').data('type');
                var return_group_name = $(this).text() || '所有分组';
                return_group_id = $(this).attr('value') || 0;
                load_page('.app__content', goods_url, {type: type, keyword: return_keyword, group_id: return_group_id, page: return_p}, '', function() {
                    $('.filter-group .zent-select-text').text(return_group_name);
                    $('body > .zent-portal').remove();
                });
            });

            // 批量修改分组
            $('.app__content').on('click', '.js-batch-tag', function (e) {
                if ($('.select-check.zent-checkbox-checked').length == 0) {
                    global_tips('请选择商品', 1);
                    return false;
                } else if ($('.goods-tag-pop').length > 0) {
                    return false;
                }

                var top = $(this).offset().top;
                var left = $(this).offset().left;
                var html = '<div class="zent-portal zent-pop goods-tag-pop zent-popover-internal-id-group zent-popover-position-top-center" style="position: absolute;">';
                    html += '   <div data-reactroot="" class="zent-popover-content">';
                    html += '       <div class="zent-pop-inner">';
                    html += '           <div class="goods-tag">';
                    html += '               <div class="goods-tag-header">';
                    html += '                   <span>修改分组</span>';
                    html += '                   <a target="_blank" href="' + group_page_url + '" rel="noopener noreferrer">管理</a>';
                    html += '               </div>';
                    html += '               <div class="goods-tag-search">';
                    html += '                   <div class="zent-input-wrapper">';
                    html += '                       <input type="text" class="zent-input" value="">';
                    html += '                   </div>';
                    html += '                   <button type="button" class="zent-btn-primary zent-btn-small zent-btn goods-tag__search-btn">搜索</button>';
                    html += '               </div>';

                $.post(group_url, {}, function (data) {
                    var data = data.message || [];
                    if (data.length > 0) {
                        html += '<ul class="goods-tag-list">';
                        for (var i in data) {
                            html += '<li>';
                            html += '   <label class="zent-checkbox-wrap">';
                            html += '       <span class="zent-checkbox">';
                            html += '           <span class="zent-checkbox-inner"></span>';
                            html += '           <input type="checkbox" name="group_id" value="' + data[i].wid + '" />';
                            html += '       </span>';
                            html += '       <span value="' + data[i].wid + '">' + data[i].title + '</span>';
                            html += '   </label>';
                            html += '</li>';
                        }
                        html += '</ul>';
                    } else {
                        html += '<div class="goods-tag-empty">没有找到匹配项</div>';
                    }
                    html += '               <div class="goods-tag-footer">';
                    html += '                   <button type="button" class="zent-btn-primary zent-btn-small zent-btn">保存</button>';
                    html += '                   <button type="button" class="zent-btn-cancel zent-btn">取消</button>';
                    html += '               </div>';
                    html += '           </div>';
                    html += '       </div>';
                    html += '       <i class="zent-pop-arrow" style="left: 15%"></i>';
                    html += '   </div>';
                    html += '</div>';
                    $('body > .zent-portal').remove();
                    $('body').append(html);
                    $('body > .zent-portal').css({'top': top - $('.zent-popover-internal-id-group').height() - 10 , 'left': left});

                    // 搜索分组
                    $(document).on('click', '.goods-tag-search .zent-btn-primary', function (e) {
                        var keyword = $.trim($('.goods-tag-search .zent-input').val()) || '';
                        var html = '';
                        $('.goods-tag-list').remove();
                        $('.goods-tag-empty').remove();
                        if (keyword == '') {
                            if (data.length > 0) {
                                html += '<ul class="goods-tag-list">';
                                for (var i in data) {
                                    html += '<li>';
                                    html += '   <label class="zent-checkbox-wrap">';
                                    html += '       <span class="zent-checkbox">';
                                    html += '           <span class="zent-checkbox-inner"></span>';
                                    html += '           <input type="checkbox" name="group_id" value="' + data[i].wid + '" />';
                                    html += '       </span>';
                                    html += '       <span value="' + data[i].wid + '">' + data[i].title + '</span>';
                                    html += '   </label>';
                                    html += '</li>';
                                }
                                html += '</ul>';
                            } else {
                                html += '<div class="goods-tag-empty">没有找到匹配项</div>';
                            }
                        } else {
                            if (data.length > 0) {
                                var empty = true;
                                var _html = '';
                                for (var i in data) {
                                    if (data[i].title.indexOf(keyword) >= 0) {
                                        empty = false;
                                        _html += '<li>';
                                        _html += '   <label class="zent-checkbox-wrap">';
                                        _html += '       <span class="zent-checkbox">';
                                        _html += '           <span class="zent-checkbox-inner"></span>';
                                        _html += '           <input type="checkbox" name="group_id" value="' + data[i].wid + '" />';
                                        _html += '       </span>';
                                        _html += '       <span value="' + data[i].wid + '">' + data[i].title + '</span>';
                                        _html += '   </label>';
                                        _html += '</li>';
                                    }
                                }
                                if (empty) {
                                    html += '<div class="goods-tag-empty">没有找到匹配项</div>';
                                } else {
                                    html += '<ul class="goods-tag-list">' + _html + '</ul>';
                                }
                            } else {
                                html += '<div class="goods-tag-empty">没有找到匹配项</div>';
                            }
                        }
                        $('.goods-tag-search').after(html);
                    });

                    // 选择分组
                    $(document).on('click', '.goods-tag-list .zent-checkbox-wrap', function (e) {
                        if (!$(this).hasClass('zent-checkbox-checked')) {
                            $(this).addClass('zent-checkbox-checked');
                            $(this).find("input[name='group_id']").attr('checked', true);
                        } else {
                            $(this).removeClass('zent-checkbox-checked');
                            $(this).find("input[name='group_id']").attr('checked', false);
                        }
                        return false;
                    });

                    // 取消分组选择
                    $(document).on('click', '.goods-tag-footer .zent-btn-cancel', function (e) {
                        $('.goods-tag-pop').remove();
                    });

                    // 保存修改
                    $(document).on('click', '.goods-tag-footer .zent-btn-primary', function (e) {
                        var goods = [];
                        $('.goods-list-table .tbody').find('.zent-checkbox-checked').each(function () {
                            var goods_id = parseInt($(this).find("input[name='goods_id']").val()) || 0;
                            var _data = {};
                            _data.goods_id = goods_id;
                            var _groups = [];console.log($('.goods-tag-list').find('.zent-checkbox-checked').length)
                            $('.goods-tag-list').find('.zent-checkbox-checked').each(function () {
                                var group_id = parseInt($(this).find("input[name='group_id']").val()) || 0;
                                _groups.push(group_id);
                            });
                            _data.groups = _groups;
                            goods.push(_data);
                        });

                        $.post(set_group_url, {'goods': goods}, function (data) {
                            if (data.err_code == 0) {
                                global_tips(data.err_msg);
                                $('body > .zent-portal').remove();
                            } else {
                                global_tips(data.err_msg, 1);
                            }
                        });
                    });
                });
            });
        });
    }


    // 选择
    $('.app__content').on('click', '.select-check', function() {
        if (typeof $(this).closest('.tbody').html() == 'undefined') {
            if ($(this).closest('label').hasClass('zent-checkbox-checked')) {
                $('.select-check').removeClass('zent-checkbox-checked');
            } else {
                $('.select-check').addClass('zent-checkbox-checked').removeClass('zent-checkbox-indeterminate');
            }
        } else {
            if ($(this).closest('label').hasClass('zent-checkbox-checked')) {
                $(this).removeClass('zent-checkbox-checked');
            } else {
                $(this).addClass('zent-checkbox-checked');
            }

            var has_class = false;
            var is_all = true;
            $('.tbody').find('.select-check').each(function () {
                if (!$(this).hasClass('zent-checkbox-checked')) {
                    is_all = false;
                } else {
                    has_class = true;
                }
            });

            if (is_all) {
                $('.select-check').eq(0).addClass('zent-checkbox-checked').removeClass('zent-checkbox-indeterminate');
            } else if (has_class) {
                $('.select-check').eq(0).removeClass('zent-checkbox-checked').addClass('zent-checkbox-indeterminate');
            } else {
                $('.select-check').eq(0).removeClass('zent-checkbox-checked').removeClass('zent-checkbox-indeterminate');
            }
        }
    });



    // 回车提交搜索
    $(window).keydown(function(event){
        if (event.keyCode == 13 && $('.js-search').is(':focus')) {
                var keyword = $('.js-search').val();
                var type = $('.js-app-third-sidebar').find('.active').attr('data-type');

                $('.app__content').load(goods_url, {type: type, 'keyword': keyword}, function(){
                    $('.js-search').val(keyword);
                });
        }
    });

    // 分页
    $('.app__content').on('click', '.js-page-list > a', function(e){
        var page = $(this).attr('data-page-num');

        if ($(this).hasClass('js-page_btn')) {
            page = $('.js-page_input').val().trim();
            if (page.length == 0) {
                return;
            }
        }

        var type = $('.js-app-third-sidebar').find('.active').attr('data-type');
        var order = $('.js-sort').closest('div').data('order');
        var sort = $('.js-sort').hasClass('asc') ? 'asc' : 'desc';
        var keyword = $('.js-search').val();

        //保留分组
        var group_id = '';
        var group = '';
        if ($('.filter-group .chosen-single').attr('group-id') != '') {
            group_id = $('.filter-group .group-single').attr('group-id');
            group = $('.filter-group .group-single > span').text();
        }

        // 关键字
        var keyword = $('.js-search').val();
        $('.app__content').load(goods_url, {'page': page, 'keyword': keyword, type: type, 'group_id': group_id, 'order': order, 'sort': sort}, function(){
            if (group != '') {
                $('.filter-group .group-single > span').text(group);
            }
            if (keyword != '') {
                $('.js-search').val(keyword);
            }
            if(group_id != ''){
                $('.filter-group .group-single').attr('group-id', group_id);
            }
        });
    });

    // 排序
    $('.app__content').on('click', '.goods-list-table .thead a', function () {
        var dom = $(this).closest('div');
        var order = dom.data('order');
        var sort = 'desc';
        if (typeof dom.find('.js-sort').html() != 'undefined') {
            sort = dom.find('.js-sort').hasClass('asc') ? 'desc' : 'asc';
        }
        var type = $('.js-app-third-sidebar').find('.active').attr('data-type');
        var keyword = $('.js-search').val();

        //保留分组
        var group_id = '';
        var group = '';
        if ($('.filter-group .chosen-single').attr('group-id') != '') {
            group_id = $('.filter-group .group-single').attr('group-id');
            group = $('.filter-group .group-single > span').text();
        }
        $('.app__content').load(goods_url, {'page': 1, 'keyword': keyword, type: type, 'group_id': group_id, 'order': order, 'sort': sort}, function(){
            if (group != '') {
                $('.filter-group .group-single > span').text(group);
            }
            $('.js-search').val(keyword);
            if(group_id != ''){
                $('.filter-group .group-single').attr('group-id', group_id);
            }
        });
    });

    // 下架
    $('.app__content').on('click', '.js-batch-change_type', function (e) {
        var goods_wid_arr = [];
        $('.select-check').each(function () {
            if ($(this).hasClass('zent-checkbox-checked')) {
                if (typeof $(this).closest('.tr').data('wid') != 'undefined') {
                    goods_wid_arr.push($(this).closest('.tr').data('wid'));
                }
            }
        });

        var text = $(this).html();
        var change_type = 1;
        if (text == '下架') {
            change_type = 2;
        }

        if (goods_wid_arr.length == 0) {
            layer_tips(1, '请选择要' + text + '的商品');
            return;
        }

        button_box($(this), e, 'right', 'confirm', '确定要' + text + '这些商品？', function () {
            $.post(change_type_url, {goods_wid_str: goods_wid_arr.toString(), change_type: change_type}, function (result) {
                if (result.code == 0) {
                    layer_tips(0, result.message);
                    var data = getCondition();

                    $('.app__content').load(goods_url, data, function(){
                        if (data.group != '') {
                            $('.filter-group .group-single > span').text(data.group);
                        }
                        $('.js-search').val(data.keyword);
                        if(data.group_id != ''){
                            $('.filter-group .group-single').attr('group-id', data.group_id);
                        }
                    });
                } else {
                    layer_tips(1, result.message);
                }
            });
            close_button_box();
        });
    });

    // 删除
    $('.app__content').on('click', '.js-batch-delete', function (e) {
        var goods_wid_arr = [];
        $('.select-check').each(function () {
            if ($(this).hasClass('zent-checkbox-checked')) {
                if (typeof $(this).closest('.tr').data('wid') != 'undefined') {
                    goods_wid_arr.push($(this).closest('.tr').data('wid'));
                }
            }
        });

        if (goods_wid_arr.length == 0) {
            layer_tips(1, '请选择要删除的商品');
            return;
        }

        button_box($(this), e, 'right', 'confirm', '确定要删除这些商品？', function () {
            $.post(delete_url, {goods_wid_str: goods_wid_arr.toString()}, function (result) {
                if (result.code == 0) {
                    layer_tips(0, result.message);
                    var data = getCondition();

                    $('.app__content').load(goods_url, data, function(){
                        if (data.group != '') {
                            $('.filter-group .group-single > span').text(data.group);
                        }
                        $('.js-search').val(data.keyword);
                        if(data.group_id != ''){
                            $('.filter-group .group-single').attr('group-id', data.group_id);
                        }
                    });
                } else {
                    layer_tips(1, result.message);
                }
            });
            close_button_box();
        });
    });

    // 获取搜索条件
    function getCondition() {
        var dom = $('.js-sort').closest('div');
        var order = dom.data('order');
        var sort = dom.find('.js-sort').hasClass('asc') ? 'asc' : 'desc';

        var type = $('.js-app-third-sidebar').find('.active').attr('data-type');
        var keyword = $('.js-search').val();
        var page = $('.js-page-list').find('.active').data('page-num');

        //保留分组
        var group_id = '';
        if ($('.filter-group .chosen-single').attr('group-id') != '') {
            group_id = $('.filter-group .group-single').attr('group-id');
        }

        return {type: type, keyword: keyword, page: page, group_id: group_id, order: order, sort: sort};
    }

    $(document).on('click', '.js-promotion-btn', function(e) {
        var url = $(this).data('url');
        var id = $(this).data('id');
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        var html = '<div class="ui-popover top-center">';
        html +='	<div class="ui-popover-inner promotion-popover">';
        html += '		<div class="input-append url-content">';
        html += '			<input type="text" class="txt js-url-placeholder url-placeholder" readonly="" value="' + url + '">';
        html += '			<button type="button" class="btn js-btn-copy" data-clipboard-text="' + url + '">复制</button>';
        html += '		</div>';
        html += '		<div class="qrcode-content">';
        html += '			<p class="team-code loading">';
        html += '				<img src="/goods/index/qrcode/id/' + id + '" alt="">';
        html += '			</p>';
        html += '			<p class="download-code">';
        html += '				<a href="/goods/index/qrcode/id/' + id + '" download="">下载二维码</a>';
        html += '			</p>';
        html += '		</div>';
        html += '	</div>';
        html += '	<div class="arrow"></div>';
        html += '</div>';

        $('body > .ui-popover').remove();
        $('body').append(html);
        $('body > .ui-popover').css({'top': top + 15, 'left': left - $('body > .ui-popover').width() / 2 + 10});

        $.getScript('/static/lib/jquery.zclip.min.js',function(){
            var clip = new Clipboard($('.js-btn-copy').get(0), {
                target: function() {
                    return $('.js-url-placeholder').get(0);
                }
            });
            clip.on('success', function(e) {
                e.clearSelection();
                global_tips('复制成功', 0);
            });
        });
    });

    // 设置分销
    $(document).on('click', '.drp-setting', function (e) {
        var goods_id = parseInt($(this).data('id') || 0);
        var name = $(this).closest('.tr').find('.goods-info a').html();
        if (goods_id == 0) {
            global_tips('缺少必要的参数', 1);
            return false;
        }

        var html = '<div class="zent-portal zent-dialog-r-anchor">';
            html += '   <div data-reactroot="" tabindex="-1" class="zent-dialog-r-root">';
            html += '       <div class="zent-dialog-r-backdrop"></div>';
            html += '           <div class="zent-dialog-r-wrap">';
            html += '               <div class="zent-dialog-r " style="min-width: 450px; max-width: 75%;">';
            html += '                   <button type="button" class="zent-dialog-r-close zent-dialog-r-has-title">×</button>';
            html += '                   <div class="zent-dialog-r-header">';
            html += '                       <div class="zent-dialog-r-title">';
            html += '                           <div class="vip-dialog-header">';
            html += '                               分销设置 - ' + name;
            html += '                           </div>';
            html += '                       </div>';
            html += '                   </div>';
            html += '                   <div class="zent-dialog-r-body">';
            html += '                       <div class="zent-loading-container zent-loading-container-static" style="height: initial;">';
            html += '                           <div class="vip-dialog__loading" style="display: none;"></div>';
            html += '                           <div class="vip-dialog" style="">';
            html += '                               <div class="vip-dialog__inner">';
            html += '                                   <div class="vip-table-wrap">';
            html += '                                   </div>';
            html += '                               </div>';
            html += '                               <div class="vip-dialog__bottom">';
            html += '                                   <div class="batch-container">';
            html += '                                       <div>';
            html += '                                           <button type="button" class="zent-btn" style="margin-left: 10px;">批量</button>';
            html += '                                       </div>';
            html += '                                   </div>';
            html += '                                   <div class="vip-dialog__bottom-right">';
            html += '                                       <button type="button" class="zent-btn-primary zent-btn">保存</button>';
            html += '                                       <button type="button" class="zent-btn-cancel zent-btn">取消</button>';
            html += '                                   </div>';
            html += '                               </div>';
            html += '                           </div>';
            html += '                       </div>';
            html += '                   </div>';
            html += '               </div>';
            html += '           </div>';
            html += '       </div>';
            html += '</div>';
        $('body > .zent-dialog-r-anchor').remove();
        $('body').append(html);

        load_page('.vip-table-wrap',goods_sku_url,{'goods_id': goods_id},'', function () {});
    });

    // 分销设置窗口关闭
    $(document).on('click', '.zent-portal .zent-dialog-r-close', function (e) {
        $('body > .zent-dialog-r-anchor').remove();
    });
    // 分销设置取消
    $(document).on('click', '.zent-portal .zent-btn-cancel', function (e) {
        $('.zent-portal .zent-dialog-r-close').trigger('click');
    });
    // 批量设置
    $(document).on('click', '.batch-container .zent-btn', function (e) {
        var html = '<div><span class="batch-label">批量设置:</span><div class="zent-popover-wrapper zent-select card-select " style="display: inline-block;font-size: 0;"><div class="zent-select-text">请选择</div><!-- react-empty: 200 --></div><div class="batch-set"><!-- react-text: 203 --> <!-- /react-text --><div class="zent-number-input-wrapper batch-input"><div class="zent-input-wrapper batch-input"><input type="text" class="zent-input" value=""></div></div><!-- react-text: 207 --> 元<!-- /react-text --></div><span class="sure-batch">确定</span><span class="cancel-batch">取消</span></div><!-- react-text: 196 --><!-- /react-text -->';
        $('.batch-container').html(html);
    });
    // 批量设置
    $(document).on('click', '.batch-container .card-select', function (e) {
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        var drp_max_level = $('.th-profit').length;
        var html = '<div class="zent-portal zent-select zent-popover zent-popover-internal-id-level zent-popover-position-top-left" style="position: absolute;">';
            html += '   <div data-reactroot="" class="zent-popover-content" style="opacity: 1">';
            html += '       <div class="zent-select-popup" tabindex="0" style="height: auto;opacity: 1;z-index: 99999">';
            for (var i = 0; i < drp_max_level; i++ ) {
                var level_text = ['一级分润', '二级分润', '直销分润']
                var current = '';
                if (i == 0) {
                    current = 'current';
                }
                if (drp_max_level == i + 1) {
                    level_text[i] = '直销分润'
                }
                html += '       <span value="' + (i + 1) + '" class="zent-select-option ' + current + '">' + level_text[i] + '</span>';
            }
            html += '       </div>';
            html += '   </div>';
            html += '</div>';

        $('body > .zent-popover').remove();
        $('body').append(html);
        $('body > .zent-popover').css({'top': top + $(this).height() - 1, 'left': left});
    });

    // hover
    $(document).on('mouseover', '.zent-select .zent-select-option', function (e) {
        $(this).addClass('current').siblings('.zent-select-option').removeClass('current');
    });
    // selected
    $(document).on('click', '.zent-select .zent-select-option', function (e) {
        var name = $(this).text();
        var value = $(this).attr('value');
        $('.card-select .zent-select-text').data('value', value).text(name);
        $('body > .zent-popover-internal-id-level').remove();
    });
    // 确定
    $(document).on('click', '.batch-container .sure-batch', function (e) {
        var level = $(this).closest('.batch-container').find('.zent-select-text').data('value') || '';
        var profit = $(this).closest('.batch-container').find('.zent-input').val() || '';
        $(this).closest('.batch-container').find('.batch-error').remove();
        if (level == '') {
            $(this).closest('.batch-container').append('<div class="batch-error">请选择要批量设置的分润级别</div>');
            return false;
        } else if (profit == '') {
            $(this).closest('.batch-container').append('<div class="batch-error">请填写分润金额</div>');
            return false;
        }
        profit = parseFloat(profit).toFixed(2);
        $('.table-sku-stock').find("input[name='sku_profit_" + level + "']").val(profit);
        $('.batch-container .cancel-batch').trigger('click');
    });
    // 批量分润金额输入验证
    $(document).on('keypress', ".batch-input .zent-input", function (e) {
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
    $(document).on('blur', ".batch-input .zent-input", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });
    // 分润金额输入验证
    $(document).on('keypress', ".td-profit .zent-input", function (e) {
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
    $(document).on('blur', ".td-profit .zent-input", function (e) {
        if ($(this).val() == '') {
            return false;
        }
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });
    // 保存分润设置
    $(document).on('click', '.zent-dialog-r-anchor .zent-btn-primary', function (e) {
        var settings = [];
        $('.table-sku-stock > tbody > tr').each(function (i) {
            var goods_id = parseFloat($(this).data('goods-id') || 0);
            if (goods_id == 0) {
                golbal_tips('缺少必要的参数', 1);
                return false;
            }
            var sku_id = parseInt($(this).data('sku-id') || 0);
            var sku_profit_1 = parseFloat($("input[name='sku_profit_1']").val() || 0).toFixed(2);
            var sku_profit_2 = parseFloat($("input[name='sku_profit_2']").val() || 0).toFixed(2);
            var sku_profit_3 = parseFloat($("input[name='sku_profit_3']").val() || 0).toFixed(2);
            var _obj = {};
            _obj.sku_id = sku_id;
            _obj.goods_id = goods_id;
            _obj.sku_profit_1 = sku_profit_1;
            _obj.sku_profit_2 = sku_profit_2;
            _obj.sku_profit_3 = sku_profit_3;
            settings.push(_obj);
        });
        if (settings.length == 0) {
            return false;
        }
        $.post(drp_setting_url, {'settings': settings}, function (data) {
            if (data.err_code == 0) {
                golbal_tips(data.err_msg);
                $('body > .zent-dialog-r-anchor').remove();
                location_page(location.hash);
            } else {
                golbal_tips(data.err_msg);
            }
            return;
        });
    })
    // 取消
    $(document).on('click', '.batch-container .cancel-batch', function (e) {
        var html = '<div><button type="button" class="zent-btn" style="margin-left: 10px;">批量</button></div>';
        $('.batch-container').html(html);
    });

    //点击“删除窗口”之外区域删除“删除窗口”
    $('body').click(function(e){
        var _con = $('.ui-popover');   // 设置目标区域
        if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
            $('.ui-popover').remove();
        }

        var _con = $('.zent-popover');   // 设置目标区域
        if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
            $('.zent-popover').remove();
        }
    });
});
function msg_hide() {
    $('.notifications').html('');
    clearTimeout(t);
}

function getQueryString(name) {
	var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) {
		return decodeURI(r[2]);
	}
	return null;
}
