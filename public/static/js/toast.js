console.log(666);

$(function () {
    $('form').on('submit', function () {

        //加载层-默认风格

        layer.load();
        console.log(888);

        url = $(this).closest("form").attr("action");
        type = $(this).closest("form").attr("method");
        data = $(this).serialize();

        console.log(url);
        console.log(type);
        console.log(data);

        var $button = $(this).find('button')
            .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>  提交中...')
            .prop('disabled', true);


        $.ajax({
                url: url,
                type: type,
                data: data,
                dataType: 'json',
                success: function (ret) {
                    layer.closeAll('loading');
                    console.log("url请求成功");
                    // console.log(ret);
                    console.log(ret.code);

                    if (ret.code == 1) {
                        console.log("tp返回数据处理成功");

                        layer.msg(ret.msg, {
                            icon: 1,
                            time: 3000, //s后自动关闭
                        });
                        setTimeout(function () {
                            window.location.href = ret.url;

                        }, 1000);


                    } else {
                        layer.msg(ret.msg, function () {
                            //关闭后的操作
                        });
                    }
                    $button.prop('disabled', false).text('提交');
                },
                error: function (xhr) {
                    layer.msg(xhr);
                },
            }
        );


        return false;
    });
});