/**
 * 上传图片
 * @param url   服务器接口地址
 * @param ele   绑定元素
 */
function uploadImg(url, ele){
    layui.use(['jquery', 'upload'], function() {
        /**
         * 页面规则
         */
        // <div class="layui-form-item">
        //     <div class="layui-input-inline" style="width:200px;">
        //         <button type="button" class="layui-btn" data-role="picture">
        //             <i class="layui-icon">&#xe67c;</i>选择封面
        //         </button>
        //     </div>
        //     <div style="display: none;align-items: flex-end" data-role="_picture">
        //         <input type="hidden" name="form[picture]" value=""/>
        //         <img width="100px" height="100px" src=""/>
        //         <a href="javascript:;" data-role="del_picture" style="margin-left: 20px;">删除</a>
        //     </div>
        // </div>
        //
        // 实例
        // uploadImg(url, '[data-role="picture"]');

        var $ = layui.jquery,
            upload = layui.upload;
        /**
         * 上传图片
         */
        var picUpload = typeof url == 'undefined' ? '' : url;

        var uploadInst = upload.render({
            elem: ele //绑定元素
            ,url: picUpload ? picUpload : '' //上传接口
            ,done: function(res){
                layer.msg(res.msg);
                if(res.code){
                    var imgBox = $('[data-role="_picture"]');
                    var img = imgBox.find('img');
                    var input = imgBox.find('input');
                    var src = res.code.url;
                    img.attr('src',src);
                    imgBox.css('display','flex');
                    input.attr('value',res.code.id);
                }
            }
        });

        /**
         * 清除图片
         */
        $('[data-role="del_picture"]').on('click',function () {
            var imgBox = $('[data-role="_picture"]');
            var img = imgBox.find('img');
            var input = imgBox.find('input');
            img.attr('src','');
            imgBox.css('display','none');
            input.attr('value','');
        });
    });
}
