// $(function () {
//
//     $('.user').mouseover(function () {
//         load_html = '<div class="spinner-border text-primary" role="status">\n' +
//             '        <span class="sr-only">Loading...</span>\n' +
//             '    </div>';
//         load = $('.load').laod_html;
//         data = $(this).attr('data-user_id');
//         data = 'user_id=' + data;
//
//         console.log(data);
//         // data-html="true"
//         $(this).attr('data-html', true);
//         $(this).attr('data-trigger', 'manual');
//         // $(this).attr('data-title', "个人信息");
//         $(this).attr('data-content', load_html);
//         console.log('ok');
//         $(this).popover('show');
//         $(this).attr('data-content', "ret");
//         var check = $(this);
//
//         $.ajax({
//             url: '/index/index/user_info/' + data,
//             type: 'GET',
//             data: data,
//             dataType: 'json',
//             success: function (ret) {
//                 check.popover('hide');
//                 layer.closeAll('loading');
//                 console.log("url请求成功");
//                 console.log(ret);
//                 check.attr('data-content', ret);
//                 check.popover('show');
//
//             },
//             error: function (xhr) {
//                 console.log(xhr);
//             }
//         })
//
//     });
//
//     $('.user').mouseleave(function () {
//
//         var close = $(this);
//         console.log('clo');
//         setTimeout(function () {
//             console.log('close');
//             close.popover('hide');
//         }, 3000)
//
//
//     });
//
//
// });

$(function () {

    $('.user').mouseover(function () {

        console.log('1');
        var e = this;
        var id = $(e).data("id");
        $(e).data("st") && clearTimeout($(e).data("st"));

        var o = setTimeout(function () {
            $.ajax({
                url: '/index/index/user_info/',
                data: {
                    id: id,
                    type: 'json'
                },
                success: function (t) {
                    $(e).hasClass("btn-thanks") && 0 === $(".thanks-list a", $(t)).size() || ($(e).popover({
                        trigger: "manual",
                        html: !0,
                        content: t,
                        container: document.body
                    }), $(e).popover("show"), $(".popover").on("mouseleave", function () {
                        $(e).popover("hide")
                    }));

                    $(".popover").on("mouseleave", function () {
                        $(e).popover("hide")
                    });
                }
            })
        }, 300);
        $(e).data("st", o);
    });


    $('.user').mouseleave(function () {
        var t = this;
        $(t).data("st") && clearTimeout($(t).data("st")), setTimeout(function () {
            $(".popover:hover").length || $(t).popover("hide")
        }, 300)
    });


});
