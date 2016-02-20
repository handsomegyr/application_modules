require("./public");
require("./cartAjax");
require("./bottomFun");
$(function(){

	// 刷新验证码
	$("#Zshowimg, #ZshowimgA").on(click, function(){
		$("#Zshowimg").attr({"src":$("#Zshowimg").attr("src")});
	});
	
	$("#butSubmit").on("click",function(){
		var theme = $("input[name='Zti']:checked").val();
		var name = $("#Zname").val();
		var telephone = $("#Zphone").val();
		var email = $("#Zemail").val();
		var content = $("#Zcontent").val();
		var captcha = $("#Zyanma").val();
		if( !_email.test(email) ){
			FailDialog(160,60,'邮箱不正确！');
			return;
		}
		if( content=="" ){
			FailDialog(160,60,'内容不能为空！');
			return;
		}
		if( captcha=="" ){
			FailDialog(160,60,'验证码不能为空！');
			return;
		}
		$("#butSubmit").off("click");
        $.ajax({
            url: "/site/service/suggest",
            type: 'POST',
            dataType: 'json',
            data: { theme:theme, name:name, telephone:telephone, email:email, content:content, captcha:captcha }
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
            	FailDialog(160,60,'提交成功！',url);
            }else{
            	FailDialog(160,60,'提交失败！');
            }
        })
        .fail(function() {

        })
        .always(function() {

        });
	});





































})