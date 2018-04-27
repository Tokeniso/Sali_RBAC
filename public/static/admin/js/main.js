/**
 * Created by szh on 2018/4/25.
 */

/**
 * layui图片放大功能
 * @param name  图片名称
 * @param url  图片地址
 */
function pictureHtml(name, url) {
    var load = layer.load();
    var img = new Image();
    img.setAttribute("src", url);
    img.onload = function() {
        var height = img.naturalHeight;
        var width = img.naturalWidth;
        var max = height >= width ? height : width;
        //宽高最大为min的值，大于该值进行缩放
        var min = 700;
        if(max > min){
            if(height >= width){
                height = min;
                width = min/max*width;
            }else{
                width = min;
                height = min/max*height;
            }
        }
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            shadeClose: true,
            area: [ width + 'px', height + 'px'], //宽高
            content: "<img width='"+width+"px' height='"+height+"px' alt=" + name + " title=" + name + " src=" + url + " />"
        });
        layer.close(load);
    };
}
