$(function () {

    $('.user').mouseover(function () {
        load_html = '<div class="spinner-border text-primary" role="status">\n' +
            '        <span class="sr-only">Loading...</span>\n' +
            '    </div>';
        load = $('.load').laod_html;
        data = $(this).attr('data-user_id');
        data = 'user_id=' + data;

        console.log(data);
        // data-html="true"
        $(this).attr('data-html', true);
        $(this).attr('data-title', "个人信息");
        $(this).attr('data-content', load_html);
        console.log('ok');
        $(this).popover('show');
        $(this).attr('data-content', "ret");
        var check = $(this);

        $.ajax({
            url: '/index/index/user_info/' + data,
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function (ret) {
                check.popover('hide');
                layer.closeAll('loading');
                console.log("url请求成功");
                console.log(ret);
                check.attr('data-content', ret);
                check.popover('show');

            },
            error: function (xhr) {
                console.log(xhr);
            }
        })

    });

    $('.user').mouseleave(function () {

        var close = $(this);
        console.log('clo');
        setTimeout(function () {
            console.log('close');
            close.popover('hide');
        }, 3000)


    });


});
