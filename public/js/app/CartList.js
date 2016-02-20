$(function(){

	// 人气加入购物车
	$(document).on("click",".u-cart-add",function(){
		var codeid = $(this).parents(".f-recomm-list").attr('codeid');
		cartAddFun(codeid,1,function(data){
            FailDialog(160,60,'添加成功！',url);
        });
	});


	// 增加商品数量
	$(".z-plus").on("click",function(){
		var $this = $(this);
		var val = eval($this.siblings("input").val())+1;
		var maxnum = eval($this.siblings("input").attr("maxnum"));
		var goodsid = $this.siblings("input").attr("goodsid");
		if( val>maxnum ) return;
		cartNumFun(goodsid,val,function(data){
			$this.siblings("input").val(val);
			$this.parents("dd").find(".orange").html('￥'+(val)+'.00');
			cartListNum();
		});
	});

	// 减少商品数量
	$(".z-less").on("click",function(){
		var $this = $(this);
		var val = eval($this.siblings("input").val())-1;
		var goodsid = $this.siblings("input").attr("goodsid");
		if( val<1 ) return;
		cartNumFun(goodsid,val,function(){
			$this.siblings("input").val(val);
			$this.parents("dd").find(".orange").html('￥'+(val)+'.00');
			cartListNum();
		});
	});

	// 选择商品
	$(".z-check").on("click",function(){
		var $this = $(this);
		if( $this.find("b").hasClass("z-comms") ){
			$this.html('');
		}else{
			$this.html('<b class="z-comms"></b>');
		}
		cartListNum();
	});

	// 输入价格
	$(".cartNum").on("keyup", function(){
		var $this = $(this);
		var goodsid = $(this).attr("goodsid");
		var val = eval($this.val());
		if( val<=0 ) return;
		cartNumFun(goodsid,val,function(){
			$this.parents("dd").find(".orange").html('￥'+(val)+'.00');
			cartListNum();
		});
	});

	// 全选
	$("#sSelAll").on("click",function(){
		var $this = $(this);
		if( $this.find("b").hasClass("z-comms") ){
			$(".z-check").html('<b class="z-comms"></b>');
		}else{
			$(".z-check").html('');
			$("#hidTotalMoney").val(0);
		}
		cartListNum();
	});
	$("#btnSelAll").on("click",function(){
		if( $(this).parent().prev().find("b").hasClass("z-comms") ){
			$(".z-check").html('');
			$("#hidTotalMoney").val(0);
		}else{
			$(".z-check").html('<b class="z-comms"></b>');
		}
		cartListNum();
	});

	// 删除
	$("#btnDelete").on("click",function(){
		
	});

	// 循环购物车数量
	function cartListNum(){
		var $dd = $("#dlCartList dd");
		var ddNum = $dd.length;
		var val = 0;
		for( var i=0; i<ddNum; i++ ) {
			if( $dd.eq(i).find("b").hasClass("z-comms") ){
				val += eval($dd.eq(i).find(".cartNum").val());
			}
		}
		$("#hidTotalMoney").val(val);
		$("#iTotalMoney").html('￥'+val+'.00');
	}

	// 数量增加和减少函数
	function addRemoveNum(_this,e){
		if( !_this.parents('dd').find('b').hasClass('z-comms') ) return;

		if( e=='add' ){
			$("#hidTotalMoney").val(eval($("#hidTotalMoney").val())+1);
			return false;
		}
		$("#hidTotalMoney").val(eval($("#hidTotalMoney").val())-1);
	}

	$("#divRecList .f-recomm-list").hover(function(){
		$(this).addClass("f-recomm-hover");
		$(this).append('<div class="z-mask"></div><a href="javascript:;" class="u-cart-add transparent-png"></a>');
	},function(){
		$(this).removeClass("f-recomm-hover");
		$(this).find(".z-mask,.u-cart-add").remove();
	});

	$("#divBankList cite span").on("click",function(){
		var index = $(this).index();
		$(this).addClass("current").siblings().removeClass("current");
		if( index==0 ){
			$("#dlCXK").show();
			$("#dlXYK").hide();
		}else{
			$("#dlCXK").hide();
			$("#dlXYK").show();
		}
	});

	// $("#submitOK").on("click", function(){
	// 	tanFun(490,237,'<div class="payment_ts">'
 //                +'<h3><s class="z-arrows"></s>请在新开窗口完成支付！</h3>'
 //                +'<ul>'
 //                    +'<li class="payment_ts_con">如您的浏览器不支持，请复制以下链接到IE浏览器打开，完成付款后跟据您的情况进行以下操作！</li>'
 //                    +'<li class="payment_ts_links">http://cart.1yyg.com/payment.do</li>'
 //                    +'<li class="payment_ts_but"><a id="btnBuyOk" href="javascript:gotoClick();" class="pay_tipsbut">完成支付</a><a id="btnReSelect" href="javascript:gotoClick();" class="blue">支付遇到问题？返回重新选择</a></li>'
 //                +'</ul>'
 //            +'</div>');
	// });
































})