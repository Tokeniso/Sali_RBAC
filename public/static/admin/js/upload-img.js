/**
 * 多图上传
 * @param url  多图上传地址
 * @param ele  layui获取input element
 * @param box_ele   存放图片容器 element
 * @param opts   配置项
 */
function uploadImges(url, ele, box_ele, opts){
    var options = {
        'name' : 'picture',
        'multiple' : true,
    };
    layui.use(['jquery', 'upload'], function() {
        /**
         * 页面规则
         */
            // <div class="layui-form-item" data-role="pictures_box">
            //     <div class="layui-input-inline" style="width:200px;">
            //         <button type="button" class="layui-btn" data-role="pictures">
            //         <i class="layui-icon">&#xe67c;</i>选择封面
            //     </button>
            //     </div>
            //     <div class="gl-picture-upload">
            //         <input type="hidden" name="form[picture]" value=""/>
            //         <img width="100px" height="100px" src=""/>
            //         <a class="iconfont icon-2guanbi gl-picture-remove" href="javascript:;" data-role="del_pictures"></a>
            //     </div>
            // </div>
            //
            // 实例
            // uploadImges(url, '[data-role="pictures_box"]');

        var $ = layui.jquery,
            upload = layui.upload;
        /**
         * 上传图片
         */
        var picUpload = typeof url == 'undefined' ? '' : url;
        var index;
        var uploadInsts = upload.render({
            elem: ele //绑定元素
            ,url: picUpload ? picUpload : '' //上传接口
            ,field: 'pictures'
            ,multiple: options.multiple
            ,before: function(){
                index = layer.load();
            }
            ,done: function(data){
                layer.msg(data.info);
                if(data.code){
                    var imgBox = $(box_ele);
                    var pictures = data.data;

                    var len = pictures.length;

                    //循环输出图片
                    for (var i = 0; i < len; i++) {
                        if (pictures[i].error == false) {
                            if (!options.multiple) {
                                imgBox.find(".gl-picture-upload").remove();
                            }
                            imgBox.append('<div class="gl-picture-upload">'+
                                '<input type="hidden" name="form['+name+']" value="'+ pictures[i].id +'"/>'+
                                '<a href="javascript:pictureHtml(\''+ pictures[i].path +'\', \''+ pictures[i].path +'\');"><img width="100px" height="100px" src="'+ pictures[i].path +'"/></a>'+
                                '<span class="iconfont icon-2guanbi gl-picture-remove" data-role="del_pictures">xx</span>'+
                                '</div>');
                            if (!options.multiple) {
                                break;
                            }
                        }
                    }
                }
                layer.close(index);
            }
            ,error: function(){
                layer.msg('上传错误！');
                layer.close(index);
            }
        });

        /**
         * 清除图片
         */
        $('[data-role="del_pictures"]').on('click',function () {
            alert(222);
            var imgBox = $(this).parent();
            imgBox.remove();
        });
    });
}