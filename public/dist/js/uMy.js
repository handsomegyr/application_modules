webpackJsonp([11],[
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(7);
	__webpack_require__(8);
	__webpack_require__(9);
	__webpack_require__(3);
	__webpack_require__(12);
	__webpack_require__(13);
	__webpack_require__(5);
	$(function(){
		var userId = $.query.get('id');

		// 标签切换
		$("#ul_Menu li").on("click",function(){
			var index = $(this).index();
			$(this).addClass("current").siblings().removeClass("current");
			$(".content-wrap").hide();
			if( index==0 ){
				$("#div_BuyList").show();
				$("#midNavLine").animate({left:'297px',width:"64px"});
				getuserpagebuyrafpostFirstDataFun(1,8,index,userId);
			}else if( index==1 ){
				$("#div_OrderList").show();
				$("#midNavLine").animate({left:'424px',width:"82px"});
				getuserpagebuyrafpostFirstDataFun(1,8,index,userId);
			}else if( index==2 ){
				$("#div_PostList").show();
				$("#midNavLine").animate({left:'567px',width:"40px"});
				getuserpagebuyrafpostFirstDataFun(1,8,index,userId);
			}
		});

		
	    // 加好友
	    $("#btnAddFriend").on("click",function(){
	    	if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/applyfriend",
		        type: 'POST',
		        dataType: 'json',
		        data: { userWeb:userId }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
					FailDialog(190,60,'申请发送成功！');
				}else{
					FailDialog(190,60,'申请发送失败！');
				}
		    })
		    .fail(function() {
		        alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
	    });


		// 显示举报
		$(".map-banner").hover(function(){
			$("#div_jb").show();
		},function(){
			$("#div_jb").hide();
		});

		// 举报内容
		$("#div_jb").click(function(){
			tanFun(410,400,'<div class="b-wrap"><div class="b-inner"><h6>举报</h6><div class="b-list-wrap"><ul class="b-list"><li _type="1"><span class="txt">钓鱼欺诈</span><span class="b-right-btn" style="display:none;"></span></li><li _type="2"><span class="txt">广告骚扰</span><span class="b-right-btn" style="display:none;"></span></li><li _type="3"><span class="txt">色情暴力</span><span class="b-right-btn" style="display:none;"></span></li><li _type="4"><span class="txt">其他</span><span class="b-right-btn" style="display:none;"></span><div class="edit-wrap" style="display:none;"><textarea id="txtInfo" rows="3" cols="10"></textarea></div><div id="div_tips"></div></li></ul></div><a href="javascript:;" class="b-submit-btn">提交</a></div></div>');
		});

		// 举报选择
		$(document).on("click",".b-list-wrap ul li",function(){
			$(this).addClass('this').siblings().removeClass('this');
			$(this).parents().find(".b-right-btn").hide();
			$(this).find(".b-right-btn").show();
		});

		// 提交举报
		$(document).on("click",".b-submit-btn",function(){
			var _type = $(".b-list-wrap li.this").attr('_type');
		});

		// 发送私信
		$("#btnSendMsg").on("click",function(){
			$("#divMsgBox").show();
			$("#divMsgBox .Comment_form").html('<iframe hidefocus="true" name="myFrame" frameborder="0" scrolling="no" src="/yungou/message.html?page=uMy&msgTouserWeb='+userId+'" style="width: 100%;height:100%;cursor:text;"></iframe>');
		});

		// 关闭私信
		$("#divMsgBox .Close").on("click",function(){
			$("#divMsgBox").hide();
		});

		window.insertmsgFun = function(){
			$("#divMsgBox").hide();
			FailDialog(180,60,'发送成功！');
		}


		/************** 云购记录 ******************/

		getuserpagebuyrafpostFirstDataFun(1,8,0,userId);

		function getuserpagebuyrafpostFirstDataFun(page,limit,type,userID){
			$(".good-list").html('<div class="loading-2015"><em></em></div>');
			var dataObj = {
				page:page,
				limit:limit,
				type:type,
				userID:userID,
			}
			var pagination = '#g-pagination-a';
			if( type==0 ){
				pagination = '#g-pagination-a';
			}else if( type==1 ){
				pagination = '#g-pagination-b';
			}else if( type==2 ){
				pagination = '#g-pagination-c';
			}
			// 数据加载 第一次
			getuserpagebuyrafpostAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getuserpagebuyrafpostAddDataFun(data,type);
				// 分页
				PageCreate(pagination,total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					getuserpagebuyrafpostAjaxFun(dataObj,function(data){
						getuserpagebuyrafpostAddDataFun(data,type);
					});
			    });
			});
		}

		// 数据插入
		function getuserpagebuyrafpostAddDataFun(data,type){
			if( data.success ){
		    	var dataLi = '';
				if( type==0 ){ //云购
					$.each(data.result.datas, function(i,item){
			    		var buyNum = item.buyNum; //: "1"
			    		var buyTime = item.buyTime; //: "3分钟前"
			    		var codeID = item.codeID; //: 2746247
			    		var codePeriod = item.codePeriod; //: "1622"
			    		var codePrice = item.codePrice; //: "5999.00"
			    		var codeQuantity = item.codeQuantity; //: "5999"
			    		var codeRNO = item.codeRNO; //: "10001182"
			    		var codeRTime = item.codeRTime; //: "2016-01-04 15:10:50"
			    		var codeSales = item.codeSales; //: "1195"
			    		var codeState = item.codeState; //: "1"
			    		var codeType = item.codeType; //: "0"
			    		var goodsPic = item.goodsPic; //: "20151023164405174.jpg"
			    		var goodsSName = item.goodsSName; //: "苹果（Apple）iPad Pro 12.9 英寸平板电脑 32G WiFi版"
			    		var limitBuy = item.limitBuy; //: "0"
			    		var userName = item.userName; //: "169257Q-猪头Q"
			    		var userPhoto = item.userPhoto; //: "20160104144619329.jpg"
			    		var userWeb = item.userWeb; //: "1012509077"

			    		if( codeState=="1" ){
							dataLi = '<li>'
							    +'<a class="g-pic" target="_blank" href="/yungou/product/index?id='+codeID+'">'
							        +'<img alt="" src="'+goodsPic+'" width="100" height="100">'
							        +'<span class="g-bg"> </span>'
							        +'<span class="g-txt">进行中 <span class="dotting"> </span> </span>'
							    +'</a>'
							    +'<div class="g-info">'
							        +'<h2 class="g-title">'
							            +'<a target="_blank" rel="nofollow" href="javascript:;">'+goodsSName+'</a>'
							        +'</h2>'
							        +'<p class="g-price">价值：￥'+codePrice+'</p>'
							        +'<div class="g-progress">'
							            +'<dl class="m-progress">'
							                +'<dt title="已完成'+(codeSales/codeQuantity)*100+'%"><b style="width:'+(codeSales/codeQuantity)*100+'%"><i class="cur"></i></b></dt>'
							                +'<dd><span class="orange fl"><em>'+codeSales+'</em>已参与</span><span class="gray6 fl"><em>'+codeQuantity+'</em>总需人次</span><span class="blue fr"><em>'+(codeQuantity-codeSales)+'</em>剩余</span></dd>'
							            +'</dl>'
							        +'</div>'
							    +'</div>'
							    +'<div class="g-total">'
							        +'参与&nbsp;<span class="orange">'+buyNum+'</span>&nbsp;人次'
							    +'</div>'
							    +'<a class="g-buy" rel="nofollow" target="_blank" href="/yungou/product/detail?id='+codeID+'">跟随云购</a>'
							    +'<i class="single"><i class="single"></i></i>'
							    +'<div class="g-time"><div class="aricle"><div class="cir"></div></div><div class="time-str"><div class="str">'+buyTime+'</div></div></div>'
							    +'<div class="clear"></div>'
							+'</li>';	
			    		}else{
							dataLi += '<li>'
			                    +'<a class="g-pic" target="_blank" href="/yungou/lottery/detail?id='+codeID+'"><img alt="" src="'+goodsPic+'" width="100" height="100"><span class="g-bg g-end"></span><span class="g-txt">已揭晓</span></a>'
			                    +'<div class="g-info">'
			                        +'<h2 class="g-title"><a rel="nofollow" target="_blank" href="/yungou/lottery/detail?id='+codeID+'">(第'+codePeriod+'云)'+goodsSName+'</a></h2>'
			                        +'<p class="g-price">价值：￥'+codePrice+'</p>'
			                        +'<div class="g-older"><p>获得者：<a href="/yungou/member/index?id='+userWeb+'" target="_blank">'+userName+'</a></p><p>揭晓时间：'+codeRTime+'</p></div>'
			                    +'</div>'
			                    +'<div class="g-total">参与&nbsp;<span class="orange">'+buyNum+'</span>&nbsp;人次</div>'
			                    +'<a class="g-see" target="_blank" href="/yungou/lottery/detail?id='+codeID+'">查看详情</a><i class="single"><i class="single"></i></i>'
			                    +'<div class="g-time"><div class="aricle"><div class="cir"></div></div><div class="time-str"><div class="str">'+buyTime+'</div></div></div><div class="clear"></div>'
			                +'</li>';
			    		}
			    	});
					
					if( dataLi=='' ){
						$("#g-pagination").hide();
						$("#div_BuyList li, .loading-2015").remove();
						$("#div_BuyList .good-list").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>暂无记录！</span></div>');
					}else{
						$("#g-pagination").show();
						$("#div_BuyList li, .null-retips-wrapper, .loading-2015").remove();
						$("#div_BuyList .good-list").html(dataLi);
					}
				}else if( type==1 ){ //获得商品
					$.each(data.result.datas, function(i,item){
			    		var buyNum = item.buyNum;//: "1"
						var codeID = item.codeID;//: 1955887
						var codePeriod = item.codePeriod;//: "271"
						var codePrice = item.codePrice;//: "1080.00"
						var codeRNO = item.codeRNO;//: "10000029"
						var codeRTime = item.codeRTime;//: "2015-11-17 00:49"
						var codeType = item.codeType;//: "0"
						var goodsPic = item.goodsPic;//: "20150625155346462.jpg"
						var goodsSName = item.goodsSName;//: "亚马逊 Kindle Paperwhite 3 全新升级版电子书阅读器 黑色"
						var limitBuy = item.limitBuy;//: "0"
						dataLi += '<li>'
						    +'<a class="g-pic" target="_blank" href="javascript:;"><img alt="" src="'+goodsPic+'" width="100" height="100"></a>'
						    +'<div class="g-info">'
						        +'<h2 class="g-title owner"><a rel="nofollow" target="_blank" href="/yungou/lottery/detail?id='+codeID+'">'+goodsSName+'</a></h2>'
						        +'<div class="g-older"><p class="g-price">价值：￥'+codePrice+'</p><p>幸运云购码：<b class="orange">'+codeRNO+'</b></p></div>'
						    +'</div>'
						    +'<div class="g-total">参与&nbsp;<span class="orange">'+buyNum+'</span>&nbsp;人次</div>'
						    +'<a rel="nofollow" class="g-see" target="_blank" href="/yungou/lottery/detail?id='+codeID+'">查看详情</a>'
						    +'<i class="single"><i class="single"></i></i>'
						    +'<div class="g-time"><div class="aricle"><div class="cir"></div></div><div class="time-str"><div class="str">'+codeRTime+'</div></div></div><div class="clear"></div>'
						+'</li>';	    		
			    	});
					
					if( dataLi=='' ){
						$("#g-pagination").hide();
						$("#div_OrderList li, .loading-2015").remove();
						$("#div_OrderList .good-list").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>TA还没有获得商品哦！</span></div>');
					}else{
						$("#g-pagination").show();
						$("#div_OrderList li, .null-retips-wrapper, .loading-2015").remove();
						$("#div_OrderList .good-list").html(dataLi);
					}
				}else if( type==2 ){ //晒单
					$.each(data.result.datas, function(i,item){
			    		var postAllPic = item.postAllPic.split(',')//: "20160105111905438.jpg,20160105111940569.jpg,20160105112019025.jpg"
			    		var postContent = item.postContent//: "真的很意外，本来想着就买着玩，没想到真的中了，还是我很喜欢的亚马逊电子阅读器，因为…"
			    		var postHits = item.postHits//: "1"
			    		var postID = item.postID//: "133461"
			    		var postPic = item.postPic//: "20160105111905438.jpg"
			    		var postReplyCount = item.postReplyCount//: "2"
			    		var postTime = item.postTime//: "59分钟前"
			    		var postTitle = item.postTitle//: "感谢云购"
			    		var replyContent = item.replyContent//: "大学商盟，当代理首先送一台苹果手机。诶反复说了好多遍，你才会…"
			    		var replyUserName = item.replyUserName//: "大学商盟代理送苹果6s"
			    		var replyUserPhoto = item.replyUserPhoto//: "20151230144448387.jpg"
			    		var replyUserWeb = item.replyUserWeb//: "1012217871"
			    		var imgLi = ''
						for( var i=0; i<postAllPic.length; i++ ) {
							imgLi += +'<li><span><img width="71px" height="71px" src="'+postAllPic[i]+'"></span><div class="pic-hover transparent-png" style="display: none;"></div></li>';
						};
						dataLi += '<li>'
						    +'<h3 class="s-title"><a target="_blank" href="javascript:;">'+postTitle+'</a></h3>'
						    +'<p class="s-info"><a rel="nofollow" target="_blank" href="/yungou/post/detail?id='+postID+'">'+postContent+'</a></p>'
						    +'<div class="pic-list-wrap">'
						        +'<ol class="pic-list">'+imgLi+'</ol>'
						    +'</div>'
						    +'<i class="single"><i class="single"></i></i>'
						    +'<div class="g-time">'
						        +'<div class="aricle"><div class="cir"></div></div>'
						        +'<div class="time-str"><div class="str">'+postTime+'</div></div>'
						    +'</div>'
						    +'<div class="clear"></div>'
						+'</li>';	    		
			    	});
					
					if( dataLi=='' ){
						$("#g-pagination").hide();
						$("#div_PostList li, .loading-2015").remove();
						$("#div_PostList .good-list").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>暂无晒单！</span></div>');
					}else{
						$("#g-pagination").show();
						$("#div_PostList li, .null-retips-wrapper, .loading-2015").remove();
						$("#div_PostList .good-list").html(dataLi);
					}
				}
		    }else{

		    }
		}

		//函数
		function getuserpagebuyrafpostAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/order/service/getuserpagebuyrafpost",
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



		/************** 近期访客 ******************/
		getrecentvisitorsAjaxFun();
		function getrecentvisitorsAjaxFun(){
			$.ajax({
		        url: "/member/service/getrecentvisitors",
		        type: 'POST',
		        dataType: 'json',
		        data: { userId:userId }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	var dataLi = "";
		        	$.each(data.result.datas, function(i,item){
			    		var birthAreaNameState = item.birthAreaNameState; //: " hidden"
			        	var browserTime = item.browserTime; //: "1分钟前"
			        	var gradeLevel = item.gradeLevel; //: "01"
			        	var userBirthAreaName = item.userBirthAreaName; //: ""
			        	var userName = item.userName; //: "中个奖有这么困难嘛"
			        	var userPhoto = item.userPhoto; //: "20151212152923647.jpg"
			        	var userWeb = item.userWeb; //: "1010029819"
						dataLi += '<li>'
							+'<a class="pic" uweb="'+userWeb+'" type="showCard" href="/yungou/member/index?id='+userWeb+'"><img src="'+userPhoto+'" width="50" height="50"></a>'
								+'<div class="info"><span class="class-icon01">'
									+'<a class="name" uweb="'+userWeb+'" type="showCard" href="/yungou/member/index?id='+userWeb+'" rel="nofollow">'+userName+'</a><s></s></span><p>'+browserTime+'</p>'
								+'</div>'
							+'</li>';	    		
			    	});
					
					if( dataLi=='' ){
						$("#ul_visitors").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>暂无访客哦！</span></div>');
					}else{
						$("#ul_visitors").html(dataLi);
					}
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {

		    });
		}


		// 好友检查
		checkuserfriendAjaxFun();
		function checkuserfriendAjaxFun(){
			$.ajax({
		        url: "/member/service/checkuserfriend",
		        type: 'POST',
		        dataType: 'json',
		        data: { userID:userId }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	$(".option-wrapper li:eq(0)").hide();
		        }else{
		        	$(".option-wrapper li:eq(0)").show();
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
/* 3 */,
/* 4 */,
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

	    // 立即支付
	    $("#submitOK").on("click",function(){
	        if( logoTrue ){
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

/***/ },
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

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_RESULT__;/* WEBPACK VAR INJECTION */(function(__webpack_provided_window_dot_jQuery, jQuery) {/*
	 * pagination.js 2.0.7
	 * A jQuery plugin to provide simple yet fully customisable pagination
	 * https://github.com/superRaytin/paginationjs

	 * Homepage: http://paginationjs.com
	 *
	 * Copyright 2014-2100, superRaytin
	 * Released under the MIT license.
	*/
	var $ = __webpack_require__(1);
	!function(a,b){function c(a){throw new Error("Pagination: "+a)}function d(a){a.dataSource||c('"dataSource" is required.'),"string"==typeof a.dataSource?"undefined"==typeof a.totalNumber?c('"totalNumber" is required.'):b.isNumeric(a.totalNumber)||c('"totalNumber" is incorrect. (Number)'):j.isObject(a.dataSource)&&("undefined"==typeof a.locator?c('"dataSource" is an Object, please specify "locator".'):"string"==typeof a.locator||b.isFunction(a.locator)||c(""+a.locator+" is incorrect. (String | Function)"))}function e(a){var c=["go","previous","next","disable","enable","refresh","show","hide","destroy"];b.each(c,function(b,c){a.off(i+c)}),a.data("pagination",{}),b(".paginationjs",a).remove()}function f(a,b){return("object"==(b=typeof a)?null==a&&"null"||Object.prototype.toString.call(a).slice(8,-1):b).toLowerCase()}"undefined"==typeof b&&c("Pagination requires jQuery.");var g="pagination",h="addHook",i="__pagination-";b.fn.pagination&&(g="pagination2"),b.fn[g]=function(f){if("undefined"==typeof f)return this;var g=b(this),h={initialize:function(){var a=this;if(g.data("pagination")||g.data("pagination",{}),a.callHook("beforeInit")!==!1){g.data("pagination").initialized&&b(".paginationjs",g).remove(),a.disabled=!!l.disabled;var c=a.model={pageRange:l.pageRange,pageSize:l.pageSize};a.parseDataSource(l.dataSource,function(b){if(a.sync=j.isArray(b),a.sync&&(c.totalNumber=l.totalNumber=b.length),c.totalPage=a.getTotalPage(),!(l.hideWhenLessThanOnePage&&c.totalPage<=1)){var d=a.render(!0);l.className&&d.addClass(l.className),c.el=d,g["bottom"===l.position?"append":"prepend"](d),a.observer(),g.data("pagination").initialized=!0,a.callHook("afterInit",d)}})}},render:function(a){var c=this,d=c.model,e=d.el||b('<div class="paginationjs"></div>'),f=a!==!0;c.callHook("beforeRender",f);var g=d.pageNumber||l.pageNumber,h=l.pageRange,i=d.totalPage,j=g-h,k=g+h;return k>i&&(k=i,j=i-2*h,j=1>j?1:j),1>=j&&(j=1,k=Math.min(2*h+1,i)),e.html(c.createTemplate({currentPage:g,pageRange:h,totalPage:i,rangeStart:j,rangeEnd:k})),c.callHook("afterRender",f),e},createTemplate:function(a){var c,d,e=this,f=a.currentPage,g=a.totalPage,h=a.rangeStart,i=a.rangeEnd,j=l.totalNumber,k=l.showPrevious,m=l.showNext,n=l.showPageNumbers,o=l.showNavigator,p=l.showGoInput,q=l.showGoButton,r=l.pageLink,s=l.prevText,t=l.nextText,u=l.ellipsisText,v=l.goButtonText,w=l.classPrefix,x=l.activeClassName,y=l.disableClassName,z=l.ulClassName,A=b.isFunction(l.formatNavigator)?l.formatNavigator():l.formatNavigator,B=b.isFunction(l.formatGoInput)?l.formatGoInput():l.formatGoInput,C=b.isFunction(l.formatGoButton)?l.formatGoButton():l.formatGoButton,D=b.isFunction(l.autoHidePrevious)?l.autoHidePrevious():l.autoHidePrevious,E=b.isFunction(l.autoHideNext)?l.autoHideNext():l.autoHideNext,F=b.isFunction(l.header)?l.header():l.header,G=b.isFunction(l.footer)?l.footer():l.footer,H="",I='<input type="text" class="J-paginationjs-go-pagenumber">',J='<input type="button" class="J-paginationjs-go-button" value="'+v+'">';if(F&&(c=e.replaceVariables(F,{currentPage:f,totalPage:g,totalNumber:j}),H+=c),k||n||m){if(H+='<div class="paginationjs-pages">',H+=z?'<ul class="'+z+'">':"<ul>",k&&(1===f?D||(H+='<li class="'+w+"-prev "+y+'"><a>'+s+"</a></li>"):H+='<li class="'+w+'-prev J-paginationjs-previous" data-num="'+(f-1)+'" title="Previous page"><a href="'+r+'">'+s+"</a></li>"),n){if(3>=h)for(d=1;h>d;d++)H+=d==f?'<li class="'+w+"-page J-paginationjs-page "+x+'" data-num="'+d+'"><a>'+d+"</a></li>":'<li class="'+w+'-page J-paginationjs-page" data-num="'+d+'"><a href="'+r+'">'+d+"</a></li>";else l.showFirstOnEllipsisShow&&(H+='<li class="'+w+"-page "+w+'-first J-paginationjs-page" data-num="1"><a href="'+r+'">1</a></li>'),H+='<li class="'+w+"-ellipsis "+y+'"><a>'+u+"</a></li>";for(d=h;i>=d;d++)H+=d==f?'<li class="'+w+"-page J-paginationjs-page "+x+'" data-num="'+d+'"><a>'+d+"</a></li>":'<li class="'+w+'-page J-paginationjs-page" data-num="'+d+'"><a href="'+r+'">'+d+"</a></li>";if(i>=g-2)for(d=i+1;g>=d;d++)H+='<li class="'+w+'-page J-paginationjs-page" data-num="'+d+'"><a href="'+r+'">'+d+"</a></li>";else H+='<li class="'+w+"-ellipsis "+y+'"><a>'+u+"</a></li>",l.showLastOnEllipsisShow&&(H+='<li class="'+w+"-page "+w+'-last J-paginationjs-page" data-num="'+g+'"><a href="'+r+'">'+g+"</a></li>")}m&&(f==g?E||(H+='<li class="'+w+"-next "+y+'"><a>'+t+"</a></li>"):H+='<li class="'+w+'-next J-paginationjs-next" data-num="'+(f+1)+'" title="Next page"><a href="'+r+'">'+t+"</a></li>"),H+="</ul></div>"}return o&&A&&(c=e.replaceVariables(A,{currentPage:f,totalPage:g,totalNumber:j}),H+='<div class="'+w+'-nav J-paginationjs-nav">'+c+"</div>"),p&&B&&(c=e.replaceVariables(B,{currentPage:f,totalPage:g,totalNumber:j,input:I}),H+='<div class="'+w+'-go-input">'+c+"</div>"),q&&C&&(c=e.replaceVariables(C,{currentPage:f,totalPage:g,totalNumber:j,button:J}),H+='<div class="'+w+'-go-button">'+c+"</div>"),G&&(c=e.replaceVariables(G,{currentPage:f,totalPage:g,totalNumber:j}),H+=c),H},go:function(a,c){function d(a){if(e.callHook("beforePaging",h)===!1)return!1;if(f.direction="undefined"==typeof f.pageNumber?0:h>f.pageNumber?1:-1,f.pageNumber=h,e.render(),e.disabled&&!e.sync&&e.enable(),g.data("pagination").model=f,b.isFunction(l.formatResult)){var d=b.extend(!0,[],a);j.isArray(a=l.formatResult(d))||(a=d)}g.data("pagination").currentPageData=a,e.doCallback(a,c),e.callHook("afterPaging",h),1==h&&e.callHook("afterIsFirstPage"),h==f.totalPage&&e.callHook("afterIsLastPage")}var e=this,f=e.model;if(!e.disabled){var h=a,i=l.pageSize,k=f.totalPage;if(h=parseInt(h),!(!h||1>h||h>k)){if(e.sync)return void d(e.getDataSegment(h));var m={},n=l.alias||{};m[n.pageSize?n.pageSize:"pageSize"]=i,m[n.pageNumber?n.pageNumber:"pageNumber"]=h;var o={type:"get",cache:!1,data:{},contentType:"application/x-www-form-urlencoded; charset=UTF-8",dataType:"json",async:!0};b.extend(!0,o,l.ajax),b.extend(o.data||{},m),o.url=l.dataSource,o.success=function(a){d(e.filterDataByLocator(a))},o.error=function(a,b,c){l.formatAjaxError&&l.formatAjaxError(a,b,c),e.enable()},e.disable(),b.ajax(o)}}},doCallback:function(a,c){var d=this,e=d.model;b.isFunction(c)?c(a,e):b.isFunction(l.callback)&&l.callback(a,e)},destroy:function(){this.callHook("beforeDestroy")!==!1&&(this.model.el.remove(),g.off(),b("#paginationjs-style").remove(),this.callHook("afterDestroy"))},previous:function(a){this.go(this.model.pageNumber-1,a)},next:function(a){this.go(this.model.pageNumber+1,a)},disable:function(){var a=this,b=a.sync?"sync":"async";a.callHook("beforeDisable",b)!==!1&&(a.disabled=!0,a.model.disabled=!0,a.callHook("afterDisable",b))},enable:function(){var a=this,b=a.sync?"sync":"async";a.callHook("beforeEnable",b)!==!1&&(a.disabled=!1,a.model.disabled=!1,a.callHook("afterEnable",b))},refresh:function(a){this.go(this.model.pageNumber,a)},show:function(){var a=this;a.model.el.is(":visible")||a.model.el.show()},hide:function(){var a=this;a.model.el.is(":visible")&&a.model.el.hide()},replaceVariables:function(a,b){var c;for(var d in b){var e=b[d],f=new RegExp("<%=\\s*"+d+"\\s*%>","img");c=(c||a).replace(f,e)}return c},getDataSegment:function(a){var b=l.pageSize,c=l.dataSource,d=l.totalNumber,e=b*(a-1)+1,f=Math.min(a*b,d);return c.slice(e-1,f)},getTotalPage:function(){return Math.ceil(l.totalNumber/l.pageSize)},getLocator:function(a){var d;return"string"==typeof a?d=a:b.isFunction(a)?d=a():c('"locator" is incorrect. (String | Function)'),d},filterDataByLocator:function(a){var d,e=this.getLocator(l.locator);if(j.isObject(a)){try{b.each(e.split("."),function(b,c){d=(d?d:a)[c]})}catch(f){}d?j.isArray(d)||c("dataSource."+e+" must be an Array."):c("dataSource."+e+" is undefined.")}return d||a},parseDataSource:function(a,d){var e=this,f=arguments;j.isObject(a)?d(l.dataSource=e.filterDataByLocator(a)):j.isArray(a)?d(l.dataSource=a):b.isFunction(a)?l.dataSource(function(a){b.isFunction(a)&&c('Unexpect parameter of the "done" Function.'),f.callee.call(e,a,d)}):"string"==typeof a?(/^https?|file:/.test(a)&&(l.ajaxDataType="jsonp"),d(a)):c('Unexpect data type of the "dataSource".')},callHook:function(c){var d,e=g.data("pagination"),f=Array.prototype.slice.apply(arguments);return f.shift(),l[c]&&b.isFunction(l[c])&&l[c].apply(a,f)===!1&&(d=!1),e.hooks&&e.hooks[c]&&b.each(e.hooks[c],function(b,c){c.apply(a,f)===!1&&(d=!1)}),d!==!1},observer:function(){var a=this,d=a.model.el;g.on(i+"go",function(d,e,f){e=parseInt(b.trim(e)),e&&(b.isNumeric(e)||c('"pageNumber" is incorrect. (Number)'),a.go(e,f))}),d.delegate(".J-paginationjs-page","click",function(c){var d=b(c.currentTarget),e=b.trim(d.attr("data-num"));return!e||d.hasClass(l.disableClassName)||d.hasClass(l.activeClassName)?void 0:a.callHook("beforePageOnClick",c,e)===!1?!1:(a.go(e),a.callHook("afterPageOnClick",c,e),l.pageLink?void 0:!1)}),d.delegate(".J-paginationjs-previous","click",function(c){var d=b(c.currentTarget),e=b.trim(d.attr("data-num"));return e&&!d.hasClass(l.disableClassName)?a.callHook("beforePreviousOnClick",c,e)===!1?!1:(a.go(e),a.callHook("afterPreviousOnClick",c,e),l.pageLink?void 0:!1):void 0}),d.delegate(".J-paginationjs-next","click",function(c){var d=b(c.currentTarget),e=b.trim(d.attr("data-num"));return e&&!d.hasClass(l.disableClassName)?a.callHook("beforeNextOnClick",c,e)===!1?!1:(a.go(e),a.callHook("afterNextOnClick",c,e),l.pageLink?void 0:!1):void 0}),d.delegate(".J-paginationjs-go-button","click",function(){var c=b(".J-paginationjs-go-pagenumber",d).val();return a.callHook("beforeGoButtonOnClick",event,c)===!1?!1:(g.trigger(i+"go",c),void a.callHook("afterGoButtonOnClick",event,c))}),d.delegate(".J-paginationjs-go-pagenumber","keyup",function(c){if(13===c.which){var e=b(c.currentTarget).val();if(a.callHook("beforeGoInputOnEnter",c,e)===!1)return!1;g.trigger(i+"go",e),b(".J-paginationjs-go-pagenumber",d).focus(),a.callHook("afterGoInputOnEnter",c,e)}}),g.on(i+"previous",function(b,c){a.previous(c)}),g.on(i+"next",function(b,c){a.next(c)}),g.on(i+"disable",function(){a.disable()}),g.on(i+"enable",function(){a.enable()}),g.on(i+"refresh",function(b,c){a.refresh(c)}),g.on(i+"show",function(){a.show()}),g.on(i+"hide",function(){a.hide()}),g.on(i+"destroy",function(){a.destroy()}),l.triggerPagingOnInit&&g.trigger(i+"go",Math.min(l.pageNumber,a.model.totalPage))}};if(g.data("pagination")&&g.data("pagination").initialized===!0){if(b.isNumeric(f))return g.trigger.call(this,i+"go",f,arguments[1]),this;if("string"==typeof f){var k=Array.prototype.slice.apply(arguments);switch(k[0]=i+k[0],f){case"previous":case"next":case"go":case"disable":case"enable":case"refresh":case"show":case"hide":case"destroy":g.trigger.apply(this,k);break;case"getSelectedPageNum":return g.data("pagination").model?g.data("pagination").model.pageNumber:g.data("pagination").attributes.pageNumber;case"getTotalPage":return g.data("pagination").model.totalPage;case"getSelectedPageData":return g.data("pagination").currentPageData;case"isDisabled":return g.data("pagination").model.disabled===!0;default:c("Pagination do not provide action: "+f)}return this}e(g)}else j.isObject(f)||c("Illegal options");var l=b.extend({},arguments.callee.defaults,f);return d(l),h.initialize(),this},b.fn[g].defaults={totalNumber:1,pageNumber:1,pageSize:10,pageRange:2,showPrevious:!0,showNext:!0,showPageNumbers:!0,showNavigator:!1,showGoInput:!1,showGoButton:!1,pageLink:"",prevText:"&laquo;",nextText:"&raquo;",ellipsisText:"...",goButtonText:"Go",classPrefix:"paginationjs",activeClassName:"active",disableClassName:"disabled",inlineStyle:!0,formatNavigator:"<%= currentPage %> / <%= totalPage %>",formatGoInput:"<%= input %>",formatGoButton:"<%= button %>",position:"bottom",autoHidePrevious:!1,autoHideNext:!1,triggerPagingOnInit:!0,hideWhenLessThanOnePage:!1,showFirstOnEllipsisShow:!0,showLastOnEllipsisShow:!0,callback:function(){}},b.fn[h]=function(a,d){arguments.length<2&&c("Missing argument."),b.isFunction(d)||c("callback must be a function.");var e=b(this),f=e.data("pagination");f||(e.data("pagination",{}),f=e.data("pagination")),!f.hooks&&(f.hooks={}),f.hooks[a]=f.hooks[a]||[],f.hooks[a].push(d)},b[g]=function(a,d){arguments.length<2&&c("Requires two parameters.");var e;return e="string"!=typeof a&&a instanceof jQuery?a:b(a),e.length?(e.pagination(d),e):void 0};var j={};b.each(["Object","Array"],function(a,b){j["is"+b]=function(a){return f(a)===b.toLowerCase()}}),"function"=="function"&&__webpack_require__(2)&&!(__WEBPACK_AMD_DEFINE_RESULT__ = function(){return b}.call(exports, __webpack_require__, exports, module), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__))}(this,__webpack_provided_window_dot_jQuery);
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1), __webpack_require__(1)))

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(jQuery) {/*
	 * Lazy Load - jQuery plugin for lazy loading images
	 *
	 * Copyright (c) 2007-2013 Mika Tuupola
	 *
	 * Licensed under the MIT license:
	 *   http://www.opensource.org/licenses/mit-license.php
	 *
	 * Project home:
	 *   http://www.appelsiini.net/projects/lazyload
	 *
	 * Version:  1.8.4
	 *
	 */
	 var $ = __webpack_require__(1);
	(function(a,b,c,d){var e=a(b);a.fn.lazyload=function(c){function i(){var b=0;f.each(function(){var c=a(this);if(h.skip_invisible&&!c.is(":visible"))return;if(!a.abovethetop(this,h)&&!a.leftofbegin(this,h))if(!a.belowthefold(this,h)&&!a.rightoffold(this,h))c.trigger("appear"),b=0;else if(++b>h.failure_limit)return!1})}var f=this,g,h={threshold:0,failure_limit:0,event:"scroll",effect:"show",container:b,data_attribute:"original",skip_invisible:!0,appear:null,load:null};return c&&(d!==c.failurelimit&&(c.failure_limit=c.failurelimit,delete c.failurelimit),d!==c.effectspeed&&(c.effect_speed=c.effectspeed,delete c.effectspeed),a.extend(h,c)),g=h.container===d||h.container===b?e:a(h.container),0===h.event.indexOf("scroll")&&g.bind(h.event,function(a){return i()}),this.each(function(){var b=this,c=a(b);b.loaded=!1,c.one("appear",function(){if(!this.loaded){if(h.appear){var d=f.length;h.appear.call(b,d,h)}a("<img />").bind("load",function(){c.hide().attr("src",c.data(h.data_attribute))[h.effect](h.effect_speed),b.loaded=!0;var d=a.grep(f,function(a){return!a.loaded});f=a(d);if(h.load){var e=f.length;h.load.call(b,e,h)}}).attr("src",c.data(h.data_attribute))}}),0!==h.event.indexOf("scroll")&&c.bind(h.event,function(a){b.loaded||c.trigger("appear")})}),e.bind("resize",function(a){i()}),/iphone|ipod|ipad.*os 5/gi.test(navigator.appVersion)&&e.bind("pageshow",function(b){b.originalEvent.persisted&&f.each(function(){a(this).trigger("appear")})}),a(b).load(function(){i()}),this},a.belowthefold=function(c,f){var g;return f.container===d||f.container===b?g=e.height()+e.scrollTop():g=a(f.container).offset().top+a(f.container).height(),g<=a(c).offset().top-f.threshold},a.rightoffold=function(c,f){var g;return f.container===d||f.container===b?g=e.width()+e.scrollLeft():g=a(f.container).offset().left+a(f.container).width(),g<=a(c).offset().left-f.threshold},a.abovethetop=function(c,f){var g;return f.container===d||f.container===b?g=e.scrollTop():g=a(f.container).offset().top,g>=a(c).offset().top+f.threshold+a(c).height()},a.leftofbegin=function(c,f){var g;return f.container===d||f.container===b?g=e.scrollLeft():g=a(f.container).offset().left,g>=a(c).offset().left+f.threshold+a(c).width()},a.inviewport=function(b,c){return!a.rightoffold(b,c)&&!a.leftofbegin(b,c)&&!a.belowthefold(b,c)&&!a.abovethetop(b,c)},a.extend(a.expr[":"],{"below-the-fold":function(b){return a.belowthefold(b,{threshold:0})},"above-the-top":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-screen":function(b){return a.rightoffold(b,{threshold:0})},"left-of-screen":function(b){return!a.rightoffold(b,{threshold:0})},"in-viewport":function(b){return a.inviewport(b,{threshold:0})},"above-the-fold":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-fold":function(b){return a.rightoffold(b,{threshold:0})},"left-of-fold":function(b){return!a.rightoffold(b,{threshold:0})}})})(jQuery,window,document)
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 10 */,
/* 11 */,
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {$(function(){
	    var no = '<i class="t-icon sc-icon ng-box-bg sc-icon-past"></i>已关注';
	    var yes = '<i class="t-icon sc-icon ng-box-bg"></i>关注';
	    // 检查是否关注
	    if( pageTrue=="product"||pageTrue=="productindex" ){
	        var $sc = $("#a_sc");
	        checkcollectgoodsAjaxFun($sc.attr('goodsid'),function(data){
	            if( data.success ){
	                if( data.result ){
	                    $sc.addClass('has-in').html(no);
	                }else{
	                    $sc.removeClass('has-in').html(yes);
	                }
	            }
	        });
	    }

		// 关注
	    $("#a_sc").on("click",function(){
	        var $this = $(this);
	        var goodsid = $this.attr('goodsid');
	        if( logoTrue ){
	            if( $this.hasClass("has-in") ){
	                delcollectgoodsAjaxFun(goodsid,function(data){
	                    if( data.success ){
	                        $this.removeClass('has-in').html(yes);
	                    }
	                });
	            }else{
	                addcollectgoodsAjaxFun(goodsid,function(data){
	                    if( data.success ){
	                        $this.addClass('has-in').html(no);
	                    }
	                });
	            }
	        }else{
	            tanLoginFun(url);
	        }
	    });

	    // 关注
	    function addcollectgoodsAjaxFun(goodsid,Callback){
	        if( !ajaxlack ) return; ajaxlack = false;
	        $.ajax({
	            url: "/goods/service/addcollectgoods",
	            type: 'POST',
	            dataType: 'json',
	            data: { goods_id:goodsid }
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
	            // //alert('网络错误！')
	        })
	        .always(function() {
	            ajaxlack = true;
	        });
	    }

	    // 取消关注
	    function delcollectgoodsAjaxFun(goodsid,Callback){
	        if( !ajaxlack ) return; ajaxlack = false;
	        $.ajax({
	            url: "/goods/service/delcollectgoods",
	            type: 'POST',
	            dataType: 'json',
	            data: { goods_id:goodsid }
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
	            // //alert('网络错误！')
	        })
	        .always(function() {
	            ajaxlack = true;
	        });
	    }

	    // 检查是否关注
	    function checkcollectgoodsAjaxFun(goodsid,Callback){
	        if( !ajaxlack ) return; ajaxlack = false;
	        $.ajax({
	            url: "/goods/service/checkcollectgoods",
	            type: 'POST',
	            dataType: 'json',
	            data: { goods_id:goodsid }
	        })
	        .done(function(data) {
	            console.log(data);
	            if( data.success ){
	                Callback(data);
	            }else{
	                
	            }
	        })
	        .fail(function() {
	            // //alert('网络错误！')
	        })
	        .always(function() {
	            ajaxlack = true;
	        });
	    }









































	});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {$(function(){
		var cartAjaxTrue = true;

		// 判读显示购物车
		if( pageTrue=="home"||pageTrue=="productindex"||pageTrue=="productlist" ){
			$("#divRTool").remove();
		}else{
			$("#rightTool").remove();
		}

		// 右侧浮动窗口鼠标放上去效果
		$("#divRTool li").hover(function(){
			$(this).addClass('cart-hover');
		},function(){
			$(this).removeClass('cart-hover');
		});

		// 滚动条监控事件
		// $(document).scroll(function() {
		// 	if( $(document).scrollTop()>100 ){
		// 		$("#rightTool, #divRTool").fadeIn();
		// 	}else{
		// 		$("#rightTool, #divRTool").fadeOut();
		// 	}
		// });
		$("#rightTool, #divRTool").fadeIn();

		// 我的关注
		$("#ulRToolList .f-attention").hover(function(){
			$(".g-status-standard").height(winheight);
			$(this).addClass('cart-hover');
			$("#divRTCartMain").stop().animate({"right":"-240px"});
			if( !logoTrue ){
				$("#divRTColect").show().css({"bottom":"0px", "height":winheight}).stop().animate({"right":"37px"});
			}
		},function(){
			$(this).removeClass('cart-hover');
		});

		// 右侧购物车列表
		$(".f-shopping-cart").hover(function(){
			rightcartlabelFun();
			$(".g-status-standard").height(winheight);
			$("#divRTCartMain").show().css({"bottom":"0px", "height":winheight}).stop().animate({"right":"37px"});
			$("#divRTColect").stop().animate({"right":"-240px"});
			
		});

		// 鼠标移开后处理事件
		$("#rightTool").mouseleave(function(){
			$("#divRTColect, #divRTCartMain").stop().animate({"right":"-240px"});
		});

		// 点击关注
		$(".f-attention").click(function(){
			if( logoTrue ){
				window.location.href = "/member/index/collectlist";
			}
		});

		// 弹出登录
		$(document).on("click","#btnCltLogin",function(){
	    	tanLoginFun('/member/index/collectlist');
	    });

	    function rCartFun(){
	        $(".r-plus, .r-less, .modify, .modify, .z-ygrc, #cart_shower").off("click");
	        $(".modify").off("keyup");
	        // 增加商品数量
	        $(".r-plus").on("click",function(){
	            var $this = $(this);
	            var val = eval($this.siblings("input").val())+1;
	            var codeSurplus = eval($this.siblings("input").attr("codeSurplus"));
	            var goodsid = $this.siblings("input").attr("goodsid");
	            var codeID = $this.siblings("input").attr("codeID");
	            if( val>codeSurplus ) return;
	            cartNumFun(codeID,val,function(){
	                var jg = val;
	                $this.siblings("input").val(jg);
	                $this.parents("dl").find(".jiage").html(jg);
	                $this.parents("dl").find(".shuliang").html(jg);
	                cartListNum();
	            });
	        });

	        // 减少商品数量
	        $(".r-less").on("click",function(){
	            var $this = $(this);
	            var val = eval($this.siblings("input").val())-1;
	            var goodsid = $this.siblings("input").attr("goodsid");
	            var codeID = $this.siblings("input").attr("codeID");
	            if( val<1 ) return;
	            cartNumFun(codeID,val,function(){
	                var jg = val;
	                $this.siblings("input").val(jg);
	                $this.parents("dl").find(".jiage").html(jg);
	                $this.parents("dl").find(".shuliang").html(jg);
	                cartListNum();
	            });
	        });

	        // 输入价格
	        $(".modify").on("keyup", function(){
	            var $this = $(this);
	            var goodsid = $(this).attr("goodsid");
	            var codeID = $(this).attr("codeID");
	            var val = $this.val();
	            cartNumFun(codeID,$this.val(),function(){
	                $this.parents("dl").find(".jiage").html(jg);
	                $this.parents("dl").find(".shuliang").html(jg);
	                cartListNum();
	            });
	        });

	        // 删除
	        $("#cart_shower .delete-close").on("click", function(){
	            var goodsid = $(this).attr("goodsid");
	            var codeID = $(this).attr("codeID");
	            $(this).parents("dl").addClass("deleteDl");
	            cartClearFun(codeID,function(){
	                $(".deleteDl").remove();
	                var $cartNumID = $("#rightCartNum");
	                console.log($("#cart_shower dl").length);
	                if( $("#cart_shower dl").length<=0 ){
	                	$(".cartEmpty").show().siblings().hide();
	                	$cartNumID.html(0);
	                	return;
	                }
		            var cartNum = eval($cartNumID.html())-1;
		            $cartNumID.html(cartNum); 
	            });
	        });

	        // 右侧购物车鼠标放上去调整商品数量
	        $(".z-ygrc").hover(function(){
	            $(this).parents("dl").addClass("hover");
	            $(this).find(".three-row").show().siblings().hide();
	        },function(){
	            $(this).find(".three-row").hide().siblings().show();
	            $(this).parents("dl").removeClass("hover");
	        });
	    }

	    // 去结算
	    $(".accounts-btn").on("click", function(){
	        if( logoTrue ){
	            window.location.href = '/yungou/cart/list';
	        }else{
	            tanLoginFun('/yungou/cart/list');
	        }
	    });


		// 购物车输出
		window.rightcartlabelFun = function (){
			if( !cartAjaxTrue ) return; cartAjaxTrue = false;
			$.ajax({
		        url: "/order/cart/cartlabel",
		        type: 'POST',
		        dataType: 'json',
		        data: {}
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
					var dataLi = '';
					var count = data.result.count;//': 6,
					var money = data.result.money;//': 6

					// 购物车数量
					var $numID = $("#rightCartNum");
					if ( count<=0 ) {
						$numID.hide().html(0);
					}
					else{
						$numID.show().html(count);
						$("#divCart").html('<cite id="myCart"><a href="/yungou/cart/list" class="gray9" target="_blank">您的购物车有<em class="orange">'+count+'</em>件商品，合计:<em class="orange">￥'+money+'.00</em><i class="f-tran">&gt;</i></a></cite>');
					}

					$.each(data.result.datas, function(i,item){
						var codeID = item.codeID//': 2797956,
			            var goodsPic = item.goodsPic//': '20151023164405174.jpg',
			            var goodsName = item.goodsName//': '苹果（Apple）iPadPro12.9英寸平板电脑32GWiFi版',
			            var shopNum = item.shopNum//': 1,
			            var goodsID = item.goodsID//': 22591,
			            var codeQuantity = item.codeQuantity//': 5999,
			            var codeSurplus = item.codeSurplus//': 5911,
			            var codeType = item.codeType//': 0,
			            var codeLimitBuy = item.codeLimitBuy//': 0,
			            var myLimitSales = item.myLimitSales//': 0
						dataLi += '<dl goodsid="'+goodsID+'" codeID="'+codeID+'">'
				            +'<dd>'
				                +'<a href="javascript:;" target="_blank">'
				                    +'<img src="'+goodsPic+'">'
				                +'</a>'
				            +'</dd>'
				            +'<dd class="z-ygrc">'
				                +'<p class="two-row" style="display: block;">'
				                    +'<cite>'
				                        +'<em>云购人次：</em><em class="shuliang">'+shopNum+'</em>'
				                    +'</cite>'
				                    +'<cite>'
				                        +'<em>小计：</em>￥<em class="jiage" style="color:#f60;">'+shopNum+'</em>.00'
				                    +'</cite>'
				                +'</p>'
				                +'<p class="three-row" style="display: none;">'
				                    +'<span class="gray6">剩余 '+codeSurplus+'人次</span>'
				                    +'<span class="modify">'
				                        +'<a href="javascript:;" class="r-less">-</a>'
				                        +'<input type="text" value="'+shopNum+'" codeSurplus="'+codeSurplus+'" goodsid="'+goodsID+'" codeID="'+codeID+'">'
				                        +'<a href="javascript:;" class="r-plus">+</a>'
				                    +'</span>'
				                    +'<span id="miniTips"></span>'
				                +'</p>'
				            +'</dd>'
				            +'<dd class="z-close">'
				                +'<a href="javascript:;" title="删除" class="delete-close transparent-png" goodsid="'+goodsID+'" codeID="'+codeID+'"></a>'
				            +'</dd>'
				        +'</dl>';
			    	});
					
					if( dataLi=='' ){
						$(".cartEmpty").show().siblings().hide();
					}else{
						$(".cartEmpty").hide().siblings().show();
						$("#cart_shower").html(dataLi);
						$(".f-unfold-pay li.total-txt").html('共<em class="orange">'+count+'</em>个商品，合计：<b class="orange">'+money+'.00</b>元');
					}
		        }else{

		        }
		    })
		    .fail(function() {

		    })
		    .always(function() {
		    	cartAjaxTrue = true;
		    	rCartFun();
		    });
		}

		rightcartlabelFun();


	    // 循环购物车数量
	    function cartListNum(){
	        var $dd = $("#cart_shower dl");
	        var ddNum = $dd.length;
	        var val = 0;
	        for( var i=0; i<ddNum; i++ ) {
	            val += eval($dd.eq(i).find(".jiage").html());
	        }
	        $(".f-unfold-pay li.total-txt").html('共<em class="orange">'+ddNum+'</em>个商品，合计：<b class="orange">'+val+'.00</b>元');
	    }


	});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }
]);