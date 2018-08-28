$(document).ready(function() {
    $(".cc_test").click(function(){
        var next = $(this).attr('data-value');
        var sn = parseInt($("#sn").val()) + parseInt(next);
        var bid = $("#bid").val();
        var uid = $("#uid").val();

        $.post("/wldf/do-read", {sn: sn, bid:bid, uid:uid}, function (result) {
            var str = '<section class="read-section jsChapterWrapper"><h3>'+result.section+'</h3><p>'+result.detail+'</p></section>';
            if(next == 1){
                $("#chapterContent").append(str);
            }else {
                $("#chapterContent").prepend(str);
            }

            $("#sn").val(result.sn);
        },'json');

    });

    $("content").click(function(){
        $("footer").toggle();
    });
    $("#readBtnMode").click(function () {
        if($(this).find("h4").text() == '夜间'){
            $("body").css({"background-color":"black"});
            $(".page-read, .page-read-cover").css({"color":"#f6f7f9"});
            $(this).find("h4").text("日间");
            $(this).find(".icon-day").show();
            $(this).find(".icon-night").hide();
        }else {
            $("body").css({"background-color":"#c4b395"});
            $(".page-read, .page-read-cover").css({"color":"black"});
            $(this).find("h4").text("夜间");
            $(this).find(".icon-day").hide();
            $(this).find(".icon-night").show();
        }

    });

});