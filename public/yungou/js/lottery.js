$(function(){


	$("#divLottery .m-lottery-list").hover(function(){
		if( $(this).attr('type')!="isRaff" ) return;
		$(this).addClass("m-lottery-hover");
	},function(){
		$(this).removeClass("m-lottery-hover");
	});

	// 已经揭晓
	getlotterylistFirstDataFun(1,8,'');

	function getlotterylistFirstDataFun(page,limit,SortID){
		$("#divLottery").html('<div class="loading-2015"><em></em></div>');
		var dataObj = {
			page:page,
			limit:limit,
			SortID:SortID
		}
		// 数据加载 第一次
		getlotterylistAjaxFun(dataObj,function(data){
			var total = data.result.total;
			getlotterylistAddDataFun(data);
			// 分页
			PageCreate('#g-pagination',total,limit,1,function(pageNumber){
				if( total!=null ){ total = null; return; }
				dataObj.page = pageNumber;
				// 翻页后数据加载
				getlotterylistAjaxFun(dataObj,function(data){
					getlotterylistAddDataFun(data);
				});
		    });
		});
	}

	// 数据插入
	function getlotterylistAddDataFun(data){
		if( data.success ){
	    	var dataLi = '';
	    	$.each(data.result.datas, function(i,item){
	    		var codeID = item.codeID;//: "5688c368887c22cf6c8b4699"
				var codePeriod = item.codePeriod;//: "2"
				var codePrice = item.codePrice;//: "2.00"
				var codeType = item.codeType;//: 0
				var goodsName = item.goodsName;//: "包邮港荣奶香蒸蛋糕整箱1kg 蒸蛋糕鸡蛋糕 代餐糕点零食品 新日期"
				var goodsPic = item.goodsPic;//: "/upload/goods/1/1_161660a1e2tb1w8ix___0-item_pic.jpg"
				var postID = item.postID;//: 0
				var raffTime = item.raffTime;//: "2016-01-03 14:58:29.883"
				var seconds = item.seconds;//: 194508
				var userAddr = item.userAddr;//: "中国上海"
				var userBuyNum = item.userBuyNum;//: "1"
				var userName = item.userName;//: "56761****87"
				var userPhoto = item.userPhoto;//: "/upload/member/avatar/UserFace-160-0000.jpg"
				var userRNO = item.userRNO;//: "10000002"
				var userWeb = item.userWeb;//: "56761153887c22184e8b45b5"
				dataLi += '<div class="m-lottery-list" type="isRaff">'
				    +'<ul>'
				        +'<li class="f-lott-comm"><a href="/yungou/lottery/detail?id='+codeID+'" target="_blank"><img src="'+goodsPic+'"></a></li>'
				        +'<li class="f-lott-detailed">'
				            +'<div class="u-user-info">'
				                +'<p class="fl"><a href="/yungou/member/index?id='+userWeb+'" target="_blank"><img type="userPhoto" src="'+userPhoto+'"><s></s></a></p>'
				                +'<dl class="fl">'
				                    +'<dt><em>获得者：</em><span><a href="/yungou/member/index?id='+userWeb+'" target="_blank">'+userName+'</a></span></dt>'
				                    +'<dd class="z-lott-lz">来自：'+userAddr+'</dd>'
				                    +'<dd>幸运云购码：<strong class="orange">'+userRNO+'</strong></dd>'
				                    +'<dd>本云参与：<i class="orange">'+userBuyNum+'</i>人次</dd>'
				                +'</dl>'
				            +'</div>'
				            +'<div class="u-comm-info">'
				                +'<dl>'
				                    +'<dt><a href="javascript:;" target="_blank">'+goodsName+'</a></dt>'
				                    +'<dd>商品价值：￥'+codePrice+'</dd>'
				                    +'<dd>揭晓时间：'+raffTime+'</dd>'
				                    +'<dd class="z-lott-btn"><span><a href="/yungou/lottery/detail?id='+codeID+'" target="_blank" title="查看详情">查看详情</a></span></dd>'
				                +'</dl>'
				            +'</div>'
				        +'</li>'
				    +'</ul>'
				+'</div>';	    		
	    	});
			
			if( dataLi=='' ){
				$("#g-pagination").hide();
				$("#divLottery").html('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
			}else{
				$(".loading-2015").remove();
				$("#g-pagination").show();
				$("#divLottery").append(dataLi);
			}

			// 添加样式
			addClassFun();
	    }else{

	    }
	}

	// 添加样式函数
	function addClassFun(){
		$("#divLottery .m-lottery-list").removeClass('m-lottery-special m-lottery-bor-rb');
		$("#divLottery .m-lottery-list").each(function(index, item){
		    var $div = $(item);
		    if( (index+1) % 3 === 0 ){
		        if( $div.attr("type")=="isRaff" ){
		        	$div.addClass('m-lottery-special');
		        }else{
		        	$div.addClass('m-lottery-bor-rb');
		        }
		    }
		});
	}

	//函数
	function getlotterylistAjaxFun(dataObj,Callback){
		if( !ajaxlack ) return; ajaxlack = false;
		$.ajax({
	        url: "/goods/service/getlotterylist",
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



	var maxSeconds = '';
    getstartrafflealllistAjaxFun(1,100,maxSeconds);
	// 最新揭晓
	function getstartrafflealllistAjaxFun(page,limit,time){
        $.ajax({
            url: "/goods/service/getstartrafflealllist",
            type: 'POST',
            dataType: 'json',
            data: { page:page, limit:limit, time:time }
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
            	var dataLi = '';
            	maxSeconds = data.result.maxSeconds;
            	$.each(data.result.datas, function(i,item){
					var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
					var goodsPic = item.goodsPic;
					var goodsSName = item.goodsSName;
					var codeID = item.codeID;
					var codeQuantity = item.codeQuantity;
					var codeSales = item.codeSales;
					var codeType = item.codeType;
					var period = item.period;
					var price = item.price;
					var seconds = item.seconds;
					dataLi = '<div class="m-lottery-list m-lottery-anning" id="'+codeID+'">'
					    +'<ul>'
					        +'<li class="f-lott-comm">'
					            +'<a href="/yungou/product/detail?id='+codeID+'" target="_blank"><img src="'+goodsPic+'"></a>'
					        +'</li>'
					        +'<li class="f-lott-detailed">'
					            +'<dl>'
					                +'<dt><a href="/yungou/product/detail?id='+codeID+'" target="_blank">'+goodsSName+'</a></dt>'
					                +'<dd>商品价值：￥'+price+'</dd>'
					                +'<dd class="z-ymy">已满员</dd>'
					                +'<dd class="z-jx-time">'
					                	+'<p>揭晓倒计时</p>'
					                    +'<cite class="timecite"></cite>'
					                +'</dd>'
					            +'</dl>'
					        +'</li>'
					    +'</ul>'
					    +'<b class="transparent-png"></b>'
					+'</div>';    
				
					$("#ul_Lottery").append(dataLi);
					CountdownFun("#"+codeID,seconds,function(e,id){
						if( e==true ){
							getbarcodernoinfoAjaxFun(codeID);
						}else{
							$(id).find(".timecite").html('<span class="minute">'+e[0]+'</span><em>:</em><span class="second">'+e[1]+'</span><em>:</em><span><i class="millisecond">'+e[2]+'</i></span>');
						}
					});
				});
            }else{
                alert(data.error_msg);
            }
        })
        .fail(function() {
            alert('网络错误！')
        })
        .always(function() {

        });
    }

    // 倒计时完成后显示结果
    function getbarcodernoinfoAjaxFun(codeID){
        $.ajax({
            url: "/goods/service/getbarcodernoinfo",
            type: 'POST',
            dataType: 'json',
            data: { codeID:codeID }
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
            	var buyCount = data.result.buyCount; //"1"
            	var buyTime = data.result.buyTime; //"2016-01-01 12:31:54.693"
            	var codePeriod = data.result.codePeriod; //"1"
            	var codeRNO = data.result.codeRNO; //"10000001"
            	var codeRTime = data.result.codeRTime; //"2016-01-01 12:36:37.440"
            	var codeType = data.result.codeType; //0
            	var goodsName = data.result.goodsName; //"春夏韩版宽松中长款薄款开衫镂空针织衫外套大码开衫空调衫女披肩"
            	var goodsPic = data.result.goodsPic; //"/upload/goods/1/1_00606f377ftb2myox___2226277913.jpg"
            	var ipAddr = data.result.ipAddr; //"中国上海"
            	var price = data.result.price; //"1.00"
            	var userNC = data.result.userNC; //"15821****14"
            	var userName = data.result.userName; //"15821****14"
            	var userPhoto = data.result.userPhoto; //"/upload/member/avatar/UserFace-160-0000.jpg"
            	var userWeb = data.result.userWeb; //"56761153887c22184e8b45b5"

            	$("#"+codeID).html('<div class="m-lottery-list m-lottery-special" type="isRaff">'
				    +'<ul>'
				        +'<li class="f-lott-comm"><a href="javascript:;" target="_blank"><img src="'+goodsPic+'"></a></li>'
				        +'<li class="f-lott-detailed">'
				            +'<div class="u-user-info">'
				                +'<p class="fl"><a href="javascript:;" target="_blank"><img type="userPhoto" src="'+userPhoto+'"><s></s></a></p>'
				                +'<dl class="fl">'
				                    +'<dt><em>获得者：</em><span><a href="/yungou/member/index?id='+userWeb+'" target="_blank">'+userName+'</a></span></dt>'
				                    +'<dd class="z-lott-lz">来自：'+ipAddr+'</dd>'
				                    +'<dd>幸运云购码：<strong class="orange">'+codeRNO+'</strong></dd>'
				                    +'<dd>本云参与：<i class="orange">'+buyCount+'</i>人次</dd>'
				                +'</dl>'
				            +'</div>'
				            +'<div class="u-comm-info">'
				                +'<dl>'
				                    +'<dt><a href="javascript:;" target="_blank">'+goodsName+'</a></dt>'
				                    +'<dd>商品价值：￥'+price+'</dd>'
				                    +'<dd>揭晓时间：'+codeRTime+'</dd>'
				                    +'<dd class="z-lott-btn"><span><a href="/yungou/member/index?id='+userWeb+'" target="_blank" title="查看详情">查看详情</a></span></dd>'
				                +'</dl>'
				            +'</div>'
				        +'</li>'
				    +'</ul>'
				+'</div>');
            }else{
                alert(data.error_msg);
            }
        })
        .fail(function() {

        })
        .always(function() {

        });
    }

	setInterval(function(){
		getstartrafflealllistAjaxFun(1,5,maxSeconds);
	},20000);










})