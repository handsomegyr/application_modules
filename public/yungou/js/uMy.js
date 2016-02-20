$(function(){

	// 标签切换
	$("#ul_Menu li").on("click",function(){
		var index = $(this).index();
		$(this).addClass("current").siblings().removeClass("current");
		$(".content-wrap").hide();
		if( index==0 ){
			$("#div_BuyList").show();
			$("#midNavLine").animate({left:'297px',width:"64px"});
		}else if( index==1 ){
			$("#div_OrderList").show();
			$("#midNavLine").animate({left:'424px',width:"82px"});
		}else if( index==2 ){
			$("#div_PostList").show();
			$("#midNavLine").animate({left:'567px',width:"40px"});
		}
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
		$("#divMsgBox .Comment_form").html('<iframe hidefocus="true" name="myFrame" frameborder="0" scrolling="no" src="/yungou/message.html?page=uMy" style="width: 100%;height:100%;cursor:text;"></iframe>');
	});

	// 关闭私信
	$("#divMsgBox .Close").on("click",function(){
		$("#divMsgBox").hide();
	});


	/************** 云购记录 ******************/

	// getuserpagebuyrafpostFirstDataFun(1,8,'');

	function getuserpagebuyrafpostFirstDataFun(page,limit,state){
		$(".good-list").html('<div class="loading-2015"><em></em></div>');
		var dataObj = {
			page:page,
			limit:limit,
			state:state
		}
		// 数据加载 第一次
		getuserpagebuyrafpostAjaxFun(dataObj,function(data){
			var total = data.result.total;
			getuserpagebuyrafpostAddDataFun(data);
			// 分页
			PageCreate('#g-pagination',total,limit,1,function(pageNumber){
				if( total!=null ){ total = null; return; }
				dataObj.page = pageNumber;
				// 翻页后数据加载
				getuserpagebuyrafpostAjaxFun(dataObj,function(data){
					getuserpagebuyrafpostAddDataFun(data);
				});
		    });
		});
	}

	// 数据插入 云购记录
	function getuserpagebuyrafpostAddDataFun(data){
		if( data.success ){
	    	var dataLi = '';
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
				dataLi += '<li>'
                    +'<a class="g-pic" target="_blank" href="javascript:;"><img alt="" src="'+goodsPic+'" width="100" height="100"><span class="g-bg g-end"></span><span class="g-txt">已揭晓</span></a>'
                    +'<div class="g-info">'
                        +'<h2 class="g-title"><a rel="nofollow" target="_blank" href="../detail.html">(第'+codePeriod+'云)'+goodsSName+'</a></h2>'
                        +'<p class="g-price">价值：￥'+codePrice+'</p>'
                        +'<div class="g-older"><p>获得者：<a href="javascript:;" target="_blank">'+userName+'</a></p><p>揭晓时间：'+codeRTime+'</p></div>'
                    +'</div>'
                    +'<div class="g-total">参与&nbsp;<span class="orange">'+buyNum+'</span>&nbsp;人次</div>'
                    +'<a class="g-see" target="_blank" href="javascript:;">查看详情</a><i class="single"><i class="single"></i></i>'
                    +'<div class="g-time"><div class="aricle"><div class="cir"></div></div><div class="time-str"><div class="str">'+buyTime+'</div></div></div><div class="clear"></div>'
                +'</li>';

                dataLi = '<li>'
				    +'<a class="g-pic" target="_blank" href="javascript:;">'
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
				    +'<a class="g-buy" rel="nofollow" target="_blank" href="javascript:;">跟随云购</a>'
				    +'<i class="single"><i class="single"></i></i>'
				    +'<div class="g-time"><div class="aricle"><div class="cir"></div></div><div class="time-str"><div class="str">'+buyTime+'</div></div></div>'
				    +'<div class="clear"></div>'
				+'</li>';	    		
	    	});
			
			if( dataLi=='' ){
				$("#g-pagination").hide();
				$("#div_BuyList li, .loading-2015").remove();
				$("#div_BuyList .good-list").html('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
			}else{
				$("#g-pagination").show();
				$("#div_BuyList li, .null-data, .loading-2015").remove();
				$("#div_BuyList .good-list").html(dataLi);
			}
	    }else{

	    }
	}

	// 数据插入 获得商品
	function getuserpagebuyrafpostAddDataFun(data){
		if( data.success ){
	    	var dataLi = '';
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
				        +'<h2 class="g-title owner"><a rel="nofollow" target="_blank" href="javascript:;">'+goodsSName+'</a></h2>'
				        +'<div class="g-older"><p class="g-price">价值：￥'+codePrice+'</p><p>幸运云购码：<b class="orange">'+codeRNO+'</b></p></div>'
				    +'</div>'
				    +'<div class="g-total">参与&nbsp;<span class="orange">'+buyNum+'</span>&nbsp;人次</div>'
				    +'<a rel="nofollow" class="g-see" target="_blank" href="javascript:;">查看详情</a>'
				    +'<i class="single"><i class="single"></i></i>'
				    +'<div class="g-time"><div class="aricle"><div class="cir"></div></div><div class="time-str"><div class="str">'+codeRTime+'</div></div></div><div class="clear"></div>'
				+'</li>';	    		
	    	});
			
			if( dataLi=='' ){
				$("#g-pagination").hide();
				$("#div_OrderList li, .loading-2015").remove();
				$("#div_OrderList .good-list").html('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
			}else{
				$("#g-pagination").show();
				$("#div_OrderList li, .null-data, .loading-2015").remove();
				$("#div_OrderList .good-list").html(dataLi);
			}
	    }else{

	    }
	}

	// 数据插入 晒单
	function getuserpagebuyrafpostAddDataFun(data){
		if( data.success ){
	    	var dataLi = '';
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
				    +'<p class="s-info"><a rel="nofollow" target="_blank" href="javascript:;">'+postContent+'</a></p>'
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
				$("#div_PostList .good-list").html('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
			}else{
				$("#g-pagination").show();
				$("#div_PostList li, .null-data, .loading-2015").remove();
				$("#div_PostList .good-list").html(dataLi);
			}
	    }else{

	    }
	}

	//函数
	function getuserpagebuyrafpostAjaxFun(dataObj,Callback){
		if( !ajaxlack ) return; ajaxlack = false;
		$.ajax({
	        url: "/post/service/getuserpointsdetaillist",
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
	function getrecentvisitorsAjaxFun(){
		$.ajax({
	        url: "getrecentvisitors",
	        type: 'POST',
	        dataType: 'json',
	        data: {}
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
						+'<a class="pic" uweb="'+userWeb+'" type="showCard" href="javascript:;"><img src="'+userPhoto+'" width="50" height="50"></a>'
							+'<div class="info"><span class="class-icon01">'
								+'<a class="name" uweb="'+userWeb+'" type="showCard" href="javascript:;" rel="nofollow">'+userName+'</a><s></s></span><p>'+browserTime+'</p>'
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








































})