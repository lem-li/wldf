<?php
/**
 * Created by PhpStorm.
 * User: liwenling
 * Date: 2018/8/31
 * Time: 下午4:58
 */

?>
<div id="iSlider-wrapper">




</div>


<script type="text/javascript" src="http://eux.baidu.com/iSlider/demo/public/js/iSlider.min.js"></script>
<script type="text/javascript" src="http://eux.baidu.com/iSlider/demo/public/js/iSlider.animate.min.js"></script>
<script>
    var list = [
        {
            content: '<div style="font-size:4em;color:white;text-align: center">fawefawefawegf</div>'
        },
        {
            content: '<div style="font-size:4em;color:white;text-align: center">HTML String</div>'
        },
        {
            content: (function () {
                var dom = document.createElement('div');
                dom.innerHTML = 'Element';
                dom.style.cssText = 'font-size:3em;color:rgb(230, 230, 63);';
                return dom;
            })()
        },
        {
            content: (function () {
                var frag = document.createDocumentFragment();
                var img = new Image()
                var dom = document.createElement('div');
                dom.innerHTML = 'Fragment';
                dom.style.cssText = 'font-size:3em;color:rgb(230, 63, 230);';
                frag.appendChild(dom);
                return frag;
            })()
        },
        {
            content: document.querySelector('#hidden-space > p')
        },
        {
            content: '' +
            '<div style="padding-top:.2em;font-size:3em;color:rgb(230, 63, 230);position:absolute;top:0;left:0;height:100%;width:100%;z-index:1">' +
            '<span style="padding:.2em;background-color:black;">Iframe</span>' +
            '</div>' +
            '<iframe style="position:relative;z-index:0;height:100%;" src="http://mobile.baidu.com"></iframe>'
        }
    ];
    console.log(list);

    var S = new iSlider(document.getElementById('iSlider-wrapper'), list, {
        isLooping: 0,
        isOverspread: 1,
        isAutoplay: 0,
        animateTime: 800,
        animateType: '3d'
    });

    S.on('slideChanged', function () {
        alert(12);
    });

</script>
