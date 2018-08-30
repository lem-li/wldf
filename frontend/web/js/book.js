$(document).ready(function() {
    skin = [
        "#c3b397",
        "#cad9e7",
        "#d2ecd2",
        "#e6e6e6"
    ];
    ckskin = $.cookie('local-skin');
    if(ckskin){
        var skskinArr = ckskin.split('-');
        $("body").css({"background-color":skskinArr[1]});
        $("#readSetSkin").find("li").each(function () {
            $(this).find(".read-skin-light").css({"color": ""})
            if($(this).find("input").attr('data-index') == skskinArr[0]){
                $(this).find("label").find("svg").css({"color": "#ed424b"})
            }

        });
    }
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

    $("article").click(function(){
        $("footer").toggle();
        $("#readOptSet").hide();
    });
    $("#readBtnSet").click(function () {
        $("#readOptSet").toggle();
    });
    $("#readBtnMode").click(function () {
        if($(this).find("h4").text() == '夜间'){
            $("body").css({"background-color":"black"});
            $(".page-read, .page-read-cover").css({"color":"#faf9f9","opacity":"0.3"});
            $(this).find("h4").text("日间");
            $(this).find(".icon-day").show();
            $(this).find(".icon-night").hide();
        }else {
            $("body").css({"background-color":"#c4b395"});
            $(".page-read, .page-read-cover").css({"color":"black","opacity":"0.8"});
            $(this).find("h4").text("夜间");
            $(this).find(".icon-day").hide();
            $(this).find(".icon-night").show();
        }
    });
    $("#readFontUp").click(function () {
        var cssfontSize=$("article").css('font-size');
        var csslineHeight=$("article").css('line-height');
        var unit=cssfontSize.slice(-2);
        var c=parseFloat(cssfontSize);
        var lineHeight=parseFloat(csslineHeight);
        if(c>28)
            return false;
        var fontSize=c+2;
        lineHeight=lineHeight+2;

        $("article").css('font-size',fontSize+unit);
        $("article").css('line-height',lineHeight+unit);

        var w = (c - 12) / 16 * 279;
        $(".range-track").css({"border-left-width":w+unit});


    });
    $("#readFontDown").click(function () {
        var cssfontSize=$("article").css('font-size');
        var csslineHeight=$("article").css('line-height');
        var unit=cssfontSize.slice(-2);
        var c=parseFloat(cssfontSize);
        var lineHeight=parseFloat(csslineHeight);
        if(c<'16')
            return false;
        var fontSize=c-2;
        lineHeight=lineHeight-2;

        $("article").css('font-size',fontSize+unit);
        $("article").css('line-height',lineHeight+unit);

        var w = (c - 16) / 16 * 279;
        $(".range-track").css({"border-left-width":w+unit});
    });

    $("#readSetSkin .btn-group-cell").click(function () {
        $.cookie('local-skin',$(this).find("input").attr('data-index') + '-' + skin[$(this).find("input").attr('data-index')],{ expires: 30, path: '/' });
        $("body").css({"background-color":skin[$(this).find("input").attr('data-index')]});
    });

    $(".read-set-font-opacity").click(function () {

    });
    $("#readOpacityDown").click(function () {

    });
});