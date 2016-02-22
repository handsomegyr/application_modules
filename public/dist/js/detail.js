webpackJsonp([1],[
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(7); //获取URL参数插件
	__webpack_require__(8); //分页插件
	__webpack_require__(9); //图片预加载
	__webpack_require__(10); //图片放大效果
	__webpack_require__(11); //图片放大效果
	__webpack_require__(3); //整站公共部分
	__webpack_require__(12);
	__webpack_require__(5);
	__webpack_require__(13);
	$(function(){
		var codeID = $.query.get('id');

		//图片预加载
		$("img.lazy").lazyload();
		
		// 列表页面展开搜索
		$("#btnExpend").on(click,function(){
			var $ulheight = $("#ulBrandList");
			if( $ulheight.height()<=84 ){
				$ulheight.height('auto');
			}else{
				$ulheight.height(84);
			}
		});

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

		// 云购标签切换
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


			// 最新云购记录
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
				var val = eval($this.siblings("input").val());
				var goodsid = $this.siblings("input").attr("goodsid");
				var remainpersontime = eval($("#remainPersonTime").attr('remainpersontime'));
				console.log(val>remainpersontime,val,remainpersontime)
				if( val>=remainpersontime ){
					$this.siblings("input").val(remainpersontime);
					return;
				}
				$this.siblings("input").val(eval(val)+1);
			});

			// 减少商品数量
			$(".mius").on("click",function(){
				var $this = $(this);
				var val = eval($this.siblings("input").val());
				var goodsid = $this.siblings("input").attr("goodsid");
				if( val<=1 ) return;
				$this.siblings("input").val(val-1);
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

		if( pageTrue=='productdetail' ){
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
	                    +'<span class="people">'+buyNum+'<a name="showCode" href="javascript:;">查看云购码</a></span>'
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
					var gradeName = item.gradeName;//: "云购小将"
					var postAllPic = item.postAllPic.split(',');//: "/upload/post/5688bd8d887c226e6a8b5725,/upload/post/5688bd69887c22184e8b4600,/upload/post/5688bd58887c2210688b4619"
					var postContent = item.postContent.substr(0,180)+"...";//: "感言3333云购感言3333"
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
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(jQuery) {//**************************************************************
	// jQZoom allows you to realize a small magnifier window,close
	// to the image or images on your web page easily.
	//
	// jqZoom version 2.1
	// Author Doc. Ing. Renzi Marco(www.mind-projects.it)
	// First Release on Dec 05 2007
	// i'm searching for a job,pick me up!!!
	// mail: renzi.mrc@gmail.com
	//**************************************************************

	(function($){

			$.fn.jqueryzoom = function(options){
			var settings = {
					xzoom: 200,//zoomed width default width
					yzoom: 200,//zoomed div default width
					offset: 10,	//zoomed div default offset
					position: "right",//zoomed div default position,offset position is to the right of the image
					lens:1, //zooming lens over the image,by default is 1;
					preload: 1
				};

				if(options) {
					$.extend(settings, options);
				}

			    var noalt='';
			    $(this).hover(function(){

			    var imageLeft = this.offsetLeft;
			    var imageRight = this.offsetRight;
			    var imageTop =  $(this).get(0).offsetTop;
			    var imageWidth = $(this).children('img').get(0).offsetWidth;
			    var imageHeight = $(this).children('img').get(0).offsetHeight;


	            noalt= $(this).children("img").attr("alt");

			    var bigimage = $(this).children("img").attr("jqimg");

	            $(this).children("img").attr("alt",'');

			    if($("div.zoomdiv").get().length == 0){

			    $(this).after("<div class='zoomdiv'><img class='bigimg' src='"+bigimage+"'/></div>");


			    $(this).append("<div class='jqZoomPup'>&nbsp;</div>");

			    }


			    if(settings.position == "right"){

	            if(imageLeft + imageWidth + settings.offset + settings.xzoom > screen.width){

	            leftpos = imageLeft  - settings.offset - settings.xzoom;

	            }else{

			    leftpos = imageLeft + imageWidth + settings.offset;
	            }
			    }else{
			    leftpos = imageLeft - settings.xzoom - settings.offset;
			    if(leftpos < 0){

	            leftpos = imageLeft + imageWidth  + settings.offset;

			    }

			    }

			    $("div.zoomdiv").css({ top: imageTop,left: leftpos });

			    $("div.zoomdiv").width(settings.xzoom);

			    $("div.zoomdiv").height(settings.yzoom);

	            $("div.zoomdiv").show();

	            if(!settings.lens){
	              $(this).css('cursor','crosshair');
				}




					   $(document.body).mousemove(function(e){



	                   mouse = new MouseEvent(e);

	                   /*$("div.jqZoomPup").hide();*/


					    var bigwidth = $(".bigimg").get(0).offsetWidth;

					    var bigheight = $(".bigimg").get(0).offsetHeight;

					    var scaley ='x';

					    var scalex= 'y';


					    if(isNaN(scalex)|isNaN(scaley)){

					    var scalex = (bigwidth/imageWidth);

					    var scaley = (bigheight/imageHeight);




					    $("div.jqZoomPup").width((settings.xzoom)/scalex );

			    		$("div.jqZoomPup").height((settings.yzoom)/scaley);

	                    if(settings.lens){
	                    $("div.jqZoomPup").css('visibility','visible');
						}

					   }



	                    xpos = mouse.x - $("div.jqZoomPup").width()/2 - imageLeft;

	                    ypos = mouse.y - $("div.jqZoomPup").height()/2 - imageTop ;

	                    if(settings.lens){

	                    xpos = (mouse.x - $("div.jqZoomPup").width()/2 < imageLeft ) ? 0 : (mouse.x + $("div.jqZoomPup").width()/2 > imageWidth + imageLeft ) ?  (imageWidth -$("div.jqZoomPup").width() -2)  : xpos;

						ypos = (mouse.y - $("div.jqZoomPup").height()/2 < imageTop ) ? 0 : (mouse.y + $("div.jqZoomPup").height()/2  > imageHeight + imageTop ) ?  (imageHeight - $("div.jqZoomPup").height() -2 ) : ypos;

	                    }


	                    if(settings.lens){

	                    $("div.jqZoomPup").css({ top: ypos,left: xpos });

	                    }



						scrolly = ypos;

						$("div.zoomdiv").get(0).scrollTop = scrolly * scaley;

						scrollx = xpos;

						$("div.zoomdiv").get(0).scrollLeft = (scrollx) * scalex ;


					    });
			    },function(){

	               $(this).children("img").attr("alt",noalt);
			       $(document.body).unbind("mousemove");
			       if(settings.lens){
			       $("div.jqZoomPup").remove();
			       }
			       $("div.zoomdiv").remove();

			    });

	        count = 0;

			if(settings.preload){

			$('body').append("<div style='display:none;' class='jqPreload"+count+"'>sdsdssdsd</div>");

			$(this).each(function(){

	        var imagetopreload= $(this).children("img").attr("jqimg");

	        var content = jQuery('div.jqPreload'+count+'').html();

	        jQuery('div.jqPreload'+count+'').html(content+'<img src=\"'+imagetopreload+'\">');

			});

			}

			}

	})(jQuery);

	function MouseEvent(e) {
	this.x = e.pageX
	this.y = e.pageY


	}



	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {
	//=====================全局函数========================
	//Tab控制函数
	function tabs(tabId, tabNum){
		//设置点击后的切换样式
		$(tabId + " .tab li").removeClass("curr");
		$(tabId + " .tab li").eq(tabNum).addClass("curr");
		//根据参数决定显示内容
		$(tabId + " .tabcon").hide();
		$(tabId + " .tabcon").eq(tabNum).show();
	}
	//=====================全局函数========================

	//==================图片详细页函数=====================
	//鼠标经过预览图片函数
	window.preview = function(img){
		$("#preview .jqzoom img").attr("src",$(img).attr("bimg"));
		$("#preview .jqzoom img").attr("jqimg",$(img).attr("bimg"));
	}

	//图片放大镜效果
	$(function(){
		$(".jqzoom").jqueryzoom({xzoom:380,yzoom:410});
	});

	//图片预览小图移动效果,页面加载时触发
	$(function(){
		var tempLength = 0; //临时变量,当前移动的长度
		var viewNum = 5; //设置每次显示图片的个数量
		var moveNum = 2; //每次移动的数量
		var moveTime = 300; //移动速度,毫秒
		var scrollDiv = $(".spec-scroll .items ul"); //进行移动动画的容器
		var scrollItems = $(".spec-scroll .items ul li"); //移动容器里的集合
		var moveLength = scrollItems.eq(0).width() * moveNum; //计算每次移动的长度
		var countLength = (scrollItems.length - viewNum) * scrollItems.eq(0).width(); //计算总长度,总个数*单个长度
		  
		//下一张
		$(".spec-scroll .next").bind("click",function(){
			if(tempLength < countLength){
				if((countLength - tempLength) > moveLength){
					scrollDiv.animate({left:"-=" + moveLength + "px"}, moveTime);
					tempLength += moveLength;
				}else{
					scrollDiv.animate({left:"-=" + (countLength - tempLength) + "px"}, moveTime);
					tempLength += (countLength - tempLength);
				}
			}
		});
		//上一张
		$(".spec-scroll .prev").bind("click",function(){
			if(tempLength > 0){
				if(tempLength > moveLength){
					scrollDiv.animate({left: "+=" + moveLength + "px"}, moveTime);
					tempLength -= moveLength;
				}else{
					scrollDiv.animate({left: "+=" + tempLength + "px"}, moveTime);
					tempLength = 0;
				}
			}
		});
	});
	//==================图片详细页函数=====================
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
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