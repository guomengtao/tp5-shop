$(function () {



    $('[data-toggle="popover"]').mouseover(function () {

        var e = this;
        $(e).data('leave', false);
        var loadHtml = ' <div class="spinner-grow text-warning" role="status">\n' +
            '  <span class="sr-only">Loading...</span>\n' +
            '</div>';
        $(this).popover({
            html: true,
            trigger: 'manual',
            placement: 'right',
        });
        $('[data-toggle="popover"]').popover('hide');

        var id = $(e).data("id");

        $(e).attr('data-content', loadHtml);

        $(e).popover('show');

        var st = setTimeout(function () {
            $.ajax({
                url: '/index/index/user_info/',
                type: 'POST',
                data: {
                    user_id: id,
                },
                success: function (ret) {


                    $(e).attr('data-content', ret);

                    l = $(e).data('leave');
                    l || $(e).popover('show');

                    $('.popover').mouseleave(function () {
                            $('[data-toggle="popover"]').popover('hide');
                        }
                    );


                },

            })
        }, 500);


        $(e).data('st', st);

    });


    $('[data-toggle="popover"]').mouseleave(function () {

        var e = this;
        l = $(".popover:hover").length;

        l || $(e).popover('hide');
        l || clearTimeout($(e).data('st'));
        $(e).data('leave', true);
    });

});