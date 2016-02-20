$(function(){
	// 上传图片
    function showPreview(coords){
        coord = coords;
        console.log(coords);
        if (parseInt(coords.w) > 0){
            var imgSRC = $("#imgPhoto").attr("src");
            var rx160 = 160 / coords.w;
            var ry160 = 160 / coords.h;
            var rx80 = 80 / coords.w;
            var ry80 = 80 / coords.h;
            var rx30 = 30 / coords.w;
            var ry30 = 30 / coords.h;
            $('#img160').attr({"src":imgSRC}).css({
                width: Math.round(rx160 * 400) + 'px',
                height: Math.round(ry160 * 400) + 'px',
                marginLeft: '-' + Math.round(rx160 * coords.x) + 'px',
                marginTop: '-' + Math.round(ry160 * coords.y) + 'px'
            });
            $('#img80').attr({"src":imgSRC}).css({
                width: Math.round(rx80 * 400) + 'px',
                height: Math.round(ry80 * 400) + 'px',
                marginLeft: '-' + Math.round(rx80 * coords.x) + 'px',
                marginTop: '-' + Math.round(ry80 * coords.y) + 'px'
            });
            $('#img30').attr({"src":imgSRC}).css({
                width: Math.round(rx30 * 400) + 'px',
                height: Math.round(ry30 * 400) + 'px',
                marginLeft: '-' + Math.round(rx30 * coords.x) + 'px',
                marginTop: '-' + Math.round(ry30 * coords.y) + 'px'
            });
        }
    }

    var cxTrue = 0;
    // 重新选择
    $("#btnResetUpFile").on("click",function(){
        cxTrue = 1;
        $("#fuploadFace").click();
    });

    var options = {
        //target: '#request',           // 把服务器返回的内容放进这个id里面显示出来
        beforeSubmit: showRequest,  // 提交前回调
        success: showResponse,      // 提交后回调
        url: "/service/file/upload?upload_path=member/avatar",           // 如果重新申明了就会覆盖action这个url
        dataType:  "json"         // 'xml', 'script', or 'json' (接受服务器返回的类型)
    };
    $("#fuploadFace").on("change",function(){
        var addImg = $(this).val();
        if(addImg!=""||addImg!=null){
            if(addImg.toLowerCase().indexOf(".jpg") != -1 || addImg.toLowerCase().indexOf(".gif") != -1 || addImg.toLowerCase().indexOf(".png") != -1 || addImg.toLowerCase().indexOf(".bmp") != -1){
                $('#form').ajaxForm(options);
                $("#btn_submit").click();
            }else{
                alert("上传失败，超时或非gif，jpg，png，bmp格式图片，请重试！");
            }
        }
    });
    //提交前回调
    function showRequest(formData, jqForm, options){
        var queryString = $.param(formData);
        tanFun(180,70,'<div class="z-popUp z-pop-box"><span class="box-loading">上传中，请稍等...</span></div>');
        return true;
    } 
    //提交后回调
    function showResponse(responseText){
        avatar = responseText.upload.id;
        console.log(responseText);
        console.log(responseText.upload.id);    
        $("#fuploadFace, #div_upfile, #pageDialogBG, #pageDialog").hide();    
        if( cxTrue==0 ){
            $("#imgPhoto, #div_operate").show();
            $("#imgPhoto").attr({"src":windwoHost+"service/file/index?upload_path=member/avatar&id="+avatar+"&w=400&h=400&a=1"});
            // 上传图片
            $('#imgPhoto').Jcrop({
                onChange: showPreview,
                onSelect: showPreview,
                setSelect:[100,100,200,200],
                aspectRatio: 1
            });
        }else{
            $(".jcrop-holder img, #img160, #img80, #img30").attr({"src":windwoHost+"service/file/index?upload_path=member/avatar&id="+avatar+"&w=400&h=400&a=1"});
        }
    }

    // 保存裁切的图片
    $("#btnSavePhoto").on("click", function(){
        console.log(coord);
        var _x = coord.x;
        var _y = coord.y;
        var _w = coord.w;
        var _h = coord.h;

        var dataObj = {
            avatar : avatar,
            x : _x,
            y : _y,
            width : _w,
            height : _h
        }

        if( !ajaxlack ) return; ajaxlack = false;
        $.ajax({
            url: "/member/service/uploadphoto",
            type: 'POST',
            dataType: 'json',
            data: dataObj
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
                FailDialog(162,60,"上传成功","/member/admin/membermodify");
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

})