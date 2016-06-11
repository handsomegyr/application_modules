$(function(){
	var codeID = $.query.get('id');

	//详细页查看更多弹层
	$("#li_more").on(click, function(){
		$(".ng-show-past").animate({marginTop:"0"});
	});

	// 关闭更多期弹层
	$("#a_close").on(click, function(){
		$(".ng-show-past").animate({marginTop:"-524px"});
	});

	// 直达多少期
	$("#btnGo").on(click, function(){
		var len = $(".ng-pt-inner li").length;
		var txtPeriod = eval(len-$("#txtPeriod").val());
		window.location.href = $(".ng-pt-inner li:eq("+txtPeriod+") a").attr("href");
	});

	// 羡慕
    $("#p_xianmu, .p_xianmu").on("click",function(){
    	var $this = $(this);
    	if( !ajaxlack||$this.hasClass("xianmu-past") ) return; ajaxlack= false;
    	$.ajax({
            url: "/post/service/insertposthits",
            type: 'POST',
            dataType: 'json',
            data: { postid:( postID=="" ? $this.attr('postid') : postID ) }
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
            	$this.find("em").html(eval($this.find("em").text())+1);
            	$this.addClass("xianmu-past");
            }else{

            }
        })
        .fail(function() {

        })
        .always(function() {
        	ajaxlack = true;
        });
    });

	// 集资购标签切换
	$("#div_buynav a").on("click", function(){
		var index = $(this).index();
		$(this).addClass('current').siblings().removeClass('current');
		if( index==0 ){
			$("#div_buynav .b-line").animate({"left":"0px"});
			$("#div_goodsrecord").show();
			$("#div_myrecord").hide();
		}else if( index==1 ){
			$("#div_buynav .b-line").animate({"left":"100px"});
			$("#div_myrecord").show();
			$("#div_goodsrecord").hide();
		}
	});


	if( pageTrue=='productindex' || pageTrue=='productdetail' ){
		// 标签切换
		$("#div_menu li").on("click", function(){
			var index = $(this).index();
			$(this).addClass('current').siblings().removeClass('current');
			$(".div-data-box-a, .div-data-box-b, .div-data-box-c").hide();
			if( index==0 ){
				$(".div-data-box-a").show();
			}else if( index==1 ){
				$(".div-data-box-b").show();
				getuserbuylistbycodeDataFun(1,10,codeID); //参与记录
			}else if( index==2 ){
				$(".div-data-box-c").show();
				getgoodspostlistDataFun(1,10,codeID);  //晒单记录
			}
		});


		// 最新集资购记录
		var UserBuyNewListidname = "#UserBuyNewList";
		var $UserBuyNewListid = $(UserBuyNewListidname);
		var maxId = '';
		var dataObj = {
			page:1,
			limit:11,
			codeID:codeID,
			id:maxId
		}
		getuserbuylistbycodeAjaxFun(dataObj,function(data){
			dataObj.limit = 11;
			ListaddDataFun(data);
		});
		setInterval(function(){
			dataObj.limit = 1;
			getuserbuylistbycodeAjaxFun(dataObj,function(data){
				ListaddDataFun(data);
			});
		},10000);

		function ListaddDataFun(data){
			var dataLi = '';
        	maxId = data.result.maxId;
        	$.each(data.result.datas, function(i,item){
				var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
				var buyDevice = item.buyDevice; //0
				var buyID = item.buyID; //"56761153887c22184e8b45b5"
				var buyIP = item.buyIP; //"222.70.142.142"
				var buyIPAddr = item.buyIPAddr; //"中国上海"
				var buyNum = item.buyNum; //1
				var buyTime = item.buyTime; //"2016-01-01 14:20:38.499"
				var userName = item.userName; //"15821****14"
				var userWeb = item.userWeb; //"56761153887c22184e8b45b5"
				dataLi += '<li><a rel="nofollow" href="/yungou/member/index?id='+userWeb+'" target="_blank" class="buy-name"><i class="head-s-pic"><img src="'+userPhoto+'" width="22" height="22"></i>'+userName+'</a><span class="buy-num">'+buyNum+'</span>人次</li>';			    
			});

        	if( dataLi==""&&!maxId ){
        		$UserBuyNewListid.html('<div class="null-tips-wrapper clearfix"><div class="gth-icon transparent-png"></div><div class="null-text"><p>还没有人参与？</p><p>梦想与您只有1元的距离！</p></div></div>');
        		return;
        	}

			if( dataObj.limit>1 ){
				$UserBuyNewListid.html(dataLi);
			}else if( dataLi!="" ){
				$("#div_goodsrecord .select-all").show();
				$UserBuyNewListid.prepend(dataLi);
				$UserBuyNewListid.css({marginTop:"-33px"}).animate({marginTop:"0"},function(){
					if( $UserBuyNewListid.find("li").length>11 ){
						$(UserBuyNewListidname+" li:last").remove();
					}
				});
			}
		}

		// 查看全部
		$("#div_goodsrecord .select-all").on("click",function(){
			$("#ul_menu li:eq(1)").addClass('current').siblings().removeClass('current');
			$("#div_desc, #div_allrecord, #div_postlist").hide();
			$("#div_allrecord").show();
			$(document).scrollTop(750);
			getuserbuylistbycodeDataFun(1,10,codeID);
		});

		// 增加商品数量
		$(".add").on("click",function(){ // cur 不可选择样式
			var $this = $(this);
			var val = $this.siblings("input").val();
			var goodsid = $this.siblings("input").attr("goodsid");
			cartNumFun(goodsid,1,function(){
				$this.siblings("input").val(eval(val)+1);
			});
		});

		// 减少商品数量
		$(".mius").on("click",function(){
			var $this = $(this);
			var val = $this.siblings("input").val();
			var goodsid = $this.siblings("input").attr("goodsid");
			if( val<=1 ) return;
			cartNumFun(goodsid,1,function(){
				$this.siblings("input").val(eval(val)-1);
			});
		});

		// 数量选择
		var purchasePersonTime = $("#purchasePersonTime").attr('purchasePersonTime'); //已参与
		var totalPersonTime = $("#totalPersonTime").attr('totalPersonTime'); //总需人次
		var remainPersonTime = $("#remainPersonTime").attr('remainPersonTime'); //剩余
		var numPerson = parseInt(totalPersonTime-remainPersonTime); //还剩余
		var $checkNum =  $(".check-num");
		if( numPerson>=200 ){
			$checkNum.append('<li>200</li>')
		}else if( numPerson>=100 ){
			$checkNum.append('<li>100</li>')
		}else if( numPerson>=50 ){
			$checkNum.append('<li>50</li>')
		}else if( numPerson>=10 ){
			$checkNum.append('<li>10</li>')
		}

		// 价格选择
		var t;
		$(".ng-goods-detail .check-num li").on("click", function(){
			var val = $(this).text();
			$("#divNumber input").val(val);
			$(this).addClass("current").siblings().removeClass("current");
			$(".mine-prob").show().html('<span class="txt">获得机率'+(parseInt(val/totalPersonTime*100).toFixed(2))+'%</span>');//<i></i>
			t = setTimeout(function(){
				$(".mine-prob").hide();
				clearTimeout(t);
			},2000);
		});
	}

	if( pageTrue=='product-b' ){
		CountdownFun("#ul_lotterytime",$("#ul_lotterytime").attr('seconds'),function(e,id){
			if( e==true ){
				$(id).hide();
				$("#div_eveling").show();
				window.location.href = '/yungou/lottery/detail?id='+$.query.get('id');
			}else{
				$(id).html('<li class="cur">'+e[0]+'</li><li>:</li><li class="cur">'+e[1]+'</li><li>:</li><li class="cur">'+e[2]+'</li>');
			}
		});
	}


	// **********参与记录*********/
	function getuserbuylistbycodeDataFun(page,limit,codeID){
		$("#div_allrecord .record").html('<div class="ng-b-wait"></div>');
		var dataObj = {
			page:page,
			limit:limit,
			codeID:codeID
		}
		// 数据加载 第一次
		getuserbuylistbycodeAjaxFun(dataObj,function(data){
			var total = data.result.total;
			getuserbuylistbycodeAddDataFun(data);
			// 分页
			PageCreate('#g-pagination-a',total,limit,1,function(pageNumber){
				if( total!=null ){ total = null; return; }
				dataObj.page = pageNumber;
				// 翻页后数据加载
				getuserbuylistbycodeAjaxFun(dataObj,function(data){
					getuserbuylistbycodeAddDataFun(data);
				});
		    });
		});
	}

	// 数据插入
	function getuserbuylistbycodeAddDataFun(data){
		if( data.success ){
	    	var dataLi = '';
	    	$.each(data.result.datas, function(i,item){
	    		var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
				var buyDevice = item.buyDevice; //0
				var buyID = item.buyID; //"56761153887c22184e8b45b5"
				var buyIP = item.buyIP; //"222.70.142.142"
				var buyIPAddr = item.buyIPAddr; //"中国上海"
				var buyNum = item.buyNum; //1
				var buyTime = item.buyTime; //"2016-01-01 14:20:38.499"
				var userName = item.userName; //"15821****14"
				var userWeb = item.userWeb; //"56761153887c22184e8b45b5"
	    		dataLi += '<li>'
                    +'<span class="time">'+buyTime+'</span>'
                    +'<span class="name">'
                        +'<span class="w">'
                            +'<a href="/yungou/member/index?id='+userWeb+'" target="_blank">'
                                +'<i class="head-s-img">'
                                    +'<img src="'+userPhoto+'" width="22" height="22">'
                                +'</i>'+userName+'</a>'
                        +'</span>'
                    +'</span>'
                    +'<span class="people">'+buyNum+'<a name="showCode" href="javascript:;">查看集资购码</a></span>'
                    +'<span class="ip">'+buyIPAddr+'&nbsp;&nbsp;'+buyIP+'</span>'
                    +'<span class="form"><a href="javascript:;" target="_blank">PC端<i class="f-icon pc"></i></a></span>'
                +'</li>';
	    	});
			
			if( dataLi=='' ){
				$("#g-pagination-a").hide();
				$("#div_allrecord .record").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>还没有人参与？梦想与您只有1元的距离！</span></div>');
			}else{
				$("#g-pagination-a").show();
				$("#div_allrecord .record").html('<ul class="record-list">'
	                +'<li class="record-menu">'
	                    +'<span class="time">时间</span><span class="name">会员</span><span class="people">参与人次</span><span class="ip">IP</span><span class="form">来源</span>'
	                +'</li>'+dataLi
	            +'</ul>');
			}
	    }else{

	    }
	}

	//函数
	function getuserbuylistbycodeAjaxFun(dataObj,Callback){
		if( !ajaxlack ) return; ajaxlack = false;
		$.ajax({
	        url: "/order/service/getuserbuylistbycode",
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


	// **********晒单*********/
	function getgoodspostlistDataFun(page,limit,goodsID){
		$(".ng-share-data").html('<div class="ng-b-wait"></div>');
		var dataObj = {
			page:page,
			limit:limit,
			goodsID:goodsID
		}
		// 数据加载 第一次
		getgoodspostlistAjaxFun(dataObj,function(data){
			var total = data.result.total;
			getgoodspostlistAddDataFun(data);
			// 分页
			PageCreate('#g-pagination-a',total,limit,1,function(pageNumber){
				if( total!=null ){ total = null; return; }
				dataObj.page = pageNumber;
				// 翻页后数据加载
				getgoodspostlistAjaxFun(dataObj,function(data){
					getgoodspostlistAddDataFun(data);
				});
		    });
		});
	}

	// 数据插入
	function getgoodspostlistAddDataFun(data){
		if( data.success ){
	    	var dataLi = '';
	    	$("#div_postlist .total .gray6").html(data.result.Count);
	    	$("#div_postlist .total .orange").html(data.result.CountEx);
	    	$.each(data.result.datas, function(i,item){
	    		var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
				var codePeriod = item.codePeriod;//: "2"
				var grade = item.grade;//: "01"
				var gradeName = item.gradeName;//: "集资购小将"
				var postAllPic = item.postAllPic.split(',');//: "/upload/post/5688bd8d887c226e6a8b5725,/upload/post/5688bd69887c22184e8b4600,/upload/post/5688bd58887c2210688b4619"
				var postContent = item.postContent.substr(0,180)+"...";//: "感言3333集资购感言3333"
				var postHits = item.postHits;//: "2"
				var postID = item.postID;//: "5688af90887c22054a8b45ea"
				var postReplyCount = item.postReplyCount;//: "0"
				var postTimeEx = item.postTimeEx;//: "2016-01-03 14:19:59"
				var postTitle = item.postTitle;//: "我的主体啊"
				var userName = item.userName;//: "13564****96"
				var userWeb = item.userWeb;//: "56757a39887c22034a8b4596"
				var imgLi = ''
				for( var i=0; i<postAllPic.length; i++ ) {
					imgLi += '<li><span><img width="71px" height="71px" src="'+postAllPic[i]+'"></span><div class="pic-hover transparent-png" style="display:none;"></div></li>';
				};

	    		dataLi += '<div class="ng-share-detail clearfix">'
	                +'<div class="ng-share-pic">'
	                    +'<a href="/yungou/member/index?id='+userWeb+'" type="showCard" uweb="'+userWeb+'" target="_blank">'
	                        +'<img src="'+userPhoto+'" alt="" width="80" height="80">'
	                    +'</a>'
	                +'</div>'
	                +'<div class="ng-share-con">'
	                    +'<div class="name-line">'
	                        +'<a href="/yungou/post/detail?id='+postID+'" target="_blank" rel="nofollow" class="u-name">'+userName+'</a>'
	                        +'<span class="u-time">'+postTimeEx+'</span>'
	                    +'</div>'
	                    +'<div class="u-data">'
	                        +'<span class="u-num">第<span>'+codePeriod+'</span>云晒单</span>'
	                        +'<a href="/yungou/post/detail?id='+postID+'" class="u-show" target="_blank">'+postTitle+'</a>'
	                    +'</div>'
	                    +'<div class="share-info">'
	                        +'<p>'
	                            +'<a href="/yungou/post/detail?id='+postID+'" target="_blank">'+postContent+'</a>'
	                        +'</p>'
	                    +'</div>'
	                    +'<div class="pic-list-wrap">'
	                        +'<ul class="pic-list clearfix">'+imgLi+'</ul>'
	                        +'<div class="talk-wrap">'
	                            +'<a href="javascript:;" num="0" postid="'+postID+'" class="xianmu '+(grade=="01" ? "xianmu-past" : "")+'">'
	                                +'<i class="transparent-png"></i>羡慕(<em>'+postHits+'</em>)<img class="transparent-png" src="/Images/xin.png">'
	                            +'</a>'
	                            +'<a rel="nofollow" href="/yungou/post/detail?id='+postID+'" target="_blank" class="pinglun">'
	                                +'<i class="transparent-png"></i>评论(<em>'+postReplyCount+'</em>)'
	                            +'</a>'
	                        +'</div>'
	                    +'</div>'
	                +'</div>'
	                +'<div class="clear"></div>'
	            +'</div>';
	    	});
			
			if( dataLi=='' ){
				$("#g-pagination-b, #div_postlist .total").hide();
				$("#div_postlist .ng-share-data").html('<div class="null-retips-wrapper"><div class="gth-icon transparent-png"></div><span>暂无晒单记录哦！</span></div>');
			}else{
				$("#g-pagination-b, #div_postlist .total").show();
				$(".null-retips-wrapper, .ng-b-wait").remove();
				$("#div_postlist .ng-share-data").html(dataLi);
			}
	    }else{

	    }
	}

	//函数
	function getgoodspostlistAjaxFun(dataObj,Callback){
		if( !ajaxlack ) return; ajaxlack = false;
		$.ajax({
	        url: "/post/service/getgoodspostlist",
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

	if( pageTrue=='lotteryDetail' ){

		// 展开
		$("#div_showmore").on('click',function(){
			if( $(this).hasClass('up') ){
				$("#div_nginner").animate({"height":"332px"});
				$(this).removeClass('up').html('<span>展开全部100条数据<b><s></s></b></span>');
			}else{
				$("#div_nginner").animate({"height":"4004px"});
				$(this).addClass('up').html('<span>收起<b><s></s></b></span>');
			}
		});

		// 标签切换
		$("#ul_menu li").on("click", function(){
			var index = $(this).index();
			$(this).addClass('current').siblings().removeClass('current');
			$(".div-data-box-a, .div-data-box-b, .div-data-box-c").hide();
			if( index==0 ){
				$("#midNavLine").animate({"left":"384px"});
				$(".div-data-box-a").show();
			}else if( index==1 ){
				$("#midNavLine").animate({"left":"558px"});
				$(".div-data-box-b").show();
				getuserbuylistbycodeDataFun(1,10,codeID); //参与记录
			}else if( index==2 ){
				$("#midNavLine").animate({"left":"732px"});
				$(".div-data-box-c").show();
				getgoodspostlistDataFun(1,10,codeID);  //晒单记录
			}
		});

		getlotteryrecordsAjaxFun();
		function getlotteryrecordsAjaxFun(){
			$.ajax({
		        url: "/goods/service/getlotteryrecords",
		        type: 'POST',
		        dataType: 'json',
		        data: { codeId:$.query.get('id') }
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	var recordEndA = data.result.recordEnd2;
		        	var recordEndB = data.result.recordEnd3;
		        	var dataLiA = '';
		        	var dataLiB = '';
		        	$.each(recordEndA, function(i,item){
						var buyID = item.buyID; //: "56861ab5887c22024a8b468b"
						var buyNum = item.buyNum;//: 1
						var buyTime = item.buyTime;//: "2016-01-01 14:20:38.499"
						var goodsName = item.goodsName;//: "【周黑鸭旗舰店_锁鲜装】盒装鸭翅190g*2 武汉特产官方食品零食"
						var goodsPeriod = item.goodsPeriod;//: "1"
						var timeCodeVal = item.timeCodeVal;//: "142038499"
						var userName = item.userName;//: "15821****14"
						var userWeb = item.userWeb;//: "56761153887c22184e8b45b5"
						dataLiA += '<li>'
                            +'<span class="time">'+buyTime+'</span>'
                            +'<span class="code">'+timeCodeVal+'</span>'
                            +'<span class="name"><a href="/yungou/member/index?id='+userWeb+'" target="_blank">'+userName+'</a></span>'
                            +'<span class="num">'+buyNum+'人次</span>'
                            +'<span class="pro"><a href="javascript:;" target="_blank">'+goodsName+'</a>'
                            +'</span>'
                        +'</li>';			    
					});
					$.each(recordEndB, function(i,item){
						var buyID = item.buyID; //: "56861ab5887c22024a8b468b"
						var buyNum = item.buyNum;//: 1
						var buyTime = item.buyTime;//: "2016-01-01 14:20:38.499"
						var goodsName = item.goodsName;//: "【周黑鸭旗舰店_锁鲜装】盒装鸭翅190g*2 武汉特产官方食品零食"
						var goodsPeriod = item.goodsPeriod;//: "1"
						var timeCodeVal = item.timeCodeVal;//: "142038499"
						var userName = item.userName;//: "15821****14"
						var userWeb = item.userWeb;//: "56761153887c22184e8b45b5"
						dataLiB += '<li>'
                            +'<span class="time">'+buyTime+'</span>'
                            +'<span class="code"></span>'
                            +'<span class="name"><a href="/yungou/member/index?id='+userWeb+'" target="_blank">'+userName+'</a></span>'
                            +'<span class="num">'+buyNum+'人次</span>'
                            +'<span class="pro"><a href="javascript:;" target="_blank">'+goodsName+'</a>'
                            +'</span>'
                        +'</li>';			    
					});

					$("#div_nginner ul").html(dataLiA);
					$("ul.ng-tc-ul").html(dataLiB);
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



});