$(function(){


	// 列表页面展开搜索
	$("#btnExpend").on(click,function(){
		var $ulheight = $("#ulBrandList");
		if( $ulheight.height()<=84 ){
			$ulheight.height('auto');
		}else{
			$ulheight.height(84);
		}
	});










});
