$(document).ready(function() {
    $(".cc_test").click(function(){
        var next = $(this).attr('data-value');
        var sn = parseInt($("#sn").val()) + parseInt(next);
        var bid = $("#bid").val();
        var uid = $("#uid").val();

        $.post("/wldf/do-read", {sn: sn, bid:bid, uid:uid}, function (result) {
            var str = '<section class="read-section jsChapterWrapper"><h3>'+result.section+'</h3><p>'+result.detail+'</p></section>';
            $("#chapterContent").append(str);
            $("#sn").val(result.sn);
        },'json');

    });


});