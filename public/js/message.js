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


	$saytext.on("blur input keydown", function() {
		if( $(this).val()!=''||$(this).val().length>2 ){
			$btnSubmitMsg.removeClass('disBtn');
		}else{
			$btnSubmitMsg.addClass('disBtn');
		}	
	});









































});