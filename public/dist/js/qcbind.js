webpackJsonp([10],[
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(7);
	__webpack_require__(3);
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
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 1 */,
/* 2 */,
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {winheight = $(window).height();
	winwidth = $(window).width();
	docheight = $(document).height();
	scrolltop = $(document).scrollTop();
	click = "click";
	url = window.location.href;
	ajaxOrigin = 'http://webcms.didv.cn/';
	_sizeTel = /^1[3,4,5,7,8]{1}[0-9]{9}$/i;
	_email = /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/i;
	errorIcon = '<i class="passport-icon transparent-png"></i>';
	ajaxlack = true;
	windwoHost = 'http://webcms.didv.cn/';
	reg = new RegExp("^[0-9]*$");  
	pageTrue = $("#pageID").val(); //页面
	logoTrue = false;
	// 个人用户信息
	myuserID = '';
	myuserPhoto = '/Images/UserFace-160-0000.jpg';
	myuserWeb = '';
	myusername = '暂无';

	$(function(){
		$("#liMobile, #liMember").hover(function(){
			$(this).addClass('u-arr-hover');
		},function(){
			$(this).removeClass('u-arr-hover');
		});

		$("#txtHSearch").on("focus", function(){
			$(this).next("span").hide();
		});
		$("#txtHSearch").on("blur", function(){
			$(this).next("span").show();
		});

		$("#divHotGoodsList .g-hotL-list").hover(function(){
			$(this).addClass('g-hotL-hover');
		},function(){
			$(this).removeClass('g-hotL-hover');
		});

		$("#divSoonGoodsList .soon-list-con, #divNewGoodsList .soon-list-con").hover(function(){
			$(this).addClass('soon-list-hover');
		},function(){
			$(this).removeClass('soon-list-hover');
		});

		// 登陆弹层函数
		window.tanLoginFun = function(forward){
			tanFun(390,407,'<iframe name="fastLoginFrame" id="fastLoginFrame" frameborder="0" src="http://webcms.didv.cn/member/passport/ShopLogin.html?forward='+encodeURIComponent(forward)+'" style="width:390px;height:407px;padding:0px;overflow:auto;"></iframe>');
		}

		// 弹层
		window.tanFun = function(_width,_height,_con){
			winheight = $(window).height();
			winwidth = $(window).width();
			docheight = $(document).height();
			scrolltop = $(document).scrollTop();
			$("#pageDialogBG").attr({'style':'opacity: 0.3; width: '+winwidth+'px; height: '+docheight+'px; display: block;'});
			$("#pageDialogClose").show();
			// $("#pageDialogBorder").attr({'style':'opacity: 0.1; width: 400px; height: 417px; top: 1361.5px; left: 103px; display: block;'});
			$("#pageDialog").attr({'style':'top: '+((winheight/2-_height/2)+scrolltop)+'px; left: '+((winwidth/2-_width/2))+'px; display: block;'});
			$("#pageDialogMain").attr({'style':'width: '+_width+'px; height: '+_height+'px;'});
			$("#pageDialogMain .content").html(_con);
		}

		// 关闭弹层
		$("#pageDialogClose").on(click, function(){
			gotoClick();
		});


		window.gotoClick = function(){
			$("#pageDialogBG, #pageDialog").hide();
		}

		var t;
		window.FailDialog = function(_w,_h,_con,_URL){ //162x60
			winheight = $(window).height();
			winwidth = $(window).width();
			docheight = $(document).height();
			scrolltop = $(document).scrollTop();
			$("#pageDialogBG").attr({'style':'opacity: 0.3; width: '+winwidth+'px; height: '+docheight+'px; display: block;'});
			$("#pageDialogClose").hide();
			$("#pageDialog").attr({'style':'top: '+((winheight/2-_h/2)+scrolltop)+'px; left: '+((winwidth/2-_w/2))+'px; display: block;'});
			$("#pageDialogMain").attr({'style':'width: '+_w+'px; height:'+_h+'px;'});
			$("#pageDialogMain .content").html('<div class="mAltOK"><s></s>'+_con+'</div>');
			t = setTimeout(function(){
				gotoClick();
				clearTimeout(t);
				//console.log(_URL);
				if( _URL==undefined||_URL=='' ) return;
				window.location.href = _URL;
			},2000);
		}
		

		
		// 输入框
		$(".iptfocus").focus(function(){
			$(this).siblings(".iptem").hide();
			$(this).parents("li").addClass("enter-focus");
		});
		$(".iptfocus").blur(function(){
			//console.log($(this).val());
			$(this).parent().removeClass("error-text").find(".orange").hide();
			if( $(this).val()=="" ){
				$(this).siblings(".iptem").show();
			}
			$(this).parents("li").removeClass("enter-focus");
		});
		$(".iptem").on(click, function(){
			$(this).siblings("input").focus();
		});

		//输入框提示
		$(".inputFocus").focus(function(){
			$(this).addClass("focus");
			if( $(this).val()==this.defaultValue ){  
				$(this).val("");           
			} 
		}).blur(function(){
			if ( $(this).val()=='' ) {
				$(this).val(this.defaultValue);
				$(this).removeClass("focus");
			}
		});

		

		// 单选框
		$(".box-check").on(click, function(){
			$(this).parent().parent().find(".box-check").removeClass("current");
			$(this).addClass("current");
			$(this).parent().parent().parent().find("input").val($(this).next(".blank1").attr("name"));
		});

		// 复选框
		$(".lab").on(click, function(){
			if( $(this).hasClass("current") ){
				$(this).parent().find("input").val("0");
				$(this).removeClass("current");
			}else{
				$(this).addClass("current");
				$(this).parent().find("input").val("1");
			}
		});

		// 隐私设置
		$(".i-side p > label").on(click, function(){
			$(this).parents('.i-side').find(".label").removeClass("current");
			$(this).addClass("current");
			$(this).parents('.i-side').find(".follow").hide();
			$(this).parent().find(".follow").show();
		});

		$(".follow label").on(click, function(){
			$(this).addClass("current").siblings().removeClass("current");;
			if( $(this).attr("f")=="Only" ){
				$(this).parent().find(".inp").removeAttr("disabled");
			}else if( $(this).attr("f")=="All" ){
				$(this).parent().find(".inp").attr({"disabled":"disabled"});
			}
		});

		window.ColseMessage = function(_this,_id){
			$(_this).parents(_id).hide();
		}

		// 导航二级
		var indexNavNum = 0;
		$("#divGoodsSort").hover(function(){
			if( $(this).find("#divSortList").is(":hidden") ){
				indexNavNum = 1;
			};
			$(this).find("#divSortList").show();
		},function(){
			if( indexNavNum==0 ) return;
			$(this).find("#divSortList").hide();
		});
		

		var rules = [{
		    reg:/\d+/,
		    weight:2
		},{
		    reg:/[a-z]+/,
		    weight:4
		},{
		    reg:/[A-Z]+/,
		    weight:8
		},{
		    reg:/[~!@#\$%^&*\(\)\{\};,.\?\/'"]/,
		    weight:16
		}];

		var strongLevel = {
		    '1':'弱',
		    '2':'中',
		    '3':'强'
		};
		window.passwordsFun = function(passwords){
		    var weight=0;
		    for(var j=rules.length-1;j>=0;j--){
		        if(rules[j].reg.test(passwords)){
		            weight|=rules[j].weight;
		        }
		    }
		    var key='3';
		    if(weight<=5)key='1';
		    else if(weight<=14)key='2';
		    return strongLevel[key];
		}


		// 累积参与人次
		totalbuycountFun();
		for( var i=0; i<9; i++ ){
			$("#ulHTotalBuy .num cite").append("<em>"+(i+1)+"</em>")
		}
		function totalbuycountFun(){
			$.ajax({
	            url: "/order/service/totalbuycount",
	            type: 'GET',
	            dataType: 'json',
	            data: {}
	        })
	        .done(function(data) {
	            // //console.log(data);
	            if( data.success ){
	            	var fundTotal = data.result.fundTotal.toString();
	            	// var fundTotal = "16";
	            	var fundTotalLen = fundTotal.length;
	            	var numLen = $("#ulHTotalBuy .num").length;
	            	for( var i=0; i<(numLen-fundTotalLen); i++ ){
						fundTotal = "0"+fundTotal;
					}
	            	// //console.log(fundTotal,fundTotalLen,numLen)
	 				for ( var i=1; i<=numLen; i++ ) {
						var substringVar = fundTotal.substring(i-1,i);
						$("#ulHTotalBuy .num").eq(i-1).find("cite").animate({"top":"-"+(substringVar*27)+"px"},1000);
						// //console.log(i,substringVar);
					};

	            }else{
	                alert(data.error_msg);
	            }
	        })
	        .fail(function() {
	            // //alert('网络错误！')
	        })
	        .always(function() {

	        });
		}

		// 检查是否登陆
		window.logoFun = function(Callback){
			$.ajax({
	            url: "/member/service/checklogin",
	            type: 'POST',
	            dataType: 'json',
	            data: {}
	        })
	        .done(function(data) {
	            //console.log(data);
	            if( data.success ){
	            	logoTrue = true;
	            	Callback(true);
	            }else{
	            	logoTrue = false;
	                Callback(false);
	            }
	        })
	        .fail(function() {
	            //alert('网络错误！')
	        })
	        .always(function() {

	        });
		}
		logoFun(function(e){  //个人信息
			$.ajax({
	            url: "/member/service/logininfo",
	            type: 'POST',
	            dataType: 'json',
	            data: {}
	        })
	        .done(function(data) {
	            //console.log(data);
	            if( data.success ){
	            	myuserID = data.result.userID//: "56761153887c22184e8b45b5"
					myuserPhoto = data.result.userPhoto//: "/upload/member/avatar/UserFace-160-0000.jpg"
					myuserWeb = data.result.userWeb//: "56761153887c22184e8b45b5"
					myusername = data.result.username//: "15821039514"
	            }
	        });
		});



		// 微信二维码提示
		$("#qr_box").mouseover(function(){
			$("#guide").animate({"marginLeft":"149px","opacity":"1"},1000).show();
		});
		// $("#qr_box").mouseout(function(){
		// 	$("#guide").animate({"marginLeft":"-101px","opacity":"0"},1000,function(){//console.log(2)});
		// });

		// 返回顶部
		$(".u-float-list").on("click",function(){
			$(document).scrollTop(0);
		});

		// 倒计时
		var setT;
		window.setIntervalFun = function(_TIME,Callback){
			clearInterval(setT);
			setT = setInterval(function(){
				_TIME--;
				Callback(_TIME);
				//console.log(_TIME);
				if( _TIME<=0 ){
					clearInterval(setT);
				}
			},1000);
		}

		// 发送验证码接口
		window.ajaxCodeFun = function(dataPOST,ajaxNum,Callback){
			var ajaxURL;
			var dataObj; 
			if( ajaxNum==0 ){
				ajaxURL = "/service/vcode/sendcodeemail";
				dataObj = {
					userEmail:dataPOST
				}
			}else if( ajaxNum==1 ){
				ajaxURL = "/service/vcode/sendcodesms";
				dataObj = {
					mobile:dataPOST
				}
			}
			$.ajax({
		        url: ajaxURL,
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        //console.log(data);
		        Callback();
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });	
		}

		// 账户是否存在函数
		window.checkEmailMobileFun = function(dataPOST,ajaxNum,Callback){
			var ajaxURL;
			var dataObj;
			if( ajaxNum==0 ){
				ajaxURL = "/member/service/checkmobile";
				dataObj = {
					mobile:dataPOST
				}
			}else if( ajaxNum==1 ){
				ajaxURL = "/member/service/checkemail";
				dataObj = {
					email:dataPOST
				}
			}
			$.ajax({
		        url: ajaxURL,
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        //console.log(data);
		        Callback(data);
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {

		    });	
		}

		window.yellowTxt = function (size){
			var txt = [];
			if( size==2 ){
				txt = ['100','强'];
			}else if( size==1 ){
				txt = ['66.66','中'];
			}else{
				txt = ['33.33','弱'];
			}
			return '<p class="yellow"><strong>安全强度：</strong><cite><dfn style="width:'+txt[0]+'%;"></dfn></cite>'+txt[1]+'</p>';
		}

		// 分页
		window.PageCreate = function(_ID,total,pageSize,pageNumber,Callback){
			var container = $(_ID);
			container.hide();
			if( (total/pageSize)<=1 ){
				Callback();
				return;
			}
	        var sources = function(){
	            var result = [];
	            for(var i = 1; i <= total; i++){
	                result.push(i);
	            }
	            return result;
	        }();
	        var options = {
	            dataSource: sources,
	            showGoInput: true,
	            showGoButton: true,
	            pageNumber: pageNumber,
	            pageSize: pageSize,
	            callback: function(response, pagination){
	                Callback(pagination.pageNumber);
	            },
	        };
	        container.show();
	        container.pagination(options);
	    }

	    // 倒计时
	    window.CountdownFun = function(id,closeTime,callback){
	        $(id).each(function () {
	            var displayTime;
	            function showTime(){
	                var day = Math.floor(closeTime / (1000 * 60 * 60 * 24));
	                var hour = Math.floor(closeTime / (3600)) - (day * 24);
	                var minute = Math.floor(closeTime / (60)) - (day * 24 * 60) - (hour * 60);
	                var second = Math.floor(closeTime) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
	                closeTime -= 0.01;
	                if( closeTime<=0 ){
	                    clearInterval(displayTime);
	                    callback(true,id);
	                    return;
	                }
	                var hm = closeTime.toFixed(2).split(".")[1];            
	                callback([(minute<=9 ? "0"+minute : minute),(second<=9 ? "0"+second : second),hm],id);
	            }
	            showTime();
	            displayTime = setInterval(function(){
	                showTime();
	            }, 10);
	        });
	    }

	    // 搜索
	    $("#btnHSearch").on("click",function(){
	    	var $txtHSearch = $("#txtHSearch");
	    	var val = $txtHSearch.val();
	    	if( $txtHSearch.hasClass("focus") ){
	    		window.location.href = "/yungou/product/search?q="+val;
	    	}else{
	    		$txtHSearch.focus();
	    	}
	    });

	    // 弹出登录
		$(document).on("click","#a_login, .tan_login",function(){
	    	tanLoginFun(url);
	    });

		// 时间转时间戳
		window.timeGetTimeFun = function(_time){
			var str = _time; // 日期字符串
			str = str.replace(/-/g,'/'); // 将-替换成/，因为下面这个构造函数只支持/分隔的日期字符串
			//console.log(str);
			var date = new Date(str); // 构造一个日期型数据，值为传入的字符串
			var time = date.getTime();
			return time;
		}

		// 分享
		window.heyShare = function(web,content,title,url,msg) {
		    switch (web) {
		        case "tsina"://分享新浪微博
		            window.open("http://v.t.sina.com.cn/share/share.php?title=" + encodeURIComponent(content) + "&url=" + encodeURIComponent(url) + "");
		            break;
		        case "tqq": //分享到qq微博
		            javascript: (function() { window.open('http://v.t.qq.com/share/share.php?title=' + encodeURIComponent(content) + '&url=' + encodeURIComponent(url) + '&source=bookmark', '_blank', 'width=610,height=350'); })();
		            break;
				case "qzone"://分享到qq空间
				 	window.open("http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?title=" + encodeURIComponent(content) + "&url=" + encodeURIComponent(url) + "");
					break;
		    }
		}


		// 复制代码
		window.oCopy = function(obj){
			var e=document.getElementById("txtInfo");//对象是contents 
	        e.select(); //选择对象 
	        document.execCommand("Copy"); //执行浏览器复制命令
		}


		



	});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(jQuery) {/**
	* http://plugins.jquery.com/project/query-object
	* jQuery.query - Query String Modification and Creation for jQuery
	* Written by Blair Mitchelmore (blair DOT mitchelmore AT gmail DOT com)
	* Licensed under the WTFPL (http://sam.zoy.org/wtfpl/).
	* Date: 2009/8/13
	*
	* @author Blair Mitchelmore
	* @version 2.1.7
	** jquery.query-2.1.7.js**/
	new function(settings) {
	    // Various Settings
	    var $separator = settings.separator || '&';
	    var $spaces = settings.spaces === false ? false : true;
	    var $suffix = settings.suffix === false ? '' : '[]';
	    var $prefix = settings.prefix === false ? false : true;
	    var $hash = $prefix ? settings.hash === true ? "#" : "?" : "";
	    var $numbers = settings.numbers === false ? false : true;

	    jQuery.query = new function() {
	        var is = function(o, t) {
	            return o != undefined && o !== null && (!!t ? o.constructor == t : true);
	        };
	        var parse = function(path) {
	            var m, rx = /\[([^[]*)\]/g , match = /^([^[]+)(\[.*\])?$/ .exec(path), base = match[1], tokens = [];
	            while (m = rx.exec(match[2])) tokens.push(m[1]);
	            return [base, tokens];
	        };
	        var set = function(target, tokens, value) {
	            var o, token = tokens.shift();
	            if (typeof target != 'object') target = null;
	            if (token === "") {
	                if (!target) target = [];
	                if (is(target, Array)) {
	                    target.push(tokens.length == 0 ? value : set(null, tokens.slice(0), value));
	                } else if (is(target, Object)) {
	                    var i = 0;
	                    while (target[i++] != null) ;
	                    target[--i] = tokens.length == 0 ? value : set(target[i], tokens.slice(0), value);
	                } else {
	                    target = [];
	                    target.push(tokens.length == 0 ? value : set(null, tokens.slice(0), value));
	                }
	            } else if (token && token.match( /^\s*[0-9]+\s*$/ )) {
	                var index = parseInt(token, 10);
	                if (!target) target = [];
	                target[index] = tokens.length == 0 ? value : set(target[index], tokens.slice(0), value);
	            } else if (token) {
	                var index = token.replace( /^\s*|\s*$/g , "");
	                if (!target) target = { };
	                if (is(target, Array)) {
	                    var temp = { };
	                    for (var i = 0; i < target.length; ++i) {
	                        temp[i] = target[i];
	                    }
	                    target = temp;
	                }
	                target[index] = tokens.length == 0 ? value : set(target[index], tokens.slice(0), value);
	            } else {
	                return value;
	            }
	            return target;
	        };

	        var queryObject = function(a) {
	            var self = this;
	            self.keys = { };

	            if (a.queryObject) {
	                jQuery.each(a.get(), function(key, val) {
	                    self.SET(key, val);
	                });
	            } else {
	                jQuery.each(arguments, function() {
	                    var q = "" + this;
	                    q = q.replace( /^[?#]/ , ''); // remove any leading ? || #
	                    q = q.replace( /[;&]$/ , ''); // remove any trailing & || ;
	                    if ($spaces) q = q.replace( /[+]/g , ' '); // replace +'s with spaces

	                    jQuery.each(q.split( /[&;]/ ), function() {
	                        var key = decodeURIComponent(this.split('=')[0] || "");
	                        var val = decodeURIComponent(this.split('=')[1] || "");

	                        if (!key) return;

	                        if ($numbers) {
	                            if ( /^[+-]?[0-9]+\.[0-9]*$/ .test(val)) // simple float regex
	                                val = parseFloat(val);
	                            else if ( /^[+-]?[0-9]+$/ .test(val)) // simple int regex
	                                val = parseInt(val, 10);
	                        }

	                        val = (!val && val !== 0) ? true : val;

	                        if (val !== false && val !== true && typeof val != 'number')
	                            val = val;

	                        self.SET(key, val);
	                    });
	                });
	            }
	            return self;
	        };

	        queryObject.prototype = {
	            queryObject: true,
	            has: function(key, type) {
	                var value = this.get(key);
	                return is(value, type);
	            },
	            GET: function(key) {
	                if (!is(key)) return this.keys;
	                var parsed = parse(key), base = parsed[0], tokens = parsed[1];
	                var target = this.keys[base];
	                while (target != null && tokens.length != 0) {
	                    target = target[tokens.shift()];
	                }
	                return typeof target == 'number' ? target : target || "";
	            },
	            get: function(key) {
	                var target = this.GET(key);
	                if (is(target, Object))
	                    return jQuery.extend(true, { }, target);
	                else if (is(target, Array))
	                    return target.slice(0);
	                return target;
	            },
	            SET: function(key, val) {
	                var value = !is(val) ? null : val;
	                var parsed = parse(key), base = parsed[0], tokens = parsed[1];
	                var target = this.keys[base];
	                this.keys[base] = set(target, tokens.slice(0), value);
	                return this;
	            },
	            set: function(key, val) {
	                return this.copy().SET(key, val);
	            },
	            REMOVE: function(key) {
	                return this.SET(key, null).COMPACT();
	            },
	            remove: function(key) {
	                return this.copy().REMOVE(key);
	            },
	            EMPTY: function() {
	                var self = this;
	                jQuery.each(self.keys, function(key, value) {
	                    delete self.keys[key];
	                });
	                return self;
	            },
	            load: function(url) {
	                var hash = url.replace( /^.*?[#](.+?)(?:\?.+)?$/ , "$1");
	                var search = url.replace( /^.*?[?](.+?)(?:#.+)?$/ , "$1");
	                return new queryObject(url.length == search.length ? '' : search, url.length == hash.length ? '' : hash);
	            },
	            empty: function() {
	                return this.copy().EMPTY();
	            },
	            copy: function() {
	                return new queryObject(this);
	            },
	            COMPACT: function() {

	                function build(orig) {
	                    var obj = typeof orig == "object" ? is(orig, Array) ? [] : { } : orig;
	                    if (typeof orig == 'object') {

	                        function add(o, key, value) {
	                            if (is(o, Array))
	                                o.push(value);
	                            else
	                                o[key] = value;
	                        }

	                        jQuery.each(orig, function(key, value) {
	                            if (!is(value)) return true;
	                            add(obj, key, build(value));
	                        });
	                    }
	                    return obj;
	                }

	                this.keys = build(this.keys);
	                return this;
	            },
	            compact: function() {
	                return this.copy().COMPACT();
	            },
	            toString: function() {
	                var i = 0, queryString = [], chunks = [], self = this;
	                var encode = function(str) {
	                    str = str + "";
	                    if ($spaces) str = str.replace( / /g , "+");
	                    return encodeURIComponent(str);
	                };
	                var addFields = function(arr, key, value) {
	                    if (!is(value) || value === false) return;
	                    var o = [encode(key)];
	                    if (value !== true) {
	                        o.push("=");
	                        o.push(encode(value));
	                    }
	                    arr.push(o.join(""));
	                };
	                var build = function(obj, base) {
	                    var newKey = function(key) {
	                        return !base || base == "" ? [key].join("") : [base, "[", key, "]"].join("");
	                    };
	                    jQuery.each(obj, function(key, value) {
	                        if (typeof value == 'object')
	                            build(value, newKey(key));
	                        else
	                            addFields(chunks, newKey(key), value);
	                    });
	                };

	                build(this.keys);

	                if (chunks.length > 0) queryString.push($hash);
	                queryString.push(chunks.join($separator));

	                return queryString.join("");
	            }
	        };

	        return new queryObject(location.search, location.hash);
	    };
	}(jQuery.query || { });  // Pass in jQuery.query as settings object
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }
]);