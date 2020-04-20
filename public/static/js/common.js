jQuery("#fa-bars").click(function () {
    console.log(2);
    $.get("{:url('index/guess/sidebarShow')}",
        function (data) {
            console.log("Data Loaded: " + data);
        });
});