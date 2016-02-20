$(function(){

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
	    		var goodsCommonID = item.goodsCommonID;
				var goodsID = item.goodsID;
				var goodsName = item.goodsName;
				var goodsPeriod = item.goodsPeriod;
				var goodsPic = item.goodsPic;
				var isSale = item.isSale;
				var limitBuy = item.limitBuy;
				var quantity = item.quantity;
				var sales = item.sales;
				var percentage = (sales/quantity)*100 //销量百分比
	    		dataLi += '<li><div class="n-pro-img">'
	                    +'<a target="_blank" href="javascript:;">'
	                        +'<img src="'+goodsPic+'"><s></s>'
	                    +'</a></div>'
	                +'<div class="n-info-wrapper n-info-wrapper01" style="display: block;">'
	                    +'<p class="gray3">'+goodsName+'</p>'
	                    +'<div class="n-pro-num">第<span class="orange">'+goodsPeriod+'</span>云&nbsp;进行中<span class="dotting"></span></div>'
	                +'</div>'
	                +'<div class="n-info-wrapper n-info-wrapper02" style="display: none;">'
	                    +'<div class="n-line-wrapper">'
	                        +'<div class="n-line-inner">'
	                            +'<div class="line" style="width:'+percentage+'%"></div>'
	                        +'</div>'
	                    +'</div>'
	                    +'<a href="javascript:;" goodsid="'+goodsID+'" class="n-pro-add">加入购物车</a>'
	                    +'<a href="javascript:;" goodsid="'+goodsID+'" class="n-btn-del u-personal"></a>'
	                +'</div></li>';
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
	        url: "",
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
		$(this).find('.n-info-wrapper02').show();
	});
	// 鼠标移开
	$(document).on("mouseout", "#div_collectlist li",function(){
		$(this).find('.n-info-wrapper02').hide();
	});
	// 删除关注
	$(document).on("click", ".n-btn-del",function(){
		var goodsid = $(this).attr("goodsid");
		$(this).parents('ul').find("li").removeClass("delData");
		$(this).parents('li').addClass("delData");
		tanFun(398,197,'<div class="z-popUp z-pop-box">'
            +'<span class="gray3">确定要删除所选的商品？</span>'
            +'<a href="javascript:gotoClick();" title="取消" class="z-btn-cancel">取消</a>'
            +'<a href="javascript:;" title="确定" goodsid="'+goodsid+'" class="z-btn-determine cart_delete_determine" id="guanzhuDelBtn">确定</a>'
            +'</div>');
	});
	// 删除关注
	$(document).on("click", "#guanzhuDelBtn",function(){
		var goodsid = $(this).attr("goodsid");
		var dataObj = {
			goodsid:goodsid
		}
		delcollectgoodslistAjaxFun(dataObj,function(){
			FailDialog(160,60,'删除成功！');
			if( $("#div_collectlist li").length<=0 ){
				$(".null-data").show();
			}else{
				$(".delData").remove();
			}
		});
	});

	// 加入购物车
	$(document).on("click", ".n-pro-add",function(){
		var goodsid = $(this).attr("goodsid");
		var $em = $(".u-float-list em");
		var num = parseInt($em.text())+1;
		cartAddFun(goodsid,1,function(){
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
		$(".a-screen li:eq("+state+") a").addClass("z-checked");
	});

	$(".u-select-con .gray6").on("click",function(){
		thisVal = '全部状态';
		$(this).html(thisVal+'<s class="u-personal"></s>');
		callback(0);
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
}


/*******************************************************云购记录**************************************************/
if( pageTrue=="userbuylist" ){

	// 日期选择筛选
	$("#a_serach").on("click",function(){
		var dpd1 = $("#dpd1").val();
		var dpd2 = $("#dpd2").val();
		if( timeGetTimeFun(dpd1)>timeGetTimeFun(dpd2) ){
			FailDialog(280,60,'开始时间不能大于结束时间！');
			return;
		}
	});

	
	memberXialaFun(function(e){

	});

	userbuylistFirstDataFun(1,8,0,'2015-01-01','2017-01-01','');

	function userbuylistFirstDataFun(page,limit,state,beginTime,endTime,keyWords){
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
	    		var codePeriod = item.codeID;//: "10341"
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
	    		dataLi += '<dd class="has-announced"><span class="u-commodity-pic">'
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

	    	});
			
			if( dataLi=='' ){
				$("#g-pagination").hide();
				$("#div_UserBuyList dd").remove();
				$("#div_UserBuyList").append('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
			}else{
				$("#g-pagination").show();
				$(".null-data, #div_UserBuyList dd").remove();
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


	var beginTime = $("#dpd1").val();
	var endTime = $("#dpd2").val();
	orderlistFirstDataFun(1,8);

	function orderlistFirstDataFun(page,limit,beginTime,endTime){
		var dataObj = {
			page:page,
			limit:limit,
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
				$(".null-data").show();
			}else{
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
	memberXialaFun(function(e){

	});

	// getuserpointsdetaillistFirstDataFun(1,8,'');

	function getuserpointsdetaillistFirstDataFun(page,limit,state){
		$("#ul_points").html('<div class="loading-2015"><em></em></div>');
		var dataObj = {
			page:page,
			limit:limit,
			state:state
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
				var logPointNum = item.logDescript;//: "-100";
				var logTime = item.logDescript;//: "2016.01.02 14:35:42"
				var className = (eval(logPointNum)>0 ? 'add' : "mius");
				dataLi += '<li><span class="time">'+logTime+'</span><span class="order '+className+'"><em>'+logPointNum+'</em></span><span class="protail">'+logDescript+'</span></li>';	    		
	    	});
			
			if( dataLi=='' ){
				$("#g-pagination").hide();
				$("#ul_points li, .loading-2015").remove();
				$("#ul_points").html('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
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
}
/*******************************************************我的福分结束*********************************************/






































});