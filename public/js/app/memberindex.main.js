require("../libs/query");
require("../libs/jquery-form");
require("../libs/jquery.Jcrop");
require("../libs/pagination-min");
require("../libs/bootstrap-datepicker");
require("./cartAjax");
require("./public");
require("./bottomFun");
require("./postsingle");
require("./memberPublic");
require("./address");
require("./userphoto");
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