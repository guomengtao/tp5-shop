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
        $(this).attr('data-trigger', 'manual');
        // $(this).attr('data-title', "个人信息");
        // $(this).attr('data-content', load_html);
        console.log('ok');
        // $(this).popover('show');
        $(this).attr('data-content', "ret");
        var check = $(this);

        myVar = setTimeout(function () {

            $.ajax({
                url: '/index/index/user_info/' + data,
                type: 'GET',
                data: data,
                dataType: 'json',
                success: function (ret) {
                    // check.popover('hide');
                    layer.closeAll('loading');
                    console.log("url请求成功");
                    // console.log(ret);
                    check.attr('data-content', ret);
                    check.popover('show');
                    $(".popover").on("mouseleave", function () {
                        console.log('inside');
                        var l = $(".popover:hover").length;
                        console.log(l);
                        check.popover('hide');
                    });
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            })

        }, 500);

        $(this).data("myVar", myVar);
    });

    $('.user').mouseleave(function () {

        var t = $(this);
        console.log('levave');
        // setTimeout(function () {
        //     console.log('close');
        //     close.popover('hide');
        // }, 3000)

        clearTimeout(t.data("myVar"));
        var l = $(".popover:hover").length;
        console.log(l);
        l || t.popover("hide");


    });


});

// $(function () {
//
//     $('.user').mouseover(function () {
//
//         console.log('jinguo');
//         var e = this;
//         var id = $(e).data("user_id");
//         $(e).data("st") && clearTimeout($(e).data("st"));
//
//         var o = setTimeout(function () {
//             $.ajax({
//                 url: '/index/index/user_info/',
//                 data: {
//                     user_id: id,
//                     type: 'json'
//                 },
//                 success: function (t) {
//                     $(e).hasClass("btn-thanks") && 0 === $(".thanks-list a", $(t)).size() || ($(e).popover({
//                         trigger: "manual",
//                         html: !0,
//                         content: t,
//                         container: document.body
//                     }), $(e).popover("show"), $(".popover").on("mouseleave", function () {
//                          console.log('33');
//                         $(e).popover("hide")
//                     }));
//
//                     $(".popover").on("mouseleave", function () {
//                         console.log('8');
//                         $(e).popover("hide")
//                     });
//                 }
//             })
//         }, 300);
//         $(e).data("st", o);
//     });
//
//
//     $('.usery').mouseleave(function () {
//         var t = this;
//         $(t).data("st") && clearTimeout($(t).data("st")), setTimeout(function () {
//             $(".popover:hover").length || $(t).popover("hide")
//         }, 0)
//     });
//
//     $('.user').mouseleave(function () {
//         var t = this;
//         var l = $(".popover:hover").length;
//         console.log(l);
//         l || $(t).popover("hide");
//
//     });
//
//
// });
