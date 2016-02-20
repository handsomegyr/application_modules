$(function(){
    if( pageTrue=="postsinglelist" ){

        $("#div_singletip .gray9").on("click", function(){
            if( $(this).attr("exp")=="1" ){
                $("#div_singletip").animate({height:"40px"},function(){
                    $(this).find(".gray6").hide();
                });
                $(this).attr("exp","2");
                $(this).html('展开<s class="u-personal"></s>');
            }else{
                $(this).attr("exp","1");
                $(this).html('收起<s class="u-personal"></s>');
                $("#div_singletip").animate({height:"258px"}).find(".gray6").show();
            }
        });

        var options = {
            //target: '#request',           // 把服务器返回的内容放进这个id里面显示出来
            beforeSubmit: showRequest,  // 提交前回调
            success: showResponse,      // 提交后回调
            url: "/service/file/upload?upload_path=post",           // 如果重新申明了就会覆盖action这个url
            dataType:  "json"         // 'xml', 'script', or 'json' (接受服务器返回的类型)
        };
        $("#fuploadFile").on("change", function(){

            if( $("#ul_ImgList li").length>10 ){
                FailDialog(250,60,"图片数量不能小于3张大于10张！");
                return;
            }

            var addImg = $(this).val();
            if(addImg!=""||addImg!=null){
                if(addImg.toLowerCase().indexOf(".jpg") != -1 || addImg.toLowerCase().indexOf(".gif") != -1 || addImg.toLowerCase().indexOf(".bmp") != -1){
                    $('#form').ajaxForm(options);
                    $("#btn_submit").click();
                }else{
                    FailDialog(500,60,"上传失败，超时或非gif，jpg，bmp格式图片，请重试！",url);
                }
            }
        });
        //提交前回调
        function showRequest(formData, jqForm, options){
            var queryString = $.param(formData);
            tanFun(180,70,'<div class="z-popUp z-pop-box"><span class="box-loading">上传中，请稍等...</span></div>');
            $("#pageDialogClose").hide();
            return true;
        } 
        //提交后回调
        function showResponse(responseText){
            avatar = responseText.upload.id;
            $("#ul_ImgList").prepend('<li id="'+avatar+'"><img src="/service/file/index?upload_path=post&id='+avatar+'&w=400&h=400&a=1" alt=""><a href="javascript:;" class="z-delete u-personal"></a></li>');
            gotoClick();
        }

        // 删除图片
        $(document).on("click", ".z-delete", function(){
            $(this).parents('li').remove();
        });

        // 提交晒单
        $("#btnSubmit").on("click", function(){
            var postTitle = $("#postTitle").val();
            var postContent = $("#postContent").val();
            var imgLen = $("#ul_ImgList li").length;
            var postCodeID = $.query.get("goods_id");
            var postAllPic = "";

            if( postTitle.length<5&&$("#postTitle").hasClass("focus") ){
                FailDialog(160,60,"字数少于5个！");
                return;
            }
            if( postTitle.length>100 ){
                FailDialog(160,60,"超过字数限制！");
                return;
            }
            if( postContent.length<100&&$("#postContent").hasClass("focus") ){
                FailDialog(180,60,"不能小于100字符！");
                return;
            }
            if( imgLen<4||imgLen>11 ){
                FailDialog(280,60,"图片数量不能小于3张大于10张！");
                return;
            }
            for ( var i=1; i<imgLen; i++) {
                var id = $("#ul_ImgList li").eq(i-1).attr("id");
                postAllPic += ( postAllPic=="" ? id : ","+id );
            };

            var dataObj = {
                postCodeID : postCodeID,
                postTitle : postTitle,
                postContent : postContent,
                postAllPic : postAllPic
            }
            if( !ajaxlack ) return; ajaxlack = false;
            $.ajax({
                url: "/post/service/insertpostsingle",
                type: 'POST',
                dataType: 'json',
                data: dataObj
            })
            .done(function(data) {
                console.log(data);
                if( data.success ){
                    FailDialog(162,60,"上传成功",'/member/index/postsingleadd?goods_id='+$.query.get("goods_id"));
                }else{
                    alert(data.error_msg);
                }
            })
            .fail(function() {
                alert('网络错误！')
            })
            .always(function() {
                ajaxlack = true;
            });     
        });
    }
})