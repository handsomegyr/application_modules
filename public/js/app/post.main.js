require("../libs/query");
require("../libs/pagination-min");
require("../libs/jquery.lazyload");
require("./public");
require("./collectFun");
require("./bottomFun");
require("./cartAjax");

$(function(){


	var postID = $.query.get("id");
	var floorID = 0;
	/*********************************************************************首页*****************************************************/
	if( pageTrue=="postindex" ){
		$("#div_category").hover(function(){
			$(".classification").show();
		},function(){
			$(".classification").hide();
		});
	}

	// 羡慕
    $("#p_xianmu, .p_xianmu").on("click",function(){
    	var $this = $(this);
    	if( !ajaxlack||$this.hasClass("u-xianmu-past") ) return; ajaxlack= false;
    	$.ajax({
            url: "/post/service/insertposthits",
            type: 'POST',
            dataType: 'json',
            data: { postid:( postID=="" ? $this.attr('postid') : postID ) }
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
            	$this.find(".em").html(eval($this.find(".em").attr("votenum"))+1);
            	$this.addClass("u-xianmu-past");
            }else{

            }
        })
        .fail(function() {

        })
        .always(function() {
        	ajaxlack = true;
        });
    });

    // 删除回复
    $(document).on("click",'.signDelete',function(){
    	var id = $(this).attr("replayid");
    	$(this).parents("ul").removeClass("removeClass");
    	$(this).parents(".liClass").addClass("removeClass");
    	tanFun(282,110,'<div class="PopMsgC"><s></s>确定要删除吗？</div>'
	    	+'<div class="PopMsgbtn">'
	    	+'<a href="javascript:;" id="btnMsgOK" class="orangebut" replayid="'+id+'">确认</a>&nbsp;&nbsp;<a href="javascript:gotoClick();" id="btnMsgCancel" class="cancelBtn">取消</a>'
	    	+'</div>');
    });

    // 确认删除回复
    $(document).on("click","#btnMsgOK",function(){
    	var id = $(this).attr("replayid");
    	if( !ajaxlack ) return; ajaxlack= false;
    	deletepostreplyFun(id,function(data){
    		if( data.success ){
    			FailDialog(160,60,'删除成功！');
    			$(".removeClass").remove();
    			gotoClick();
    			ajaxlack = true;
    		}
    	});
    });


    // 回复回复的回复
    $(document).on("click", ".signReply",function(){
    	replyID = $(this).attr("replayid");
    	floorID = $(this).attr("floorID");
    	$(".from-to-close").remove();
    	$(".input-box").prepend('<div name="comment-title" class="from-to-close" style=""><span>对 '+floorID+'楼 说</span><a class="delete-close" href="javascript:"></a></div>')
    });

    // 关闭回复回复的回复
    $(document).on("click", ".delete-close",function(){
    	replyID = replyinfoId;
    	$(".from-to-close").remove();
    });



	if( pageTrue=="postdetail" ){
		// TA的其他晒单
		getpostotherlistAjaxFun(1,10,postID);
		function getpostotherlistAjaxFun(page,limit,postid){
	        $.ajax({
	            url: "/post/service/getpostotherlist",
	            type: 'POST',
	            dataType: 'json',
	            data: { page:page, limit:limit, postid:postid }
	        })
	        .done(function(data) {
	            console.log(data);
	            if( data.success ){
	            	$.each(data.result.datas, function(i,item){
	            		var codePeriod = item.codePeriod; //: "1"
	            		var postContent = item.postContent; //: "嗯呵呵O(∩_∩)O~，货真价实的读卡器，包装很好~~~物流还不错，基本上都不错，没有什么问题"
	            		var postID = item.postID; //: "5688926f887c22054a8b45e3"
	            		var postImg = item.postImg; //: "/upload/post/5688bd6a887c22014a8b4692,/upload/post/5688bd68887c22024a8b46a0,/upload/post/5688bd61887c22054a8b45eb"
	            		var postTime = item.postTime; //: "2016-01-03 14:19:27"
	            		var postTitle = item.postTitle; //: "终于中奖啦123"
	            		var reply_num = item.reply_num; //: "0"
	            		var userName = item.userName; //: "15821****14"
	            		var userPhoto = item.userPhoto; //: "/upload/member/avatar/UserFace-160-0000.jpg"
	            		var userSName = item.userSName; //: "15821****14"
	            		var userWeb = item.userWeb; //: "56761153887c22184e8b45b5"
	            		var vote_num = item.vote_num; // : "0"

						$(".other-single dl").append('<dd class="sig-xmjdh">'
							+'<p><a href="detail.html">'+postTitle+'</a></p>'
                            +'<span class="sig-xmjdh">'
                                +'<a rel="nofollow" href="/yungou/post/detail?id='+postID+'" class="xianmu"><i class="transparent-png"></i>'+vote_num+'</a>'
                                +'<a rel="nofollow" href="/yungou/post/detail?id='+postID+'" class="pinglun"><i class="transparent-png"></i>'+reply_num+'</a>'
                            +'</span>'
                        +'</dd>');
					});
	            }else{

	            }
	        })
	        .fail(function() {

	        })
	        .always(function() {

	        });
	    }

	    // 评论
	    window.wininsertpostreplyaFun = function(originalContent){
	    	insertpostreplyAjaxFun(postID,originalContent,'','',0,function(data){
	    		if( data.success ){
		        	ajaxlack = true;
		        	getpostreplybyidFirstDataFun(1,8,postID);
		        	FailDialog(160,60,'评论成功！');
		        }else{
		        	FailDialog(160,60,'评论失败！');
		        }
	    	});
	    }

	    function insertpostreplyAjaxFun(postid,originalContent,code,refReplyId,refFloor,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/post/service/insertpostreply",
		        type: 'POST',
		        dataType: 'json',
		        data: { postid:postid, originalContent:originalContent, code:code, refReplyId:refReplyId, refFloor:refFloor  }
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

		// 评论回复
	    window.wininsertpostreplybFun = function(originalContent){
	    	insertpostreplyAjaxFun(postID,originalContent,'',replyID,floorID,function(data){
	    		if( data.success ){
		        	FailDialog(160,60,'评论成功！');
		        	ajaxlack = true;
		        	getpostreplyinfoFun();
		        }else{
		        	FailDialog(160,60,'评论失败！');
		        }
	    	});
	    }





	    // 评论列表
        getpostreplybyidFirstDataFun(1,8,postID);

		function getpostreplybyidFirstDataFun(page,limit,postid){
			var dataObj = {
				page:page,
				limit:limit,
				postid:postid
			}
			// 数据加载 第一次
			getpostreplybyidAjaxFun(dataObj,function(data){
				var total = data.result.total;
				getpostreplybyidAddDataFun(data);
				// 分页
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					getpostreplybyidAjaxFun(dataObj,function(data){
						getpostreplybyidAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function getpostreplybyidAddDataFun(data){
			if( data.success ){
				var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		$("#em_ReplayCount").html(data.result.total);
		    		var replyContent = item.replyContent;//: "专业投-手，看上号码"
		    		var replyCount = item.replyCount;//: "0"
		    		var replyID = item.replyID;//: "1676004"
		    		var replyTime = item.replyTime;//: "5分钟前"
		    		var replyUserName = item.replyUserName;//: "1658538217"
		    		var replyUserWeb = item.replyUserWeb;//: "1012231163"
		    		var userPhoto = item.userPhoto;//: "20160103182127763.jpg"
		    		dataLi += '<li>'
	                    +'<div class="input-pic fl">'
	                        +'<a replyUserWeb="'+replyUserWeb+'" type="showCard" href="/yungou/member/index?id='+replyUserWeb+'" target="_blank">'
	                            +'<img src="'+userPhoto+'" alt="">'
	                            +'<i class="transparent-png"></i>'
	                        +'</a>'
	                    +'</div>'
	                    +'<div class="m-review fl">'
	                        +'<dl>'
	                            +'<dt>'
	                                +'<a replyUserWeb="'+replyUserWeb+'" type="showCard" href="/yungou/member/index?id='+replyUserWeb+'" target="_blank">'+replyUserName+'</a>'+replyTime+'<em></em>'
	                            +'</dt>'
	                            +'<dd>'
	                                +'<span class="gray3">'+replyContent+'</span>'
	                                +'<cite>'
	                                    +'<a href="javascript:;" class="posthfbtn" name="SignReplay" replyID="'+replyID+'">回复<em>'+( replyCount!="0" ? "("+replyCount+")" : "" )+'</em></a>'
	                                +'</cite>'
	                            +'</dd>'
	                        +'</dl>'
	                        +'<div name="ReplyBox" class="reply-comment clrfix" style="display:none;">'
	                            +'<div name="ReplyForm" class="replyform"></div>'
	                            +'<div name="ReplyList" class="comment-main clrfix replylist" style="display:block;"></div>'
	                       +'</div>'
	                   +' </div>'
	                +'</li>';
		    	});
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#commentList li").remove();
					$(".null-data").html('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
				}else{
					$("#g-pagination").show();
					$(".null-data, #div_UserBuyList li").remove();
					$("#commentList ul").html(dataLi);
				}
		    }else{

		    }
		}

		//函数
		function getpostreplybyidAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/post/service/getpostreplybyid",
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

		// 回复
		$(document).on("click",'.posthfbtn',function(){
			var parentsLi = $(this).parents("li");
			replyID = $(this).attr('replyID');
			replyinfoId = replyID;
			$(".from-to-close").remove();
			if( parentsLi.find(".reply-comment").is(":hidden") ){ 
				$("#commentList .reply-comment").hide();
			}
			parentsLi.find(".reply-comment").toggle();
			if( logoTrue ){
				getpostreplyinfoFun();
				parentsLi.find(".replyform").html('<div name="InputBox" class="input-comment clrfix">'
				    +'<div name="userFace" class="input-pic fl">'
				        +'<a target="_blank" href="/yungou/member/index?id='+myuserWeb+'">'
				            +'<img name="imgUserPhoto" src="'+myuserPhoto+'" alt=""><i class="transparent-png"></i>'
				        +'</a>'
				    +'</div>'
				    +'<div class="input-box fl">'
				        +'<div class="textCon">'
				            +'<iframe hidefocus="true" name="myFrame" frameborder="0" scrolling="no" src="/yungou/message.html?page=postdetailb" style="width:486px; height: 155px;cursor:text;"></iframe>'
				        +'</div>'
				    +'</div>'
				+'</div>');
			}else{
				parentsLi.find(".replyform").html('<div class="not-login clrfix">'
				    +'<div name="userFace" class="input-pic fl">'
				        +'<img name="imgUserPhoto" src="'+myuserPhoto+'" alt="">'
				        +'<i class="transparent-png"></i>'
				    +'</div>'
				    +'<div class="point-box fl gray6">'
				        +'请您<a href="javascript:;" name="replyLoginBtn" class="tan_login">登录</a>或'
				        +'<a href="/member/passport/register">注册</a>后再评论<b><s></s></b>'
				    +'</div>'
				+'</div>');
			}
		})

		// 回复收起
		$(document).on("click",".put-away",function(){
			$(".reply-comment").hide();
		});

		// 获取回复函数
		function getpostreplyinfoFun(){
			$.ajax({
	            url: "/post/service/getpostreplyinfo",
	            type: 'POST',
	            dataType: 'json',
	            data: { replyId:replyinfoId }
	        })
	        .done(function(data) {
	            console.log(data);
	            if( data.success ){
					var dataLi = '';
					$.each(data.result.datas, function(i,item){
			    		var floorID = item.floorID;//: 1
						var isDel = item.isDel;//: 1
						var replyContent = item.replyContent;//: "[s:13]"
						var replyID = item.replyID;//: 1692901
						var replyRefFloor = item.replyRefFloor;//: 0
						var replyTime = item.replyTime;//: "1分钟前"
						var replyUserID = item.replyUserID;//: 9533390
						var replyUserName = item.replyUserName;//: "中个奖有这么困难嘛"
						var replyUserWeb = item.replyUserWeb;//: "1010029819"
						var userPhoto = item.userPhoto;//: "20151212152923647.jpg"
			    		dataLi += '<li class="liClass">'
						        +'<div class="input-pic fl">'
						            +'<a uweb="'+replyUserWeb+'" type="showCard" rel="nofollow" href="/yungou/member/index?id='+replyUserWeb+'" target="_blank">'
						                +'<img src="'+userPhoto+'" alt=""><i class="transparent-png"></i>'
						            +'</a>'
						        +'</div>'
						        +'<div class="m-review fl">'
						            +'<dl>'
						                +'<dt>'
						                    +'<a replyUserWeb="'+replyUserWeb+'" type="showCard" href="/yungou/member/index?id='+replyUserWeb+'" target="_blank">'+replyUserName+'</a>'
						                    +( replyRefFloor!="0" ? '<i class="f-tran-prev">回复 '+replyRefFloor+'楼</i>' : "" )
						                    +'<i class="f-tran-prev"></i>'+replyTime+'<em>'+floorID+'楼</em>'
						                +'</dt>'
						                +'<dd>'
						                    +'<span class="gray3">'+replyContent+'</span>'+
						                    ( isDel==true ? '<cite><a href="javascript:;" name="signDelete" class="signDelete" replayid="'+replyID+'">删除</a></cite>' : '<cite><a href="javascript:;" name="signReply" class="signReply" floorID="'+floorID+'" replayid="'+replyID+'">回复</a>' )
						                +'</dd>'
						            +'</dl>'
						        +'</div>'
						    +'</li>';
			    	});
					
					if( dataLi=='' ){
						$(".replylist").html("");
					}else{
						$(".replylist").html('<ul>'+dataLi+'<div class="put-away"><a href="javascript:;">收起<b></b></a></div></ul>');
					}
				}
	        })
	        .fail(function() {

	        })
	        .always(function() {

	        });
		}

		// 删除评论回复
		function deletepostreplyFun(replyId,Callback){
			$.ajax({
	            url: "/post/service/deletepostreply",
	            type: 'POST',
	            dataType: 'json',
	            data: { replyId:replyId }
	        })
	        .done(function(data) {
	            console.log(data);
	            Callback(data);
	        })
	        .fail(function() {

	        })
	        .always(function() {

	        });
		}



	    // 其他获得者
	    var codeID = $("#dl_otherget").attr("codeID");
	    var pageNum = 1;
	    var pagetotal = 0;

	    $("#otherleft").on("click",function(){
	    	if( pageNum<=1 ){
	    		$("#otherleft").addClass("other-left-none");
	    		return;
	    	}else{
	    		$("#otherright").removeClass("other-left-none");
	    	}
	    	pageNum--;
	    	getpostoldperiodlistAjaxFun(pageNum,5,codeID);
	    });

	    $("#otherright").on("click",function(){
	    	if( pageNum>=pagetotal ){
	    		$("#otherright").addClass("other-left-none");
	    		return;
	    	}else{
	    		$("#otherleft").removeClass("other-left-none");
	    	}
	    	pageNum++;
	    	getpostoldperiodlistAjaxFun(pageNum,5,codeID);
	    });

		getpostoldperiodlistAjaxFun(pageNum,5,codeID);
		function getpostoldperiodlistAjaxFun(page,limit,codeID){
	        $.ajax({
	            url: "/post/service/getpostoldperiodlist",
	            type: 'POST',
	            dataType: 'json',
	            data: { page:page, limit:limit, codeID:codeID }
	        })
	        .done(function(data) {
	            console.log(data);
	            if( data.success ){
	            	$("#dl_otherget dd").remove();
	            	pagetotal = parseInt(data.result.total/5)+1;
	            	if( pagetotal<=1 ){
	            		$("#otherleft, #otherright").addClass("other-left-none");
	            	}
	            	$.each(data.result.datas, function(i,item){
	            		var codePeriod = item.codePeriod;//: "2"
	            		var postContent = item.postContent.substr(0,42)+"..."; //: "嗯呵呵O(∩_∩)O~，货真价实的读卡器，包装很好~~~物流还不错..."
						var postID = item.postID; //: "5688926f887c22054a8b45e3"
						var postImg = item.postImg.split(','); //: "/upload/post/5688bd6a887c22014a8b4692,/upload/post/5688bd68887c22024a8b46a0,/upload/post/5688bd61887c22054a8b45eb"
						var postTime = item.postTime; //: "2016-01-03 14:19:27"
						var postTitle = item.postTitle; //: "终于中奖啦123"
						var userName = item.userName; //: "15821****14"
						var userPhoto = item.userPhoto; //: "/upload/member/avatar/UserFace-160-0000.jpg"
						var userWeb = item.userWeb; //: "56761153887c22184e8b45b5"
						$("#dl_otherget").append('<dd><cite class="fl"><a href="/yungou/member/index?id='+userWeb+'" target="_blank"><img src="'+userPhoto+'"><s class="transparent-png"></s></a></cite><span class="fl"><p><em class="f-tran-prev"><a href="/yungou/member/index?id='+userWeb+'" target="_blank">'+userName+'</a></em>获得了第'+codePeriod+'期</p><a href="/yungou/post/detail?id='+postID+'" class="link-btn see-btn">查看晒单</a></span></dd>');
					});
	            }else{

	            }
	        })
	        .fail(function() {

	        })
	        .always(function() {

	        });
	    }

	    // 最新晒单
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
	            		var postContent = item.postContent.substr(0,42)+"..."; //: "嗯呵呵O(∩_∩)O~，货真价实的读卡器，包装很好~~~物流还不错..."
						var postID = item.postID; //: "5688926f887c22054a8b45e3"
						var postImg = item.postImg.split(','); //: "/upload/post/5688bd6a887c22014a8b4692,/upload/post/5688bd68887c22024a8b46a0,/upload/post/5688bd61887c22054a8b45eb"
						var postTime = item.postTime; //: "2016-01-03 14:19:27"
						var postTitle = item.postTitle; //: "终于中奖啦123"
						var userName = item.userName; //: "15821****14"
						var userPhoto = item.userPhoto; //: "/upload/member/avatar/UserFace-160-0000.jpg"
						var userWeb = item.userWeb; //: "56761153887c22184e8b45b5"
						$(".new-single ul").append('<li>'
                            +'<cite><i></i><em><a href="javascript:;" title="'+postTitle+'">'+postTitle+'</a></em></cite>'
                            +'<a rel="nofollow" href="/yungou/post/detail?id='+postID+'"><span class="gray9">'+postContent+'</span></a>'
                            +'<p>'
                                +'<a rel="nofollow" href="/yungou/post/detail?id='+postID+'"><img src="'+postImg[0]+'" border="0" alt=""></a>'
                                +'<a rel="nofollow" href="/yungou/post/detail?id='+postID+'"><img src="'+postImg[1]+'" border="0" alt=""></a>'
                                +'<a rel="nofollow" href="/yungou/post/detail?id='+postID+'"><img src="'+postImg[2]+'" border="0" alt=""></a>'
                            +'</p>'
                        +'</li>');
					});
	            }else{

	            }
	        })
	        .fail(function() {

	        })
	        .always(function() {

	        });
	    }









	}







































})