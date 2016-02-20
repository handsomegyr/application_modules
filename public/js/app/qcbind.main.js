require("../libs/query");
require("./public");
$(function(){

	// 获取验证码
	var snTrue = true;
	$("#btnSend").on(click, function(){
		var $this = $(this);
		var $name = $("#txtUserName");
		var username = $name.val();
		var testEmail = _email.test(username);
		var ajaxNum;

		// 判断是否是手机
		if( username.length==11 && _sizeTel.test(username) ){
			ajaxNum = 1;
	    }else if( testEmail ){
	    	ajaxNum = 0;
	    }else{
	    	$name.focus();
	    	lifocus(0,'请输入正确的手机号或邮箱地址');
	        return false;
	    }
	    if( !ajaxlack ||!snTrue ) return; ajaxlack = false; snTrue = false;
	    $name.parents("li").find(".orange").hide();

	    ajaxCodeFun(username,ajaxNum,function(){
			$this.addClass("current");
        	setIntervalFun(120,function(_TIME){
        		$this.html('重新发送('+_TIME+')');
        		if( _TIME<=0 ){
        			$this.removeClass("current");
        			$this.html('重新发送');
        			snTrue = true;
        		}
        	});
		});
	});

	$('#username, #pwd').keydown(function(e){
		if( e.keyCode==13 ){
			loginFun();
		}
	});

	// 登录函数
	function loginFun(){
		var username = $("#txtUserName").val();
		var password = $("#txtPwd").val();
		var testEmail = _email.test(username);
		var dataObj;		
		// 判断是否是手机
		if( username.length==11 && _sizeTel.test(username) ){
			dataObj = {
				mobile : username,
				password : password
			}
	    }else if( testEmail ){
	    	dataObj = {
				password : password,
				email : username
			}
	    }else{
	    	$("#txtUserName").focus();
	    	lifocus(0,'请输入正确的手机号或邮箱地址');
	        return;
	    }	

	    if( password=='' ){
	    	$("#txtPwd").focus();
	    	lifocus(1,'密码不正确！');
	    	return;
		}	

		if( !ajaxlack ) return; ajaxlack = false;
		$("#btnBind").html('正在绑定···').css({"letter-spacing":"0"});
		$.ajax({
	        url: "/member/service/qcbindaccount",
	        type: 'POST',
	        dataType: 'json',
	        data: dataObj
	    })
	    .done(function(data) {
	        console.log(data);
	        if( data.success ){
	        	FailDialog(160,60,'绑定成功！','/');
	        }else{
	        	$("#txtPwd").focus();
	    		lifocus(2,'密码不正确！');
	        }
	    })
	    .fail(function() {
	        alert('网络错误！')
	    })
	    .always(function() {
	    	ajaxlack = true;
	    	$("#btnBind").html('绑定').attr("style","");
	    });
	}

	// 验证
	function verificationFun(_focus){
		var txtUserName = $("#txtUserName").val();
		var txtPwd = $("#txtPwd").val();
		var txtConPwd = $("#txtConPwd").val();
		var txtCode = $("#txtCode").val();
		var vcode = $("#txtvcode").val();
		var testEmail = _email.test(txtUserName);
		var dataObj;

		if( txtUserName.length==11 && _sizeTel.test(txtUserName) ){
			dataObj = {
				mobile : txtUserName,
				password : txtPwd,
				password_confirm : txtConPwd,
				captcha : txtCode,
				vcode : vcode
			}
	    }else if( testEmail ){
	    	dataObj = {
				password : txtPwd,
				email : txtUserName,
				password_confirm : txtConPwd,
				captcha : txtCode,
				vcode : vcode
			}
	    }else{
	    	$("#txtUserName").focus();
	    	lifocus(0,'请输入正确的手机号或邮箱地址');
	    	return;
	    }

	    if( txtUserName=='' ){
	    	$("#txtUserName").focus();
	    	lifocus(0,'请输入您的手机号/邮箱地址');
	    	return;
	    }

	    if( vcode=="" ){
	    	$("#txtvcode").focus();
	    	lifocus(1,'请认真核对验证码是否正确');
	    	return;
	    }

	    if( txtPwd=='' ){
	    	$("#txtPwd").focus();
	    	lifocus(2,'请设置登录密码');
	    	return;
	    }

	    if( txtConPwd==''||txtConPwd.length<8||txtConPwd.length>20 ){
	    	$("#txtPwd").focus();
	    	lifocus(2,'密码由8-20位字母');
	    	return;
	    }

	    if( txtPwd!=txtConPwd ){
	    	$("#txtConPwd").focus();
	    	lifocus(3,'两次输入的密码不一致');
	    	return;
	    }

	    if( txtCode=='' ){
	    	$("#txtCode").focus();
	    	lifocus(4,'请输入验证码');
	    	return;
	    }

	    agreeFun(dataObj);
	}

	function lifocus(eq,text){
		var $li = $(".register-form-con li");
		$li.eq(eq).addClass("enter-focus error-text").find(".orange").html(errorIcon+text).show();
	}

	// 注册函数
	function agreeFun(dataObj){
		if( !ajaxlack ) return; ajaxlack = false;
		$(".register-form-con li").find('.orange').hide();
		$.ajax({
	        url: "/member/service/qcbindaccount",
	        type: 'POST',
	        dataType: 'json',
	        data: dataObj
	    })
	    .done(function(data) {
	        console.log(data);
	        if( data.success ){
	        	FailDialog(160,60,'绑定成功！','/');
	        }else{
	        	console.log(data.error_msg);
	        	if( data.error_code==513 ){
	        		$("#txtvcode").focus();
	        		lifocus(1,'请认真核对验证码是否正确');
	        	}else if( data.error_code==597 ){
					lifocus(4,'请认真核对验证码是否正确');
	        	}else{
	        		alert('提交失败，请重试！');
	        	}
	        }
	    })
	    .fail(function() {
	        alert('网络错误！')
	    })
	    .always(function() {
	    	ajaxlack = true;
	    });
	}


	// 刷新验证码
	$("#imgCode, #btnChangeCode").on(click, function(){
		$("#imgCode").attr({"src":$("#imgCode").attr("src")});
	});


	// 账号验证
	var verifiedby = true;
	$("#txtUserName").on('blur', function(){
		var $this = $(this);
		var thisParent = $this.parent();
		var txtUserName = $this.val();
		var testEmail = _email.test(txtUserName);

		if( txtUserName.length==11 && _sizeTel.test(txtUserName) ){
			ajaxNum = 0;
	    }else if( testEmail ){
	    	ajaxNum = 1;
	    }else{
	    	if( $this.val()=='' ) return;
	    	lifocus(0,'请输入正确的手机号或邮箱地址');
	    	return;
	    }

	    checkEmailMobileFun(txtUserName,ajaxNum,function(data){
	    	if( data.success ){
	    		verifiedby = false;
	    		$(".liHide").show();
	        }else{
	        	verifiedby = true;
	        	$(".liHide").hide();
	        }
	    });
	    
	});

	// 已有账户还是注册账户
	$("#btnBind").on('click', function(){
		if( verifiedby ){ //已有账户
			loginFun();
		}else{ //没有账户
			verificationFun();
		}
	});
	



	// 失去焦点
	$("#txtPwd").on("blur", function(){
		var txtPwd = $("#txtPwd").val();
		var txtConPwd = $("#txtConPwd").val();
		if( txtPwd=='' ) return;
		$("#pwdStrength").hide();
		if( txtPwd.length<8||txtPwd.length>20 ){
			lifocus(2,'密码由8-20位字母');
			$(".register-form-con li:eq(1)").removeClass('correct-text');
			return;
		}
		if( txtPwd.length>8&&txtPwd.length<20 ){
			$(".register-form-con li:eq(1)").addClass('correct-text').find(".passport-icon").show();
			return;
		}
	});

	// 密码强度验证
	$("#txtPwd").on("keyup", function(){
		var txtPwd = $("#txtPwd").val();
		var $div = $("#pwdStrength");
		if( txtPwd.length>8&&txtPwd.length<=20 ){
			var qiang = passwordsFun(txtPwd);
			console.log(passwordsFun(txtPwd));
			$div.show();
			if( qiang=="弱" ){
				$div.html(yellowTxt(0));
			}else if( qiang=="中" ){
				$div.html(yellowTxt(1));
			}else{
				$div.html(yellowTxt(2));
			}
		}else{
			$div.html(yellowTxt(0));
		}
	});

	$("#txtConPwd").on("blur", function(){
		var txtPwd = $("#txtPwd").val();
		var txtConPwd = $("#txtConPwd").val();
		if( txtConPwd=='' ) return;
		if( txtPwd==txtConPwd ){
	    	$(".register-form-con li:eq(2), .register-form-con li:eq(3)").addClass('correct-text').find(".passport-icon").show();
	    }else{
	    	$("#txtConPwd").parent().addClass("enter-focus error-text").find(".orange").html(errorIcon+'两次输入的密码不一致').show();
	    	$("#txtConPwd").parent().removeClass('correct-text');
	    }
	});
	








});