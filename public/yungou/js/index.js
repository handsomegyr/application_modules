$(function(){
	jQuery("#div_slide").slide({ mainCell:".bd ul", titCell:".hd ul",autoPage:true, effect:"left",prevCell:".prev",nextCell:".next", delayTime:1000, autoPlay:true });







	/*********************************************************************首页*****************************************************/

	// 正在云购
	var UserBuyNewListidname = "#UserBuyNewList";
	var $UserBuyNewListid = $(UserBuyNewListidname);
	var maxId = '';
	// 正在云购函数
	getuserbuynewlistAjaxFun(1,10);
	function getuserbuynewlistAjaxFun(page,limit){
        $.ajax({
            url: "/order/service/getuserbuynewlist",
            type: 'POST',
            dataType: 'json',
            data: { page:page, limit:limit, id:maxId }
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
            	var dataLi = '';
            	maxId = data.result.maxId;
            	$.each(data.result.datas, function(i,item){
					var userPhoto = item.userPhoto=='' ? '/Images/UserFace-160-0000.jpg':item.userPhoto;
					var buyID = item.buyID;
					var goodsID = item.goodsID;
					var goodsName = item.goodsName;
					var goodsPic = item.goodsPic;
					var userName = item.userName;
					var userPhoto = item.userPhoto;
					var userWeb = item.userWeb;
					var buyID = item.buyID;
					dataLi += '<li><span class="fl">'
				        +'<a href="/yungou/member/index?id='+userWeb+'" target="_blank">'
				            +'<img width="40" height="40" src="'+userPhoto+'">'
				            +'<i class="transparent-png"></i>'
				        +'</a>'
				    +'</span>'
				    +'<p>'
				        +'<a target="_blank" href="/yungou/member/index?id='+userWeb+'" class="blue">'+userName+'</a><br>'
				        +'<a target="_blank" href="/yungou/product/index?id='+goodsID+'" class="u-ongoing">'+goodsName+'</a>'
				    +'</p>'
				+'</li>';			    
				});
				if( limit>1 ){
					$UserBuyNewListid.html(dataLi);
				}else if( dataLi!="" ){
					$UserBuyNewListid.prepend(dataLi);
					$UserBuyNewListid.css({marginTop:"-89px"}).animate({marginTop:"0"},function(){
						if( $UserBuyNewListid.find("li").length>9 ){
							$(UserBuyNewListidname+" li:last").remove();
						}
					});
				}
            }else{
                alert(data.error_msg);
            }
        })
        .fail(function() {
            // alert('网络错误！')
        })
        .always(function() {

        });
    }
	

    var maxSeconds = '';
    getstartrafflealllistAjaxFun(1,5,maxSeconds);
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
					dataLi = '<li id="'+codeID+'" class="current">'
			            +'<dl class="m-in-progress">'
			                +'<dt>'
			                    +'<a href="/yungou/product/index?id='+codeID+'" target="_blank">'
			                        +'<img src="'+goodsPic+'">'
			                    +'</a>'
			                +'</dt>'
			                +'<dd class="u-name">'
			                    +'<a href="/yungou/product/index?id='+codeID+'">'+goodsSName+'</a>'
			                +'</dd>'
			                +'<dd class="gray">价值：￥'+price+'</dd>'
			                +'<dd class="u-time">'
			                    +'<em>揭晓倒计时</em><span class="dd_time"><b>00</b>:<b>00</b>:<b>00</b></span>'
			                +'</dd>'
			            +'</dl>'
			            +'<s class="transparent-png"></s>'
			        +'</li>';    
				
					$("#ul_Lottery").append(dataLi);
					CountdownFun("#"+codeID,seconds,function(e,id){
						if( e==true ){
							getbarcodernoinfoAjaxFun(codeID);
						}else{
							$(id).find(".dd_time").html('<b>'+e[0]+'</b>:<b>'+e[1]+'</b>:<b>'+e[2]+'</b>');
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

            	$("#"+codeID).html('<li><dl><dt><a href="javascript:;" target="_blank"><img src="'+goodsPic+'"></a></dt>'
				+'<dd class="f-gx"><div class="f-gx-user"><span>恭喜</span><span><a href="/yungou/member/index?id='+userWeb+'" target="_blank" class="blue">'+userName+'</a></span><span>获得</span></div></dd>'
				+'<dd class="u-name"><a href="/yungou/member/index?id='+userWeb+'" target="_blank">'+goodsName+'</a></dd><dd class="gray">'+ipAddr+'</dd></dl><cite></cite></li>');
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
		getuserbuynewlistAjaxFun(1,1);
		getstartrafflealllistAjaxFun(1,5,maxSeconds);
	},20000);


	// 晒单分享
	getrecpostlistAjaxFun();
	function getrecpostlistAjaxFun(){
        $.ajax({
            url: "/post/service/getrecpostlist",
            type: 'POST',
            dataType: 'json',
            data: { }
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
            	$.each(data.result.datas, function(i,item){
					var postContent = item.postContent; //: "嗯呵呵O(∩_∩)O~，货真价实的读卡器，包装很好~~~物流还不错..."
					var postID = item.postID; //: "5688926f887c22054a8b45e3"
					var postImg = item.postImg.split(',')[0]; //: "/upload/post/5688bd6a887c22014a8b4692,/upload/post/5688bd68887c22024a8b46a0,/upload/post/5688bd61887c22054a8b45eb"
					var postTime = item.postTime; //: "2016-01-03 14:19:27"
					var postTitle = item.postTitle; //: "终于中奖啦123"
					var userName = item.userName; //: "15821****14"
					var userPhoto = item.userPhoto; //: "/upload/member/avatar/UserFace-160-0000.jpg"
					var userWeb = item.userWeb; //: "56761153887c22184e8b45b5"

					if( i==0 ){
						$("#divPostRec dl").html('<dl><dt><a href="/yungou/post/detail?id='+postID+'" target="_blank"><img src="'+postImg+'" style="display: block;"></a></dt>'
							+'<dd class="u-user"><p class="u-head"><a href="/yungou/member/index?id='+userWeb+'" target="_blank"><img src="'+userPhoto+'" width="40" height="40" style="display: block;"><i class="transparent-png"></i></a></p>'
							+'<p class="u-info"><span><a href="javascript:;" target="_blank">'+userName+'</a><em>'+postTime+'</em></span><cite><a href="/yungou/post/detail?id='+postID+'" target="_blank">'+postTitle+'</a></cite></p>'
							+'</dd><dd class="m-summary"><cite><a href="/yungou/post/detail?id='+postID+'" target="_blank">'+postContent+'</a></cite><b><s></s></b></dd></dl>');
					}else{
						$("#ul_PostList").append('<li><a href="/yungou/post/detail?id='+postID+'" target="_blank"><cite><img src="'+postImg+'" style="display: block;width:100%;"></cite><p>'+postTitle+'</p></a></li>');
					}
				});
            }else{

            }
        })
        .fail(function() {

        })
        .always(function() {

        });
    }




































})