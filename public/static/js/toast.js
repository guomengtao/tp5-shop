// $(function () {
//
//     $('form').on('submit', function (e) {
//         console.log("拦截跳转成功，开始处理");
//         console.log($(this).serialize());
//
//         url = $(this).closest("form").attr("action");
//         console.log(url);
//
//         var $button = $(this).find('button')
//             .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>  提交中...')
//             .prop('disabled', true);
//
//
//         $.ajax({
//             url: url,
//             type: "POST",
//             dataType: "json",
//             data: $(this).serialize(),
//             success: function (ret) {
//                 console.log(ret);
//                 console.log(ret.msg);
//                 layer.msg(ret.msg);
//                 if (ret.code == 1) {
//                     console.log("提交成功");
//                     console.log(ret.url);
//                     // 跳转
//                     window.location.href = ret.url;
//                 }
//                 $button.prop('disabled', false).text('提交');
//             },
//             error: function (xhr) {
//                 layer.msg(xhr);
//                 console.log("url访问失败");
//             }
//         });
//
//
//         return false;
//
//     });
//
//
// });
//
//
// // $(function () {
// //     $('form :input:first').select();
// //
// //     $('form').on('submit', function (e) {
// //         e.preventDefault();
// //         var form = this;
// //         var $error = $("#error");
// //         var $success = $("#success");
// //         var $button = $(this).find('button')
// //             .text("安装中...")
// //             .prop('disabled', true);
// //         $.ajax({
// //             url: "",
// //             type: "POST",
// //             dataType: "json",
// //             data: $(this).serialize(),
// //             success: function (ret) {
// //                 if (ret.code == 1) {
// //                     var data = ret.data;
// //                     $error.hide();
// //                     $(".form-group", form).remove();
// //                     $button.remove();
// //                     $("#success").text(ret.msg).show();
// //
// //                     $buttons = $(".form-buttons", form);
// //                     $("<a class='btn' href='./'>访问首页</a>").appendTo($buttons);
// //
// //                     if (typeof data.adminName !== 'undefined') {
// //                         var url = location.href.replace(/install\.php/, data.adminName);
// //                         $("#warmtips").html("温馨提示：请将以下后台登录入口添加到你的收藏夹，为了你的安全，不要泄漏或发送给他人！如有泄漏请及时修改！" + '<a href="' + url + '">' + url + '</a>').show();
// //                         $('<a class="btn" href="' + url + '" id="btn-admin" style="background:#18bc9c">' + "进入后台" + '</a>').appendTo($buttons);
// //                     }
// //                     localStorage.setItem("fastep", "installed");
// //                 } else {
// //                     $error.show().text(ret.msg);
// //                     $button.prop('disabled', false).text("点击安装");
// //                     $("html,body").animate({
// //                         scrollTop: 0
// //                     }, 500);
// //                 }
// //             },
// //             error: function (xhr) {
// //                 $error.show().text(xhr.responseText);
// //                 $button.prop('disabled', false).text("点击安装");
// //                 $("html,body").animate({
// //                     scrollTop: 0
// //                 }, 500 );
// //             }
// //         });
// //         return false;
// //     });
// // });
