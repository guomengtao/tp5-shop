console.log(666);

$(function () {
    $('form').on('submit', function () {
        console.log(888);

        url = $(this).closest("form").attr("action");
        data = $(this).serialize();

        console.log(url);
        console.log(data);

        var $button = $(this).find('button')
            .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>  提交中...')
            .prop('disabled', true);

        $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (ret) {
                    console.log("url请求成功");
                    console.log(ret);
                    console.log(ret.code);
                    //提示层

                    layer.msg(ret.msg);

                    if (ret.code == 1) {
                        console.log("tp返回数据处理成功");
                        // 跳转
                        window.location.href = ret.url;
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