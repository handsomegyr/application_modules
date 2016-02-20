$(function(){

	/*******************************************************常用设置**************************************************/
	if( pageTrue=="noticesettings" ){
		$("#btnSave").on("click",function(){
	    	var ckSysMsg = $("#ckSysMsg").find("input").val();
	    	var ckWxMail = $("#ckWxMail").find("input").val();
	    	var dataObj = {
	    		sysMsgSet:ckSysMsg,
	    		wxMailSet:ckWxMail
	    	}
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/membercenternoticeset",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	FailDialog(162,60,"修改成功",url);
		        }else{
		        	FailDialog(162,60,"修改失败");
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		});
	}
	/*******************************************************常用设置结束*********************************************/

	/*******************************************************隐私设置************************************************/
	if( pageTrue=="privacysettings" ){
		//http://webcms.didv.cn/member/service/membercenterupdateprivset?msgSet=1&areaSet=1&searchSet=1&buySet=1&rafSet=1&postSet=1&buyShowNum=1&rafShowNum=1&postShowNum=1
		$("#btnSaveSettings").on("click",function(){
	    	var msgSet = $("#MsgVal .current").attr("value");
	    	var areaSet = $("#AreaVal .current").attr("value");
	    	var searchSet = $("#SearchVal .current").attr("value");
	    	var buySet = 0;
	    	var	buyShowNum = 0;
	    	var	rafSet = 0;
	    	var	rafShowNum = 0;
	    	var	postSet = 0;
	    	var	postShowNum = 0;

	    	var BuyVal = $("#BuyVal .current").siblings("span").find(".current").attr("value");
	    	console.log(BuyVal);
	    	if( BuyVal==0 ){
	    		buySet = 0;
	    		buyShowNum = 0;
	    	}else if( BuyVal==1 ){
	    		buySet = 0;
	    		buyShowNum = $("#buyShowOnlyVal").val();
	    	}else if( BuyVal==2 ){
	    		buySet = 1;
	    		buyShowNum = 0;
	    	}else if( BuyVal==3 ){
	    		buySet = 1;
	    		buyShowNum = $("#buyFriendOnlyVal").val();
	    	}else if( BuyVal==undefined ){
	    		buySet = 2;
	    		buyShowNum = 0;
	    	}

	    	if( buyShowNum=="" ){
	    		buyShowNum = 10;
	    	}

	    	var rafShowVal = $("#rafShowVal .current").siblings("span").find(".current").attr("value");
	    	console.log(rafShowVal);
	    	if( rafShowVal==0 ){
	    		rafSet = 0;
	    		rafShowNum = 0;
	    	}else if( rafShowVal==1 ){
	    		rafSet = 0;
	    		rafShowNum = $("#rafShowOnlyVal").val();
	    	}else if( rafShowVal==2 ){
	    		rafSet = 1;
	    		rafShowNum = 0;
	    	}else if( rafShowVal==3 ){
	    		rafSet = 1;
	    		rafShowNum = $("#rafFriendOnlyVal").val();
	    	}else if( rafShowVal==undefined ){
	    		rafSet = 2;
	    		rafShowNum = 0;
	    	}

	    	if( buyShowNum=="" ){
	    		rafShowNum = 10;
	    	}

	    	var postShowVal = $("#postShowVal .current").siblings("span").find(".current").attr("value");
	    	console.log(postShowVal);
	    	if( postShowVal==0 ){
	    		postSet = 0;
	    		postShowNum = 0;
	    	}else if( postShowVal==1 ){
	    		postSet = 0;
	    		postShowNum = $("#postShowOnlyVal").val();
	    	}else if( postShowVal==2 ){
	    		postSet = 1;
	    		postShowNum = 0;
	    	}else if( postShowVal==3 ){
	    		postSet = 1;
	    		postShowNum = $("#postFriendOnlyVal").val();
	    	}else if( postShowVal==undefined ){
	    		postSet = 2;
	    		postShowNum = 0;
	    	}

	    	if( postShowNum=="" ){
	    		postShowNum = 10;
	    	}

	    	var dataObj = {
	    		msgSet:msgSet,
	    		areaSet:areaSet,
	    		searchSet:searchSet,
	    		buySet:buySet,
	    		buyShowNum:buyShowNum,
	    		rafSet:rafSet,
	    		rafShowNum:rafShowNum,
	    		postSet:postSet,
	    		postShowNum:postShowNum
	    	}
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/membercenterupdateprivset",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	FailDialog(162,60,"修改成功",url);
		        }else{
		        	FailDialog(162,60,"修改失败");
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		});
	}
	/*******************************************************隐私设置结束******************************************/

    /*******************************************************账户安全********************************************/
    if( pageTrue=="security" ){
	    var pwdT;
	    $("#btnSubmitSave").on("click",function(){

	    	var userOldPwd = $("#OldPass").val();
	    	var NewPass = $("#NewPass").val();
	    	var NewPassAgain = $("#NewPassAgain").val();

	    	$("#pwdStrength").hide();

	    	pwdT = setTimeout(function(){
	    		$("#div_tips1, #div_tips2, #div_tips3").html('');
				clearTimeout(pwdT);
			},3000);

	    	if( userOldPwd=="" ){
	    		$("#div_tips1").html('<div class="form-orange-tips">原密码不能为空</div>');
	    		return;
	    	}else{
	    		$("#div_tips1").html('');
	    	}

	    	if( NewPass==""||NewPass.length<8||NewPass.length>20 ){
	    		$("#div_tips2").html('<div class="form-orange-tips">密码由8-20位字母、数字或符号两种或以上组合</div>');
	    		return;
	    	}else{
	    		$("#div_tips2").html('');
	    	}

	    	if( NewPassAgain=="" ){
	    		$("#div_tips3").html('<div class="form-orange-tips">确认密码不能为空</div>');
	    		return;
	    	}else{
	    		$("#div_tips3").html('');
	    	}

	    	if( NewPassAgain!=NewPass ){
	    		$("#div_tips3").html('<div class="form-orange-tips">两次密码输入不一致,请重新输入</div>');
	    		return;
	    	}else{
	    		$("#div_tips3").html('');
	    	}

	    	var dataObj = {
	    		userOldPwd:userOldPwd,
	    		userNewPwd:NewPass
	    	}


			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/updateuserpwd",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	FailDialog(162,60,"修改成功",'/member/passport/login');
		        }else{
		        	if( data.errorCode==514 ){
		        		$("#div_tips1").html('<div class="form-orange-tips">原密码错误,请重新输入</div>');
		        	}else{
		        		FailDialog(162,60,"修改失败");
		        	}
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		});

		// 密码强度验证
		$("#NewPass").on("keyup", function(){
			var NewPass = $("#NewPass").val();
			var $div = $("#pwdStrength");
			console.log(NewPass)
			if( NewPass.length>8&&NewPass.length<=20 ){
				var qiang = passwordsFun(NewPass);
				console.log(passwordsFun(NewPass));
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

		// 登录密码保护
		$("#btnSetWxMsg").on("keyup", function(){
			
		});		

		window.checkingokFun = function(){
			setIntervalFun(10,function(_TIME){
        		$("#spanNum").html(_TIME);
        		if( _TIME==0 ){
        			window.location.href = '/member/security/index';
        		}
        	});
		}

		// 登录保护 开启
		$("#btnSetWxMsg").on("click",function(){
			tanFun(282,146,'<div class="z-pop-box clrfix"><span class="gray3">您确定要开启登录提醒吗？</span><a id="btn01" href="javascript:;" onclick="gotoClick()" title="取消" class="z-btn-cancel">取消</a><a id="setlogintipOK" href="javascript:;" title="确定" class="z-btn-determine">确定</a></div>');
		});
		$(document).on("click","#setlogintipOK",function(){
			setlogintipFun(1);
		});


		// 登录保护 关闭
		$("#btnCloseWxMsg").on("click",function(){
			tanFun(282,146,'<div class="z-pop-box clrfix"><span class="gray3">您确定要关闭登录提醒吗？</span><a id="btn01" href="javascript:;" onclick="gotoClick()" title="取消" class="z-btn-cancel">取消</a><a id="setlogintipNo" href="javascript:;" title="确定" class="z-btn-determine">确定</a></div>')
		});
		$(document).on("click","#setlogintipNo",function(){
			setlogintipFun(0);
		});

		function setlogintipFun(is_open){
		    $.ajax({
		        url: '/member/service/setlogintip',
		        type: 'POST',
		        dataType: 'json',
		        data: { is_open:is_open }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	FailDialog(162,60,"设置成功",window.location.href);
		        }else{
		        	FailDialog(162,60,"设置失败",window.location.href);
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		}





	}

	if( pageTrue=='userauth' ){
		// 邮箱或手机验证
		var UserAuth = $.query.get('UserAuth');
		var snTrue = true;
		var regesterBy;
		$("#btnGetCode").on(click, function(){
			var $this = $(this);
			var ajaxNum;
			regesterBy = $this.attr("regesterBy");
			if( !snTrue ) return; snTrue = false;
			if( UserAuth=="email" ){
				ajaxNum = 0;
			}else if( UserAuth=="mobile" ){
				ajaxNum = 1;
			}  
			codeNew = new ajaxCodeFun(regesterBy,ajaxNum,function(){
				$("#btnSubmit").removeClass('gray-btn');
				$this.addClass("current");
	        	setIntervalFun(120,function(_TIME){
	        		$this.html('重新发送('+_TIME+')');
	        		if( _TIME<=0 ){
	        			$this.html('重新发送');
	        			$this.removeClass("current");
	        			snTrue = true;
	        		}
	        	});
			});
		});

		// 校验绑定验证码是否正确
		$("#btnSubmit").on(click, function(){
			var txtCheckCode = $("#txtCheckCode").val();
			var ajaxNum = 0;
			if( $(this).hasClass("gray-btn") ){
				return;
			}
			if( txtCheckCode.length!=6 ){
			    $('#div_tips').html('<div class="form-orange-tips">请输入正确验证码！</div>');
			    return;
			}else{
				$('#div_tips').html('');
			}
			if( UserAuth=="email" ){
				ajaxNum = 0;
			}else if( UserAuth=="mobile" ){
				ajaxNum = 1;
			}  
			if( !ajaxlack ) return; ajaxlack = false; 
			txtCheckFun(regesterBy,txtCheckCode,ajaxNum,function(data){
				if( data.success ){
					$("#BindingSend").show().siblings().hide();
					snTrue = true;
					codeNew = null;
		        }else{
		        	$('#div_tips').html('<div class="form-orange-tips">请输入正确验证码！</div>');
		        }
			});
		});

		/**----- 验证通过开始绑定 -----**/
		var verifiedby = true;
		// 账号验证
		$("#txtSaveSN").on('blur', function(){
			var $this = $(this);
			var txtUserName = $this.val();
			var testEmail = _email.test(txtUserName);
			if( txtUserName.length==11 && _sizeTel.test(txtUserName) ){
				ajaxNum = 0;
		    }else if( testEmail ){
		    	ajaxNum = 1;
		    }else{
		    	if( $this.val()=='' ) return;
		    	if( UserAuth=="email" ){
					$("#div_tips1").html('<div class="form-orange-tips">请输入正确的手机号</div>');
				}else if( UserAuth=="mobile" ){
					$("#div_tips1").html('<div class="form-orange-tips">请输入正确的邮箱地址</div>');
				}
		    	return;
		    }

		    checkEmailMobileFun(txtUserName,ajaxNum,function(data){
		    	if( data.success ){
		    		verifiedby = false;
		    		$("#div_tips1").html('');
		        }else{
		        	verifiedby = true;
		        	if( UserAuth=="email" ){
						$("#div_tips1").html('<div class="form-orange-tips">手机号已存在</div>');
					}else if( UserAuth=="mobile" ){
						$("#div_tips1").html('<div class="form-orange-tips">邮箱地址已存在</div>');
					}
		        }
		    });
		    
		});

		// 绑定 获取验证码
		$("#btnSendSN").on(click, function(){
			var $this = $(this)
			var txtSaveSN = $("#txtSaveSN").val();
			var dataObj;
			var ajaxNum;
			if( !ajaxlack || !snTrue || verifiedby ) return;
			ajaxlack = false;  snTrue = false;
			if( UserAuth=="email" ){
				ajaxNum = 1;
			}else if( UserAuth=="mobile" ){
				ajaxNum = 0;
			}  
			ajaxCodeFun(txtSaveSN,ajaxNum,function(){
				$("#butSaveSubmit").removeClass('gray-btn');
				$this.addClass("current");
	        	setIntervalFun(120,function(_TIME){
	        		$("#btnSendSN").html('重新发送('+_TIME+')');
	        		if( _TIME<=0 ){
	        			$this.removeClass("current");
	        			$this.html('重新发送');
	        			snTrue = true;
	        		}
	        	});
			});
		});

		// 绑定 开始绑定
		$("#butSaveSubmit").on(click, function(){
			var txtSaveSN = $("#txtSaveSN").val();
			var vcode = $("#txtSN").val();
			var testEmail = _email.test(txtSaveSN);
			var dataObj;
			if( $(this).hasClass("gray-btn") ){
				return;
			}
			if( txtSaveSN.length==11 && _sizeTel.test(txtSaveSN) ){
				dataObj = {
					mobile : txtSaveSN,
					vcode : vcode
				}
				ajaxURL = "/member/service/bindmobile";
		    }else if( testEmail ){
		    	dataObj = {
		    		email : txtSaveSN,
					vcode : vcode
				}
				ajaxURL = "/member/service/bindemail";
		    }else{
		    	$("#txtSaveSN").focus();
		    	if( UserAuth=="email" ){
					$("#div_tips1").html('<div class="form-orange-tips">请输入正确的手机号</div>');
				}else if( UserAuth=="mobile" ){
					$("#div_tips1").html('<div class="form-orange-tips">请输入正确的邮箱地址</div>');
				} 
		        return false;
		    }

		    if( vcode.length!=6 ){
			    $('#div_tips2').html('<div class="form-orange-tips">请输入正确验证码！</div>');
			    return;
			}

		    if( !ajaxlack ) return; ajaxlack = false;
		    $.ajax({
		        url: ajaxURL,
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	$("#SendSuccess").show().siblings().hide();
		        	setIntervalFun(10,function(_TIME){
		        		$("#spanNum").html(_TIME);
		        		if( _TIME<=0 ){
		        			window.location.href = '/member/admin/membermodify';
		        		}
		        	});
		        }else{

		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		});

		// 校验绑定验证码是否通过函数
		function txtCheckFun(dataPOST,sn,ajaxNum,Callback){
			var ajaxURL;
			var dataObj;
			if( ajaxNum==0 ){
				ajaxURL = "/service/vcode/verifyemailsn";
				dataObj = {
					userEmail:dataPOST,
					sn:sn
				}
			}else if( ajaxNum==1 ){
				ajaxURL = "/service/vcode/sendcodesms";
				dataObj = {
					mobile:dataPOST,
					sn:sn
				}
			}
			$.ajax({
		        url: ajaxURL,
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        Callback(data);
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });	
		}
	}

	// 小额设置
	if( pageTrue=='setsmallmoney' ){
		var isopen = $.query.get('isopen');
		// http://webcms.didv.cn/member/service/setsmallmoney?money=500&is_open=1&vcode=xx&key=xxx
		// 小额设置
		var moneyVal = $("#ulItems li.current").attr('value');
		var money = (moneyVal==undefined) ? $("#emMoney").text() : moneyVal;
		$("#ulItems li").on("click",function(){
			$(this).addClass("current").siblings().removeClass("current");
			$("#liDefined").find("span").show();
			$("#liDefined").find("p").hide();
		});

		$("#liDefined").on("click",function(){
			$(this).find("span").hide();
			$(this).find("p").show();
		});

		if( isopen==0 ){
			$(".s-much-inner .div").hide();
			// $("#isopenNull").show();
		}else if( isopen==1 ){

		}else if( isopen==2 ){
			var smallmoney = $("#ulItems").attr("smallmoney");
			var $ulItems = $("#ulItems li")
			if( smallmoney==50 ){
				$ulItems.eq(0).addClass("current").siblings().removeClass("current");
			}else if( smallmoney==100 ){
				$ulItems.eq(1).addClass("current").siblings().removeClass("current");
			}else if( smallmoney==200 ){
				$ulItems.eq(2).addClass("current").siblings().removeClass("current");
			}else if( smallmoney==500 ){
				$ulItems.eq(3).addClass("current").siblings().removeClass("current");
			}else{
				$ulItems.eq(4).addClass("current").siblings().removeClass("current");
				$("#liDefined").find("span").hide();
				$("#liDefined").find("p").show();
				$("#emMoney").html(smallmoney);
			}
		}


		// 邮箱或手机验证
		var snTrue = true;
		var regesterBy;
		var codeNew;
		$("#btnGetCode").on(click, function(){
			var $this = $(this);
			var ajaxNum;
			regesterBy = $this.attr("regesterBy");
			if( !snTrue ) return; snTrue = false;
			if( regesterBy.indexOf("@")!=-1 ){
				ajaxNum = 0;
			}else{
				ajaxNum = 1;
			}  
			codeNew = new ajaxCodeFun(regesterBy,ajaxNum,function(){
				$("#btnSubmit").removeClass('gray-btn');
				$this.addClass("current");
	        	setIntervalFun(120,function(_TIME){
	        		$this.html('重新发送('+_TIME+')');
	        		if( _TIME<=0 ){
	        			$this.html('重新发送');
	        			$this.removeClass("current");
	        			snTrue = true;
	        		}
	        	});
			});
		});

		$("#btnSubmit").on(click, function(){
			var txtCheckCode = $("#txtCheckCode").val();
			if( $(this).hasClass("gray-btn") ) return;
			if( txtCheckCode.length!=6 ){
				$("#div_tips").html('<div class="form-orange-tips">验证码不正确！</div>');
				setIntervalFun(3,function(){
						$("#div_tips").html('');
					});
				return;
			}
			if( isopen==0 ){
				var dataObj = {
		    		money:money,
		    		key:regesterBy,
		    		is_open:0,
		    		vcode:txtCheckCode
		    	}
				setsmallmoneyAjaxFun(dataObj,function(data){
					console.log(data);
					if( data.success ){
		                FailDialog(162,60,"设置成功",'/member/security/index');
		            }else{
		                // FailDialog(162,60,"设置失败",'/member/security/index');
		            }
				});
			}else if( isopen==1||isopen==2 ){
				var dataObj = {
		    		money:money==""?1:money,
		    		key:regesterBy,
		    		is_open:1,
		    		vcode:txtCheckCode
		    	}
				setsmallmoneyAjaxFun(dataObj,function(data){
					console.log(data);
					if( data.success ){
		                FailDialog(162,60,"设置成功",'/member/security/index');
		            }else{
		                // FailDialog(162,60,"设置失败",'/member/security/index');
		            }
				});
			}
		});

		// 小额设置函数
		function setsmallmoneyAjaxFun(dataObj,Callback){
			$.ajax({
	            url: "/member/service/setsmallmoney",
	            type: 'POST',
	            dataType: 'json',
	            data: dataObj
	        })
	        .done(function(data) {
	            console.log(data);
	            Callback(data);
	        })
	        .fail(function() {
	            //alert('网络错误！')
	        })
	        .always(function() {
	            ajaxlack = true;
	        });
		}





	}

	// 设置支付密码
	if( pageTrue=='setpaypwd' ){
		var type = $.query.get('type');
		// http://webcms.didv.cn/member/service/updatepaypwd?paypwd=xxx&key=xx&vcode=xxx

		// 邮箱或手机验证
		var snTrue = true;
		var regesterBy;
		var codeNew;
		$("#btnGetCode").on(click, function(){
			var $this = $(this);
			var ajaxNum;
			regesterBy = $this.attr("regesterBy");
			if( !snTrue ) return; snTrue = false;
			if( regesterBy.indexOf("@")!=-1 ){
				ajaxNum = 0;
			}else{
				ajaxNum = 1;
			}  
			codeNew = new ajaxCodeFun(regesterBy,ajaxNum,function(){
				$("#btnSubmit").removeClass('gray-btn');
				$this.addClass("current");
	        	setIntervalFun(120,function(_TIME){
	        		$this.html('重新发送('+_TIME+')');
	        		if( _TIME<=0 ){
	        			$this.html('重新发送');
	        			$this.removeClass("current");
	        			snTrue = true;
	        		}
	        	});
			});
		});

		if( type==1 ){
			$(".person-list li:eq(3), .person-list li:eq(4)").show();
		}
		
		$("#btnSubmit").on(click, function(){
			var txtCheckCode = $("#txtCheckCode").val();
			var txtPwd1 = $("#txtPwd1").val();
			var txtPwd2 = $("#txtPwd2").val();
			if( $(this).hasClass("gray-btn") ) return;
			if( txtCheckCode.length!=6 ){
				$("#div_tips").html('<div class="form-orange-tips">验证码不正确！</div>');
				setIntervalFun(3,function(){
					$("#div_tips").html('');
				});
				return;
			}
			if( type==0 ){
				var dataObj = {
		    		paypwd:'',
		    		key:regesterBy,
		    		vcode:txtCheckCode
		    	}
				updatepaypwdAjaxFun(dataObj,function(data){
					console.log(data);
					if( data.success ){
		                FailDialog(162,60,"设置成功",'/member/security/index');
		            }else{
		                // FailDialog(162,60,"设置失败",'/member/security/index');
		            }
				});
			}else if( type==1 ){
				if( txtPwd1.length!=6&&!reg.test(txtPwd1) ){
					$("#div_tips1").html('<div class="form-orange-tips">支付密码必须6位纯数字！</div>');
					setIntervalFun(3,function(){
						$("#div_tips1").html('');
					});
					return;
				}
				console.log(txtPwd1,txtPwd2)
				if( txtPwd1!=txtPwd2 ){
					$("#div_tips2").html('<div class="form-orange-tips">两次密码不一致！</div>');
					setIntervalFun(3,function(){
						$("#div_tips1").html('');
					});
					return;
				}
				var dataObj = {
		    		paypwd:txtPwd1,
		    		key:regesterBy,
		    		vcode:txtCheckCode
		    	}
				updatepaypwdAjaxFun(dataObj,function(data){
					console.log(data);
					if( data.success ){
		                FailDialog(162,60,"设置成功",'/member/security/index');
		            }else{
		                // FailDialog(162,60,"设置失败",'/member/security/index');
		            }
				});
			}
		});

		// 支付密码函数
		function updatepaypwdAjaxFun(dataObj,Callback){
			$.ajax({
	            url: "/member/service/updatepaypwd",
	            type: 'POST',
	            dataType: 'json',
	            data: dataObj
	        })
	        .done(function(data) {
	            console.log(data);
	            Callback(data);
	        })
	        .fail(function() {
	            //alert('网络错误！')
	        })
	        .always(function() {
	            ajaxlack = true;
	        });
		}
	}
    /*******************************************************账户安全结束*********************************************/

    /*******************************************************地址管理**************************************************/
    if( pageTrue=="address" ){

    	$("#a_add").on("click",function(){
			$(this).hide();
			$("#btnSaveAddress").attr({"_id":""});
			$("#div_form").show();
		});

	    // 取消
	    $("#btnCancel").on("click",function(){
	    	window.location.reload();
	    });

		// 删除
	    $(".btn-delete").on("click",function(){
	    	var id = $(this).attr("id");
	    	tanFun(398,197,'<div class="z-popUp z-pop-box">'
            +'<span class="gray3">确定要删除？</span>'
            +'<a href="javascript:gotoClick();" title="取消" class="z-btn-cancel">取消</a>'
            +'<a href="javascript:;" title="确定" id="'+id+'" class="z-btn-determine address-delete">确定</a>'
            +'</div>');
	    });

	    // 确认删除地址
	    $(document).on("click",".address-delete",function(){
	        var id = $(this).attr("id");
	        var dataObj = {
	    		id:id
	    	}
	    	deleteaddressFun(dataObj);
	    });

	    // 设置默认
	    $(".set-normal").on("click",function(){
	    	var id = $(this).attr("id");
	    	var dataObj = {
	    		id:id
	    	}
	    	setmembercontactdefaultFun(dataObj);
	    });

	    // 修改
	    $(".btn-modify").on("click",function(){
	    	var $this = $(this);
	    	var id = $this.attr("id");
	    	var name = $this.parents("li").find(".u-name").text();
	    	var tel = $this.parents("li").find(".u-tel").text();
	    	var address = $this.parents("li").find(".u-address").text().split(" ");
	    	var code = $this.parents("li").find(".u-code").text();
	    	var provinceID = $this.parents("li").find(".u-address").attr('provinceID');
	    	var cityID = $this.parents("li").find(".u-address").attr('cityID');
	    	var districtID = $this.parents("li").find(".u-address").attr('districtID');

	    	$("#btnSaveAddress").attr({"_id":id});
	    	$("#a_add").hide();
			$("#div_form").show();

			$("#selProvinceID .select-xiala").html(address[0]+'<s class="u-personal"></s>');
			$("#selCityID .select-xiala").html(address[1]+'<s class="u-personal"></s>');
			$("#selCountyID .select-xiala").html(address[2]+'<s class="u-personal"></s>');

			$("#selProvinceID input").val(provinceID);
			$("#selCityID input").val(cityID);
			$("#selCountyID input").val(districtID);

			$("#txtAddress").val(address[3]); //详细地址
	    	$("#txtPostCode").val(code); //邮政编码
	    	$("#txtConsignee").val(name); //收货人
	    	$("#txtTel").val(''); //固定电话
	    	$("#txtMobile").val(tel); //手机号码

	    	if( $this.parents("li").index()==0 ){
	    		$("#a_default").attr("value",1);//默认地址
	    		$(".set-default").addClass("z-click");
	    	}else{
	    		$("#a_default").attr("value",0);//默认地址
	    		$(".set-default").removeClass("z-click");
	    	}

	    	// 城市接口
			var selProvinceID = $("#selProvinceID").find("input").val(); //城市
			provinceFun(selProvinceID,function(data){
				$("#selCityID .select-state").html('');
				for( var v in data.result ){
					$("#selCityID .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
			});

			// 城市区接口
			var selCityID = $("#selCityID").find("input").val(); //区
			getdistrictsFun(selCityID,function(data){
				$("#selCountyID .select-state").html('');
				for( var v in data.result ){
					$("#selCountyID .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
			});
	    });
	}
    /*******************************************************地址管理结束**************************************************/

    /*******************************************************个人资料**************************************************/
    if( pageTrue=="membermodify" ){

    	var nickname = $("#txtName").attr("nickname");
    	if( nickname!="" ){
    		$("#txtName").val(nickname).removeClass("inputFocus").css({"color":"#000"});
    	}

    	var txtPhone = $("#txtPhone").attr("telMobile");
    	if( txtPhone!="" ){
    		$("#txtPhone").val(txtPhone).removeClass("inputFocus").css({"color":"#000"});
    	}

		var sex = $("#sex").val();
		var boxDIV = $(".z-content .info-list .sex-list .box");
		if( sex=="0" ){
			boxDIV.eq(2).find(".box-check").addClass("current");
		}else if( sex=="1" ){
			boxDIV.eq(0).find(".box-check").addClass("current");
		}else if( sex=="2" ){
			boxDIV.eq(1).find(".box-check").addClass("current");
		}

		var sltYear = $("#sltYear").find("input").val();
		if( sltYear!="" ){
			$("#sltYear").find(".select-xiala").html(sltYear+'<s class="u-personal"></s>');
		}

		var sltMonth = $("#sltMonth").find("input").val();
		if( sltMonth!="" ){
			$("#sltMonth").find(".select-xiala").html(sltMonth+'<s class="u-personal"></s>');
		}

		var sltDay = $("#sltDay").find("input").val();
		if( sltDay!="" ){
			$("#sltDay").find(".select-xiala").html(sltDay+'<s class="u-personal"></s>');
		}

		var sltCons = $("#sltCons").find("input").val();
		if( sltCons!="0" ){
			var $div = $("#sltCons .select-state a");
			for ( var i=0; i<$div.length; i++) {
				if( $div.eq(i).attr("value")==sltCons ){
					$("#sltCons").find(".select-xiala").html($div.eq(i).html()+'<s class="u-personal"></s>');
				}
			}
		}


		// 所在地默认数据
		var selLiveA = $("#selLiveA").find("input").val();
		if( selLiveA!="" ){
			var $div = $("#selLiveA .select-state a");
			for ( var i=0; i<$div.length; i++) {
				if( $div.eq(i).attr("value")==selLiveA ){
					$("#selLiveA").find(".select-xiala").html($div.eq(i).html()+'<s class="u-personal"></s>');
					// 城市
					provinceFun(selLiveA,function(data){
						$("#selLiveB .select-state").html('');
						for( var v in data.result ){
							$("#selLiveB .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
						}
						selLiveBdataFun();
					});
				}
			}
		}

		function selLiveBdataFun(){
			var selLiveB = $("#selLiveB").find("input").val();
			var $div = $("#selLiveB .select-state a");
			console.log($div.length)
			for ( var i=0; i<$div.length; i++) {
				if( $div.eq(i).attr("value")==selLiveB ){
					$("#selLiveB").find(".select-xiala").html($div.eq(i).html()+'<s class="u-personal"></s>');
				}
			}
			// 城市
			getdistrictsFun(selLiveB,function(data){
				var selLiveC = $("#selLiveC").find("input").val();
				for( var v in data.result ){
					$("#selLiveC .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
				var $div = $("#selLiveC .select-state a");
				for ( var i=0; i<$div.length; i++) {
					if( $div.eq(i).attr("value")==selLiveC ){
						$("#selLiveC").find(".select-xiala").html($div.eq(i).html()+'<s class="u-personal"></s>');
					}
				}
			});
		}

		// 家乡默认数据
		var selHomeA = $("#selHomeA").find("input").val();
		if( selHomeA!="" ){
			var $div = $("#selHomeA .select-state a");
			for ( var i=0; i<$div.length; i++) {
				if( $div.eq(i).attr("value")==selHomeA ){
					$("#selHomeA").find(".select-xiala").html($div.eq(i).html()+'<s class="u-personal"></s>');
					// 城市
					provinceFun(selHomeA,function(data){
						$("#selHomeB .select-state").html('');
						for( var v in data.result ){
							$("#selHomeB .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
						}
						selHomeAdataFun();
					});
				}
			}
		}

		function selHomeAdataFun(){
			var selHomeB = $("#selHomeB").find("input").val();
			var $div = $("#selHomeB .select-state a");
			console.log($div.length)
			for ( var i=0; i<$div.length; i++) {
				if( $div.eq(i).attr("value")==selHomeB ){
					$("#selHomeB").find(".select-xiala").html($div.eq(i).html()+'<s class="u-personal"></s>');
				}
			}
			// 城市
			getdistrictsFun(selHomeB,function(data){
				var selHomeC = $("#selHomeC").find("input").val();
				for( var v in data.result ){
					$("#selHomeC .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
				var $div = $("#selHomeC .select-state a");
				for ( var i=0; i<$div.length; i++) {
					if( $div.eq(i).attr("value")==selHomeC ){
						$("#selHomeC").find(".select-xiala").html($div.eq(i).html()+'<s class="u-personal"></s>');
					}
				}
			});
		}

		var txtQQ = $("#txtQQ").attr("txtqq");
		if( txtQQ!='' ){
			$("#txtQQ").val(txtQQ).removeClass("inputFocus").css({"color":"#000"});
		}

		// 星座
		var selMonthIncome = $("#selMonthIncome").find("input").val();
		if( selMonthIncome!="0" ){
			var $div = $("#selMonthIncome .select-state a");
			for ( var i=0; i<$div.length; i++) {
				if( $div.eq(i).attr("value")==selMonthIncome ){
					$("#selMonthIncome").find(".select-xiala").html($div.eq(i).html()+'<s class="u-personal"></s>');
				}
			}
		}

		var txtSignature = $("#txtSignature").attr("txtsignature");
		if( txtSignature!="" ){
			$("#txtSignature").val(txtSignature).removeClass("inputFocus").css({"color":"#000"});
		}

		// 下拉框
		$(".select-xiala").on(click, function(){
			$(".select-xiala").next(".select-state").hide();
			$(this).next(".select-state").toggle();
		});
		$(document).on(click, ".select-state a", function(){
			var text = $(this).text();
			var val = $(this).attr("value");
			var $parent = $(this).parent().parent();
			$(this).parent(".select-state").hide();
			$parent.find("input").val(val);
			$parent.find(".gray6").html(text+'<s class="u-personal"></s>');
		});

		// 个人资料提交
		$("#membermodifySave").on("click",function(){
			var txtName = $("#txtName").val(); //昵称
			var txtPhone = $("#txtPhone").val(); //手机
			var sex = $("#sex").val(); //性别
			var birthday = $("#sltYear").find('input').val()+"-"+$("#sltMonth").find('input').val()+"-"+$("#sltDay").find('input').val(); //生日
			var sltCons = $("#sltCons").find('input').val(); //星座
			var selLive = $("#selLiveA").find('input').val()+"|"+$("#selLiveB").find('input').val()+"|"+$("#selLiveC").find('input').val(); //所在地
			var selHome = $("#selHomeA").find('input').val()+"|"+$("#selHomeB").find('input').val()+"|"+$("#selHomeC").find('input').val(); //家乡
			var txtQQ = $("#txtQQ").val(); //QQ
			var selMonthIncome = $("#selMonthIncome").find('input').val(); //月收入
			var txtSignature = $("#txtSignature").val(); //签名

			if( txtName=='' ){
				$("#txtName").parents("li").find(".orange").html("昵称不能为空");
				return;
			}
			if( txtName.length<1||txtName.length>20 ){
				$("#txtName").parents("li").find(".orange").html("昵称必须为2-20个字符");
				return;
			}
			$("#txtName").parents("li").find(".orange").html("");

			if( txtPhone=="手机号/座机" ){
				txtPhone = '';
			}

			// 生日判断
			if( birthday=="--" ){
				birthday = '';
			}else if( $("#sltYear").find('input').val()==""||$("#sltMonth").find('input').val()==""||$("#sltDay").find('input').val()=="" ){
				$("#span_tip").html("请选择完整生日");
				return;
			}else{
				birthday += " 00:00:00";
			}
			$("#span_tip").html('生日当月享双倍福分(保存后,一年后才能再次编辑)');


			// 所在地判断
			if( selLive=="||" ){
				selLive = '';
			}else if( $("#selLiveA").find('input').val()==""||$("#selLiveB").find('input').val()==""||$("#selLiveC").find('input').val()=="" ){
				$("#div_live").parents("li").find(".orange").html("请选择完整的所在地");
				return;
			}
			$("#div_live").parents("li").find(".orange").html("");

			// 家乡判断
			if( selHome=="||" ){
				selHome = '';
			}else if( $("#selHomeA").find('input').val()==""||$("#selHomeB").find('input').val()==""||$("#selHomeC").find('input').val()=="" ){
				$("#div_home").parents("li").find(".orange").html("请选择完整的家乡");
				return;
			}
			$("#div_home").parents("li").find(".orange").html("");


			if( txtQQ=="您的QQ号码" ){
				txtQQ = '';
			}

			if( txtSignature=='让别人看到不一样的你' ){
				txtSignature = '';
			}

			var dataObj = {
				nickname : txtName,
				tel_mobile : txtPhone,
				sex : sex,
				birthday : birthday,
				constellation : sltCons,
				location : selLive,
				hometown : selHome,
				qq : txtQQ,
				monthly_income : selMonthIncome,
				signature : txtSignature
			}

			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/updateuserto",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	FailDialog(162,60,"修改成功",window.location.href);
		        }else{
		        	FailDialog(162,60,"修改失败");
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });

		});

		// 城市接口
		$("#selLiveA .select-state a").on("click",function(){
			var province = $(this).attr("value"); //城市
			provinceFun(province,function(data){
				$("#selLiveB .select-state, #selLiveC .select-xiala").html('');
				$("#selLiveB .select-xiala, #selLiveC .select-xiala").html('---请选择---<s class="u-personal"></s>');
				for( var v in data.result ){
					$("#selLiveB .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
			});
		});

		$(document).on("click","#selLiveB .select-state a",function(){
			var province = $(this).attr("value"); //城市
			getdistrictsFun(province,function(data){
				$("#selLiveC .select-state").html('');
				$("#selLiveC .select-xiala").html('---请选择---<s class="u-personal"></s>');
				for( var v in data.result ){
					$("#selLiveC .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
			});
		});

		// 城市接口
		$("#selHomeA .select-state a").on("click",function(){
			var province = $(this).attr("value"); //城市
			provinceFun(province,function(data){
				$("#selHomeB .select-state, #selHomeC .select-state").html('');
				$("#selHomeB .select-xiala, #selHomeC .select-state").html('---请选择---<s class="u-personal"></s>');
				for( var v in data.result ){
					$("#selHomeB .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
			});
		});

		$(document).on("click","#selHomeB .select-state a",function(){
			var province = $(this).attr("value"); //城市
			getdistrictsFun(province,function(data){
				$("#selHomeC .select-state").html('');
				$("#selHomeC .select-xiala").html('---请选择---<s class="u-personal"></s>');
				for( var v in data.result ){
					$("#selHomeC .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
			});
		});

    }
	/*******************************************************个人资料结束**************************************************/

})