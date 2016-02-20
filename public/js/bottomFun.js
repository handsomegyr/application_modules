$(function(){

	// 无购物车
	$("#divRTool li").hover(function(){
		$(this).addClass('cart-hover').siblings().removeClass('cart-hover');
	},function(){
		$(this).removeClass('cart-hover');
	});

	// 滚动条监控事件
	$(document).scroll(function() {
		if( $(document).scrollTop()>100 ){
			$("#rightTool, #divRTool").fadeIn();
		}else{
			$("#rightTool, #divRTool").fadeOut();
		}
	});

	// 判读显示购物车
	if( pageTrue=="home"||pageTrue=="productindex"||pageTrue=="productlist" ){
		$("#divRTool").remove();
	}else{
		$("#rightTool").remove();
	}


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

	$(".f-shopping-cart").hover(function(){
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



});