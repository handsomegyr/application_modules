require("../libs/query");
require("../libs/jquery.qqFace");
require("./public");
$(function(){
	var pageGet = $.query.get("page");
	var forward = $.query.get("forward");
	var $btnSubmitMsg = $("#btnSubmitMsg");
	var $saytext = $("#saytext");
	// 好友私信
	if( pageGet=='friend'||pageGet=='userprivmsgdetail' ){
		var msgToUID = $.query.get("msgToUID");
		$btnSubmitMsg.on('click',function(){
			if( $(this).hasClass("disBtn") ) return;
	        var str = $("#saytext").val();
	        insertuserprivatemsgAjaxFun(msgToUID,replace_em(str),function(data){
	        	if( data.success ){
					if( pageGet=='friend' ){
						parent.window.friendMessageFun();
					}else if( pageGet=='userprivmsgdetail' ){
						parent.window.userprivmsgdetailMessageFun();
					}
				}else{
					
				}
	        });
	    });

	    // 私信好友
		function insertuserprivatemsgAjaxFun(msgToUID,msgContent,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/message/service/insertuserprivatemsg",
		        type: 'POST',
		        dataType: 'json',
		        data: { msgToUID:msgToUID, msgContent:encodeURIComponent(msgContent) }
		    })
		    .done(function(data) {
		        console.log(data);
		        Callback(data);
		    })
		    .fail(function() {
		        alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		}
	}

	// 晒单回复
	if( pageGet=="postdetaila" ){

		$btnSubmitMsg.on('click',function(){
			if( $(this).hasClass("disBtn") ) return;
	        var str = replace_em($("#saytext").val());
	        parent.window.wininsertpostreplyaFun(encodeURIComponent(str));
	    });  
	}

	// 晒单回复回复
	if( pageGet=="postdetailb" ){

		$btnSubmitMsg.on('click',function(){
			if( $(this).hasClass("disBtn") ) return;
	        var str = replace_em($("#saytext").val());
	        parent.window.wininsertpostreplybFun(encodeURIComponent(str));
	    });  
	}


	// 个人主页发私信
	if( pageGet=="uMy" ){
		var msgTouserWeb = $.query.get("msgTouserWeb");
		$btnSubmitMsg.on('click',function(){
			if( $(this).hasClass("disBtn") ) return;
	        var str = $("#saytext").val();
	        insertmsgAjaxFun(msgTouserWeb,replace_em(str),function(data){
	        	if( data.success ){
					parent.window.insertmsgFun();
				}else{
					FailDialog(180,60,'发送失败！');
				}
	        });
	    });   

	    // 私信好友
		function insertmsgAjaxFun(msgTouserWeb,msgContent,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/message/service/insertuserprivatemsg",
		        type: 'POST',
		        dataType: 'json',
		        data: { msgToUID:msgTouserWeb, msgContent:encodeURIComponent(msgContent) }
		    })
		    .done(function(data) {
		        console.log(data);
		        Callback(data);
		    })
		    .fail(function() {
		        alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		}

	}

	$saytext.on("blur input keydown", function() {
		if( $(this).val()!=''||$(this).val().length>2 ){
			$btnSubmitMsg.removeClass('disBtn');
		}else{
			$btnSubmitMsg.addClass('disBtn');
		}	
	});



	$('.emotion').qqFace({
	    id : 'facebox', 
	    assign:'saytext', 
	    path:'/qqFace/arclist/' //表情存放的路径
	});
	//查看结果
	function replace_em(str){
	    str = str.replace(/\</g,'&lt;');
	    str = str.replace(/\>/g,'&gt;');
	    str = str.replace(/\n/g,'<br/>');
	    str = str.replace(/\[em_([0-9]*)\]/g,'<img src="/qqFace/arclist/$1.gif" border="0" />');
	    return str;
	}






































});