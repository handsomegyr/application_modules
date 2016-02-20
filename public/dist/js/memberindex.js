webpackJsonp([6],[
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(7);
	__webpack_require__(15);
	__webpack_require__(16);
	__webpack_require__(8);
	__webpack_require__(17);
	__webpack_require__(5);
	__webpack_require__(3);
	__webpack_require__(13);
	__webpack_require__(18);
	__webpack_require__(19);
	__webpack_require__(20);
	__webpack_require__(21);
	$(function(){

	// 日历JS
	if( pageTrue=="orderlist"||pageTrue=="userbuylist"||pageTrue=="userbalance"||pageTrue=="memberpoints" ){
		var checkin = $('#dpd1').datepicker({
	        format: 'yyyy-mm-dd'
	    }).on('changeDate', function(ev) {
	        checkin.hide();
	    }).data('datepicker');

	    var checkout = $('#dpd2').datepicker({
	        format: 'yyyy-mm-dd'
	    }).on('changeDate', function(ev) {
	        checkout.hide();
	    }).data('datepicker');
	}

	// 消息管理
	if( pageTrue=="usermessage"||pageTrue=="friendsapply"||pageTrue=="replycommentsmsg"||pageTrue=="userprivmsg" ){
		getusermsgcountAjaxFun();
		function getusermsgcountAjaxFun(){
			$.ajax({
		        url: "/message/service/getusermsgcount",
		        type: 'POST',
		        dataType: 'json'
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	var $ulMenuLi = $("#ul_menu li");
		        	var FriendCount = data.result.FriendCount;
		        	var privMsgCount = data.result.privMsgCount;
		        	var replyMsgCount = data.result.replyMsgCount;
		        	var sysMsgCount = data.result.sysMsgCount;
		        	$ulMenuLi.find("span").remove();
		        	if( FriendCount>0 ){ //好友
		        		$ulMenuLi.eq(1).append('<span>'+FriendCount+'</span>');
		        	}
		        	if( privMsgCount>0 ){ //私信
		        		$ulMenuLi.eq(3).append('<span>'+privMsgCount+'</span>');
		        	}
		        	if( replyMsgCount>0 ){ //评论回复
		        		$ulMenuLi.eq(2).append('<span>'+replyMsgCount+'</span>');
		        	}
		        	if( sysMsgCount>0 ){ //系统
		        		$ulMenuLi.eq(0).append('<span>'+sysMsgCount+'</span>');
		        	}
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {

		    });
		}
	}

	/**--------------------------------------系统消息------------------------------------**/
	if( pageTrue=="usermessage" ){

		getusermessagelistFirstDataFun(1,5);

		function getusermessagelistFirstDataFun(page,limit){
			$("#ul_sysmsg").html('<div class="loading-2015"><em></em></div>');
			var dataObj = {
				page:page,
				limit:limit
			}
			// 数据加载 第一次
			getusermessagelistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getusermessagelistAddDataFun(data);
				// 分页
				PageCreate('#div_pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					$("#ul_sysmsg").html('<div class="loading-2015"><em></em></div>');
					getusermessagelistAjaxFun(dataObj,function(data){
						getusermessagelistAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function getusermessagelistAddDataFun(data){
			if( data.success ){
				var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var msgContent = item.msgContent;//: "<a href="http://u.1yyg.com/1001851285" class="blue" target="_blank">职业代买扣54387756</a> 已通过您的好友请求。"
					var msgID = item.msgID;//: 4511375
					var msgRead = item.msgRead;//: 1
					var showTime = item.showTime;//: "2015-12-30 15:51"
		    		dataLi += '<li><span class="u-info">'+msgContent+'</span><span class="u-time">'+showTime+'</span>'
				        +'<a class="close-icon" href="javascript:;" msgid="'+msgID+'"><i class="transparent-png"></i></a>'
				        +'</li>';
		    	});
				if( dataLi=='' ){
					$(".fri-page-wrap").hide();
					$("#div_pagination").html('');
					$("#ul_sysmsg").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>您还没有系统消息哦！</span></div>');
				}else{
					$(".fri-page-wrap").show();
					$("#ul_sysmsg").html(dataLi);
				}
		    }else{

		    }
		}

		//函数
		function getusermessagelistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/message/service/getusermessagelist",
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

		$(document).on("click",'.close-icon',function(){
			var msgid = $(this).attr("msgid");
			$(this).parents("ul").find("li").removeClass("removeClass");
	    	$(this).parents("li").addClass("removeClass");
	    	tanFun(282,150,'<div class="z-pop-box clrfix"><span class="gray3">您确定要删除吗？</span><a id="btn01" href="javascript:gotoClick();" title="取消" class="z-btn-cancel">取消</a><a id="btn02" href="javascript:;" title="确定" class="z-btn-determine" msgid="'+msgid+'">确定</a></div>');
		});

		// 确认删除回复消息
	    $(document).on("click","#btn02",function(){
	    	var msgid = $(this).attr("msgid");
	    	deleteusermessageFun(msgid,function(data){
	    		if( data.success ){
	    			FailDialog(160,60,'删除成功！');
	    			$(".removeClass").remove();
	    			gotoClick();
	    			ajaxlack = true; 
	    			if( $("#ul_sysmsg li").length<=0 ){
	    				$(".fri-page-wrap").hide();
	    				$("#ul_sysmsg").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>您还没有系统消息哦！</span></div>');
	    			}
	    		}
	    	});
	    });

	    // 清空所有
	    $(document).on("click","#a_clear",function(){
	    	deleteusermessageFun('',function(data){
	    		if( data.success ){
	    			FailDialog(160,60,'清空成功！',url);
	    		}
	    	});
	    });

		// 删除系统消息
		function deleteusermessageFun(msgID,Callback){
			$.ajax({
		        url: "/message/service/deleteusermessage", 
		        type: 'POST',
		        dataType: 'json',
		        data: { msgID:msgID }
		    })
		    .done(function(data) {
		        console.log(data);
		        Callback(data);
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {

		    });
		}
	}
	/**--------------------------------------系统消息结束------------------------------------**/

	/**--------------------------------------评论回复消息------------------------------------**/
	if( pageTrue=="replycommentsmsg" ){


		getreplymsgpagebyuseridFirstDataFun(1,5);

		function getreplymsgpagebyuseridFirstDataFun(page,limit){
			$("#ul_msglist").html('<div class="loading-2015"><em></em></div>');
			var dataObj = {
				page:page,
				limit:limit
			}
			// 数据加载 第一次
			getreplymsgpagebyuseridAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getreplymsgpagebyuseridAddDataFun(data);
				// 分页
				PageCreate('#div_pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					$("#ul_msglist").html('<div class="loading-2015"><em></em></div>');
					getreplymsgpagebyuseridAjaxFun(dataObj,function(data){
						getreplymsgpagebyuseridAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function getreplymsgpagebyuseridAddDataFun(data){
			if( data.success ){
				var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var heContent = item.heContent;// "[s:13]"
						var msgID = item.msgID;// : 250143
						var msgType = item.msgType;// : "1"
						var relateID = item.relateID;// : "133109"
						var replyTime = item.replyTime;// : "01月04日 10:42"
						var replyUserID = item.replyUserID;// : "9533390"
						var userName = item.userName;// : "中个奖有这么困难嘛"
						var userWeb = item.userWeb;// : "1010029819"
						var youContent = item.youContent;// : "[s:13]"
			    		dataLi += '<li>'
			                +'<a target="_blank" href="/yungou/member/index?id='+userWeb+'" class="u-name">'+userName+'</a>'
			                +'<span>'+( msgType=="1" ? '回复了您发表的晒单评论' : '回复了您发表的晒单' )+'</span>'
			                +'<a target="_blank" href="/yungou/post/detail?id='+relateID+'" class="u-title">'+youContent+'</a>'
			                +'<span class="u-time">'+replyTime+'</span>'
			                +'<a href="javascript:;" msgid="'+msgID+'" class="close-icon"><i class="transparent-png"></i></a>'
			            +'</li>';
		    	});
				if( dataLi=='' ){
					$(".fri-page-wrap").hide();
					$("#ul_msglist").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>您还没有回复消息哦！</span></div>');
				}else{
					$(".fri-page-wrap").show();
					$("#ul_msglist").html(dataLi);
				}
		    }else{

		    }
		}

		//函数
		function getreplymsgpagebyuseridAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/message/service/getreplymsgpagebyuserid",
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

		$(document).on("click",'.close-icon',function(){
			var msgid = $(this).attr("msgid");
			$(this).parents("ul").find("li").removeClass("removeClass");
	    	$(this).parents("li").addClass("removeClass");
	    	tanFun(282,150,'<div class="z-pop-box clrfix"><span class="gray3">您确定要删除吗？</span><a id="btn01" href="javascript:gotoClick();" title="取消" class="z-btn-cancel">取消</a><a id="btn02" href="javascript:;" title="确定" class="z-btn-determine" msgid="'+msgid+'">确定</a></div>');
		});

		// 确认删除回复消息
	    $(document).on("click","#btn02",function(){
	    	var msgid = $(this).attr("msgid");
	    	deletereplymsgbyuseridFun(msgid,function(data){
	    		if( data.success ){
	    			FailDialog(160,60,'删除成功！');
	    			$(".removeClass").remove();
	    			gotoClick();
	    			ajaxlack = true;
	    		}
	    	});
	    });

	    // 清空所有
	    $(document).on("click","#a_clear",function(){
	    	deletereplymsgbyuseridFun(0,function(data){
	    		if( data.success ){
	    			FailDialog(160,60,'清空成功！',url);
	    		}
	    	});
	    });

		// 删除回复消息
		function deletereplymsgbyuseridFun(msgID,Callback){
			$.ajax({
		        url: "/post/service/deletereplymsgbyuserid", 
		        type: 'POST',
		        dataType: 'json',
		        data: { msgID:msgID }
		    })
		    .done(function(data) {
		        console.log(data);
		        Callback(data);
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {

		    });
		}
	}
	/**--------------------------------------评论回复消息结束------------------------------------**/


	/**--------------------------------------会员首页------------------------------------**/
	if( pageTrue=="memberingdex" ){
		$(document).on("mouseover",".commodity-list ul",function(){
			$(this).parents(".productsCon").addClass("products-hover");
			$(this).find(".hideShow").hide();
			$(this).find(".showHide").show();
		});

		$(document).on("mouseout",".commodity-list ul",function(){
			$(this).parents(".productsCon").removeClass("products-hover");
			$(this).find(".hideShow").show();
			$(this).find(".showHide").hide();
		});

		// 加入购物车
		$(document).on("click", ".list-ing",function(){
			var codeID = $(this).attr("codeID");
			var $em = $("#rightCartNum");
			var num = parseInt($em.text())+1;
			cartAddFun(codeID,1,function(){
				FailDialog(160,60,'添加成功！');
				$em.show().html(num);
	        });
		});


		// 云购记录
		getmembercenterbuymessageAjaxFun(1,4,0,'','','');
		function getmembercenterbuymessageAjaxFun(page,limit){
			$.ajax({
		        url: "/order/service/getmembercenterbuymessage", 
		        type: 'POST',
		        dataType: 'json',
		        data: { page:page, limit:limit }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
					var dataLi = '';
					$.each(data.result.datas, function(i,item){
						var buyNum = item.buyNum//: "2"
						var codeID = item.codeID//: "5699d739887c2261708b45b5"
						var codePeriod = item.codePeriod//: "2"
						var codePrice = item.codePrice//: "2.00"
						var codeQuantity = item.codeQuantity//: 2
						var codeRTime = item.codeRTime//: "2016-01-17 12:00:01.746"
						var codeSales = item.codeSales//: 2
						var codeState = item.codeState//: "3"
						var codeType = item.codeType//: "0"
						var goodsID = item.goodsID//: "563728c07f50eab004000401"
						var goodsName = item.goodsName//: "micmak马卡龙 法国进口料零食品糕点正宗法式马卡龙甜点12枚甜品"
						var goodsPic = item.goodsPic//: "/service/file/index?id=1_1609826e82tb1ca5x___0-item_pic.jpg&upload_path=goods/1"
						var purchase_time = item.purchase_time//: "2016-01-17 11:56:01.559"
						var refundNum = item.refundNum//: "0"
						var userId = item.userId//: "56761153887c22184e8b45b5"
						var userName = item.userName//: "15821****14"
						var userWeb = item.userWeb//: "56761153887c22184e8b45b5"

						if( codeState==1 ){
							dataLi += '<div class="productsCon">'
								+'<div class="proList">'
									+'<ul>'
										+'<li class="list-pic"><a target="_blank" href="/yungou/product/index?id='+codeID+'"><img src="'+goodsPic+'&w=130&h=130"></a></li>'
										+'<li class="list-name"><a target="_blank" href="/yungou/product/index?id='+codeID+';"> (第'+codePeriod+'云)'+goodsName+'</a></li>'
										+'<li class="g-progress hideShow" style="display: list-item;"><dl class="m-progress"><dt><b style="width:'+(codeSales/codeQuantity)*100+'%;"></b></dt></dl></li>'
										+'<li class="gray9 showHide" style="display: none;">您已参与'+buyNum+'人次</li>'
										+'<li class="list-add showHide" codeid="'+codeID+'" style="display: none;"><a href="javascript:;">追加</a></li>'
									+'</ul>'
								+'</div>'
							+'</div>';
						}else if( codeState==2 ){
							dataLi += '<div class="productsCon">'
								+'<div class="proList">'
									+'<ul>'
										+'<li class="list-pic"><a target="_blank" href="/yungou/lottery/detail?id='+codeID+'"><img src="'+goodsPic+'&w=130&h=130"></a></li>'
										+'<li class="list-name"><a target="_blank" href="/yungou/lottery/detail?id='+codeID+'"> (第'+codePeriod+'云)'+goodsName+'</a></li>'
										+'<li class="list-over"><a href="/yungou/lottery/detail?id='+codeID+'"> 正在揭晓 </a></li>'
									+'</ul>'
								+'</div>'
							+'</div>';
						}else if( codeState==3 ){
							dataLi += '<div class="productsCon">'
								+'<div class="proList">'
									+'<ul>'
										+'<li class="list-pic"><a target="_blank" href="/yungou/product/detail?id='+codeID+'"><img src="'+goodsPic+'&w=130&h=130"></a></li>'
										+'<li class="list-name"><a target="_blank" href="/yungou/product/detail?id='+codeID+'"> (第'+codePeriod+'云)'+goodsName+'</a></li>'
										+'<li class="list-over"><a href="/yungou/product/detail?id='+codeID+'"> 已经揭晓 </a></li>'
									+'</ul>'
								+'</div>'
							+'</div>';
						}
			    	});
					
					if( dataLi=='' ){
						$("#g_buys_records").hide();
					}else{
						$("#g_buys_records").show();
						$("#div_UserBuyList").html(dataLi);
					}


		        }else{

		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {

		    });
		}


		// 关注
		getcollectgoodslistindexAjaxFun(1,4);
		function getcollectgoodslistindexAjaxFun(page,limit){
			$.ajax({
		        url: "/goods/service/getcollectgoodslist", 
		        type: 'POST',
		        dataType: 'json',
		        data: { page:page, limit:limit }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
					var dataLi = '';
					$.each(data.result.datas, function(i,item){
						var codeID = item.codeID//: "5699cff6887c2261708b45a9"
						var codePeriod = item.codePeriod//: "3"
						var codeType = item.codeType//: 0
						var goodsID = item.goodsID//: "563728c07f50eab004000404"
						var goodsName = item.goodsName//: "【周黑鸭旗舰店_锁鲜装】盒装鸭翅190g*2 武汉特产官方食品零食"
						var goodsPic = item.goodsPic//: "/service/file/index?id=1_161020d50ftb1ztcx___0-item_pic.jpg&upload_path=goods/1"
						var isSale = item.isSale//: "1"
						var limitBuy = item.limitBuy//: 0
						var quantity = item.quantity//: 2
						var sales = item.sales//: 0
						if( isSale==1 ){
				    		dataLi += '<div class="productsCon">'
							    +'<div class="proList">'
							        +'<ul>'
							            +'<li class="list-pic"><a target="_blank" href="/yungou/product/index?id='+goodsID+'"><img src="'+goodsPic+'&w=130&h=130"></a></li>'
							            +'<li class="list-name hideShow" style="display: block;"><a target="_blank" href="javascript:;">'+goodsName+'</a></li>'
							            +'<li style="display: none;" class="g-progress showHide"><dl class="m-progress"><dt><b style="width:'+(sales/quantity)*100+'%;"></b></dt></dl></li>'
							            +'<li style="display: none;" class="list-ing showHide" codeid="'+codeID+'"><a href="javascript:;">加入购物车</a></li>'
							            +'<li class="list-ing hideShow" style="display: list-item;"><a href="javascript:;">第<em class="orange">'+codePeriod+'</em>云进行中…</a></li>'
							        +'</ul>'
							    +'</div>'
							+'</div>';
						}
			    	});
					
					if( dataLi=='' ){
						$(".g-my-attention").hide();
					}else{
						$(".g-my-attention").show();
						$("#div_AttentionList").html(dataLi);
					}
		        }else{

		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {

		    });
		}


		// 最新晒单
		getpagelistpostsingleindexAjaxFun(1,9);
		function getpagelistpostsingleindexAjaxFun(page,limit){
			$.ajax({
		        url: "/post/service/getpagelistpostsingle", 
		        type: 'POST',
		        dataType: 'json',
		        data: { page:page, limit:limit }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
					var dataLi = '';
					$.each(data.result.datas, function(i,item){
			    		dataLi += '<div class="m-single">'
							+'<div class="single-list">'
								+'<ul>'
									+'<li class="sig-pic"><a target="_blank" href="javascript:;"> <img src=".jpg"></a></li>'
									+'<li class="sig-title"><a target="_blank" href="javascript:;"> <b>中了！中了！！！ </b></a></li>'
									+'<li class="sig-text">非常感谢云购！两元钱中了个电饭锅！！在我投入几百块后中的价值最高的一份！！感谢运气…</li>'
									+'<li>昨天 19:37</li>'
									+'<li class="sig-xmjdh"><a href="javascript:;"> <cite class="fl"><img src=".jpg"><s></s></cite> <em class="fl"> 18606****75 </em></a> <b class="curr-arrow"> <s class="u-personal"> </s></b></li>'
								+'</ul>'
							+'</div>'
						+'</div>';
			    	});
					
					if( dataLi=='' ){
						$("#div_PostList").html('<div class="null-data" style="margin:40px 0;"><b class="gth-icon"></b>暂无云友购买记录！</div>');
					}else{
						$("#div_PostList").html(dataLi);
					}

		        }else{

		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {

		    });
		}

		// 好友动态
		var friendmsgLen = $("#ul_friendmsg li").length;
		if( friendmsgLen>=9 ){
			// setInterval(function(){
			// 	$("#ul_friendmsg").prepend("<li>"+$("#ul_friendmsg li:last").html()+"</li>").css({marginTop:"-76px"}).animate({marginTop:"0",function(){
			// 		$("#ul_friendmsg li:last").remove();
			// 	}});
			// },3000);
		}



	}
	/**--------------------------------------会员首页结束------------------------------------**/


	/**--------------------------------------配货地址增加修改------------------------------------**/
	if( pageTrue=="orderdetail" ){
		var AddressHTML = $("#shiAddress").html();
		// 地址选择
		$("#ul_addrlist li").on("click", function(){
			var $this = $(this);
			if( !$this.hasClass("new-address") ){
				$this.addClass("z-che").siblings().removeClass("z-che");
			}else{
				// 新增地址
				console.log(AddressHTML);
				tanFun(765,466,AddressHTML);
			}
		});

		// 修改和新增地址确认
		$(document).on("click", "#btnSubmit", function(){
			var $zche = $("#ul_addrlist .z-che");
			var name = $zche.attr('name');
			var tel = $zche.attr('tel');
			var address = $zche.attr('address');
			var id = $zche.attr('id');
			tanFun(600,250,'<div class="z-pop-box"><h2 class="gray3">确认地址后将不可修改，是否确认提交地址？</h2><span class="gray6 box-bgcolor">'+name+'<i class="f-mar-left">'+tel+'</i><br><em>'+address+'</em></span><a id="btnCancel" href="javascript:gotoClick()" title="取消" class="z-btn-cancel">取消</a><a id="btnOK" href="javascript:;" title="确定" _id="'+id+'" class="z-btn-determine">确定</a></div>');
		});

		// 提交地址
		$(document).on("click", "#btnOK", function(){
			var id = $(this).attr('_id');
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/order/service/confirmorderconsignee", 
		        type: 'POST',
		        dataType: 'json',
		        data: { order_no:$.query.get('orderno'), consignee_id:id, message:$("#txtRemark").val() }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	tanFun(518,270,'<div class="share-Pop-ups"><h4><b><i class="u-personal"></i>地址已提交！请您耐心等待发货</b></h4><p class="gray9">向朋友们炫耀一下吧！</p><ul class="share-content"><li class="f-share-icon"><div class="bdsharebuttonbox bdshare-button-style2-16" data-bd-bind="1450577368417"><a href="javascript:;" data-cmd="weixin" class="z-wx-friends" title="分享到微信"></a><a href="javascript:;" data-cmd="sqq" class="z-wx-group" title="分享到QQ好友"></a><a href="javascript:;" data-cmd="tsina" class="z-sina" title="分享到新浪微博"></a><a href="javascript:;" data-cmd="qzone" class="z-qq" title="分享到QQ空间"></a></div></li></ul></div>');
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		});

		// 地址确认后刷新页面
		

		// 修改地址
	    $(".z-alter").on("click",function(){
	    	var $this = $(this);
	    	var id = $this.attr("id");
	    	var $thisLi = $this.parents("li");
	    	var name = $thisLi.attr("name");
	    	var tel = $thisLi.attr("tel");
	    	var address = $thisLi.attr("address").split(" ");
	    	var code = $thisLi.attr("code");
	    	var provinceID = $thisLi.attr('provinceID');
	    	var cityID = $thisLi.attr('cityID');
	    	var districtID = $thisLi.attr('districtID');

	    	tanFun(765,466,AddressHTML);
	    	
	    	$("#btnSaveAddress").attr({"_id":id});
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

		// 确认收货
		$("#btnConfirmReceipt").on("click", function(){
			var id = $(this).attr('_id');
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/order/service/confirmorderreceive", 
		        type: 'POST',
		        dataType: 'json',
		        data: { order_no:$.query.get('orderno') }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
					FailDialog(160,60,'确认成功！',url);
		        }else{
		        	FailDialog(160,60,'确认失败！');
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
	/**--------------------------------------配货地址增加修改结束------------------------------------**/


	/*******************************************************查找好友**************************************************/
	if( pageTrue=="searchfriends" ){
		// 标签切换
		$("#ul_menu li").on("click",function(){
			var index = $(this).index();
			$(this).addClass("current").siblings().removeClass("current");
			if( index==0 ){
				searchDataFun(index,'',1,9);
				$("#div_search, #div_title").show();
				$("#div_change").hide();
				$("#midNavLine").animate({left:"269px",width:"32px"});
			}else if( index==1 ){
				searchDataFun(index,'',1,9);
				$("#div_search, #div_title").hide();
				$("#div_change").show();
				$("#midNavLine").animate({left:"365px",width:"96px"});
			}else if( index==2 ){
				searchDataFun(index,'',1,9);
				$("#div_search, #div_title").hide();
				$("#div_change").show();
				$("#midNavLine").animate({left:"528px",width:"64px"});
			}else if( index==3 ){
				searchDataFun(index,'',1,9);
				$("#div_search, #div_title").hide();
				$("#div_change").show();
				$("#midNavLine").animate({left:"657px",width:"64px"});
			}
		});

		// 换一批
		$("#div_change").on("click",function(){
			var index = $("#ul_menu li.current").index();
			console.log(index);
			searchDataFun(0,'',1,9);
		});

		// 加好友
		$(document).on("click",".f-add",function(){
			var userid = $(this).attr("userid");
			insertuserfriendapplyAjaxFun(userid,function(data){
				if( data.success ){
					FailDialog(260,60,'申请成功，请等待对方通过！');
				}else{
					FailDialog(260,60,'申请失败，请重试！');
				}
			});
		});

		function insertuserfriendapplyAjaxFun(userID,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/insertuserfriendapply",
		        type: 'POST',
		        dataType: 'json',
		        data: {userID:userID}
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
		
		// 搜索
		$("#btnSearch").on("click",function(){
			var type = 0;
			var key = $("#txtKeyWord").val();
			var page = 1;
			var limit = 9;

			if( key==""||key.length<2 ){
				FailDialog(305,60,'请输入至少两个字符的昵称搜索！');
				return;
			}
			searchDataFun(type,key,page,limit);
		});
		searchDataFun(0,'',1,9);
		function searchDataFun(type,key,page,limit){
			$("#div_userlist").html('<div style="text-align: center;"><img src="/Images/loding.gif"></div>');
			var dataObj = {
				type:type,
				key:key,
				page:page,
				limit:limit
			}
			getmembersearchfriendsAjaxFun(dataObj,function(data){
				var total = data.result.total;
				dataListFun(data);
				PageCreate('#g-pagination',total,9,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					getmembersearchfriendsAjaxFun(dataObj,function(data){
						dataListFun(data);
					});
			    });
			});
		}

		// 数据插入
		function dataListFun(data){
			var dataLi = '';
			$.each(data.result.datas, function(i,item){
				var userWeb = item.userWeb;
				var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
				var gradeName = item.gradeName;
				var address = item.address;
				var sign = item.sign;
				var userID = item.userID;
				var userName = item.userName;
				dataLi += '<li><div class="pro">'
					+'<a target="_blank" href="'+userWeb+'" class="h-pic">'
					  	+'<img src="'+userPhoto+'" width="80" height="80">'
					+'</a>'
					+'<div class="tail">'
					  +'<a target="_blank" href="'+userWeb+'" class="u-name">'+userName+'</a>'
					  +'<p>'
					      +'<span class="level class-icon02"><s></s>'+gradeName+'</span>'
					      +'<span class="u-address">'
					        	+'<i class="transparent-png"></i>'+address
					      +'</span>'
					  +'</p>'
					  +'<p class="qm">'+sign+'</p>'
					  +'<a href="javascript:;" userid="'+userID+'" class="f-add"><i class="transparent-png"></i>加好友</a>'
					+'</div>'
				+'</div></li>'				    
			});
			if( dataLi=='' ){
				$("#div_userlist").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>暂无记录</span></div>');
			}else{
				$("#div_userlist").html('<ul class="fri-ship-list"></ul>').find("ul").html(dataLi);
			}
		}

		// 查找好友AJAX函数
		function getmembersearchfriendsAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/getmembersearchfriends",
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
	/*******************************************************查找好友结束*********************************************/



	/*******************************************************好友列表*********************************************/
	if( pageTrue=="myfriends" ){
		// 删除好友
		$(document).on("click",".f-del",function(){
			var friendID = $(this).attr('userID');
			$(this).parents("li").addClass("deleteuserfriendclass");
			tanFun(398,197,'<div class="z-popUp z-pop-box">'
	            +'<span class="gray3">确定要删除？</span>'
	            +'<a href="javascript:gotoClick();" title="取消" class="z-btn-cancel">取消</a>'
	            +'<a href="javascript:;" title="确定" id="'+friendID+'" class="z-btn-determine address-delete">确定</a>'
	            +'</div>');
		});

		// 确认删除地址
	    $(document).on("click",".address-delete",function(){
	        var id = $(this).attr("id");
	        deleteuserfriendAjaxFun(id,function(){
	        	gotoClick();
				if( data.success ){
					FailDialog(160,60,'删除成功！',url);
					$(".deleteuserfriendclass").remove();
				}else{
					FailDialog(260,60,'删除失败,请重试！');
				}
			});
	    });

	    // 发私信
	    $(document).on("click",".private_btn",function(){
	        var _con = $(this).attr("_con"); _con = '<iframe hidefocus="true" frameborder="0" scrolling="no" src="/yungou/message.html'+_con+'" style="width:100%;height:100%;"></iframe>';
	        $("#pageDialogClose").show();
	        $(".title").remove();
	        $("#pageDialogMain").prepend('<div class="title">对 '+$(this).attr("username")+' 说：</div>');
	        tanFun(500,195,_con);
	    });

		// 搜索
		$("#btnSearch").on("click",function(){
			var key = $("#txtKeyWord").val();
			myfriendsDataFun(key,1,9);
		});
		myfriendsDataFun('',1,9);
		function myfriendsDataFun(key,page,limit){
			$("#div_userlist").html('<div style="text-align: center;"><img src="/Images/loding.gif"></div>');
			var dataObj = {
				key:key,
				page:1,
				limit:9
			}
			getmemberfriendsAjaxFun(dataObj,function(data){
				var total = data.result.total;
				myfriendsListFun(data);
				PageCreate('#g-pagination',total,9,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					getmemberfriendsAjaxFun(dataObj,function(data){
						myfriendsListFun(data);
					});
			    });
			});
		}

		// 数据插入
		function myfriendsListFun(data){
			var dataLi = '';
			$.each(data.result.datas, function(i,item){
				var userWeb = item.userWeb;
				var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
				var gradeName = item.gradeName;
				var address = item.address;
				var sign = item.sign;
				var userID = item.userID;
				var userName = item.userName;
				dataLi += '<li><div class="pro">'
					+'<a target="_blank" href="'+userWeb+'" class="h-pic">'
					  	+'<img src="'+userPhoto+'" width="80" height="80">'
					+'</a>'
					+'<div class="tail">'
					  +'<a target="_blank" href="'+userWeb+'" class="u-name">'+userName+'</a>'
					  +'<p>'
					      +'<span class="level class-icon02"><s></s>'+gradeName+'</span>'
					      +'<span class="u-address">'
					        	+'<i class="transparent-png"></i>'+address
					      +'</span>'
					  +'</p>'
					  +'<p class="qm">'+sign+'</p>'
					  +'<div class="f-option-wrap">'
	                        +'<a href="javascript:;" _con="?page=friend&msgToUID='+userID+'" username="'+userName+'" class="f-send private_btn">'
	                            +'<i class="transparent-png"></i>发私信'
	                        +'</a>'
	                        +'<a href="javascript:;" userID="'+userID+'" class="f-del">删除</a>'
	                    +'</div>'
					+'</div>'
				+'</div></li>'				    
			});
			if( dataLi=='' ){
				$("#div_myfriends").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>暂无记录</span></div>');
			}else{
				$("#div_myfriends").html('<ul class="fri-ship-list"></ul>').find("ul").html(dataLi);
			}
		}

		// 好友列表AJAX函数
		function getmemberfriendsAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/getmemberfriends",
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

		// 删除好友
		function deleteuserfriendAjaxFun(friendID,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/deleteuserfriend",
		        type: 'POST',
		        dataType: 'json',
		        data: { friendID:friendID }
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

		window.friendMessageFun = function(){
			gotoClick();
			$("#pageDialogMain .title").hide();
			FailDialog(180,60,'发送成功！');
		}
	}
	/*******************************************************好友列表结束*********************************************/



	/*******************************************************获取好友列表**************************************************/
	if( pageTrue=="friendsapply" ){
		// 获取好友请求列表
		getmemberfriendsapplyAjaxFun();
		function getmemberfriendsapplyAjaxFun(){
			var page = 1;
			var limit = 50;
			$.ajax({
		        url: "/member/service/getmemberfriendsapply",
		        type: 'POST',
		        dataType: 'json',
		        data: { page:page, limit:limit }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
					var dataLi = '';
					$.each(data.result.datas, function(i,item){
						var applyID = item.applyID;
						var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
						var applyTime = item.applyTime;
						var applyID = item.applyID;
						var gradeName = item.gradeName;
						var userName = item.userName;
						var userID = item.userID;
						var userWeb = item.userWeb;
						dataLi += '<li applyid="'+applyID+'"><div class="fl">'
					        +'<a target="_blank" href="'+userWeb+'" class="h-pic"><img src="'+userPhoto+'" width="80" height="80"></a>'
					        +'<div class="u-info">'
					            +'<p class="u-name"><a href="'+userWeb+'">'+userName+'</a>请求您加为好友</p>'
					            +'<span class="level class-icon01"><s></s>'+gradeName+'</span>'
					            // +'<p>51分钟前</p>'
					        +'</div>'
					    +'</div>'
					    +'<div class="u-btn-wrap">'
					        +'<a id="d_agree" herf="javascript:;" applyid="'+applyID+'" class="u-btn agree">同意</a>'
					        +'<a id="d_ignore" herf="javascript:;" applyid="'+applyID+'" class="u-btn elide">忽略</a>'
					    +'</div>'
					+'</li>';			    
					});
					if( dataLi=='' ){
						$(".fri-page-wrap").hide();
						$("#ul_friendsapply").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>您还没有好友请求哦！</span></div>');
					}else{
						$(".fri-page-wrap").show();
						$("#ul_friendsapply").html(dataLi);
					}
				}else{
					getmemberfriendsapplyAjaxFun();
				}
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	
		    });
		}

		// 同意加好友
		$(document).on("click","#d_agree",function(){
			var applyid = $(this).attr("applyid");
			agreeuserfriendAjaxFun(applyid,function(data){
				if( data.success ){
					FailDialog(200,60,'已成为好友！',url);
				}else{
					FailDialog(260,60,'处理失败,请重试！');
				}
			});
		});

		// 全部同意加好友
		$(document).on("click","#a_agree",function(){
			var applyid = 0;
			agreeuserfriendAjaxFun(applyid,function(data){
				if( data.success ){
					FailDialog(160,60,'已全部加为好友！',url);
				}else{
					FailDialog(260,60,'处理失败,请重试！');
				}
			});
		});

		// 忽略加好友
		$(document).on("click","#d_ignore",function(){ 
			var applyid = $(this).attr("applyid");
			ignoreuserfriendAjaxFun(applyid,function(data){
				if( data.success ){
					FailDialog(180,60,'忽略成功！',url);
				}else{
					FailDialog(260,60,'忽略失败,请重试！');
				}
			});
		});

		// 全部忽略加好友
		$(document).on("click","#a_ignore",function(){ 
			var applyid = 0;
			ignoreuserfriendAjaxFun(applyid,function(data){
				if( data.success ){
					FailDialog(180,60,'全部忽略成功！',url);
				}else{
					FailDialog(260,60,'全部忽略失败,请重试！');
				}
			});
		});

		// 循环获取ID
		function agreeForFun(_ID,name){
			var $ID = $(_ID);
			var nameArr = '';
			for ( var i=0; i<$ID.length; i++ ) {
				nameArr =  (nameArr=="" ? $ID.eq(i).attr(name) : nameArr+','+$ID.eq(i).attr(name) );
			};
			return nameArr;
		}

		// 同意
		function agreeuserfriendAjaxFun(applyID,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/agreeuserfriend",
		        type: 'POST',
		        dataType: 'json',
		        data: { applyID:applyID }
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

		// 忽略
		function ignoreuserfriendAjaxFun(applyID,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/ignoreuserfriend",
		        type: 'POST',
		        dataType: 'json',
		        data: { applyID:applyID }
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
	/*******************************************************获取好友列表结束*********************************************/


	/*******************************************************私信列表**************************************************/
	if( pageTrue=="userprivmsg" ){

		getuserprivmsglistAjaxFun();
		// 私信列表
		function getuserprivmsglistAjaxFun(){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/message/service/getuserprivmsglist",
		        type: 'POST',
		        dataType: 'json',
		        data: { page:1,limit:20 }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	var dataLi = '';
		        	$.each(data.result.datas, function(i,item){
		        		var msgContents = item.msgContents;
						var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
						var msgNum = item.msgNum;
						var senderUserID = item.senderUserID;
						var showTime = item.showTime;
						var unReadNum = item.unReadNum;
						var userName = item.userName;
						var userWeb = item.userWeb;
						var is_me = item.is_me;
		        		dataLi += '<li><a class="h-pic" href="/member/index/userprivmsgdetail?senderUserID='+senderUserID+'" target="_blank">'
			                  +'<img width="50" height="50" src="'+userPhoto+'">'
			              +'</a>'
			              +'<div class="info">'
			                  +(is_me==1 ? '<span class="gray9">我对</span><a class="u-name" href="javascript:;">'+userName+'</a><span class="gray9">说：</span><span>'+msgContents+'</span>' : '<a class="u-name" href="javascript:;">'+userName+'</a><span class="gray9">对您说：</span><span>'+msgContents+'</span>')
			              +'</div>'
			              +'<div class="time-wrap">'
			                  +'<span class="gray9">'+showTime+'</span>'
			                  +'<span class="fr">'
			                      +'<a href="/member/index/userprivmsgdetail?senderUserID='+senderUserID+'">共'+msgNum+'条私信</a>'
			                      +'&nbsp;&nbsp;'
			                      +'<a href="/member/index/userprivmsgdetail?senderUserID='+senderUserID+'">回复</a>'
			                  +'</span>'
			              +'</div>'
			              +'<a class="close-btn" href="javascript:;" senderuserid="'+senderUserID+'"><span></span></a></li>';
		        	});
					if( dataLi=='' ){
						$(".fri-page-wrap").hide();
						$("#ul_primsg").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>您还没有好友私信哦！</span></div>');
					}else{
						$(".fri-page-wrap").show();
						$("#ul_primsg").html(dataLi);
					}
		        }else{

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
	/*******************************************************私信列表结束*********************************************/

	/*******************************************************私信详细页**************************************************/
	if( pageTrue=="userprivmsgdetail" ){
		var sendUserID = $.query.get("senderUserID");
		$(".text-wrap").html('<iframe hidefocus="true" name="myFrame" frameborder="0" scrolling="no" src="/yungou/message.html?page=userprivmsgdetail&msgToUID='+sendUserID+'" style="width:100%;height:100%;"></iframe>');
		
		window.userprivmsgdetailMessageFun = function(){
			FailDialog(180,60,'发送成功！');
		}

		detailDataFun(1,8);
		function detailDataFun(page,limit){
			$("#ul_msglist").html('<div style="text-align: center;"><img src="/Images/loding.gif"></div>');
			var dataObj = {
				page:page,
				limit:limit,
				sendUserID:sendUserID
			}
			getuserprivmsgdetaillistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getuserprivmsgdetaillistDataFun(data);
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					getuserprivmsgdetaillistAjaxFun(dataObj,function(data){
						getuserprivmsgdetaillistDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function getuserprivmsgdetaillistDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var msgContents = item.msgContents;
					var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
					var msgID = item.msgID;
					var senderUserID = item.senderUserID;
					var showTime = item.showTime;
					var userName = item.userName;
					var userWeb = item.userWeb;
					var is_me = item.is_me;
		    		dataLi += '<li class="'+(is_me==1 ? "mine" : "other")+' clrfix">'
		              +'<a class="h-pic" href="javascript:;" target="_blank">'
		                  +'<img width="50" height="50" src="'+userPhoto+'">'
		              +'</a>'
		              +'<div class="info-tail">'
		                  +'<p>'
		                      +'<a class="blue" href="javascript:;" target="_blank">'+userName+'</a>：<span>'+msgContents+'</span>'
		                  +'</p>'
		                  +'<span class="gray9">'+showTime+'</span>'
		                  +'<b><b></b></b>'
		                  +'<a class="close-btn" href="javascript:;" oppuserid="'+msgID+'"><span></span></a>'
		              +'</div>'
		          +'</li>';
		    	});
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#ul_msglist").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>您还没有好友私信哦！</span></div>');
				}else{
					$("#g-pagination").show();
					$("#ul_msglist").html(dataLi);
				}
		    }else{

		    }
		}

		// 私信详细列表函数
		function getuserprivmsgdetaillistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/message/service/getuserprivmsgdetaillist",
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
	/*******************************************************私信详细页结束*********************************************/

	/*******************************************************关注列表页**************************************************/
	if( pageTrue=="collectlist" ){
		getcollectgoodslistDataFun(1,8);
		function getcollectgoodslistDataFun(page,limit){
			$("#div_collectlist ul").html('<div style="text-align: center;"><img src="/Images/loding.gif"></div>');
			var dataObj = {
				page:page,
				limit:limit
			}
			getcollectgoodslistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getcollectgoodslistAddDataFun(data);
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					getcollectgoodslistAjaxFun(dataObj,function(data){
						getcollectgoodslistAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function getcollectgoodslistAddDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var codeID = item.codeID//: "5699cff6887c2261708b45a9"
					var codePeriod = item.codePeriod//: "3"
					var codeType = item.codeType//: 0
					var goodsID = item.goodsID//: "563728c07f50eab004000404"
					var goodsName = item.goodsName//: "【周黑鸭旗舰店_锁鲜装】盒装鸭翅190g*2 武汉特产官方食品零食"
					var goodsPic = item.goodsPic//: "/service/file/index?id=1_161020d50ftb1ztcx___0-item_pic.jpg&upload_path=goods/1"
					var isSale = item.isSale//: "1"
					var limitBuy = item.limitBuy//: 0
					var quantity = item.quantity//: 2
					var sales = item.sales//: 0

					var percentage = (sales/quantity)*100 //销量百分比

					if( isSale==0 ){
						dataLi += '<li issale="0" class="n-pro-gray">'
						  +'<div class="n-pro-img">'
						    +'<a target="_blank" href="javascript:;"><img src="'+goodsPic+'&w=176&h=176"><s></s></a>'
						  +'</div>'
						  +'<div class="n-info-wrapper">'
						    +'<p class="gray3">'+goodsName+'</p>'
						    +'<div class="n-pro-pass">已失效</div><a style="display: none;" href="javascript:;" goodsid="'+goodsID+'" codeID="'+codeID+'" class="n-btn-del u-personal"></a>'
						  +'</div>'
						+'</li>';
					}else{
						dataLi += '<li><div class="n-pro-img">'
		                    +'<a target="_blank" href="/yungou/product/detail?id='+codeID+'">'
		                        +'<img src="'+goodsPic+'&w=176&h=176"><s></s>'
		                    +'</a></div>'
		                +'<div class="n-info-wrapper n-info-wrapper01" style="display: block;">'
		                    +'<p class="gray3">'+goodsName+'</p>'
		                    +'<div class="n-pro-num">第<span class="orange">'+codePeriod+'</span>云&nbsp;进行中<span class="dotting"></span></div>'
		                +'</div>'
		                +'<div class="n-info-wrapper n-info-wrapper02" style="display: none;">'
		                    +'<div class="n-line-wrapper">'
		                        +'<div class="n-line-inner">'
		                            +'<div class="line" style="width:'+percentage+'%"></div>'
		                        +'</div>'
		                    +'</div>'
		                    +'<a href="javascript:;" goodsid="'+goodsID+'" codeID="'+codeID+'" class="n-pro-add">加入购物车</a>'
		                    +'<a href="javascript:;" goodsid="'+goodsID+'" codeID="'+codeID+'" class="n-btn-del u-personal"></a>'
		                +'</div></li>';
					}
		    	});
				
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#div_collectlist ul").html('');
					$(".null-data").show();
				}else{
					$("#g-pagination").show();
					$("#div_collectlist ul").html(dataLi);
					$(".null-data").hide();
				}
		    }else{

		    }
		}

		//函数
		function getcollectgoodslistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/goods/service/getcollectgoodslist",
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

		//删除关注函数
		function delcollectgoodslistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/goods/service/delcollectgoods",
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

		// 鼠标放上去效果
		$(document).on("mousemove", "#div_collectlist li",function(){
			$(this).find('.n-info-wrapper01').hide();
			$(this).find('.n-info-wrapper02').show();
		});
		// 鼠标移开
		$(document).on("mouseout", "#div_collectlist li",function(){
			$(this).find('.n-info-wrapper01').show();
			$(this).find('.n-info-wrapper02').hide();
		});
		// 删除关注
		$(document).on("click", ".n-btn-del",function(){
			var goodsid = $(this).attr("goodsid");
			var codeID = $(this).attr("codeID");
			$(this).parents('ul').find("li").removeClass("delData");
			$(this).parents('li').addClass("delData");
			tanFun(300,160,'<div class="z-popUp z-pop-box">'
	            +'<span class="gray3">确定要删除所选的商品？</span>'
	            +'<a href="javascript:gotoClick();" title="取消" class="z-btn-cancel">取消</a>'
	            +'<a href="javascript:;" title="确定" goodsid="'+goodsid+'" codeID="'+codeID+'" class="z-btn-determine" id="guanzhuDelBtn">确定</a>'
	            +'</div>');
		});
		// 删除关注
		$(document).on("click", "#guanzhuDelBtn",function(){
			var goodsid = $(this).attr("goodsid");
			var codeID = $(this).attr("codeID");
			var dataObj = {
				goodsID:goodsid
			}
			delcollectgoodslistAjaxFun(dataObj,function(data){
				gotoClick();
				if( data.success ){
					FailDialog(160,60,'删除成功！',url);
					// if( $("#div_collectlist li").length<=0 ){
					// 	$(".null-data").show();
					// }else{
					// 	$(".delData").remove();
					// }
				}else{
					FailDialog(160,60,'删除失败！');
				}
			});
		});

		// 加入购物车
		$(document).on("click", ".n-pro-add",function(){
			var goodsid = $(this).attr("goodsid");
			var codeID = $(this).attr("codeID");
			var $em = $("#rightCartNum");
			var num = parseInt($em.text())+1;
			cartAddFun(codeID,1,function(){
				FailDialog(160,60,'添加成功！');
				$em.show().html(num);
	        });
		});
	}
	/*******************************************************关注列表页结束*********************************************/

	// 会员筛选拉下
	function memberXialaFun(callback){
		var thisVal = '全部状态';
		$(".u-select-con .select-state a").on("click",function(){
			var state = $(this).attr("state");
			$(this).parents('span').find('.gray6').html($(this).text()+'<s class="u-personal"></s>');
			thisVal = $(this).text();
			$(this).parent().hide();
			callback(state);
			$(".a-screen li a").removeClass("z-checked");
			if( state=="3"||state=="4" ){
				$(".a-screen li:eq("+(parseInt(state)-1)+")").find('a').addClass("z-checked");
			}else{
				$(".a-screen li:eq("+state+") a").addClass("z-checked");
			}
		});

		$(".u-select-con .gray6").on("click",function(){
			thisVal = '全部状态';
			$(this).html(thisVal+'<s class="u-personal"></s>');
			callback(0);
			$(".a-screen li a").removeClass("z-checked");
			$(".a-screen li:eq(0) a").addClass("z-checked");
		});

		$(".a-screen li").on("click",function(){
			var finda = $(this).find("a");
			var text = finda.attr("stateText");
			$(this).parents("ul").find("a").removeClass("z-checked");
			finda.addClass("z-checked");
			callback(finda.attr("state"));
			if( text!=undefined ){
				$('.u-select-con .gray6').html(text+'<s class="u-personal"></s>');
			}
		});

		$(".u-select-con").hover(function(){
			$(this).find(".gray6").html('全部状态<s class="u-personal"></s>');
			$(this).addClass("z-checked");
			$(this).find(".select-state").show();
		},function(){
			$(this).find(".gray6").html(thisVal+'<s class="u-personal"></s>');
			$(this).removeClass("z-checked");
			$(this).find(".select-state").hide();
		});

		// 日期选择筛选
		$("#a_serach").on("click",function(){
			var dpd1 = $("#dpd1").val();
			var dpd2 = $("#dpd2").val();
			if( timeGetTimeFun(dpd1)>timeGetTimeFun(dpd2) ){
				FailDialog(280,60,'开始时间不能大于结束时间！');
				return;
			}
			callback(11,dpd1,dpd2);
			$("#ul_region li a").removeClass('z-checked');
		});
	}


	/*******************************************************云购记录**************************************************/
	if( pageTrue=="userbuylist" ){

		// 时间获取
		var period1 = $("#ul_region li:eq(0) a").attr("period").split("|");
		var period3 = $("#ul_region li:eq(1) a").attr("period").split("|");
		var state = 0;
		var beginTime = period3[0];
		var endTime = period3[1];
		
		memberXialaFun(function(e,begin,end){
			switch(e){
			case "0": //全部
				state = 0;
				break;
			case "1": //进行中
				state = 1;
				break;
			case "3": //已揭晓
				state = 3;
				break;
			case "4": //已退订
				state = 4;
				break;
			case "9": //1个月
				beginTime = period1[0];
				endTime = period1[1];
				break;
			case "10":  //3个月
				beginTime = period3[0];
				endTime = period3[1];
				break;
			case 11:  //选择时间
				beginTime = begin;
				endTime = end;
				break;
			}
			userbuylistFirstDataFun(1,5,state,beginTime,endTime,'');
		});

		userbuylistFirstDataFun(1,5,state,beginTime,endTime,'');

		function userbuylistFirstDataFun(page,limit,state,beginTime,endTime,keyWords){
			$("#div_UserBuyList dd, .null-data").remove();
			$("#div_UserBuyList dl").append('<div class="loading-2015"><em></em></div>');
			var dataObj = {
				page:page,
				limit:limit,
				state:state,
				beginTime:beginTime,
				endTime:endTime,
				keyWords:keyWords
			}
			// 数据加载 第一次
			userbuylistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				userbuylistAddDataFun(data);
				// 分页
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					$("#div_UserBuyList dd, .null-data").remove();
					$("#div_UserBuyList dl").append('<div class="loading-2015"><em></em></div>');
					dataObj.page = pageNumber;
					// 翻页后数据加载
					userbuylistAjaxFun(dataObj,function(data){
						userbuylistAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function userbuylistAddDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var buyNum = item.buyNum;//: "1"
		    		var codeID = item.codeID;//: "2710789"
		    		var codePeriod = item.codePeriod;//: "10341"
		    		var codePrice = item.codePrice;//: "3488.00"
		    		var codeQuantity = item.codeQuantity;//: "3488"
		    		var codeRTime = item.codeRTime;//: "2016-01-02 14:39:47.595"
		    		var codeSales = item.codeSales;//: "3488"
		    		var codeState = item.codeState;//: "3"
		    		var codeType = item.codeType;//: "0"
		    		var goodsID = item.goodsID;//: "21976"
		    		var goodsName = item.goodsName;//: "苹果（Apple）iPad Air 2 9.7英寸平板电脑 16G WiFi版"
		    		var goodsPic = item.goodsPic;//: "20141024155736176.jpg"
		    		var refundNum = item.refundNum;//: "0"
		    		var userName = item.userName;//: "169257-猪头Q"
		    		var userWeb = item.userWeb;//: "1012509077"


		    		if( codeState=="1" ){
		    			dataLi += '<dd class="">'
			                +'<span class="u-commodity-pic">'
			                    +'<a target="_blank" href="javascript:;">'
			                        +'<img src="'+goodsPic+'">'
			                        +'<i class="u-personal"></i>'
			                    +'</a>'
			                +'</span>'
			                +'<span class="u-commodity-name gray9">'
			                    +'<h3><a target="_blank" href="javascript:;" class="gray3">(第'+codePeriod+'云)'+goodsName+'</a></h3>'
			                    +'<em>价值：￥'+codePrice+'</em>'
			                    +'<div class="g-progress"><dl class="m-progress"><dt><b style="width:'+(buyNum/codeQuantity)*100+'%;"></b></dt></dl></div>'
			                +'</span>'
			                +'<span class="u-select-con">'
			                	+'进行中<i class="dotting"></i><br><a href="javascript:;" codeid="'+codeID+'" class="z-append">追加</a>'
			                +'</span>'
			                +'<span class="u-buy-num" codeid="'+codeID+'">'
			                    +'<a href="javascript:;" class="gray6">'+buyNum+'人次</a>'
			                +'</span>'
			                +'<span class="u-operation">'
			                    +'<a target="_blank" href="javascript:;" class="z-see-details">查看所有云购码</a>'
			                +'</span>'
			                +'<span class="u-to-share" goodsid="'+goodsID+'">'
			                    +'<a href="javascript:;">一键分享</a>'
			                +'</span>'
			            +'</dd>';
		    		}else{
		    			dataLi += '<dd class=""><span class="u-commodity-pic">'
		                    +'<a target="_blank" href="javascript:;">'
		                        +'<img src="'+goodsPic+'"><i class="u-personal"></i>'
		                    +'</a>'
		                +'</span>'
		                +'<span class="u-commodity-name gray9">'
		                    +'<h3><a target="_blank" href="javascript:;" class="gray3">(第'+codePeriod+'云)'+goodsName+'</a></h3>'
		                    +'<em>价值：￥'+codePrice+'</em>获得者：<a href="javascript:;" class="z-winner-gray" target="_blank">'+userName+'</a>'
		                +'</span>'
		                +'<span class="u-select-con"><a href="javascript:;" class="gray9">已揭晓</a></span>'
		                +'<span class="u-buy-num" codeid="'+codeID+'">'
		                    +'<a href="javascript:;" class="gray6">'+buyNum+'人次</a>'
		                +'</span>'
		                +'<span class="u-operation">'
		                    +'<a target="_blank" href="javascript:;" class="z-see-details">查看所有云购码</a>'
		                +'</span></dd>';
		    		}
		    	});
				
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#div_UserBuyList dd, .loading-2015").remove();
					$("#div_UserBuyList").append('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
				}else{
					$("#g-pagination").show();
					$(".null-data, #div_UserBuyList dd, .loading-2015").remove();
					$("#div_UserBuyList").append(dataLi);
				}
		    }else{

		    }
		}

		//函数
		function userbuylistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/order/service/getmembercenterbuymessage",
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
	/*******************************************************云购记录结束*********************************************/

	/*******************************************************获得的商品记录**************************************************/
	if( pageTrue=="orderlist" ){

		// 时间获取
		var period1 = $("#ul_region li:eq(0) a").attr("period").split("|");
		var period3 = $("#ul_region li:eq(1) a").attr("period").split("|");
		var state = 0;
		var beginTime = period3[0];
		var endTime = period3[1];
		
		memberXialaFun(function(e,begin,end){
			switch(e){
			case "0": //全部
				state = 0;
				break;
			case "1": //待确认的地址
				state = 1;
				break;
			case "2": //待发货
				state = 2;
				break;
			case "3": //待收货
				state = 3;
				break;
			case "4": //待晒单
				state = 4;
				break;
			case "9": //1个月
				beginTime = period1[0];
				endTime = period1[1];
				break;
			case "10":  //3个月
				beginTime = period3[0];
				endTime = period3[1];
				break;
			case 11:  //选择时间
				beginTime = begin;
				endTime = end;
				break;
			}
			orderlistFirstDataFun(1,5,state,beginTime,endTime);
		});

		orderlistFirstDataFun(1,5,state,beginTime,endTime);

		function orderlistFirstDataFun(page,limit,state,beginTime,endTime){
			$("#div_OrderList dd, .loading-2015, .null-data").remove();
			$("#div_OrderList").append('<div class="loading-2015"><em></em></div>');
			var dataObj = {
				page:page,
				limit:limit,
				orderState:state,
				beginTime:beginTime,
				endTime:endTime
			}
			// 数据加载 第一次
			orderlistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				orderlistAddDataFun(data);
				// 分页
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					$("#div_OrderList dd, .loading-2015").remove();
					$("#div_OrderList dl").append('<div class="loading-2015"><em></em></div>');
					orderlistAjaxFun(dataObj,function(data){
						orderlistAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function orderlistAddDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var IsPostSingle = item.IsPostSingle;//: "0"
					var actAddTime = item.actAddTime;//: "20160101"
					var buyNum = item.buyNum;//: 1
					var codeID = item.codeID;//: "56861a56887c22014a8b4684"
					var codePeriod = item.codePeriod;//: "1"
					var codePrice = item.codePrice;//: "1.00"
					var codeRNO = item.codeRNO;//: "10000002"
					var codeRTime = item.codeRTime;//: "2016-01-01 15:49:30.747"
					var codeType = item.codeType;//: 0
					var goodsID = item.goodsID;//: "563728c07f50eab004000404"
					var goodsName = item.goodsName;//: "【周黑鸭旗舰店_锁鲜装】盒装鸭翅190g*2 武汉特产官方食品零食"
					var goodsPic = item.goodsPic;//: "1_161020d50ftb1ztcx___0-item_pic.jpg"
					var orderActDesc = item.orderActDesc;//: ""
					var orderAddTime = item.orderAddTime;//: "2016-01-01 15:49:30"
					var orderNo = item.orderNo;//: ""
					var orderState = item.orderState;//: "0"
					var orderType = item.orderType;//: 0
					var ordersaleprice = item.ordersaleprice;//: "1.00"
					var btnHtml = '';
					var btnText = '<a target="_blank" href="/member/index/orderdetail?orderno='+orderNo+'" class="z-see-details">查看详情</a>';

					switch(orderState){
						case "1":
						btnHtml = '<a href="/member/index/orderdetail?orderno='+orderNo+'" class="z-perfect-address">完善收货地址</a>'+btnText;
						break;
						case "2":
						btnHtml = '<a href="/member/index/orderdetail?orderno='+orderNo+'" class="z-gray-button z-wait">待发货</a>'+btnText;
						break;
						case "3":
						btnHtml = '<a href="/member/index/orderdetail?orderno='+orderNo+'" class="z-determine">确认收货</a>'+btnText;
						break;
						case "4":
						btnHtml = '<a href="/member/index/orderdetail?orderno='+orderNo+'" class="z-gray-button z-wait">已确认收货</a>'+btnText;
						break;
						case "10":
						btnHtml = '<a href="/member/index/orderdetail?orderno='+orderNo+'" class="z-determine">晒单赢福分</a>';
						break;
						case "11":
						btnHtml = '<a href="javascript:;" class="z-gray-button z-wait">已取消</a>';
						break;
					}

		    		dataLi += '<dd class="has-announced"><span class="u-commodity-pic">'
		                    +'<a target="_blank" href="/yungou/lottery/detail?id='+codeID+'"><img src="'+goodsPic+'"><i class="u-personal"></i></a>'
		                +'</span>'
		                +'<span class="u-commodity-name gray9"><h3><a target="_blank" href="/yungou/lottery/detail?id='+codeID+'" class="gray3">'+goodsName+'</a></h3>价值：￥'+ordersaleprice+'<br />幸运云购码：'+codeRNO+'<br/>揭晓时间：'+codeRTime+'</span>'
		                +'<span class="u-order-number">'+orderNo+'</span>'
		                +'<span class="u-operation">'
			                +btnHtml
		                +'</span>'
		            +'</dd>';
		    	});
				
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$(".loading-2015").remove();
					$("#div_OrderList").append('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
				}else{
					$(".loading-2015").remove();
					$("#g-pagination").show();
					$("#div_OrderList dl").html('<dt><span class="u-commodity-pic">商品图片</span><span class="u-commodity-name">商品名称</span><span class="u-order-number">订单号</span><span class="u-operation">操作</span></dt>'+dataLi);
					$(".null-data").hide();
				}
		    }else{

		    }
		}

		//函数
		function orderlistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/order/service/getmembercenteruserwinlist",
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
	/*******************************************************获得的商品记录结束*********************************************/

	/*******************************************************晒单管理**************************************************/
	if( pageTrue=="postsinglelist" ){
		memberXialaFun(function(e){
			postsinglelistFirstDataFun(1,8,e);
		});

		postsinglelistFirstDataFun(1,8,'');

		function postsinglelistFirstDataFun(page,limit,state){
			var dataObj = {
				page:page,
				limit:limit,
				state:state
			}
			// 数据加载 第一次
			postsinglelistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				postsinglelistAddDataFun(data);
				// 分页
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					postsinglelistAjaxFun(dataObj,function(data){
						postsinglelistAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function postsinglelistAddDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$("#ul_state li:eq(1) i").html(data.result.postCount);
		    	$("#ul_state li:eq(2) i").html(data.result.unPostCount);
		    	$.each(data.result.datas, function(i,item){
		    		var codeID = item.codeID;// : "56888c43887c22044a8b45e0"
		    		var codePeriod = item.codePeriod;// : "1"
		    		var codePrice = item.codePrice;// : "2.00"
		    		var codeRNO = item.codeRNO;// : "10000001"
		    		var codeRTime = item.codeRTime;// : "2016-01-03 10:55:48.756"
		    		var codeType = item.codeType;// : 0
		    		var goodsID = item.goodsID;// : "563728c07f50eab004000404"
		    		var goodsPic = item.goodsPic;// : "/upload/goods/1/1_161020d50ftb1ztcx___0-item_pic.jpg"
		    		var goodsSName = item.goodsSName;// : "【周黑鸭旗舰店_锁鲜装】盒装鸭翅190g*2 武汉特产官方食品零食"
		    		var postContent = item.postContent;// : ""
		    		var postFailReason = item.postFailReason;// : ""
		    		var postId = item.postId;// : "5688926f887c22054a8b45e3"
		    		var postPic = item.postPic.split(',')[0];// : ""
		    		var postPoint = item.postPoint;// : "0"
		    		var postState = item.postState;// : "-1"
		    		var postTime = item.postTime;// : "-0001-11-30 00:00:00"
		    		var postTitle = item.postTitle;// : ""
		    		var text = '';
		    		var btnText = '';

		    		if( postState==0 ){
		    			text = '<span class="u-select-con">审核中</span>';
		    			btnText = '<a target="_blank" href="/member/index/postsingleadd?goods_id='+codeID+'" class="z-determine">修改</a>';
		    		}
		    		if( postState==1 ){
		    			text = '<span class="u-select-con orange">未通过<br><em>您的晒单内容涉及违规信息<br>请重新修改处理</em></span>';
		    			btnText = '<a target="_blank" href="/member/index/postsingleadd?goods_id='+codeID+'" class="z-determine">修改</a>';
		    		}
		    		if( postState==2 ){
		    			text = '<span class="u-select-con green">审核通过<br><em class="gray9">奖励'+postPoint+'福分</em></span>';
		    			btnText = '<a target="_blank" href="javascript:;" class="z-see-details">查看详情</a>';
		    		}

		    		if( postState==-1 ){
		    			dataLi += '<dd>'
			    			+'<span class="u-commodity-pic">'
			                    +'<a target="_blank" href="javascript:;"><img src="'+goodsPic+'"><i class="u-personal"></i></a>'
			                +'</span>'
			                +'<span class="u-commodity-name gray9">'
			                    +'<h3><a target="_blank" href="javascript:;" class="gray3">'+goodsSName+'</a></h3>价值：￥'+codePrice+'<br />幸运云购码：'+codeRNO+'<br/>揭晓时间：'+codeRTime
			                +'</span>'
			                +'<span class="u-select-con">暂未晒单</span>'
			                +'<span class="u-operation"><a target="_blank" href="/member/index/postsingleadd?goods_id='+codeID+'" class="z-perfect-address">立即晒单</a></span>'
			            +'</dd>';
		    		}else{
		    			dataLi = '<dd>'
			                +'<span class="u-commodity-pic">'
			                    +'<a target="_blank" href="javascript:;">'
			                        +'<img src="'+postPic+'">'
			                        +'<i class="u-personal"></i>'
			                    +'</a>'
			                +'</span>'
			                +'<span class="u-commodity-name gray9">'
			                    +'<div class="f-already clrfix">'
			                        +'<h3 class="fl"><a target="_blank" href="javascript:;" class="gray3">'+postTitle+'</a></h3>'
			                        +'<cite class="fl">'+postTime+'</cite>'
			                    +'</div>'
			                    +'<p class="gray9">'+postContent+'</p>'
			                +'</span>'+text
			                +'<span class="u-operation">'
			                    +btnText
			                +'</span>'
			            +'</dd>';
		    		}
		    		
		    	});
				
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#div_PostList dd, .null-data").remove();
					$("#div_PostList dl").append('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
				}else{
					$("#div_PostList dd").remove();
					$("#g-pagination").show();
					$("#div_PostList dl").append(dataLi);
					$(".null-data").remove();
				}
		    }else{

		    }
		}

		//函数
		function postsinglelistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/post/service/getmembercenteruserpostsingle",
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
	/*******************************************************晒单管理结束*********************************************/

	/*******************************************************我的福分**************************************************/
	if( pageTrue=="memberpoints" ){
		var fen = $(".yu").text();
		var rmb = eval($(".yu").attr("rmb"));
		if( rmb<1 ){
			$("#a_applycz").hide();
		}
		// 福分充值账户
		$("#a_applycz").on("click",function(){
			tanFun(420,262,'<div class="cz-wrap">'
	        +'<div class="cz-inner">'
	            +'<div class="cz-title">充值到我的云购账户</div>'
	                +'<ul class="cz-list">'
	                    +'<li>'
	                        +'<span class="cz-label">可用福分：</span>'
	                        +'<span class="cz-txt"><ins class="orange">'+fen+'</ins>&nbsp;&nbsp;&nbsp;可充值'+rmb+'元，100福分=1元</span>'
	                    +'</li>'
	                    +'<li>'
	                        +'<span class="cz-label gray9">充值金额：</span>'
	                        +'<span class="cz-txt"><b><input id="txtAmount" class="cz-value" maxlength="7" value="">&nbsp;&nbsp;元</b>&nbsp;&nbsp;&nbsp;以整数为单位<p id="p_tips" style="color:#f60"></p></span>'
	                    +'</li>'
	                    +'<li>'
	                        +'<span class="cz-label"></span>'
	                        +'<span class="cz-txt"><a id="btnApply" href="javascript:;" class="submit-btn">确定充值</a></span>'
	                    +'</li>'
	                +'</ul><a id="a_close" href="javascript:gotoClick();" class="close-wrap"><span class="close-btn"></span></a>'
	            +'</div>'
	        +'</div>');
		});

		$(document).on("click","#btnApply",function(){
			var  predeposit = $("#txtAmount").val();
			if( predeposit>rmb ){
				$("#p_tips").html("福分不够!");
				return false;
			}
			var re = /^(?:0|[1-9][0-9]?|10000000)$/;
		    if( !re.test(predeposit) ){
		        $("#p_tips").html("只允许输入整数!");
		        return false;
		    }
		    if( predeposit<1 ){
		        $("#p_tips").html("输入的数不能小于1");
		        return false;
		    }
			if( !ajaxlack ) return; ajaxlack = false;
			$("#p_tips").html("");
			$.ajax({
		        url: "/points/service/rechargepredeposit",
		        type: 'POST',
		        dataType: 'json',
		        data: { predeposit:predeposit }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	FailDialog(162,60,"充值成功！",'/member/index/userbalance');
		        }else{
		        	$("#p_tips").html(data.error_msg);
		        }
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		});



		// 时间获取
		var period1 = $("#ul_region li:eq(0) a").attr("period").split("|");
		var period3 = $("#ul_region li:eq(1) a").attr("period").split("|");
		var beginTime = period3[0];
		var endTime = period3[1];
		
		memberXialaFun(function(e,begin,end){
			switch(e){
			case "9": //1个月
				beginTime = period1[0];
				endTime = period1[1];
				break;
			case "10":  //3个月
				beginTime = period3[0];
				endTime = period3[1];
				break;
			case 11:  //选择时间
				beginTime = begin;
				endTime = end;
				break;
			}
			getuserpointsdetaillistFirstDataFun(1,10,beginTime,endTime);
		});

		getuserpointsdetaillistFirstDataFun(1,10,beginTime,endTime);

		function getuserpointsdetaillistFirstDataFun(page,limit,beginTime,endTime){
			$("#ul_points").html('');
			$("#g-pagination").hide();
			$("#ul_points").html('<div class="loading-2015"><em></em></div>');
			var dataObj = {
				page:page,
				limit:limit,
				beginTime:beginTime,
				endTime:endTime
			}
			// 数据加载 第一次
			getuserpointsdetaillistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getuserpointsdetaillistAddDataFun(data);
				// 分页
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					$("#ul_points").html('');
					$(".null-data, #g-pagination").hide();
					$("#ul_points").append('<div class="loading-2015"><em></em></div>');
					getuserpointsdetaillistAjaxFun(dataObj,function(data){
						getuserpointsdetaillistAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function getuserpointsdetaillistAddDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var logDescript = item.logDescript;//: "云购商品编码(2710789)福分抵扣"
					var logPointNum = item.logPointNum;//: "-100";
					var logTime = item.logTime;//: "2016.01.02 14:35:42"
					var className = (eval(logPointNum)>0 ? 'add' : "mius");
					dataLi += '<li><span class="time">'+logTime+'</span><span class="order '+className+'"><em>'+logPointNum+'</em></span><span class="protail">'+logDescript+'</span></li>';	    		
		    	});
				
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#ul_points li, .loading-2015").remove();
					$("#ul_points").html('<div class="null-data" style="margin-top: 130px;"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
				}else{
					$("#g-pagination").show();
					$("#ul_points li, .null-data, .loading-2015").remove();
					$("#ul_points").html(dataLi);
				}
		    }else{

		    }
		}

		//函数
		function getuserpointsdetaillistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/points/service/getuserpointsdetaillist",
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
	/*******************************************************我的福分结束*********************************************/








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

	if( pageTrue=="updatepassword" ){
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
			moneyVal = $("#ulItems li.current").attr('value');
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
	    	var telephone = $this.parents("li").find(".u-tel").attr('telephone');
	    	var address = $this.parents("li").find(".u-address").text().split(" ");
	    	var code = $this.parents("li").find(".u-code").text();
	    	var provinceID = $this.parents("li").find(".u-address").attr('provinceID');
	    	var cityID = $this.parents("li").find(".u-address").attr('cityID');
	    	var districtID = $this.parents("li").find(".u-address").attr('districtID');

	    	$("#btnSaveAddress").attr({"_id":id});
	    	$("#a_add").hide();
			$("#div_form").show();
			
			console.log(address);
			$("#selProvinceID .select-xiala").html(address[0]+'<s class="u-personal"></s>');
			// $("#selCityID .select-xiala").html(valCity+'<s class="u-personal"></s>');
			$("#selCountyID .select-xiala").html(address[2]+'<s class="u-personal"></s>');

			$("#selProvinceID input").val(provinceID);
			$("#selCityID input").val(cityID);
			$("#selCountyID input").val(districtID);

			$("#txtAddress").val(address[3]); //详细地址
	    	$("#txtPostCode").val(code); //邮政编码
	    	$("#txtConsignee").val(name); //收货人
	    	$("#txtTel").val(telephone); //固定电话
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
				var valCity;
				$("#selCityID .select-state a").map(function(obj){
					if( $(this).attr("value")==cityID ){
						valCity = $(this).text();
					}
				});
				$("#selCityID .select-xiala").html(valCity+'<s class="u-personal"></s>');
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



	/*******************************************************邀请管理**************************************************/
	if( pageTrue=="invitedlist" ){

		getinvitedmemberinfolistFirstDataFun(1,8);

		function getinvitedmemberinfolistFirstDataFun(page,limit){
			var dataObj = {
				page:page,
				limit:limit
			}
			// 数据加载 第一次
			getinvitedmemberinfolistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getinvitedmemberinfolistAddDataFun(data);
				// 分页
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					getinvitedmemberinfolistAjaxFun(dataObj,function(data){
						getinvitedmemberinfolistAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function getinvitedmemberinfolistAddDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var regTime = item.regTime;//: "2015.11.12 12:16:06"
					var state = item.state;//: "0"
					var userCode = item.userCode;//: "1010895885"
					var userName = item.userName;//: "ch*@icathol*"
					var userPhoto = item.userPhoto;//: "00000000000000000.jpg"
					var userWeb = item.userWeb;//: "1010895885"
		    		dataLi += '<li>'
	                    +'<span class="u-name"><a target="_blank" href="javascript:;">'+userName+'</a></span>'
	                    +'<span class="u-time">'+regTime+'</span>'
	                    +'<span class="u-num">'+userCode+'</span>'
	                    +'<span class="u-flag">未参与云购</span>'
	                +'</li>';
		    	});
				
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#ul_invitedlist").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>暂无邀请记录！</span></div>');
				}else{
					$("#g-pagination").show();
					$("#ul_invitedlist").html('<li class="p-menu"><span class="u-name">用户</span><span class="u-time">时间</span><span class="u-num">邀请编号</span><span class="u-flag">消费状态</span></li>'+dataLi);
				}
		    }else{

		    }
		}

		//函数
		function getinvitedmemberinfolistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/member/service/getinvitedmemberinfolist",
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
	/*******************************************************邀请管理结束**************************************************/

	/*******************************************************佣金明细**************************************************/
	if( pageTrue=="commissionquery" ){
		memberXialaFun(function(e){ });

		getmembercentercommissionlistFirstDataFun(1,8,0,'2015-01-01','2017-01-01','');

		function getmembercentercommissionlistFirstDataFun(page,limit,state,beginTime,endTime,keyWords){
			var dataObj = {
				page:page,
				limit:limit,
				state:state,
				beginTime:beginTime,
				endTime:endTime,
				keyWords:keyWords
			}
			// 数据加载 第一次
			getmembercentercommissionlistAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getmembercentercommissionlistAddDataFun(data);
				// 分页
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					getmembercentercommissionlistAjaxFun(dataObj,function(data){
						getmembercentercommissionlistAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function getmembercentercommissionlistAddDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$.each(data.result.datas, function(i,item){

		    		dataLi += '<li>'
		                +'<span class="u-time">2015-10-22</span>'
		                +'<span class="u-name"><a href="/" target="_blank">用户昵称</a></span>'
		                +'<span class="u-option"><span>收入</span></span>'
		                +'<span class="u-info"><a href="javascript:;" target="_blank">第33698云</a></span>'
		                +'<span class="u-much"><span>￥100</span></span>'
		                +'<span class="u-money"><strong class="green">￥10</strong></span>'
		            +'</li>';
		    	});
				
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#ul_commissionlist").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>暂无记录！</span></div>');
				}else{
					$("#g-pagination").show();
					$("#ul_commissionlist").html(dataLi);
				}
		    }else{

		    }
		}

		//函数
		function getmembercentercommissionlistAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "getmembercentercommissionlist",
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
	/*******************************************************佣金明细结束**************************************************/










































	});
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
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
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

/***/ },
/* 14 */,
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * jQuery Form Plugin
	 * version: 3.51.0-2014.06.20
	 * Requires jQuery v1.5 or later
	 * Copyright (c) 2014 M. Alsup
	 * Examples and documentation at: http://malsup.com/jquery/form/
	 * Project repository: https://github.com/malsup/form
	 * Dual licensed under the MIT and GPL licenses.
	 * https://github.com/malsup/form#copyright-and-license
	 */
	/*global ActiveXObject */

	// AMD support
	(function (factory) {
	    "use strict";
	    if (true) {
	        // using AMD; register as anon module
	        !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(1)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	    } else {
	        // no AMD; invoke directly
	        factory( (typeof(jQuery) != 'undefined') ? jQuery : window.Zepto );
	    }
	}

	(function($) {
	"use strict";

	/*
	    Usage Note:
	    -----------
	    Do not use both ajaxSubmit and ajaxForm on the same form.  These
	    functions are mutually exclusive.  Use ajaxSubmit if you want
	    to bind your own submit handler to the form.  For example,

	    $(document).ready(function() {
	        $('#myForm').on('submit', function(e) {
	            e.preventDefault(); // <-- important
	            $(this).ajaxSubmit({
	                target: '#output'
	            });
	        });
	    });

	    Use ajaxForm when you want the plugin to manage all the event binding
	    for you.  For example,

	    $(document).ready(function() {
	        $('#myForm').ajaxForm({
	            target: '#output'
	        });
	    });

	    You can also use ajaxForm with delegation (requires jQuery v1.7+), so the
	    form does not have to exist when you invoke ajaxForm:

	    $('#myForm').ajaxForm({
	        delegation: true,
	        target: '#output'
	    });

	    When using ajaxForm, the ajaxSubmit function will be invoked for you
	    at the appropriate time.
	*/

	/**
	 * Feature detection
	 */
	var feature = {};
	feature.fileapi = $("<input type='file'/>").get(0).files !== undefined;
	feature.formdata = window.FormData !== undefined;

	var hasProp = !!$.fn.prop;

	// attr2 uses prop when it can but checks the return type for
	// an expected string.  this accounts for the case where a form 
	// contains inputs with names like "action" or "method"; in those
	// cases "prop" returns the element
	$.fn.attr2 = function() {
	    if ( ! hasProp ) {
	        return this.attr.apply(this, arguments);
	    }
	    var val = this.prop.apply(this, arguments);
	    if ( ( val && val.jquery ) || typeof val === 'string' ) {
	        return val;
	    }
	    return this.attr.apply(this, arguments);
	};

	/**
	 * ajaxSubmit() provides a mechanism for immediately submitting
	 * an HTML form using AJAX.
	 */
	$.fn.ajaxSubmit = function(options) {
	    /*jshint scripturl:true */

	    // fast fail if nothing selected (http://dev.jquery.com/ticket/2752)
	    if (!this.length) {
	        log('ajaxSubmit: skipping submit process - no element selected');
	        return this;
	    }

	    var method, action, url, $form = this;

	    if (typeof options == 'function') {
	        options = { success: options };
	    }
	    else if ( options === undefined ) {
	        options = {};
	    }

	    method = options.type || this.attr2('method');
	    action = options.url  || this.attr2('action');

	    url = (typeof action === 'string') ? $.trim(action) : '';
	    url = url || window.location.href || '';
	    if (url) {
	        // clean url (don't include hash vaue)
	        url = (url.match(/^([^#]+)/)||[])[1];
	    }

	    options = $.extend(true, {
	        url:  url,
	        success: $.ajaxSettings.success,
	        type: method || $.ajaxSettings.type,
	        iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank'
	    }, options);

	    // hook for manipulating the form data before it is extracted;
	    // convenient for use with rich editors like tinyMCE or FCKEditor
	    var veto = {};
	    this.trigger('form-pre-serialize', [this, options, veto]);
	    if (veto.veto) {
	        log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');
	        return this;
	    }

	    // provide opportunity to alter form data before it is serialized
	    if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
	        log('ajaxSubmit: submit aborted via beforeSerialize callback');
	        return this;
	    }

	    var traditional = options.traditional;
	    if ( traditional === undefined ) {
	        traditional = $.ajaxSettings.traditional;
	    }

	    var elements = [];
	    var qx, a = this.formToArray(options.semantic, elements);
	    if (options.data) {
	        options.extraData = options.data;
	        qx = $.param(options.data, traditional);
	    }

	    // give pre-submit callback an opportunity to abort the submit
	    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
	        log('ajaxSubmit: submit aborted via beforeSubmit callback');
	        return this;
	    }

	    // fire vetoable 'validate' event
	    this.trigger('form-submit-validate', [a, this, options, veto]);
	    if (veto.veto) {
	        log('ajaxSubmit: submit vetoed via form-submit-validate trigger');
	        return this;
	    }

	    var q = $.param(a, traditional);
	    if (qx) {
	        q = ( q ? (q + '&' + qx) : qx );
	    }
	    if (options.type.toUpperCase() == 'GET') {
	        options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
	        options.data = null;  // data is null for 'get'
	    }
	    else {
	        options.data = q; // data is the query string for 'post'
	    }

	    var callbacks = [];
	    if (options.resetForm) {
	        callbacks.push(function() { $form.resetForm(); });
	    }
	    if (options.clearForm) {
	        callbacks.push(function() { $form.clearForm(options.includeHidden); });
	    }

	    // perform a load on the target only if dataType is not provided
	    if (!options.dataType && options.target) {
	        var oldSuccess = options.success || function(){};
	        callbacks.push(function(data) {
	            var fn = options.replaceTarget ? 'replaceWith' : 'html';
	            $(options.target)[fn](data).each(oldSuccess, arguments);
	        });
	    }
	    else if (options.success) {
	        callbacks.push(options.success);
	    }

	    options.success = function(data, status, xhr) { // jQuery 1.4+ passes xhr as 3rd arg
	        var context = options.context || this ;    // jQuery 1.4+ supports scope context
	        for (var i=0, max=callbacks.length; i < max; i++) {
	            callbacks[i].apply(context, [data, status, xhr || $form, $form]);
	        }
	    };

	    if (options.error) {
	        var oldError = options.error;
	        options.error = function(xhr, status, error) {
	            var context = options.context || this;
	            oldError.apply(context, [xhr, status, error, $form]);
	        };
	    }

	     if (options.complete) {
	        var oldComplete = options.complete;
	        options.complete = function(xhr, status) {
	            var context = options.context || this;
	            oldComplete.apply(context, [xhr, status, $form]);
	        };
	    }

	    // are there files to upload?

	    // [value] (issue #113), also see comment:
	    // https://github.com/malsup/form/commit/588306aedba1de01388032d5f42a60159eea9228#commitcomment-2180219
	    var fileInputs = $('input[type=file]:enabled', this).filter(function() { return $(this).val() !== ''; });

	    var hasFileInputs = fileInputs.length > 0;
	    var mp = 'multipart/form-data';
	    var multipart = ($form.attr('enctype') == mp || $form.attr('encoding') == mp);

	    var fileAPI = feature.fileapi && feature.formdata;
	    log("fileAPI :" + fileAPI);
	    var shouldUseFrame = (hasFileInputs || multipart) && !fileAPI;

	    var jqxhr;

	    // options.iframe allows user to force iframe mode
	    // 06-NOV-09: now defaulting to iframe mode if file input is detected
	    if (options.iframe !== false && (options.iframe || shouldUseFrame)) {
	        // hack to fix Safari hang (thanks to Tim Molendijk for this)
	        // see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
	        if (options.closeKeepAlive) {
	            $.get(options.closeKeepAlive, function() {
	                jqxhr = fileUploadIframe(a);
	            });
	        }
	        else {
	            jqxhr = fileUploadIframe(a);
	        }
	    }
	    else if ((hasFileInputs || multipart) && fileAPI) {
	        jqxhr = fileUploadXhr(a);
	    }
	    else {
	        jqxhr = $.ajax(options);
	    }

	    $form.removeData('jqxhr').data('jqxhr', jqxhr);

	    // clear element array
	    for (var k=0; k < elements.length; k++) {
	        elements[k] = null;
	    }

	    // fire 'notify' event
	    this.trigger('form-submit-notify', [this, options]);
	    return this;

	    // utility fn for deep serialization
	    function deepSerialize(extraData){
	        var serialized = $.param(extraData, options.traditional).split('&');
	        var len = serialized.length;
	        var result = [];
	        var i, part;
	        for (i=0; i < len; i++) {
	            // #252; undo param space replacement
	            serialized[i] = serialized[i].replace(/\+/g,' ');
	            part = serialized[i].split('=');
	            // #278; use array instead of object storage, favoring array serializations
	            result.push([decodeURIComponent(part[0]), decodeURIComponent(part[1])]);
	        }
	        return result;
	    }

	     // XMLHttpRequest Level 2 file uploads (big hat tip to francois2metz)
	    function fileUploadXhr(a) {
	        var formdata = new FormData();

	        for (var i=0; i < a.length; i++) {
	            formdata.append(a[i].name, a[i].value);
	        }

	        if (options.extraData) {
	            var serializedData = deepSerialize(options.extraData);
	            for (i=0; i < serializedData.length; i++) {
	                if (serializedData[i]) {
	                    formdata.append(serializedData[i][0], serializedData[i][1]);
	                }
	            }
	        }

	        options.data = null;

	        var s = $.extend(true, {}, $.ajaxSettings, options, {
	            contentType: false,
	            processData: false,
	            cache: false,
	            type: method || 'POST'
	        });

	        if (options.uploadProgress) {
	            // workaround because jqXHR does not expose upload property
	            s.xhr = function() {
	                var xhr = $.ajaxSettings.xhr();
	                if (xhr.upload) {
	                    xhr.upload.addEventListener('progress', function(event) {
	                        var percent = 0;
	                        var position = event.loaded || event.position; /*event.position is deprecated*/
	                        var total = event.total;
	                        if (event.lengthComputable) {
	                            percent = Math.ceil(position / total * 100);
	                        }
	                        options.uploadProgress(event, position, total, percent);
	                    }, false);
	                }
	                return xhr;
	            };
	        }

	        s.data = null;
	        var beforeSend = s.beforeSend;
	        s.beforeSend = function(xhr, o) {
	            //Send FormData() provided by user
	            if (options.formData) {
	                o.data = options.formData;
	            }
	            else {
	                o.data = formdata;
	            }
	            if(beforeSend) {
	                beforeSend.call(this, xhr, o);
	            }
	        };
	        return $.ajax(s);
	    }

	    // private function for handling file uploads (hat tip to YAHOO!)
	    function fileUploadIframe(a) {
	        var form = $form[0], el, i, s, g, id, $io, io, xhr, sub, n, timedOut, timeoutHandle;
	        var deferred = $.Deferred();

	        // #341
	        deferred.abort = function(status) {
	            xhr.abort(status);
	        };

	        if (a) {
	            // ensure that every serialized input is still enabled
	            for (i=0; i < elements.length; i++) {
	                el = $(elements[i]);
	                if ( hasProp ) {
	                    el.prop('disabled', false);
	                }
	                else {
	                    el.removeAttr('disabled');
	                }
	            }
	        }

	        s = $.extend(true, {}, $.ajaxSettings, options);
	        s.context = s.context || s;
	        id = 'jqFormIO' + (new Date().getTime());
	        if (s.iframeTarget) {
	            $io = $(s.iframeTarget);
	            n = $io.attr2('name');
	            if (!n) {
	                $io.attr2('name', id);
	            }
	            else {
	                id = n;
	            }
	        }
	        else {
	            $io = $('<iframe name="' + id + '" src="'+ s.iframeSrc +'" />');
	            $io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });
	        }
	        io = $io[0];


	        xhr = { // mock object
	            aborted: 0,
	            responseText: null,
	            responseXML: null,
	            status: 0,
	            statusText: 'n/a',
	            getAllResponseHeaders: function() {},
	            getResponseHeader: function() {},
	            setRequestHeader: function() {},
	            abort: function(status) {
	                var e = (status === 'timeout' ? 'timeout' : 'aborted');
	                log('aborting upload... ' + e);
	                this.aborted = 1;

	                try { // #214, #257
	                    if (io.contentWindow.document.execCommand) {
	                        io.contentWindow.document.execCommand('Stop');
	                    }
	                }
	                catch(ignore) {}

	                $io.attr('src', s.iframeSrc); // abort op in progress
	                xhr.error = e;
	                if (s.error) {
	                    s.error.call(s.context, xhr, e, status);
	                }
	                if (g) {
	                    $.event.trigger("ajaxError", [xhr, s, e]);
	                }
	                if (s.complete) {
	                    s.complete.call(s.context, xhr, e);
	                }
	            }
	        };

	        g = s.global;
	        // trigger ajax global events so that activity/block indicators work like normal
	        if (g && 0 === $.active++) {
	            $.event.trigger("ajaxStart");
	        }
	        if (g) {
	            $.event.trigger("ajaxSend", [xhr, s]);
	        }

	        if (s.beforeSend && s.beforeSend.call(s.context, xhr, s) === false) {
	            if (s.global) {
	                $.active--;
	            }
	            deferred.reject();
	            return deferred;
	        }
	        if (xhr.aborted) {
	            deferred.reject();
	            return deferred;
	        }

	        // add submitting element to data if we know it
	        sub = form.clk;
	        if (sub) {
	            n = sub.name;
	            if (n && !sub.disabled) {
	                s.extraData = s.extraData || {};
	                s.extraData[n] = sub.value;
	                if (sub.type == "image") {
	                    s.extraData[n+'.x'] = form.clk_x;
	                    s.extraData[n+'.y'] = form.clk_y;
	                }
	            }
	        }

	        var CLIENT_TIMEOUT_ABORT = 1;
	        var SERVER_ABORT = 2;
	                
	        function getDoc(frame) {
	            /* it looks like contentWindow or contentDocument do not
	             * carry the protocol property in ie8, when running under ssl
	             * frame.document is the only valid response document, since
	             * the protocol is know but not on the other two objects. strange?
	             * "Same origin policy" http://en.wikipedia.org/wiki/Same_origin_policy
	             */
	            
	            var doc = null;
	            
	            // IE8 cascading access check
	            try {
	                if (frame.contentWindow) {
	                    doc = frame.contentWindow.document;
	                }
	            } catch(err) {
	                // IE8 access denied under ssl & missing protocol
	                log('cannot get iframe.contentWindow document: ' + err);
	            }

	            if (doc) { // successful getting content
	                return doc;
	            }

	            try { // simply checking may throw in ie8 under ssl or mismatched protocol
	                doc = frame.contentDocument ? frame.contentDocument : frame.document;
	            } catch(err) {
	                // last attempt
	                log('cannot get iframe.contentDocument: ' + err);
	                doc = frame.document;
	            }
	            return doc;
	        }

	        // Rails CSRF hack (thanks to Yvan Barthelemy)
	        var csrf_token = $('meta[name=csrf-token]').attr('content');
	        var csrf_param = $('meta[name=csrf-param]').attr('content');
	        if (csrf_param && csrf_token) {
	            s.extraData = s.extraData || {};
	            s.extraData[csrf_param] = csrf_token;
	        }

	        // take a breath so that pending repaints get some cpu time before the upload starts
	        function doSubmit() {
	            // make sure form attrs are set
	            var t = $form.attr2('target'), 
	                a = $form.attr2('action'), 
	                mp = 'multipart/form-data',
	                et = $form.attr('enctype') || $form.attr('encoding') || mp;

	            // update form attrs in IE friendly way
	            form.setAttribute('target',id);
	            if (!method || /post/i.test(method) ) {
	                form.setAttribute('method', 'POST');
	            }
	            if (a != s.url) {
	                form.setAttribute('action', s.url);
	            }

	            // ie borks in some cases when setting encoding
	            if (! s.skipEncodingOverride && (!method || /post/i.test(method))) {
	                $form.attr({
	                    encoding: 'multipart/form-data',
	                    enctype:  'multipart/form-data'
	                });
	            }

	            // support timout
	            if (s.timeout) {
	                timeoutHandle = setTimeout(function() { timedOut = true; cb(CLIENT_TIMEOUT_ABORT); }, s.timeout);
	            }

	            // look for server aborts
	            function checkState() {
	                try {
	                    var state = getDoc(io).readyState;
	                    log('state = ' + state);
	                    if (state && state.toLowerCase() == 'uninitialized') {
	                        setTimeout(checkState,50);
	                    }
	                }
	                catch(e) {
	                    log('Server abort: ' , e, ' (', e.name, ')');
	                    cb(SERVER_ABORT);
	                    if (timeoutHandle) {
	                        clearTimeout(timeoutHandle);
	                    }
	                    timeoutHandle = undefined;
	                }
	            }

	            // add "extra" data to form if provided in options
	            var extraInputs = [];
	            try {
	                if (s.extraData) {
	                    for (var n in s.extraData) {
	                        if (s.extraData.hasOwnProperty(n)) {
	                           // if using the $.param format that allows for multiple values with the same name
	                           if($.isPlainObject(s.extraData[n]) && s.extraData[n].hasOwnProperty('name') && s.extraData[n].hasOwnProperty('value')) {
	                               extraInputs.push(
	                               $('<input type="hidden" name="'+s.extraData[n].name+'">').val(s.extraData[n].value)
	                                   .appendTo(form)[0]);
	                           } else {
	                               extraInputs.push(
	                               $('<input type="hidden" name="'+n+'">').val(s.extraData[n])
	                                   .appendTo(form)[0]);
	                           }
	                        }
	                    }
	                }

	                if (!s.iframeTarget) {
	                    // add iframe to doc and submit the form
	                    $io.appendTo('body');
	                }
	                if (io.attachEvent) {
	                    io.attachEvent('onload', cb);
	                }
	                else {
	                    io.addEventListener('load', cb, false);
	                }
	                setTimeout(checkState,15);

	                try {
	                    form.submit();
	                } catch(err) {
	                    // just in case form has element with name/id of 'submit'
	                    var submitFn = document.createElement('form').submit;
	                    submitFn.apply(form);
	                }
	            }
	            finally {
	                // reset attrs and remove "extra" input elements
	                form.setAttribute('action',a);
	                form.setAttribute('enctype', et); // #380
	                if(t) {
	                    form.setAttribute('target', t);
	                } else {
	                    $form.removeAttr('target');
	                }
	                $(extraInputs).remove();
	            }
	        }

	        if (s.forceSync) {
	            doSubmit();
	        }
	        else {
	            setTimeout(doSubmit, 10); // this lets dom updates render
	        }

	        var data, doc, domCheckCount = 50, callbackProcessed;

	        function cb(e) {
	            if (xhr.aborted || callbackProcessed) {
	                return;
	            }
	            
	            doc = getDoc(io);
	            if(!doc) {
	                log('cannot access response document');
	                e = SERVER_ABORT;
	            }
	            if (e === CLIENT_TIMEOUT_ABORT && xhr) {
	                xhr.abort('timeout');
	                deferred.reject(xhr, 'timeout');
	                return;
	            }
	            else if (e == SERVER_ABORT && xhr) {
	                xhr.abort('server abort');
	                deferred.reject(xhr, 'error', 'server abort');
	                return;
	            }

	            if (!doc || doc.location.href == s.iframeSrc) {
	                // response not received yet
	                if (!timedOut) {
	                    return;
	                }
	            }
	            if (io.detachEvent) {
	                io.detachEvent('onload', cb);
	            }
	            else {
	                io.removeEventListener('load', cb, false);
	            }

	            var status = 'success', errMsg;
	            try {
	                if (timedOut) {
	                    throw 'timeout';
	                }

	                var isXml = s.dataType == 'xml' || doc.XMLDocument || $.isXMLDoc(doc);
	                log('isXml='+isXml);
	                if (!isXml && window.opera && (doc.body === null || !doc.body.innerHTML)) {
	                    if (--domCheckCount) {
	                        // in some browsers (Opera) the iframe DOM is not always traversable when
	                        // the onload callback fires, so we loop a bit to accommodate
	                        log('requeing onLoad callback, DOM not available');
	                        setTimeout(cb, 250);
	                        return;
	                    }
	                    // let this fall through because server response could be an empty document
	                    //log('Could not access iframe DOM after mutiple tries.');
	                    //throw 'DOMException: not available';
	                }

	                //log('response detected');
	                var docRoot = doc.body ? doc.body : doc.documentElement;
	                xhr.responseText = docRoot ? docRoot.innerHTML : null;
	                xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
	                if (isXml) {
	                    s.dataType = 'xml';
	                }
	                xhr.getResponseHeader = function(header){
	                    var headers = {'content-type': s.dataType};
	                    return headers[header.toLowerCase()];
	                };
	                // support for XHR 'status' & 'statusText' emulation :
	                if (docRoot) {
	                    xhr.status = Number( docRoot.getAttribute('status') ) || xhr.status;
	                    xhr.statusText = docRoot.getAttribute('statusText') || xhr.statusText;
	                }

	                var dt = (s.dataType || '').toLowerCase();
	                var scr = /(json|script|text)/.test(dt);
	                if (scr || s.textarea) {
	                    // see if user embedded response in textarea
	                    var ta = doc.getElementsByTagName('textarea')[0];
	                    if (ta) {
	                        xhr.responseText = ta.value;
	                        // support for XHR 'status' & 'statusText' emulation :
	                        xhr.status = Number( ta.getAttribute('status') ) || xhr.status;
	                        xhr.statusText = ta.getAttribute('statusText') || xhr.statusText;
	                    }
	                    else if (scr) {
	                        // account for browsers injecting pre around json response
	                        var pre = doc.getElementsByTagName('pre')[0];
	                        var b = doc.getElementsByTagName('body')[0];
	                        if (pre) {
	                            xhr.responseText = pre.textContent ? pre.textContent : pre.innerText;
	                        }
	                        else if (b) {
	                            xhr.responseText = b.textContent ? b.textContent : b.innerText;
	                        }
	                    }
	                }
	                else if (dt == 'xml' && !xhr.responseXML && xhr.responseText) {
	                    xhr.responseXML = toXml(xhr.responseText);
	                }

	                try {
	                    data = httpData(xhr, dt, s);
	                }
	                catch (err) {
	                    status = 'parsererror';
	                    xhr.error = errMsg = (err || status);
	                }
	            }
	            catch (err) {
	                log('error caught: ',err);
	                status = 'error';
	                xhr.error = errMsg = (err || status);
	            }

	            if (xhr.aborted) {
	                log('upload aborted');
	                status = null;
	            }

	            if (xhr.status) { // we've set xhr.status
	                status = (xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) ? 'success' : 'error';
	            }

	            // ordering of these callbacks/triggers is odd, but that's how $.ajax does it
	            if (status === 'success') {
	                if (s.success) {
	                    s.success.call(s.context, data, 'success', xhr);
	                }
	                deferred.resolve(xhr.responseText, 'success', xhr);
	                if (g) {
	                    $.event.trigger("ajaxSuccess", [xhr, s]);
	                }
	            }
	            else if (status) {
	                if (errMsg === undefined) {
	                    errMsg = xhr.statusText;
	                }
	                if (s.error) {
	                    s.error.call(s.context, xhr, status, errMsg);
	                }
	                deferred.reject(xhr, 'error', errMsg);
	                if (g) {
	                    $.event.trigger("ajaxError", [xhr, s, errMsg]);
	                }
	            }

	            if (g) {
	                $.event.trigger("ajaxComplete", [xhr, s]);
	            }

	            if (g && ! --$.active) {
	                $.event.trigger("ajaxStop");
	            }

	            if (s.complete) {
	                s.complete.call(s.context, xhr, status);
	            }

	            callbackProcessed = true;
	            if (s.timeout) {
	                clearTimeout(timeoutHandle);
	            }

	            // clean up
	            setTimeout(function() {
	                if (!s.iframeTarget) {
	                    $io.remove();
	                }
	                else { //adding else to clean up existing iframe response.
	                    $io.attr('src', s.iframeSrc);
	                }
	                xhr.responseXML = null;
	            }, 100);
	        }

	        var toXml = $.parseXML || function(s, doc) { // use parseXML if available (jQuery 1.5+)
	            if (window.ActiveXObject) {
	                doc = new ActiveXObject('Microsoft.XMLDOM');
	                doc.async = 'false';
	                doc.loadXML(s);
	            }
	            else {
	                doc = (new DOMParser()).parseFromString(s, 'text/xml');
	            }
	            return (doc && doc.documentElement && doc.documentElement.nodeName != 'parsererror') ? doc : null;
	        };
	        var parseJSON = $.parseJSON || function(s) {
	            /*jslint evil:true */
	            return window['eval']('(' + s + ')');
	        };

	        var httpData = function( xhr, type, s ) { // mostly lifted from jq1.4.4

	            var ct = xhr.getResponseHeader('content-type') || '',
	                xml = type === 'xml' || !type && ct.indexOf('xml') >= 0,
	                data = xml ? xhr.responseXML : xhr.responseText;

	            if (xml && data.documentElement.nodeName === 'parsererror') {
	                if ($.error) {
	                    $.error('parsererror');
	                }
	            }
	            if (s && s.dataFilter) {
	                data = s.dataFilter(data, type);
	            }
	            if (typeof data === 'string') {
	                if (type === 'json' || !type && ct.indexOf('json') >= 0) {
	                    data = parseJSON(data);
	                } else if (type === "script" || !type && ct.indexOf("javascript") >= 0) {
	                    $.globalEval(data);
	                }
	            }
	            return data;
	        };

	        return deferred;
	    }
	};

	/**
	 * ajaxForm() provides a mechanism for fully automating form submission.
	 *
	 * The advantages of using this method instead of ajaxSubmit() are:
	 *
	 * 1: This method will include coordinates for <input type="image" /> elements (if the element
	 *    is used to submit the form).
	 * 2. This method will include the submit element's name/value data (for the element that was
	 *    used to submit the form).
	 * 3. This method binds the submit() method to the form for you.
	 *
	 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
	 * passes the options argument along after properly binding events for submit elements and
	 * the form itself.
	 */
	$.fn.ajaxForm = function(options) {
	    options = options || {};
	    options.delegation = options.delegation && $.isFunction($.fn.on);

	    // in jQuery 1.3+ we can fix mistakes with the ready state
	    if (!options.delegation && this.length === 0) {
	        var o = { s: this.selector, c: this.context };
	        if (!$.isReady && o.s) {
	            log('DOM not ready, queuing ajaxForm');
	            $(function() {
	                $(o.s,o.c).ajaxForm(options);
	            });
	            return this;
	        }
	        // is your DOM ready?  http://docs.jquery.com/Tutorials:Introducing_$(document).ready()
	        log('terminating; zero elements found by selector' + ($.isReady ? '' : ' (DOM not ready)'));
	        return this;
	    }

	    if ( options.delegation ) {
	        $(document)
	            .off('submit.form-plugin', this.selector, doAjaxSubmit)
	            .off('click.form-plugin', this.selector, captureSubmittingElement)
	            .on('submit.form-plugin', this.selector, options, doAjaxSubmit)
	            .on('click.form-plugin', this.selector, options, captureSubmittingElement);
	        return this;
	    }

	    return this.ajaxFormUnbind()
	        .bind('submit.form-plugin', options, doAjaxSubmit)
	        .bind('click.form-plugin', options, captureSubmittingElement);
	};

	// private event handlers
	function doAjaxSubmit(e) {
	    /*jshint validthis:true */
	    var options = e.data;
	    if (!e.isDefaultPrevented()) { // if event has been canceled, don't proceed
	        e.preventDefault();
	        $(e.target).ajaxSubmit(options); // #365
	    }
	}

	function captureSubmittingElement(e) {
	    /*jshint validthis:true */
	    var target = e.target;
	    var $el = $(target);
	    if (!($el.is("[type=submit],[type=image]"))) {
	        // is this a child element of the submit el?  (ex: a span within a button)
	        var t = $el.closest('[type=submit]');
	        if (t.length === 0) {
	            return;
	        }
	        target = t[0];
	    }
	    var form = this;
	    form.clk = target;
	    if (target.type == 'image') {
	        if (e.offsetX !== undefined) {
	            form.clk_x = e.offsetX;
	            form.clk_y = e.offsetY;
	        } else if (typeof $.fn.offset == 'function') {
	            var offset = $el.offset();
	            form.clk_x = e.pageX - offset.left;
	            form.clk_y = e.pageY - offset.top;
	        } else {
	            form.clk_x = e.pageX - target.offsetLeft;
	            form.clk_y = e.pageY - target.offsetTop;
	        }
	    }
	    // clear form vars
	    setTimeout(function() { form.clk = form.clk_x = form.clk_y = null; }, 100);
	}


	// ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
	$.fn.ajaxFormUnbind = function() {
	    return this.unbind('submit.form-plugin click.form-plugin');
	};

	/**
	 * formToArray() gathers form element data into an array of objects that can
	 * be passed to any of the following ajax functions: $.get, $.post, or load.
	 * Each object in the array has both a 'name' and 'value' property.  An example of
	 * an array for a simple login form might be:
	 *
	 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
	 *
	 * It is this array that is passed to pre-submit callback functions provided to the
	 * ajaxSubmit() and ajaxForm() methods.
	 */
	$.fn.formToArray = function(semantic, elements) {
	    var a = [];
	    if (this.length === 0) {
	        return a;
	    }

	    var form = this[0];
	    var formId = this.attr('id');
	    var els = semantic ? form.getElementsByTagName('*') : form.elements;
	    var els2;

	    if (els && !/MSIE [678]/.test(navigator.userAgent)) { // #390
	        els = $(els).get();  // convert to standard array
	    }

	    // #386; account for inputs outside the form which use the 'form' attribute
	    if ( formId ) {
	        els2 = $(':input[form="' + formId + '"]').get(); // hat tip @thet
	        if ( els2.length ) {
	            els = (els || []).concat(els2);
	        }
	    }

	    if (!els || !els.length) {
	        return a;
	    }

	    var i,j,n,v,el,max,jmax;
	    for(i=0, max=els.length; i < max; i++) {
	        el = els[i];
	        n = el.name;
	        if (!n || el.disabled) {
	            continue;
	        }

	        if (semantic && form.clk && el.type == "image") {
	            // handle image inputs on the fly when semantic == true
	            if(form.clk == el) {
	                a.push({name: n, value: $(el).val(), type: el.type });
	                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
	            }
	            continue;
	        }

	        v = $.fieldValue(el, true);
	        if (v && v.constructor == Array) {
	            if (elements) {
	                elements.push(el);
	            }
	            for(j=0, jmax=v.length; j < jmax; j++) {
	                a.push({name: n, value: v[j]});
	            }
	        }
	        else if (feature.fileapi && el.type == 'file') {
	            if (elements) {
	                elements.push(el);
	            }
	            var files = el.files;
	            if (files.length) {
	                for (j=0; j < files.length; j++) {
	                    a.push({name: n, value: files[j], type: el.type});
	                }
	            }
	            else {
	                // #180
	                a.push({ name: n, value: '', type: el.type });
	            }
	        }
	        else if (v !== null && typeof v != 'undefined') {
	            if (elements) {
	                elements.push(el);
	            }
	            a.push({name: n, value: v, type: el.type, required: el.required});
	        }
	    }

	    if (!semantic && form.clk) {
	        // input type=='image' are not found in elements array! handle it here
	        var $input = $(form.clk), input = $input[0];
	        n = input.name;
	        if (n && !input.disabled && input.type == 'image') {
	            a.push({name: n, value: $input.val()});
	            a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
	        }
	    }
	    return a;
	};

	/**
	 * Serializes form data into a 'submittable' string. This method will return a string
	 * in the format: name1=value1&amp;name2=value2
	 */
	$.fn.formSerialize = function(semantic) {
	    //hand off to jQuery.param for proper encoding
	    return $.param(this.formToArray(semantic));
	};

	/**
	 * Serializes all field elements in the jQuery object into a query string.
	 * This method will return a string in the format: name1=value1&amp;name2=value2
	 */
	$.fn.fieldSerialize = function(successful) {
	    var a = [];
	    this.each(function() {
	        var n = this.name;
	        if (!n) {
	            return;
	        }
	        var v = $.fieldValue(this, successful);
	        if (v && v.constructor == Array) {
	            for (var i=0,max=v.length; i < max; i++) {
	                a.push({name: n, value: v[i]});
	            }
	        }
	        else if (v !== null && typeof v != 'undefined') {
	            a.push({name: this.name, value: v});
	        }
	    });
	    //hand off to jQuery.param for proper encoding
	    return $.param(a);
	};

	/**
	 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
	 *
	 *  <form><fieldset>
	 *      <input name="A" type="text" />
	 *      <input name="A" type="text" />
	 *      <input name="B" type="checkbox" value="B1" />
	 *      <input name="B" type="checkbox" value="B2"/>
	 *      <input name="C" type="radio" value="C1" />
	 *      <input name="C" type="radio" value="C2" />
	 *  </fieldset></form>
	 *
	 *  var v = $('input[type=text]').fieldValue();
	 *  // if no values are entered into the text inputs
	 *  v == ['','']
	 *  // if values entered into the text inputs are 'foo' and 'bar'
	 *  v == ['foo','bar']
	 *
	 *  var v = $('input[type=checkbox]').fieldValue();
	 *  // if neither checkbox is checked
	 *  v === undefined
	 *  // if both checkboxes are checked
	 *  v == ['B1', 'B2']
	 *
	 *  var v = $('input[type=radio]').fieldValue();
	 *  // if neither radio is checked
	 *  v === undefined
	 *  // if first radio is checked
	 *  v == ['C1']
	 *
	 * The successful argument controls whether or not the field element must be 'successful'
	 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
	 * The default value of the successful argument is true.  If this value is false the value(s)
	 * for each element is returned.
	 *
	 * Note: This method *always* returns an array.  If no valid value can be determined the
	 *    array will be empty, otherwise it will contain one or more values.
	 */
	$.fn.fieldValue = function(successful) {
	    for (var val=[], i=0, max=this.length; i < max; i++) {
	        var el = this[i];
	        var v = $.fieldValue(el, successful);
	        if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length)) {
	            continue;
	        }
	        if (v.constructor == Array) {
	            $.merge(val, v);
	        }
	        else {
	            val.push(v);
	        }
	    }
	    return val;
	};

	/**
	 * Returns the value of the field element.
	 */
	$.fieldValue = function(el, successful) {
	    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
	    if (successful === undefined) {
	        successful = true;
	    }

	    if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
	        (t == 'checkbox' || t == 'radio') && !el.checked ||
	        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
	        tag == 'select' && el.selectedIndex == -1)) {
	            return null;
	    }

	    if (tag == 'select') {
	        var index = el.selectedIndex;
	        if (index < 0) {
	            return null;
	        }
	        var a = [], ops = el.options;
	        var one = (t == 'select-one');
	        var max = (one ? index+1 : ops.length);
	        for(var i=(one ? index : 0); i < max; i++) {
	            var op = ops[i];
	            if (op.selected) {
	                var v = op.value;
	                if (!v) { // extra pain for IE...
	                    v = (op.attributes && op.attributes.value && !(op.attributes.value.specified)) ? op.text : op.value;
	                }
	                if (one) {
	                    return v;
	                }
	                a.push(v);
	            }
	        }
	        return a;
	    }
	    return $(el).val();
	};

	/**
	 * Clears the form data.  Takes the following actions on the form's input fields:
	 *  - input text fields will have their 'value' property set to the empty string
	 *  - select elements will have their 'selectedIndex' property set to -1
	 *  - checkbox and radio inputs will have their 'checked' property set to false
	 *  - inputs of type submit, button, reset, and hidden will *not* be effected
	 *  - button elements will *not* be effected
	 */
	$.fn.clearForm = function(includeHidden) {
	    return this.each(function() {
	        $('input,select,textarea', this).clearFields(includeHidden);
	    });
	};

	/**
	 * Clears the selected form elements.
	 */
	$.fn.clearFields = $.fn.clearInputs = function(includeHidden) {
	    var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
	    return this.each(function() {
	        var t = this.type, tag = this.tagName.toLowerCase();
	        if (re.test(t) || tag == 'textarea') {
	            this.value = '';
	        }
	        else if (t == 'checkbox' || t == 'radio') {
	            this.checked = false;
	        }
	        else if (tag == 'select') {
	            this.selectedIndex = -1;
	        }
	        else if (t == "file") {
	            if (/MSIE/.test(navigator.userAgent)) {
	                $(this).replaceWith($(this).clone(true));
	            } else {
	                $(this).val('');
	            }
	        }
	        else if (includeHidden) {
	            // includeHidden can be the value true, or it can be a selector string
	            // indicating a special test; for example:
	            //  $('#myForm').clearForm('.special:hidden')
	            // the above would clean hidden inputs that have the class of 'special'
	            if ( (includeHidden === true && /hidden/.test(t)) ||
	                 (typeof includeHidden == 'string' && $(this).is(includeHidden)) ) {
	                this.value = '';
	            }
	        }
	    });
	};

	/**
	 * Resets the form data.  Causes all form elements to be reset to their original value.
	 */
	$.fn.resetForm = function() {
	    return this.each(function() {
	        // guard against an input with the name of 'reset'
	        // note that IE reports the reset function as an 'object'
	        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType)) {
	            this.reset();
	        }
	    });
	};

	/**
	 * Enables or disables any matching elements.
	 */
	$.fn.enable = function(b) {
	    if (b === undefined) {
	        b = true;
	    }
	    return this.each(function() {
	        this.disabled = !b;
	    });
	};

	/**
	 * Checks/unchecks any matching checkboxes or radio buttons and
	 * selects/deselects and matching option elements.
	 */
	$.fn.selected = function(select) {
	    if (select === undefined) {
	        select = true;
	    }
	    return this.each(function() {
	        var t = this.type;
	        if (t == 'checkbox' || t == 'radio') {
	            this.checked = select;
	        }
	        else if (this.tagName.toLowerCase() == 'option') {
	            var $sel = $(this).parent('select');
	            if (select && $sel[0] && $sel[0].type == 'select-one') {
	                // deselect all other options
	                $sel.find('option').selected(false);
	            }
	            this.selected = select;
	        }
	    });
	};

	// expose debug var
	$.fn.ajaxSubmit.debug = false;

	// helper fn for console logging
	function log() {
	    if (!$.fn.ajaxSubmit.debug) {
	        return;
	    }
	    var msg = '[jquery.form] ' + Array.prototype.join.call(arguments,'');
	    if (window.console && window.console.log) {
	        window.console.log(msg);
	    }
	    else if (window.opera && window.opera.postError) {
	        window.opera.postError(msg);
	    }
	}

	}));

/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(jQuery) {/**
	 * jquery.Jcrop.min.js v0.9.12 (build:20130202)
	 * jQuery Image Cropping Plugin - released under MIT License
	 * Copyright (c) 2008-2013 Tapmodo Interactive LLC
	 * https://github.com/tapmodo/Jcrop
	 */
	(function(a){a.Jcrop=function(b,c){function i(a){return Math.round(a)+"px"}function j(a){return d.baseClass+"-"+a}function k(){return a.fx.step.hasOwnProperty("backgroundColor")}function l(b){var c=a(b).offset();return[c.left,c.top]}function m(a){return[a.pageX-e[0],a.pageY-e[1]]}function n(b){typeof b!="object"&&(b={}),d=a.extend(d,b),a.each(["onChange","onSelect","onRelease","onDblClick"],function(a,b){typeof d[b]!="function"&&(d[b]=function(){})})}function o(a,b,c){e=l(D),bc.setCursor(a==="move"?a:a+"-resize");if(a==="move")return bc.activateHandlers(q(b),v,c);var d=_.getFixed(),f=r(a),g=_.getCorner(r(f));_.setPressed(_.getCorner(f)),_.setCurrent(g),bc.activateHandlers(p(a,d),v,c)}function p(a,b){return function(c){if(!d.aspectRatio)switch(a){case"e":c[1]=b.y2;break;case"w":c[1]=b.y2;break;case"n":c[0]=b.x2;break;case"s":c[0]=b.x2}else switch(a){case"e":c[1]=b.y+1;break;case"w":c[1]=b.y+1;break;case"n":c[0]=b.x+1;break;case"s":c[0]=b.x+1}_.setCurrent(c),bb.update()}}function q(a){var b=a;return bd.watchKeys
	(),function(a){_.moveOffset([a[0]-b[0],a[1]-b[1]]),b=a,bb.update()}}function r(a){switch(a){case"n":return"sw";case"s":return"nw";case"e":return"nw";case"w":return"ne";case"ne":return"sw";case"nw":return"se";case"se":return"nw";case"sw":return"ne"}}function s(a){return function(b){return d.disabled?!1:a==="move"&&!d.allowMove?!1:(e=l(D),W=!0,o(a,m(b)),b.stopPropagation(),b.preventDefault(),!1)}}function t(a,b,c){var d=a.width(),e=a.height();d>b&&b>0&&(d=b,e=b/a.width()*a.height()),e>c&&c>0&&(e=c,d=c/a.height()*a.width()),T=a.width()/d,U=a.height()/e,a.width(d).height(e)}function u(a){return{x:a.x*T,y:a.y*U,x2:a.x2*T,y2:a.y2*U,w:a.w*T,h:a.h*U}}function v(a){var b=_.getFixed();b.w>d.minSelect[0]&&b.h>d.minSelect[1]?(bb.enableHandles(),bb.done()):bb.release(),bc.setCursor(d.allowSelect?"crosshair":"default")}function w(a){if(d.disabled)return!1;if(!d.allowSelect)return!1;W=!0,e=l(D),bb.disableHandles(),bc.setCursor("crosshair");var b=m(a);return _.setPressed(b),bb.update(),bc.activateHandlers(x,v,a.type.substring
	(0,5)==="touch"),bd.watchKeys(),a.stopPropagation(),a.preventDefault(),!1}function x(a){_.setCurrent(a),bb.update()}function y(){var b=a("<div></div>").addClass(j("tracker"));return g&&b.css({opacity:0,backgroundColor:"white"}),b}function be(a){G.removeClass().addClass(j("holder")).addClass(a)}function bf(a,b){function t(){window.setTimeout(u,l)}var c=a[0]/T,e=a[1]/U,f=a[2]/T,g=a[3]/U;if(X)return;var h=_.flipCoords(c,e,f,g),i=_.getFixed(),j=[i.x,i.y,i.x2,i.y2],k=j,l=d.animationDelay,m=h[0]-j[0],n=h[1]-j[1],o=h[2]-j[2],p=h[3]-j[3],q=0,r=d.swingSpeed;c=k[0],e=k[1],f=k[2],g=k[3],bb.animMode(!0);var s,u=function(){return function(){q+=(100-q)/r,k[0]=Math.round(c+q/100*m),k[1]=Math.round(e+q/100*n),k[2]=Math.round(f+q/100*o),k[3]=Math.round(g+q/100*p),q>=99.8&&(q=100),q<100?(bh(k),t()):(bb.done(),bb.animMode(!1),typeof b=="function"&&b.call(bs))}}();t()}function bg(a){bh([a[0]/T,a[1]/U,a[2]/T,a[3]/U]),d.onSelect.call(bs,u(_.getFixed())),bb.enableHandles()}function bh(a){_.setPressed([a[0],a[1]]),_.setCurrent([a[2],
	a[3]]),bb.update()}function bi(){return u(_.getFixed())}function bj(){return _.getFixed()}function bk(a){n(a),br()}function bl(){d.disabled=!0,bb.disableHandles(),bb.setCursor("default"),bc.setCursor("default")}function bm(){d.disabled=!1,br()}function bn(){bb.done(),bc.activateHandlers(null,null)}function bo(){G.remove(),A.show(),A.css("visibility","visible"),a(b).removeData("Jcrop")}function bp(a,b){bb.release(),bl();var c=new Image;c.onload=function(){var e=c.width,f=c.height,g=d.boxWidth,h=d.boxHeight;D.width(e).height(f),D.attr("src",a),H.attr("src",a),t(D,g,h),E=D.width(),F=D.height(),H.width(E).height(F),M.width(E+L*2).height(F+L*2),G.width(E).height(F),ba.resize(E,F),bm(),typeof b=="function"&&b.call(bs)},c.src=a}function bq(a,b,c){var e=b||d.bgColor;d.bgFade&&k()&&d.fadeTime&&!c?a.animate({backgroundColor:e},{queue:!1,duration:d.fadeTime}):a.css("backgroundColor",e)}function br(a){d.allowResize?a?bb.enableOnly():bb.enableHandles():bb.disableHandles(),bc.setCursor(d.allowSelect?"crosshair":"default"),bb
	.setCursor(d.allowMove?"move":"default"),d.hasOwnProperty("trueSize")&&(T=d.trueSize[0]/E,U=d.trueSize[1]/F),d.hasOwnProperty("setSelect")&&(bg(d.setSelect),bb.done(),delete d.setSelect),ba.refresh(),d.bgColor!=N&&(bq(d.shade?ba.getShades():G,d.shade?d.shadeColor||d.bgColor:d.bgColor),N=d.bgColor),O!=d.bgOpacity&&(O=d.bgOpacity,d.shade?ba.refresh():bb.setBgOpacity(O)),P=d.maxSize[0]||0,Q=d.maxSize[1]||0,R=d.minSize[0]||0,S=d.minSize[1]||0,d.hasOwnProperty("outerImage")&&(D.attr("src",d.outerImage),delete d.outerImage),bb.refresh()}var d=a.extend({},a.Jcrop.defaults),e,f=navigator.userAgent.toLowerCase(),g=/msie/.test(f),h=/msie [1-6]\./.test(f);typeof b!="object"&&(b=a(b)[0]),typeof c!="object"&&(c={}),n(c);var z={border:"none",visibility:"visible",margin:0,padding:0,position:"absolute",top:0,left:0},A=a(b),B=!0;if(b.tagName=="IMG"){if(A[0].width!=0&&A[0].height!=0)A.width(A[0].width),A.height(A[0].height);else{var C=new Image;C.src=A[0].src,A.width(C.width),A.height(C.height)}var D=A.clone().removeAttr("id").
	css(z).show();D.width(A.width()),D.height(A.height()),A.after(D).hide()}else D=A.css(z).show(),B=!1,d.shade===null&&(d.shade=!0);t(D,d.boxWidth,d.boxHeight);var E=D.width(),F=D.height(),G=a("<div />").width(E).height(F).addClass(j("holder")).css({position:"relative",backgroundColor:d.bgColor}).insertAfter(A).append(D);d.addClass&&G.addClass(d.addClass);var H=a("<div />"),I=a("<div />").width("100%").height("100%").css({zIndex:310,position:"absolute",overflow:"hidden"}),J=a("<div />").width("100%").height("100%").css("zIndex",320),K=a("<div />").css({position:"absolute",zIndex:600}).dblclick(function(){var a=_.getFixed();d.onDblClick.call(bs,a)}).insertBefore(D).append(I,J);B&&(H=a("<img />").attr("src",D.attr("src")).css(z).width(E).height(F),I.append(H)),h&&K.css({overflowY:"hidden"});var L=d.boundary,M=y().width(E+L*2).height(F+L*2).css({position:"absolute",top:i(-L),left:i(-L),zIndex:290}).mousedown(w),N=d.bgColor,O=d.bgOpacity,P,Q,R,S,T,U,V=!0,W,X,Y;e=l(D);var Z=function(){function a(){var a={},b=["touchstart"
	,"touchmove","touchend"],c=document.createElement("div"),d;try{for(d=0;d<b.length;d++){var e=b[d];e="on"+e;var f=e in c;f||(c.setAttribute(e,"return;"),f=typeof c[e]=="function"),a[b[d]]=f}return a.touchstart&&a.touchend&&a.touchmove}catch(g){return!1}}function b(){return d.touchSupport===!0||d.touchSupport===!1?d.touchSupport:a()}return{createDragger:function(a){return function(b){return d.disabled?!1:a==="move"&&!d.allowMove?!1:(e=l(D),W=!0,o(a,m(Z.cfilter(b)),!0),b.stopPropagation(),b.preventDefault(),!1)}},newSelection:function(a){return w(Z.cfilter(a))},cfilter:function(a){return a.pageX=a.originalEvent.changedTouches[0].pageX,a.pageY=a.originalEvent.changedTouches[0].pageY,a},isSupported:a,support:b()}}(),_=function(){function h(d){d=n(d),c=a=d[0],e=b=d[1]}function i(a){a=n(a),f=a[0]-c,g=a[1]-e,c=a[0],e=a[1]}function j(){return[f,g]}function k(d){var f=d[0],g=d[1];0>a+f&&(f-=f+a),0>b+g&&(g-=g+b),F<e+g&&(g+=F-(e+g)),E<c+f&&(f+=E-(c+f)),a+=f,c+=f,b+=g,e+=g}function l(a){var b=m();switch(a){case"ne":return[
	b.x2,b.y];case"nw":return[b.x,b.y];case"se":return[b.x2,b.y2];case"sw":return[b.x,b.y2]}}function m(){if(!d.aspectRatio)return p();var f=d.aspectRatio,g=d.minSize[0]/T,h=d.maxSize[0]/T,i=d.maxSize[1]/U,j=c-a,k=e-b,l=Math.abs(j),m=Math.abs(k),n=l/m,r,s,t,u;return h===0&&(h=E*10),i===0&&(i=F*10),n<f?(s=e,t=m*f,r=j<0?a-t:t+a,r<0?(r=0,u=Math.abs((r-a)/f),s=k<0?b-u:u+b):r>E&&(r=E,u=Math.abs((r-a)/f),s=k<0?b-u:u+b)):(r=c,u=l/f,s=k<0?b-u:b+u,s<0?(s=0,t=Math.abs((s-b)*f),r=j<0?a-t:t+a):s>F&&(s=F,t=Math.abs(s-b)*f,r=j<0?a-t:t+a)),r>a?(r-a<g?r=a+g:r-a>h&&(r=a+h),s>b?s=b+(r-a)/f:s=b-(r-a)/f):r<a&&(a-r<g?r=a-g:a-r>h&&(r=a-h),s>b?s=b+(a-r)/f:s=b-(a-r)/f),r<0?(a-=r,r=0):r>E&&(a-=r-E,r=E),s<0?(b-=s,s=0):s>F&&(b-=s-F,s=F),q(o(a,b,r,s))}function n(a){return a[0]<0&&(a[0]=0),a[1]<0&&(a[1]=0),a[0]>E&&(a[0]=E),a[1]>F&&(a[1]=F),[Math.round(a[0]),Math.round(a[1])]}function o(a,b,c,d){var e=a,f=c,g=b,h=d;return c<a&&(e=c,f=a),d<b&&(g=d,h=b),[e,g,f,h]}function p(){var d=c-a,f=e-b,g;return P&&Math.abs(d)>P&&(c=d>0?a+P:a-P),Q&&Math.abs
	(f)>Q&&(e=f>0?b+Q:b-Q),S/U&&Math.abs(f)<S/U&&(e=f>0?b+S/U:b-S/U),R/T&&Math.abs(d)<R/T&&(c=d>0?a+R/T:a-R/T),a<0&&(c-=a,a-=a),b<0&&(e-=b,b-=b),c<0&&(a-=c,c-=c),e<0&&(b-=e,e-=e),c>E&&(g=c-E,a-=g,c-=g),e>F&&(g=e-F,b-=g,e-=g),a>E&&(g=a-F,e-=g,b-=g),b>F&&(g=b-F,e-=g,b-=g),q(o(a,b,c,e))}function q(a){return{x:a[0],y:a[1],x2:a[2],y2:a[3],w:a[2]-a[0],h:a[3]-a[1]}}var a=0,b=0,c=0,e=0,f,g;return{flipCoords:o,setPressed:h,setCurrent:i,getOffset:j,moveOffset:k,getCorner:l,getFixed:m}}(),ba=function(){function f(a,b){e.left.css({height:i(b)}),e.right.css({height:i(b)})}function g(){return h(_.getFixed())}function h(a){e.top.css({left:i(a.x),width:i(a.w),height:i(a.y)}),e.bottom.css({top:i(a.y2),left:i(a.x),width:i(a.w),height:i(F-a.y2)}),e.right.css({left:i(a.x2),width:i(E-a.x2)}),e.left.css({width:i(a.x)})}function j(){return a("<div />").css({position:"absolute",backgroundColor:d.shadeColor||d.bgColor}).appendTo(c)}function k(){b||(b=!0,c.insertBefore(D),g(),bb.setBgOpacity(1,0,1),H.hide(),l(d.shadeColor||d.bgColor,1),bb.
	isAwake()?n(d.bgOpacity,1):n(1,1))}function l(a,b){bq(p(),a,b)}function m(){b&&(c.remove(),H.show(),b=!1,bb.isAwake()?bb.setBgOpacity(d.bgOpacity,1,1):(bb.setBgOpacity(1,1,1),bb.disableHandles()),bq(G,0,1))}function n(a,e){b&&(d.bgFade&&!e?c.animate({opacity:1-a},{queue:!1,duration:d.fadeTime}):c.css({opacity:1-a}))}function o(){d.shade?k():m(),bb.isAwake()&&n(d.bgOpacity)}function p(){return c.children()}var b=!1,c=a("<div />").css({position:"absolute",zIndex:240,opacity:0}),e={top:j(),left:j().height(F),right:j().height(F),bottom:j()};return{update:g,updateRaw:h,getShades:p,setBgColor:l,enable:k,disable:m,resize:f,refresh:o,opacity:n}}(),bb=function(){function k(b){var c=a("<div />").css({position:"absolute",opacity:d.borderOpacity}).addClass(j(b));return I.append(c),c}function l(b,c){var d=a("<div />").mousedown(s(b)).css({cursor:b+"-resize",position:"absolute",zIndex:c}).addClass("ord-"+b);return Z.support&&d.bind("touchstart.jcrop",Z.createDragger(b)),J.append(d),d}function m(a){var b=d.handleSize,e=l(a,c++
	).css({opacity:d.handleOpacity}).addClass(j("handle"));return b&&e.width(b).height(b),e}function n(a){return l(a,c++).addClass("jcrop-dragbar")}function o(a){var b;for(b=0;b<a.length;b++)g[a[b]]=n(a[b])}function p(a){var b,c;for(c=0;c<a.length;c++){switch(a[c]){case"n":b="hline";break;case"s":b="hline bottom";break;case"e":b="vline right";break;case"w":b="vline"}e[a[c]]=k(b)}}function q(a){var b;for(b=0;b<a.length;b++)f[a[b]]=m(a[b])}function r(a,b){d.shade||H.css({top:i(-b),left:i(-a)}),K.css({top:i(b),left:i(a)})}function t(a,b){K.width(Math.round(a)).height(Math.round(b))}function v(){var a=_.getFixed();_.setPressed([a.x,a.y]),_.setCurrent([a.x2,a.y2]),w()}function w(a){if(b)return x(a)}function x(a){var c=_.getFixed();t(c.w,c.h),r(c.x,c.y),d.shade&&ba.updateRaw(c),b||A(),a?d.onSelect.call(bs,u(c)):d.onChange.call(bs,u(c))}function z(a,c,e){if(!b&&!c)return;d.bgFade&&!e?D.animate({opacity:a},{queue:!1,duration:d.fadeTime}):D.css("opacity",a)}function A(){K.show(),d.shade?ba.opacity(O):z(O,!0),b=!0}function B
	(){F(),K.hide(),d.shade?ba.opacity(1):z(1),b=!1,d.onRelease.call(bs)}function C(){h&&J.show()}function E(){h=!0;if(d.allowResize)return J.show(),!0}function F(){h=!1,J.hide()}function G(a){a?(X=!0,F()):(X=!1,E())}function L(){G(!1),v()}var b,c=370,e={},f={},g={},h=!1;d.dragEdges&&a.isArray(d.createDragbars)&&o(d.createDragbars),a.isArray(d.createHandles)&&q(d.createHandles),d.drawBorders&&a.isArray(d.createBorders)&&p(d.createBorders),a(document).bind("touchstart.jcrop-ios",function(b){a(b.currentTarget).hasClass("jcrop-tracker")&&b.stopPropagation()});var M=y().mousedown(s("move")).css({cursor:"move",position:"absolute",zIndex:360});return Z.support&&M.bind("touchstart.jcrop",Z.createDragger("move")),I.append(M),F(),{updateVisible:w,update:x,release:B,refresh:v,isAwake:function(){return b},setCursor:function(a){M.css("cursor",a)},enableHandles:E,enableOnly:function(){h=!0},showHandles:C,disableHandles:F,animMode:G,setBgOpacity:z,done:L}}(),bc=function(){function f(b){M.css({zIndex:450}),b?a(document).bind("touchmove.jcrop"
	,k).bind("touchend.jcrop",l):e&&a(document).bind("mousemove.jcrop",h).bind("mouseup.jcrop",i)}function g(){M.css({zIndex:290}),a(document).unbind(".jcrop")}function h(a){return b(m(a)),!1}function i(a){return a.preventDefault(),a.stopPropagation(),W&&(W=!1,c(m(a)),bb.isAwake()&&d.onSelect.call(bs,u(_.getFixed())),g(),b=function(){},c=function(){}),!1}function j(a,d,e){return W=!0,b=a,c=d,f(e),!1}function k(a){return b(m(Z.cfilter(a))),!1}function l(a){return i(Z.cfilter(a))}function n(a){M.css("cursor",a)}var b=function(){},c=function(){},e=d.trackDocument;return e||M.mousemove(h).mouseup(i).mouseout(i),D.before(M),{activateHandlers:j,setCursor:n}}(),bd=function(){function e(){d.keySupport&&(b.show(),b.focus())}function f(a){b.hide()}function g(a,b,c){d.allowMove&&(_.moveOffset([b,c]),bb.updateVisible(!0)),a.preventDefault(),a.stopPropagation()}function i(a){if(a.ctrlKey||a.metaKey)return!0;Y=a.shiftKey?!0:!1;var b=Y?10:1;switch(a.keyCode){case 37:g(a,-b,0);break;case 39:g(a,b,0);break;case 38:g(a,0,-b);break;
	case 40:g(a,0,b);break;case 27:d.allowSelect&&bb.release();break;case 9:return!0}return!1}var b=a('<input type="radio" />').css({position:"fixed",left:"-120px",width:"12px"}).addClass("jcrop-keymgr"),c=a("<div />").css({position:"absolute",overflow:"hidden"}).append(b);return d.keySupport&&(b.keydown(i).blur(f),h||!d.fixedSupport?(b.css({position:"absolute",left:"-20px"}),c.append(b).insertBefore(D)):b.insertBefore(D)),{watchKeys:e}}();Z.support&&M.bind("touchstart.jcrop",Z.newSelection),J.hide(),br(!0);var bs={setImage:bp,animateTo:bf,setSelect:bg,setOptions:bk,tellSelect:bi,tellScaled:bj,setClass:be,disable:bl,enable:bm,cancel:bn,release:bb.release,destroy:bo,focus:bd.watchKeys,getBounds:function(){return[E*T,F*U]},getWidgetSize:function(){return[E,F]},getScaleFactor:function(){return[T,U]},getOptions:function(){return d},ui:{holder:G,selection:K}};return g&&G.bind("selectstart",function(){return!1}),A.data("Jcrop",bs),bs},a.fn.Jcrop=function(b,c){var d;return this.each(function(){if(a(this).data("Jcrop")){if(
	b==="api")return a(this).data("Jcrop");a(this).data("Jcrop").setOptions(b)}else this.tagName=="IMG"?a.Jcrop.Loader(this,function(){a(this).css({display:"block",visibility:"hidden"}),d=a.Jcrop(this,b),a.isFunction(c)&&c.call(d)}):(a(this).css({display:"block",visibility:"hidden"}),d=a.Jcrop(this,b),a.isFunction(c)&&c.call(d))}),this},a.Jcrop.Loader=function(b,c,d){function g(){f.complete?(e.unbind(".jcloader"),a.isFunction(c)&&c.call(f)):window.setTimeout(g,50)}var e=a(b),f=e[0];e.bind("load.jcloader",g).bind("error.jcloader",function(b){e.unbind(".jcloader"),a.isFunction(d)&&d.call(f)}),f.complete&&a.isFunction(c)&&(e.unbind(".jcloader"),c.call(f))},a.Jcrop.defaults={allowSelect:!0,allowMove:!0,allowResize:!0,trackDocument:!0,baseClass:"jcrop",addClass:null,bgColor:"black",bgOpacity:.6,bgFade:!1,borderOpacity:.4,handleOpacity:.5,handleSize:null,aspectRatio:0,keySupport:!0,createHandles:["n","s","e","w","nw","ne","se","sw"],createDragbars:["n","s","e","w"],createBorders:["n","s","e","w"],drawBorders:!0,dragEdges
	:!0,fixedSupport:!0,touchSupport:null,shade:null,boxWidth:0,boxHeight:0,boundary:2,fadeTime:400,animationDelay:20,swingSpeed:3,minSelect:[0,0],maxSize:[0,0],minSize:[0,0],onChange:function(){},onSelect:function(){},onDblClick:function(){},onRelease:function(){}}})(jQuery);
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(__webpack_provided_window_dot_jQuery) {/* =========================================================
	 * bootstrap-datepicker.js 
	 * http://www.eyecon.ro/bootstrap-datepicker
	 * =========================================================
	 * Copyright 2012 Stefan Petre
	 *
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 *
	 * http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 * ========================================================= */
	 
	!function( $ ) {
		
		// Picker object
		
		var Datepicker = function(element, options){
			this.element = $(element);
			this.format = DPGlobal.parseFormat(options.format||this.element.data('date-format')||'mm/dd/yyyy');
			this.picker = $(DPGlobal.template)
								.appendTo('body')
								.on({
									click: $.proxy(this.click, this)//,
									//mousedown: $.proxy(this.mousedown, this)
								});
			this.isInput = this.element.is('input');
			this.component = this.element.is('.date') ? this.element.find('.add-on') : false;
			
			if (this.isInput) {
				this.element.on({
					focus: $.proxy(this.show, this),
					//blur: $.proxy(this.hide, this),
					keyup: $.proxy(this.update, this)
				});
			} else {
				if (this.component){
					this.component.on('click', $.proxy(this.show, this));
				} else {
					this.element.on('click', $.proxy(this.show, this));
				}
			}
		
			this.minViewMode = options.minViewMode||this.element.data('date-minviewmode')||0;
			if (typeof this.minViewMode === 'string') {
				switch (this.minViewMode) {
					case 'months':
						this.minViewMode = 1;
						break;
					case 'years':
						this.minViewMode = 2;
						break;
					default:
						this.minViewMode = 0;
						break;
				}
			}
			this.viewMode = options.viewMode||this.element.data('date-viewmode')||0;
			if (typeof this.viewMode === 'string') {
				switch (this.viewMode) {
					case 'months':
						this.viewMode = 1;
						break;
					case 'years':
						this.viewMode = 2;
						break;
					default:
						this.viewMode = 0;
						break;
				}
			}
			this.startViewMode = this.viewMode;
			this.weekStart = options.weekStart||this.element.data('date-weekstart')||0;
			this.weekEnd = this.weekStart === 0 ? 6 : this.weekStart - 1;
			this.onRender = options.onRender;
			this.fillDow();
			this.fillMonths();
			this.update();
			this.showMode();
		};
		
		Datepicker.prototype = {
			constructor: Datepicker,
			
			show: function(e) {
				this.picker.show();
				this.height = this.component ? this.component.outerHeight() : this.element.outerHeight();
				this.place();
				$(window).on('resize', $.proxy(this.place, this));
				if (e ) {
					e.stopPropagation();
					e.preventDefault();
				}
				if (!this.isInput) {
				}
				var that = this;
				$(document).on('mousedown', function(ev){
					if ($(ev.target).closest('.datepicker').length == 0) {
						that.hide();
					}
				});
				this.element.trigger({
					type: 'show',
					date: this.date
				});
			},
			
			hide: function(){
				this.picker.hide();
				$(window).off('resize', this.place);
				this.viewMode = this.startViewMode;
				this.showMode();
				if (!this.isInput) {
					$(document).off('mousedown', this.hide);
				}
				//this.set();
				this.element.trigger({
					type: 'hide',
					date: this.date
				});
			},
			
			set: function() {
				var formated = DPGlobal.formatDate(this.date, this.format);
				if (!this.isInput) {
					if (this.component){
						this.element.find('input').prop('value', formated);
					}
					this.element.data('date', formated);
				} else {
					this.element.prop('value', formated);
				}
			},
			
			setValue: function(newDate) {
				if (typeof newDate === 'string') {
					this.date = DPGlobal.parseDate(newDate, this.format);
				} else {
					this.date = new Date(newDate);
				}
				this.set();
				this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
				this.fill();
			},
			
			place: function(){
				var offset = this.component ? this.component.offset() : this.element.offset();
				this.picker.css({
					top: offset.top + this.height,
					left: offset.left
				});
			},
			
			update: function(newDate){
				this.date = DPGlobal.parseDate(
					typeof newDate === 'string' ? newDate : (this.isInput ? this.element.prop('value') : this.element.data('date')),
					this.format
				);
				this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
				this.fill();
			},
			
			fillDow: function(){
				var dowCnt = this.weekStart;
				var html = '<tr>';
				while (dowCnt < this.weekStart + 7) {
					html += '<th class="dow">'+DPGlobal.dates.daysMin[(dowCnt++)%7]+'</th>';
				}
				html += '</tr>';
				this.picker.find('.datepicker-days thead').append(html);
			},
			
			fillMonths: function(){
				var html = '';
				var i = 0
				while (i < 12) {
					html += '<span class="month">'+DPGlobal.dates.monthsShort[i++]+'</span>';
				}
				this.picker.find('.datepicker-months td').append(html);
			},
			
			fill: function() {
				var d = new Date(this.viewDate),
					year = d.getFullYear(),
					month = d.getMonth(),
					currentDate = this.date.valueOf();
				this.picker.find('.datepicker-days th:eq(1)')
							.text(DPGlobal.dates.months[month]+' '+year);
				var prevMonth = new Date(year, month-1, 28,0,0,0,0),
					day = DPGlobal.getDaysInMonth(prevMonth.getFullYear(), prevMonth.getMonth());
				prevMonth.setDate(day);
				prevMonth.setDate(day - (prevMonth.getDay() - this.weekStart + 7)%7);
				var nextMonth = new Date(prevMonth);
				nextMonth.setDate(nextMonth.getDate() + 42);
				nextMonth = nextMonth.valueOf();
				var html = [];
				var clsName,
					prevY,
					prevM;
				while(prevMonth.valueOf() < nextMonth) {
					if (prevMonth.getDay() === this.weekStart) {
						html.push('<tr>');
					}
					clsName = this.onRender(prevMonth);
					prevY = prevMonth.getFullYear();
					prevM = prevMonth.getMonth();
					if ((prevM < month &&  prevY === year) ||  prevY < year) {
						clsName += ' old';
					} else if ((prevM > month && prevY === year) || prevY > year) {
						clsName += ' new';
					}
					if (prevMonth.valueOf() === currentDate) {
						clsName += ' active';
					}
					html.push('<td class="day '+clsName+'">'+prevMonth.getDate() + '</td>');
					if (prevMonth.getDay() === this.weekEnd) {
						html.push('</tr>');
					}
					prevMonth.setDate(prevMonth.getDate()+1);
				}
				this.picker.find('.datepicker-days tbody').empty().append(html.join(''));
				var currentYear = this.date.getFullYear();
				
				var months = this.picker.find('.datepicker-months')
							.find('th:eq(1)')
								.text(year)
								.end()
							.find('span').removeClass('active');
				if (currentYear === year) {
					months.eq(this.date.getMonth()).addClass('active');
				}
				
				html = '';
				year = parseInt(year/10, 10) * 10;
				var yearCont = this.picker.find('.datepicker-years')
									.find('th:eq(1)')
										.text(year + '-' + (year + 9))
										.end()
									.find('td');
				year -= 1;
				for (var i = -1; i < 11; i++) {
					html += '<span class="year'+(i === -1 || i === 10 ? ' old' : '')+(currentYear === year ? ' active' : '')+'">'+year+'</span>';
					year += 1;
				}
				yearCont.html(html);
			},
			
			click: function(e) {
				e.stopPropagation();
				e.preventDefault();
				var target = $(e.target).closest('span, td, th');
				if (target.length === 1) {
					switch(target[0].nodeName.toLowerCase()) {
						case 'th':
							switch(target[0].className) {
								case 'switch':
									this.showMode(1);
									break;
								case 'prev':
								case 'next':
									this.viewDate['set'+DPGlobal.modes[this.viewMode].navFnc].call(
										this.viewDate,
										this.viewDate['get'+DPGlobal.modes[this.viewMode].navFnc].call(this.viewDate) + 
										DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1)
									);
									this.fill();
									this.set();
									break;
							}
							break;
						case 'span':
							if (target.is('.month')) {
								var month = target.parent().find('span').index(target);
								this.viewDate.setMonth(month);
							} else {
								var year = parseInt(target.text(), 10)||0;
								this.viewDate.setFullYear(year);
							}
							if (this.viewMode !== 0) {
								this.date = new Date(this.viewDate);
								this.element.trigger({
									type: 'changeDate',
									date: this.date,
									viewMode: DPGlobal.modes[this.viewMode].clsName
								});
							}
							this.showMode(-1);
							this.fill();
							this.set();
							break;
						case 'td':
							if (target.is('.day') && !target.is('.disabled')){
								var day = parseInt(target.text(), 10)||1;
								var month = this.viewDate.getMonth();
								if (target.is('.old')) {
									month -= 1;
								} else if (target.is('.new')) {
									month += 1;
								}
								var year = this.viewDate.getFullYear();
								this.date = new Date(year, month, day,0,0,0,0);
								this.viewDate = new Date(year, month, Math.min(28, day),0,0,0,0);
								this.fill();
								this.set();
								this.element.trigger({
									type: 'changeDate',
									date: this.date,
									viewMode: DPGlobal.modes[this.viewMode].clsName
								});
							}
							break;
					}
				}
			},
			
			mousedown: function(e){
				e.stopPropagation();
				e.preventDefault();
			},
			
			showMode: function(dir) {
				if (dir) {
					this.viewMode = Math.max(this.minViewMode, Math.min(2, this.viewMode + dir));
				}
				this.picker.find('>div').hide().filter('.datepicker-'+DPGlobal.modes[this.viewMode].clsName).show();
			}
		};
		
		$.fn.datepicker = function ( option, val ) {
			return this.each(function () {
				var $this = $(this),
					data = $this.data('datepicker'),
					options = typeof option === 'object' && option;
				if (!data) {
					$this.data('datepicker', (data = new Datepicker(this, $.extend({}, $.fn.datepicker.defaults,options))));
				}
				if (typeof option === 'string') data[option](val);
			});
		};

		$.fn.datepicker.defaults = {
			onRender: function(date) {
				return '';
			}
		};
		$.fn.datepicker.Constructor = Datepicker;
		
		var DPGlobal = {
			modes: [
				{
					clsName: 'days',
					navFnc: 'Month',
					navStep: 1
				},
				{
					clsName: 'months',
					navFnc: 'FullYear',
					navStep: 1
				},
				{
					clsName: 'years',
					navFnc: 'FullYear',
					navStep: 10
			}],
			dates:{
				days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
				daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
				daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
				months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
				monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
			},
			isLeapYear: function (year) {
				return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0))
			},
			getDaysInMonth: function (year, month) {
				return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month]
			},
			parseFormat: function(format){
				var separator = format.match(/[.\/\-\s].*?/),
					parts = format.split(/\W+/);
				if (!separator || !parts || parts.length === 0){
					throw new Error("Invalid date format.");
				}
				return {separator: separator, parts: parts};
			},
			parseDate: function(date, format) {
				var parts = date.split(format.separator),
					date = new Date(),
					val;
				date.setHours(0);
				date.setMinutes(0);
				date.setSeconds(0);
				date.setMilliseconds(0);
				if (parts.length === format.parts.length) {
					var year = date.getFullYear(), day = date.getDate(), month = date.getMonth();
					for (var i=0, cnt = format.parts.length; i < cnt; i++) {
						val = parseInt(parts[i], 10)||1;
						switch(format.parts[i]) {
							case 'dd':
							case 'd':
								day = val;
								date.setDate(val);
								break;
							case 'mm':
							case 'm':
								month = val - 1;
								date.setMonth(val - 1);
								break;
							case 'yy':
								year = 2000 + val;
								date.setFullYear(2000 + val);
								break;
							case 'yyyy':
								year = val;
								date.setFullYear(val);
								break;
						}
					}
					date = new Date(year, month, day, 0 ,0 ,0);
				}
				return date;
			},
			formatDate: function(date, format){
				var val = {
					d: date.getDate(),
					m: date.getMonth() + 1,
					yy: date.getFullYear().toString().substring(2),
					yyyy: date.getFullYear()
				};
				val.dd = (val.d < 10 ? '0' : '') + val.d;
				val.mm = (val.m < 10 ? '0' : '') + val.m;
				var date = [];
				for (var i=0, cnt = format.parts.length; i < cnt; i++) {
					date.push(val[format.parts[i]]);
				}
				return date.join(format.separator);
			},
			headTemplate: '<thead>'+
								'<tr>'+
									'<th class="prev">&lsaquo;</th>'+
									'<th colspan="5" class="switch"></th>'+
									'<th class="next">&rsaquo;</th>'+
								'</tr>'+
							'</thead>',
			contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>'
		};
		DPGlobal.template = '<div class="datepicker dropdown-menu">'+
								'<div class="datepicker-days">'+
									'<table class=" table-condensed">'+
										DPGlobal.headTemplate+
										'<tbody></tbody>'+
									'</table>'+
								'</div>'+
								'<div class="datepicker-months">'+
									'<table class="table-condensed">'+
										DPGlobal.headTemplate+
										DPGlobal.contTemplate+
									'</table>'+
								'</div>'+
								'<div class="datepicker-years">'+
									'<table class="table-condensed">'+
										DPGlobal.headTemplate+
										DPGlobal.contTemplate+
									'</table>'+
								'</div>'+
							'</div>';

	}( __webpack_provided_window_dot_jQuery );
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {$(function(){
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
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {$(function(){
		$("#div_collectlist li").hover(function(){
			$(this).find(".n-info-wrapper02").show();
			$(this).find(".n-info-wrapper01").hide();
		},function(){
			$(this).find(".n-info-wrapper01").show();
			$(this).find(".n-info-wrapper02").hide();
		});

		// $(document).on("hover",".u-select-con",function(){
		// 	$(this).addClass("state-hover");
		// 	$(this).find(".select-state").show();
		// },function(){
		// 	$(this).removeClass("state-hover");
		// 	$(this).find(".select-state").hide();
		// });

		$(".private_btn").on("click",function(){
			var _con = $(this).attr("_con");
			var username = '<div class="title">对 '+$(this).attr("username")+' 说：</div>';
			$("#pageDialogMain").prepend(username);
			tanFun("420","210",_con);
		});

		$("#ulMoneyList li").on("click",function(){
			var $this = $(this);
			$this.addClass("f-checked").siblings().removeClass("f-checked");
			$("#b_money").html($this.attr("money"));
			$("#hidMoney").val($this.attr("money"));

		});

		$(".f-pay-bank dd").on("click",function(){
			var $this = $(this);
			$this.addClass("checked").siblings().removeClass("checked");
			$("#hidPayBank").val($this.find("input").val());
		});

		$("#txtOtherMoney").on("input propertychange",function(){
			var $this = $(this);
			var val = $this.val();

			if(!isNaN(val)){
			   $("#b_money").html(val);
			   $("#hidMoney").val(val);
			}else{
				$this.html('');
			}
		});

		$("#ul_menu li").on("click",function(){
			var $this = $(this);
			var index = $(this).index();
			$this.addClass("curr").siblings().removeClass("curr");
			$(".f-pay-bank").hide();
			$(".f-pay-bank").eq(index).show();
		});


		$("#a_transfer").on("click",function(){
			tanFun("428","227",'<div class="g-need-bind clrfix"><dl><dt class="orange">需要设置支付密码才能进行转账</dt><dd class="gray9">您还未设置支付密码，请先设置支付密码再进行转账操作</dd><dd><a href="UserAuth-10.html">立即设置</a></dd></dl></div>');
		});



		// 时间处理
		var date = new Date();
	    timeFun();
	    function timeFun(){
	        var $year = $("#sltYear .select-state");
	        //获取年份
	        var year = date.getFullYear();
	        //年份循环
	        for ( var i=0; i<=100; i++ ) {
	            $year.append('<a value="'+(year-i)+'">'+(year-i)+'</a>');
	        }

	        var sltMonthVal = $("#sltMonth .gray6").html();
	        if( sltMonthVal!="月" ){
	        	thisDaysFun(sltMonthVal);
	        }
	    }    

	    // 计算当月有少天
	    function daysFun(mouth){
	        //定义当月的天数；
	        var days;
	        //当月份为二月时，根据闰年还是非闰年判断天数
	        if( mouth==2 ){
	            days = date.getFullYear() % 4 == 0 ? 29 : 28;
	        }
	        else if(mouth == 1 || mouth == 3 || mouth == 5 || mouth == 7 || mouth == 8 || mouth == 10 || mouth == 12){
	            //月份为：1,3,5,7,8,10,12 时，为大月.则天数为31；
	            days=31;
	        }
	        else{
	            //其他月份，天数为：30.
	            days=30;
	        }
	        return days;
	    }

	    $("#sltMonth .select-state a").on("click",function(){
	        var days = $(this).html();
	        thisDaysFun(days);
	    });

	    // 循环天
	    function thisDaysFun(days){
	        $days = $("#sltDay .select-state");
	        $days.html('');
	        $("#sltDay .gray6").html('日<s class="u-personal"></s>');
	        //天循环
	        for ( var i=0; i<daysFun(days); i++ ) {
	            $days.append('<a value="'+((i+1)<=9 ? "0"+(i+1) : (i+1))+'">'+((i+1)<=9 ? "0"+(i+1) : (i+1))+'</a>');
	        }
	    }

	    	// 城市函数
		window.provinceFun = function (province,Callback){
			// if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/system/area/getcitys",
		        type: 'POST',
		        dataType: 'json',
		        data: { province:province }
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
		        alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		}

		// 城市区域函数
		window.getdistrictsFun = function(city,Callback){
			// if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/system/area/getdistricts",
		        type: 'POST',
		        dataType: 'json',
		        data: { city:city }
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
		        alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		}
	})
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {$(function(){
	 
		// 下拉框
		$(document).on(click,".select-xiala",function(){
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

		// 城市接口
		$(document).on("click","#selProvinceID .select-state a",function(){
			var province = $(this).attr("value"); //城市
			$("#selCityID input, #selCountyID input").val('');
			provinceFun(province,function(data){
				$("#selCityID .select-state").html('');
				$("#selCityID .select-xiala, #selCountyID .select-xiala").html('---请选择---<s class="u-personal"></s>');
				for( var v in data.result ){
					$("#selCityID .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
			});
		});

		// 城市区接口
		$(document).on(click, "#selCityID .select-state a",function(){
			var province = $(this).attr("value"); //城市
			$("#selCountyID input").val('');
			getdistrictsFun(province,function(data){
				$("#selCountyID .select-state").html('');
				$("#selCountyID .select-xiala").html('---请选择---<s class="u-personal"></s>');
				for( var v in data.result ){
					$("#selCountyID .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
				}
			});
		});

		// 默认地址选择
		$(document).on("click",".set-default",function(){
			var $this = $(this);
			if( $this.hasClass("z-click") ){
				$this.removeClass("z-click");
				$("#a_default").attr({"value":"0"});
			}else{
				$this.addClass("z-click");
				$("#a_default").attr({"value":"1"});
			}
		});

		// 提交
		$(document).on("click","#btnSaveAddress",function(){
			var id = $(this).attr("_id");
			var province = $("#selProvinceID").find("input").val() //省份
			var city = $("#selCityID").find("input").val() //城市
			var district = $("#selCountyID").find("input").val() //区
			var address = $("#txtAddress").val(); //详细地址
			var txtPostCode = $("#txtPostCode").val(); //邮政编码
			var name = $("#txtConsignee").val(); //收货人
			var txtTel = $("#txtTel").val(); //固定电话
			var txtMobile = $("#txtMobile").val(); //手机号码
			var is_default = $("#a_default").attr("value");//默认地址


			if( province==""||city==""||district=="" ){
				$("#selProvinceID").parents("li").find(".orange").show();
				return;
			}

			if( address=="" ){
				$("#txtAddress").parents("li").find(".orange").show();
				return;
			}else{
				$("#txtAddress").parents("li").find(".orange").hide();
			}

			if( name=="" ){
				$("#txtConsignee").parents("li").find(".orange").show();
				return;
			}else{
				$("#txtConsignee").parents("li").find(".orange").hide();
			}

			if( address=="" ){
				$("#txtAddress").parents("li").find(".orange").show();
				return;
			}else{
				$("#txtAddress").parents("li").find(".orange").hide();
			}

			if( txtMobile=="" ){
				$("#txtMobile").parents("li").find(".orange").show();
				return;
			}else{
				$("#txtMobile").parents("li").find(".orange").hide();
			}

			var dataObj = {
				id:id,
				name:name,
				province:province,
				city:city,
				district:district,
				address:address,
				zipcode:txtPostCode,
				telephone:txtTel,
				mobile:txtMobile,
				is_default:is_default
			}
			saveaddressFun(dataObj);
		});

		// 地址增加修改
		window.saveaddressFun = function (dataObj){
			$.ajax({
		        url: "/member/service/saveaddress",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		            FailDialog(162,60,"设置成功",window.location.href);
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
		}

		// 设置默认地址
		window.setmembercontactdefaultFun = function(dataObj){
			$.ajax({
		        url: "/member/service/setmembercontactdefault",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		            FailDialog(162,60,"设置成功",window.location.href);
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
		}

		// 删除地址
		window.deleteaddressFun = function (dataObj){
			$.ajax({
		        url: "/member/service/deleteaddress",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		            FailDialog(162,60,"删除成功",window.location.href);
		        }else{
		            FailDialog(162,60,"删除失败");
		        }
		    })
		    .fail(function() {
		        alert('网络错误！')
		    })
		    .always(function() {
		        ajaxlack = true;
		    });
		}
	});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {$(function(){
	    if( pageTrue=="userphoto" ){
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

	    }
	})
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }
]);