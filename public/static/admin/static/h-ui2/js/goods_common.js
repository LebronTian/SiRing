var selected_goods = [];

$(function () {
    location_page('');

    $(document).on('click', '.refresh-btn', function (e) {
        location_page('');
    });

    function location_page(data) {
        load_page('body', related_url, data, '', function() {
            // 初始化商品选中状态
            initSelected();

            if (data.keyword != undefined) {
                $('.keyword-input').val(data.keyword);
            }

            // 全选
            $('.thead').on('click', '.select-check', function (e) {
                if (!$(this).hasClass('zent-checkbox-checked')) {
                    $(this).addClass('zent-checkbox-checked');
                    $(this).find("input[type='checkbox']").attr('checked', true);
                    $('.tbody').find('.select-check').addClass('zent-checkbox-checked');
                    $('.tbody').find("input[type='checkbox']").attr('checked', true);
                    selected_num_func($('.selected-num'));
                    selected_goods_func(this, '+');
                } else {
                    $(this).removeClass('zent-checkbox-checked');
                    $(this).find("input[type='checkbox']").attr('checked', false);
                    $('.tbody').find('.select-check').removeClass('zent-checkbox-checked');
                    $('.tbody').find("input[type='checkbox']").attr('checked', false);
                    selected_num_func($('.selected-num'));
                    selected_goods_func(this, '-');
                }
            });

            // 单选
            $('.tbody').on('click', '.select-check', function (e) {
                if (!$(this).hasClass('zent-checkbox-checked')) {
                    $(this).addClass('zent-checkbox-checked');
                    $(this).find("input[type='checkbox']").attr('checked', true);
                    selected_num_func($('.selected-num'));
                    selected_goods_func(this, '+');
                } else {
                    $(this).removeClass('zent-checkbox-checked');
                    $(this).find("input[type='checkbox']").attr('checked', true);
                    selected_num_func($('.selected-num'));
                    selected_goods_func(this, '-');
                }
            });

            // 分页
            $('.js-page-list').on('click', '.js-page_btn', function (e) {
                var page = parseInt($('.js-page_input').val() || 0);
                if (page == 0) {
                    return false;
                }
                var keyword = $.trim($('.keyword-input').val());
                location_page({'keyword': keyword, 'page': page});
            });
            $('.js-page-list').on('click', '.fetch_page', function (e) {
                var page = parseInt($(this).data('page-num') || 1);
                if (page == 0) {
                    return false;
                }
                var keyword = $.trim($('.keyword-input').val());
                location_page({'keyword': keyword, 'page': page});
            });

            // 确定选择
            $('.zent-dialog-r').on('click', '.zent-btn-primary', function () {
                window.top.iframeParentOption.action(selected_goods);
                selected_goods = [];
            });
        });
    }

    //回车提交搜索
    $(window).keydown(function(event){
        if (event.keyCode == 13 && $('.keyword-input').is(':focus')) {
            var keyword = $.trim($('.keyword-input').val());
            location_page({'keyword': keyword});
        }
    });

    // 计算选中数量
    var selected_num_func = function (el) {
        var _check_num = parseInt($('.tbody').find('.select-check').length || 0);
        var _selected_num = parseInt($('.tbody').find('.zent-checkbox-checked').length || 0);
        el.text(_selected_num);
        if (_selected_num > 0 && _check_num != _selected_num) {
            $('.thead').find('.select-check').removeClass('zent-checkbox-checked').addClass('zent-checkbox-indeterminate');
        } else if (_selected_num > 0 && _check_num == _selected_num) {
            $('.thead').find('.select-check').removeClass('zent-checkbox-indeterminate').addClass('zent-checkbox-checked');
        }
        return _selected_num;
    };

    // 选中商品
    var selected_goods_func = function(obj, act) {
        var goods_id = $(obj).find("input[type='checkbox']").val() || 0;
        if (goods_id == 'all') { // 全选
            if (act == '+') {
                $('.tbody').find('.zent-checkbox-checked').each(function (i) {
                    goods_id = $(this).closest('.tr').find("input[type='checkbox']").val() || 0;
                    if (check_exsits(goods_id) == -1) {
                        var name = $(this).closest('.tr').find('a').text();
                        var image = $(this).closest('.tr').find('img').attr('src');
                        var data = {};
                        data.goods_id = parseInt(goods_id);
                        data.name = name;
                        data.image = image;
                        selected_goods.push(data);
                    }
                });
            } else if (act == '-') {
                $('.tbody').find('.zent-checkbox-checked').each(function (i) {
                    var key = check_exsits(goods_id);
                    if (key >= 0) {
                        selected_goods.splice(key, 1);
                    }
                });
            }
        } else {
            if (act == '+') {
                if (check_exsits(goods_id) == -1) {
                    var name = $(obj).closest('.tr').find('a').text();
                    var image = $(obj).closest('.tr').find('img').attr('src');
                    var ori_price =$(obj).closest('.tr').find('a').data('ori');
                    var cost_price =$(obj).closest('.tr').find('a').data('cost');
                    var data = {};
                    data.goods_id = parseInt(goods_id);
                    data.name = name;
                    data.image = image;
                    data.ori_price = ori_price;
                    data.cost_price = cost_price;
                    selected_goods.push(data);
                }
            } else if (act == '-') {
                var key = check_exsits(goods_id);
                if (key >= 0) {
                    selected_goods.splice(key, 1);
                }
            }
        }
    };

    // 检测是否已选择商品，不存在返回-1，否则返回索引
    var check_exsits = function (goods_id) {
        if (selected_goods.length == 0) {
            return -1;
        }
        var exsits = -1; // 不存在
        for (var i in selected_goods) {
            if (parseInt(selected_goods[i].goods_id) == parseInt(goods_id)) {
                exsits = i;
                break;
            }
        }
        return exsits;
    };

    // 页面初始化选中
    var initSelected = function () {
        if (selected_goods.length == 0) {
            return false;
        }
        for (var i in selected_goods) {
            $('.tbody').find("input[value=" + selected_goods[i].goods_id + "]").closest('.select-check').addClass('zent-checkbox-checked');
            $('.tbody').find("input[value=" + selected_goods[i].goods_id + "]").attr('checked', true);
            selected_num_func($('.selected-num'));
        }
    };
})