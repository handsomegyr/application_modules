webpackJsonp([0],[
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(3);
	__webpack_require__(4);
	__webpack_require__(5);
	$(function(){
		var Predeposit = $("#hidPredeposit").val(); //账户余额
		var Orderamount = $("#hidOrderamount").val(); //需要支付余额
		var txtPaypwd = $("#Password").val(); //是否需要输入支付密码

		// 判断账户是否有余额
		if( Predeposit>0 ){
			$("#divBankBox").hide();
		}else{
			$("#divBankBox").show();
			$("#iBankPay").html('￥'+Orderamount);
		}

		// 是否账户支付
		$("#checkBalance").on("click",function(){
			var $this = $(this);
			if( Predeposit>0 ){
				$("#divBankBox").toggle();
			}else{
				$this.html('');
			}
		});

		// 选择支付方式
		$("#dlPayForm dd").on("click",function(){
			$(this).addClass("checked").siblings().removeClass('checked');
			$("#hidPayName").val($(this).find("input").val());
		});

		// 立即支付
	    $("#submitOK").on("click",function(){
	        if( logoTrue ){
	        	// 判断是否需要输入支付密码
	        	// if( $("#checkBalance:has(b)").length!=0 ){
	        	// 	$("#divPaypwd").show();
	        	// 	return;
	        	// }


	            var pay_sn = $("#hidCheckSN").val();
	            var payway = 'weixin';
	            var integral = 0;
	            var predeposit = 1;
	            $.ajax({
	                url: "/order/pay/create",
	                type: 'POST',
	                dataType: 'json',
	                data: { pay_sn:pay_sn,payway:payway,integral:integral,predeposit:predeposit } //points_used=1&predeposit_used=1
	            })
	            .done(function(data) {
	                console.log(data);
	                if( data.success ){
	                    out_trade_no = data.result.out_trade_no;
	                    pay_state = data.result.pay_state;
	                    pay_url = data.result.pay_url;
	                    // if( pay_state ){
	                    //     window.location.href = '/yungou/cart/shopok?id='+out_trade_no;
	                    //     return;
	                    // }
	                    tanFun(490,237,'<div class="payment_ts">'
	                        +'<h3><s class="z-arrows"></s>请在新开窗口完成支付！</h3>'
	                        +'<ul>'
	                            +'<li class="payment_ts_con">如您的浏览器不支持，请复制以下链接到IE浏览器打开，完成付款后跟据您的情况进行以下操作！</li>'
	                            +'<li class="payment_ts_links">'+url+'</li>'
	                            +'<li class="payment_ts_but"><a id="btnBuyOk" href="javascript:gotoClick();" class="pay_tipsbut">完成支付</a><a id="btnReSelect" href="javascript:gotoClick();" class="blue">支付遇到问题？返回重新选择</a></li>'
	                        +'</ul>'
	                    +'</div>');
	                    getpayresultFun();
	                    window.open(pay_url);
	                }else{
	                    alert(data.error_msg);
	                }
	            })
	            .fail(function() {
	                //alert('网络错误！')
	            })
	            .always(function() {

	            });
	        }else{
	            tanLoginFun("/yungou/cart/list");
	        }
	    });

		// 支付密码验证
	    $("#txtPaypwd").on("focus",function(){
	    	var $this = $(this);
	    	var paypwd = $(this).val();
	    	var len = paypwd.length;
	    	$(this).next().show();
	    	if( paypwd==6 ){
	    		$.ajax({
		            url: "",
		            type: 'POST',
		            dataType: 'json',
		            data: { paypwd:paypwd }
		        })
		        .done(function(data) {
		            console.log(data);
		            if( data.success ){
		                $this.next().addClass("tips_txt_Correct");
		            }else{
						$this.next().addClass("tips_txt_Wrong");
		            }
		        })
		        .fail(function() {

		        })
		        .always(function() {

		        });
	    	}else{
	    		$this.next().addClass("tips_txt_Wrong");
	    	}
	    });
	    

	    function getpayresultFun(){
	        $.ajax({
	            url: "/order/pay/getpayresult",
	            type: 'POST',
	            dataType: 'json',
	            data: { id:out_trade_no } 
	        })
	        .done(function(data) {
	            console.log(data);
	            if( data.success ){
	                // window.location.href = '/yungou/cart/shopok';
	                window.location.href = '/yungou/cart/shopok?id='+out_trade_no;
	            }else{
	                getpayresultFun();
	            }
	        })
	        .fail(function() {
	            //alert('网络错误！')
	        })
	        .always(function() {

	        });
	    }






















	})
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
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {$(function(){

		// 人气加入购物车
		$(document).on("click",".u-cart-add",function(){
			var codeid = $(this).parents(".f-recomm-list").attr('codeid');
			cartAddFun(codeid,1,function(data){
	            FailDialog(160,60,'添加成功！',url);
	        });
		});


		// 增加商品数量
		$(".z-plus").on("click",function(){
			var $this = $(this);
			var val = eval($this.siblings("input").val())+1;
			var maxnum = eval($this.siblings("input").attr("maxnum"));
			var goodsid = $this.siblings("input").attr("goodsid");
			if( val>maxnum ) return;
			cartNumFun(goodsid,val,function(data){
				$this.siblings("input").val(val);
				$this.parents("dd").find(".orange").html('￥'+(val)+'.00');
				cartListNum();
			});
		});

		// 减少商品数量
		$(".z-less").on("click",function(){
			var $this = $(this);
			var val = eval($this.siblings("input").val())-1;
			var goodsid = $this.siblings("input").attr("goodsid");
			if( val<1 ) return;
			cartNumFun(goodsid,val,function(){
				$this.siblings("input").val(val);
				$this.parents("dd").find(".orange").html('￥'+(val)+'.00');
				cartListNum();
			});
		});

		// 选择商品
		$(".z-check").on("click",function(){
			var $this = $(this);
			if( $this.find("b").hasClass("z-comms") ){
				$this.html('');
			}else{
				$this.html('<b class="z-comms"></b>');
			}
			cartListNum();
		});

		// 输入价格
		$(".cartNum").on("keyup", function(){
			var $this = $(this);
			var goodsid = $(this).attr("goodsid");
			var val = eval($this.val());
			if( val<=0 ) return;
			cartNumFun(goodsid,val,function(){
				$this.parents("dd").find(".orange").html('￥'+(val)+'.00');
				cartListNum();
			});
		});

		// 全选
		$("#sSelAll").on("click",function(){
			var $this = $(this);
			if( $this.find("b").hasClass("z-comms") ){
				$(".z-check").html('<b class="z-comms"></b>');
			}else{
				$(".z-check").html('');
				$("#hidTotalMoney").val(0);
			}
			cartListNum();
		});
		$("#btnSelAll").on("click",function(){
			if( $(this).parent().prev().find("b").hasClass("z-comms") ){
				$(".z-check").html('');
				$("#hidTotalMoney").val(0);
			}else{
				$(".z-check").html('<b class="z-comms"></b>');
			}
			cartListNum();
		});

		// 删除
		$("#btnDelete").on("click",function(){
			
		});

		// 循环购物车数量
		function cartListNum(){
			var $dd = $("#dlCartList dd");
			var ddNum = $dd.length;
			var val = 0;
			for( var i=0; i<ddNum; i++ ) {
				if( $dd.eq(i).find("b").hasClass("z-comms") ){
					val += eval($dd.eq(i).find(".cartNum").val());
				}
			}
			$("#hidTotalMoney").val(val);
			$("#iTotalMoney").html('￥'+val+'.00');
		}

		// 数量增加和减少函数
		function addRemoveNum(_this,e){
			if( !_this.parents('dd').find('b').hasClass('z-comms') ) return;

			if( e=='add' ){
				$("#hidTotalMoney").val(eval($("#hidTotalMoney").val())+1);
				return false;
			}
			$("#hidTotalMoney").val(eval($("#hidTotalMoney").val())-1);
		}

		$("#divRecList .f-recomm-list").hover(function(){
			$(this).addClass("f-recomm-hover");
			$(this).append('<div class="z-mask"></div><a href="javascript:;" class="u-cart-add transparent-png"></a>');
		},function(){
			$(this).removeClass("f-recomm-hover");
			$(this).find(".z-mask,.u-cart-add").remove();
		});

		$("#divBankList cite span").on("click",function(){
			var index = $(this).index();
			$(this).addClass("current").siblings().removeClass("current");
			if( index==0 ){
				$("#dlCXK").show();
				$("#dlXYK").hide();
			}else{
				$("#dlCXK").hide();
				$("#dlXYK").show();
			}
		});

		// $("#submitOK").on("click", function(){
		// 	tanFun(490,237,'<div class="payment_ts">'
	 //                +'<h3><s class="z-arrows"></s>请在新开窗口完成支付！</h3>'
	 //                +'<ul>'
	 //                    +'<li class="payment_ts_con">如您的浏览器不支持，请复制以下链接到IE浏览器打开，完成付款后跟据您的情况进行以下操作！</li>'
	 //                    +'<li class="payment_ts_links">http://cart.1yyg.com/payment.do</li>'
	 //                    +'<li class="payment_ts_but"><a id="btnBuyOk" href="javascript:gotoClick();" class="pay_tipsbut">完成支付</a><a id="btnReSelect" href="javascript:gotoClick();" class="blue">支付遇到问题？返回重新选择</a></li>'
	 //                +'</ul>'
	 //            +'</div>');
		// });
































	})
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(6); //购物车飞入效果
	$(function(){

	    // 首页加入购物车
	    $(".u-cart").on("click",function(){
	        var addcar = $(this);
	        var codeid = addcar.attr('codeid');
	        var img = addcar.attr('_src');
	        var $cartNumID = $("#rightCartNum");
	        cartAddFun(codeid,1,function(data){
	            var cartNum = eval($cartNumID.html())+1;
	            $cartNumID.show().html(cartNum);  
	        });
	        flyFun(img);
	    });

	    // 详细页加入购物车
	    $(".consume-addcar").on("click",function(){
	        var addcar = $(this);
	        var goodsid = addcar.attr('goodsid');
	        var img = addcar.attr('_src');
	        var $cartNumID = $("#rightCartNum");
	        var num = $(".input-num").val()
	        cartNumFun(goodsid,num,function(data){
	            rightcartlabelFun();
	        });
	        flyFun(img);
	    });

	    // 购物车效果
	    function flyFun(img){
	        var scrolltop = $(document).scrollTop();
	        var offset = $("#end").offset();
	        var flyer = $('<img class="u-flyer" src="'+img+'" width="50" height="50" style="z-index:9999;border-radius: 50%;">');
	        flyer.fly({
	            start: {
	                left: event.pageX,
	                top: (event.pageY-scrolltop)
	            },
	            end: {
	                left: offset.left+10,
	                top: (offset.top+10-scrolltop),
	                width: 6,
	                height: 6
	            },
	            onEnd: function(){
	                this.destory();
	            }
	        });
	    }

	    // 立即结算
	    $("#btnGoPay").on("click",function(){
	        if( logoTrue ){
	            var goods_ids = checkoutGoodsIds();
	            console.log(goods_ids);
	            $.ajax({
	                url: "/order/cart/checkout",
	                type: 'POST',
	                dataType: 'json',
	                data: { goods_ids:goods_ids }
	            })
	            .done(function(data) {
	                console.log(data);
	                if( data.success ){
	                    window.location.href = '/yungou/cart/payment?pay_sn='+data.result;
	                }else{
	                    alert(data.error_msg);
	                }
	            })
	            .fail(function() {
	                //alert('网络错误！')
	            })
	            .always(function() {

	            });
	        }else{
	            tanLoginFun("/yungou/cart/list");
	        }
	    });

	    // 获取勾选商品id
	    function checkoutGoodsIds(){
	        var $dd = $("#dlCartList dd");
	        var ddNum = $dd.length;
	        var id = '';
	        for( var i=0; i<ddNum; i++ ) {
	            if( $dd.eq(i).find("b").hasClass("z-comms") ){
	                if( id=="" ){
	                    id = $dd.eq(i).attr("goodsid");
	                }else{
	                    id += ","+$dd.eq(i).attr("goodsid");
	                }
	            }
	        }
	        return id;
	    }

	    // 详细页立即购物
	    $(".consume-now").on("click",function(){
	        var num = $(".input-num").val();
	        var goodsid = $(this).attr("goodsid");
	        cartAddFun(goodsid,num,function(){
	            window.location.href = '/yungou/cart/list';
	        });
	    });

	    // 单个商品删除
	    $(".cart_delete").on("click",function(){
	        var goodsid = $(this).parents(".dd").attr("goodsid");
	        $(".f-box-prompt").remove();
	        $("#divCartBox").append('<div class="z-popUp f-box-prompt" style="top:'+($(this).parents(".dd").position().top+70)+'px">'
	                +'<p>确定要删除吗？</p>'
	                +'<a href="javascript:;" title="确定" class="u-btn-determine cart_delete_determine" goodsid="'+goodsid+'">确定</a>'
	                +'<a href="javascript:;" title="取消" class="u-btn-cancel cart_delete_cancel">取消</a>'
	                +'<b><s></s></b>'
	            +'</div>');
	    });

	    // 删除确认删除弹出
	    $(document).on("click",".cart_delete_cancel",function(){
	        $(".f-box-prompt").remove();
	    });

	    // 确认删除
	    $(document).on("click",".cart_delete_determine",function(){
	        var goodsid = $(this).attr("goodsid");
	        cartClearFun(goodsid,function(){
	            window.location.reload();
	        });
	    });


	    // 删除选中商品
	    $("#btnDelete").on("click",function(){
	        // 循环购物车数量
	        var $dd = $("#dlCartList dd");
	        var ddNum = $dd.length;
	        var goodsid = '';
	        for( var i=0; i<ddNum; i++ ) {
	            if( $dd.eq(i).find("b").hasClass("z-comms") ){
	                goodsid=='' ? goodsid = $dd.eq(i).attr("goodsid") : goodsid += ","+$dd.eq(i).attr("goodsid");
	            }
	        }
	        console.log(goodsid);
	        tanFun(398,197,'<div class="z-popUp z-pop-box">'
	            +'<span class="gray3">确定要删除所选的商品？</span>'
	            +'<a href="javascript:gotoClick();" title="取消" class="z-btn-cancel">取消</a>'
	            +'<a href="javascript:;" title="确定" goodsid="'+goodsid+'" class="z-btn-determine cart_delete_determine">确定</a>'
	            +'</div>');
	    });





	});

	// 购物车数量修改
	window.cartNumFun = function(goodsid,num,Callback){
	    $.ajax({
	        url: "/order/cart/updatenum",
	        type: 'POST',
	        dataType: 'json',
	        data: { goods_id:goodsid, quantity:num }
	    })
	    .done(function(data) {
	        console.log(data);
	        if( data.success ){
	            Callback(data);
	        }else{
	            alert(data.error_msg);
	        }
	    })
	    .fail(function() {
	        //alert('网络错误！')
	    })
	    .always(function() {

	    });
	}


	// 增加商品  购物车
	window.cartAddFun = function(goodsid,num,Callback){
	    $.ajax({
	        url: "/order/cart/add",
	        type: 'POST',
	        dataType: 'json',
	        data: { goods_id:goodsid, quantity:num }
	    })
	    .done(function(data) {
	        //console.log(data);
	        if( data.success ){
	            Callback(data);
	        }else{
	            alert(data.error_msg);
	        }
	    })
	    .fail(function() {
	        // //alert('网络错误！')
	    })
	    .always(function() {
	        ajaxlack = true;
	    });
	}

	// 删除购物车商品
	window.cartClearFun = function(goodsid,Callback){
	    $.ajax({
	        url: "/order/cart/clear",
	        type: 'POST',
	        dataType: 'json',
	        data: { goods_ids:goodsid }
	    })
	    .done(function(data) {
	        console.log(data);
	        if( data.success ){
	            Callback(data);
	        }else{
	            alert(data.error_msg);
	        }
	    })
	    .fail(function() {
	        //alert('网络错误！')
	    })
	    .always(function() {

	    });
	}
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(jQuery) {/*! fly - v1.0.0 - 2014-12-22
	* https://github.com/amibug/fly
	* Copyright (c) 2014 wuyuedong; Licensed MIT */
	var $ = __webpack_require__(1);
	!function(a){a.fly=function(b,c){var d={version:"1.0.0",autoPlay:!0,vertex_Rtop:20,speed:1.2,start:{},end:{},onEnd:a.noop},e=this,f=a(b);e.init=function(a){this.setOptions(a),!!this.settings.autoPlay&&this.play()},e.setOptions=function(b){this.settings=a.extend(!0,{},d,b);var c=this.settings,e=c.start,g=c.end;f.css({marginTop:"0px",marginLeft:"0px",position:"fixed"}).appendTo("body"),null!=g.width&&null!=g.height&&a.extend(!0,e,{width:f.width(),height:f.height()});var h=Math.min(e.top,g.top)-Math.abs(e.left-g.left)/3;h<c.vertex_Rtop&&(h=Math.min(c.vertex_Rtop,Math.min(e.top,g.top)));var i=Math.sqrt(Math.pow(e.top-g.top,2)+Math.pow(e.left-g.left,2)),j=Math.ceil(Math.min(Math.max(Math.log(i)/.05-75,30),100)/c.speed),k=e.top==h?0:-Math.sqrt((g.top-h)/(e.top-h)),l=(k*e.left-g.left)/(k-1),m=g.left==l?0:(g.top-h)/Math.pow(g.left-l,2);a.extend(!0,c,{count:-1,steps:j,vertex_left:l,vertex_top:h,curvature:m})},e.play=function(){this.move()},e.move=function(){var b=this.settings,c=b.start,d=b.count,e=b.steps,g=b.end,h=c.left+(g.left-c.left)*d/e,i=0==b.curvature?c.top+(g.top-c.top)*d/e:b.curvature*Math.pow(h-b.vertex_left,2)+b.vertex_top;if(null!=g.width&&null!=g.height){var j=e/2,k=g.width-(g.width-c.width)*Math.cos(j>d?0:(d-j)/(e-j)*Math.PI/2),l=g.height-(g.height-c.height)*Math.cos(j>d?0:(d-j)/(e-j)*Math.PI/2);f.css({width:k+"px",height:l+"px","font-size":Math.min(k,l)+"px"})}f.css({left:h+"px",top:i+"px"}),b.count++;var m=window.requestAnimationFrame(a.proxy(this.move,this));d==e&&(window.cancelAnimationFrame(m),b.onEnd.apply(this))},e.destory=function(){f.remove()},e.init(c)},a.fn.fly=function(b){return this.each(function(){void 0==a(this).data("fly")&&a(this).data("fly",new a.fly(this,b))})}}(jQuery);
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }
]);