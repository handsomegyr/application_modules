$(function(){

	// 标签切换
	$("#ul_menu li").on("click", function(){
		var index = $(this).index();
		$(this).addClass('current').siblings().removeClass('current');
		if( index==0 ){
			$("#midNavLine").animate({"left":"384px"});
			$("#div_evaldata").show();
			$("#div_allrecord, #div_postlist").hide();
		}else if( index==1 ){
			$("#midNavLine").animate({"left":"558px"});
			$("#div_allrecord").show();
			$("#div_evaldata, #div_postlist").hide();
		}else if( index==2 ){
			$("#midNavLine").animate({"left":"732px"});
			$("#div_postlist").show();
			$("#div_evaldata, #div_allrecord").hide();
		}
	});

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






});