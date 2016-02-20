webpackJsonp([8],{

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	__webpack_require__(9);
	__webpack_require__(3);
	__webpack_require__(13);
	 

/***/ },

/***/ 9:
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

/***/ 13:
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

});