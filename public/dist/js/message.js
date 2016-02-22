webpackJsonp([7],{

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(7);
	__webpack_require__(22);
	__webpack_require__(3);
	$(function(){
		var pageGet = $.query.get("page");
		var forward = $.query.get("forward");
		var $btnSubmitMsg = $("#btnSubmitMsg");
		var $saytext = $("#saytext");
		// 好友私信
		if( pageGet=='friend'||pageGet=='userprivmsgdetail' ){
			var msgToUID = $.query.get("msgToUID");
			$btnSubmitMsg.on('click',function(){
				if( $(this).hasClass("disBtn") ) return;
		        var str = $("#saytext").val();
		        insertuserprivatemsgAjaxFun(msgToUID,replace_em(str),function(data){
		        	if( data.success ){
						if( pageGet=='friend' ){
							parent.window.friendMessageFun();
						}else if( pageGet=='userprivmsgdetail' ){
							parent.window.userprivmsgdetailMessageFun();
						}
					}else{
						
					}
		        });
		    });

		    // 私信好友
			function insertuserprivatemsgAjaxFun(msgToUID,msgContent,Callback){
				if( !ajaxlack ) return; ajaxlack = false;
				$.ajax({
			        url: "/message/service/insertuserprivatemsg",
			        type: 'POST',
			        dataType: 'json',
			        data: { msgToUID:msgToUID, msgContent:encodeURIComponent(msgContent) }
			    })
			    .done(function(data) {
			        console.log(data);
			        Callback(data);
			    })
			    .fail(function() {
			        alert('网络错误！')
			    })
			    .always(function() {
			    	ajaxlack = true;
			    });
			}
		}

		// 晒单回复
		if( pageGet=="postdetaila" ){

			$btnSubmitMsg.on('click',function(){
				if( $(this).hasClass("disBtn") ) return;
		        var str = replace_em($("#saytext").val());
		        parent.window.wininsertpostreplyaFun(encodeURIComponent(str));
		    });  
		}

		// 晒单回复回复
		if( pageGet=="postdetailb" ){

			$btnSubmitMsg.on('click',function(){
				if( $(this).hasClass("disBtn") ) return;
		        var str = replace_em($("#saytext").val());
		        parent.window.wininsertpostreplybFun(encodeURIComponent(str));
		    });  
		}


		// 个人主页发私信
		if( pageGet=="uMy" ){
			var msgTouserWeb = $.query.get("msgTouserWeb");
			$btnSubmitMsg.on('click',function(){
				if( $(this).hasClass("disBtn") ) return;
		        var str = $("#saytext").val();
		        insertmsgAjaxFun(msgTouserWeb,replace_em(str),function(data){
		        	if( data.success ){
						parent.window.insertmsgFun();
					}else{
						FailDialog(180,60,'发送失败！');
					}
		        });
		    });   

		    // 私信好友
			function insertmsgAjaxFun(msgTouserWeb,msgContent,Callback){
				if( !ajaxlack ) return; ajaxlack = false;
				$.ajax({
			        url: "/message/service/insertuserprivatemsg",
			        type: 'POST',
			        dataType: 'json',
			        data: { msgToUID:msgTouserWeb, msgContent:encodeURIComponent(msgContent) }
			    })
			    .done(function(data) {
			        console.log(data);
			        Callback(data);
			    })
			    .fail(function() {
			        alert('网络错误！')
			    })
			    .always(function() {
			    	ajaxlack = true;
			    });
			}

		}

		$saytext.on("blur input keydown", function() {
			if( $(this).val()!=''||$(this).val().length>2 ){
				$btnSubmitMsg.removeClass('disBtn');
			}else{
				$btnSubmitMsg.addClass('disBtn');
			}	
		});



		$('.emotion').qqFace({
		    id : 'facebox', 
		    assign:'saytext', 
		    path:'/qqFace/arclist/' //表情存放的路径
		});
		//查看结果
		function replace_em(str){
		    str = str.replace(/\</g,'&lt;');
		    str = str.replace(/\>/g,'&gt;');
		    str = str.replace(/\n/g,'<br/>');
		    str = str.replace(/\[em_([0-9]*)\]/g,'<img src="/qqFace/arclist/$1.gif" border="0" />');
		    return str;
		}






































	});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },

/***/ 3:
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

/***/ 7:
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

/***/ },

/***/ 22:
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(jQuery) {// QQ表情插件
	(function($){
		$.fn.qqFace = function(options){
			var defaults = {
				id : 'facebox',
				path : 'face/',
				assign : 'content',
				tip : 'em_'
			};
			var option = $.extend(defaults, options);
			var assign = $('#'+option.assign);
			var id = option.id;
			var path = option.path;
			var tip = option.tip;
			
			if(assign.length<=0){
				alert('缺少表情赋值对象。');
				return false;
			}
			
			$(this).click(function(e){
				var strFace, labFace;
				if($('#'+id).length<=0){
					strFace = '<div id="'+id+'" style="position:absolute;display:none;z-index:1000;" class="qqFace">' +
								  '<table border="0" cellspacing="0" cellpadding="0"><tr>';
					for(var i=1; i<=45; i++){
						labFace = '['+tip+i+']';
						strFace += '<td><img src="'+path+i+'.gif" onclick="$(\'#'+option.assign+'\').setCaret();$(\'#'+option.assign+'\').insertAtCaret(\'' + labFace + '\');" /></td>';
						if( i % 15 == 0 ) strFace += '</tr><tr>';
					}
					strFace += '</tr></table></div>';
				}
				$(this).parent().append(strFace);
				var offset = $(this).position();
				var top = offset.top + $(this).outerHeight();
				$('#'+id).css('bottom',"30px");
				$('#'+id).css('left',offset.left);
				$('#'+id).show();
				e.stopPropagation();
			});

			$(document).click(function(){
				$('#'+id).hide();
				$('#'+id).remove();
			});
		};

	})(jQuery);
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }

});