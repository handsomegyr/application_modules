webpackJsonp([9],[
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(7);
	__webpack_require__(8);
	__webpack_require__(9);
	__webpack_require__(3);
	__webpack_require__(12);
	__webpack_require__(13);
	__webpack_require__(5);

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
/* 10 */,
/* 11 */,
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