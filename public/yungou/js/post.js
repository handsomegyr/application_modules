$(function(){


	var postID = $.query.get("id");
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
            	$this.find("em").html(eval($this.find("em").text())+1);
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
		    	// $.each(data.result.datas, function(i,item){
		    	// 	var replyContent = item.replyContent;//: "专业投-手，看上号码"
		    	// 	var replyCount = item.replyCount;//: "0"
		    	// 	var replyID = item.replyID;//: "1676004"
		    	// 	var replyTime = item.replyTime;//: "5分钟前"
		    	// 	var replyUserName = item.replyUserName;//: "1658538217"
		    	// 	var replyUserWeb = item.replyUserWeb;//: "1012231163"
		    	// 	var userPhoto = item.userPhoto;//: "20160103182127763.jpg"
		    		dataLi += '<li>'
	                    +'<div class="input-pic fl">'
	                        +'<a uweb="1010496029" type="showCard" href="javascript:;" target="_blank">'
	                            +'<img src="/Images/UserFace-160-0000.jpg" alt="">'
	                            +'<i class="transparent-png"></i>'
	                        +'</a>'
	                    +'</div>'
	                    +'<div class="m-review fl">'
	                        +'<dl>'
	                            +'<dt>'
	                                +'<a uweb="1010496029" type="showCard" href="javascript:;" target="_blank">测试</a>15分钟前<em></em>'
	                            +'</dt>'
	                            +'<dd>'
	                                +'<span class="gray3">太假了，大家不要被骗了，妈的，最近全部都是广州而且都是小将，总共才买了十几块，一块钱就中了</span>'
	                                +'<cite>'
	                                    +'<a href="javascript:;" class="posthfbtn" name="SignReplay" num="0" loadeditor="0">回复<em></em></a>'
	                                +'</cite>'
	                            +'</dd>'
	                        +'</dl>'
	                        +'<div name="ReplyBox" class="reply-comment clrfix" style="display:none;">'
	                            +'<div name="ReplyForm" class="replyform"></div>'
	                            +'<div name="ReplyList" class="comment-main clrfix" style="display:block;">'
	                            	+'<ul>'
									    +'<li>'
									        +'<div class="input-pic fl">'
									            +'<a uweb="1010029819" type="showCard" rel="nofollow" href="http://u.1yyg.com/1010029819" target="_blank" title="中个奖有这么困难嘛">'
									                +'<img src="http://faceimg.1yyg.com/UserFace/20151212152923647.jpg" alt=""><i class="transparent-png"></i>'
									            +'</a>'
									        +'</div>'
									        +'<div class="m-review fl">'
									            +'<dl>'
									                +'<dt>'
									                    +'<a uweb="1010029819" type="showCard" href="http://u.1yyg.com/1010029819" target="_blank" title="中个奖有这么困难嘛"> 中个奖有这么困难嘛</a>'
									                    +'<i class="f-tran-prev"></i>1分钟前<em>1楼</em>'
									                +'</dt>'
									                +'<dd>'
									                    +'<span class="gray3"><img src="http://skin.1yyg.com/Images/Emoticons/13.gif" alt=""></span>'
									                    +'<cite><a href="javascript:;" name="signDelete" replayid="1681750">删除</a></cite>'
									                +'</dd>'
									            +'</dl>'
									        +'</div>'
									    +'</li>'
									    +'<div class="put-away">'
									        +'<a href="javascript:;">收起<b></b></a>'
									    +'</div>'
									+'</ul>'
	                            +'</div>'
	                       +'</div>'
	                   +' </div>'
	                +'</li>';
		    	// });
				
				// if( dataLi=='' ){
				// 	$("#g-pagination").hide();
				// 	$("#commentList li").remove();
				// 	$(".null-data").append('<div class="null-data"><b class="gth-icon transparent-png"></b>暂无记录！</div>');
				// }else{
					// $("#g-pagination").show();
					// $(".null-data, #div_UserBuyList li").remove();
					$("#commentList ul").append(dataLi);
				// }
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
			if( parentsLi.find(".reply-comment").is(":hidden") ){ 
				$("#commentList .reply-comment").hide();
			}
			parentsLi.find(".reply-comment").toggle();
			if( logoTrue ){
				parentsLi.find(".replyform").html('<div name="InputBox" class="input-comment clrfix">'
				    +'<div name="userFace" class="input-pic fl">'
				        +'<a target="_blank" href="javascript:;">'
				            +'<img name="imgUserPhoto" src="/Images/UserFace-160-0000.jpg" alt=""><i class="transparent-png"></i>'
				        +'</a>'
				    +'</div>'
				    +'<div class="input-box fl">'
				        +'<div class="textCon">'
				            +'<iframe hidefocus="true" name="myFrame" frameborder="0" scrolling="no" src="/yungou/message.html?page=postdetail" style="width:486px; height: 155px;cursor:text;"></iframe>'
				        +'</div>'
				    +'</div>'
				+'</div>');
			}else{
				parentsLi.find(".replyform").html('<div class="not-login clrfix">'
				    +'<div name="userFace" class="input-pic fl">'
				        +'<img name="imgUserPhoto" src="/Images/UserFace-160-0000.jpg" alt="">'
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