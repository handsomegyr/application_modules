require("./public");
require("./CartList");
require("./cartAjax");
$(function(){
	var Predeposit = $("#hidPredeposit").val(); //账户余额
	var Orderamount = $("#hidOrderamount").val(); //需要支付余额
	var txtPaypwd = $("#Password").val(); //是否需要输入支付密码

	// 判断账户是否有余额
	if( Predeposit>0 ){
		$("#divBankBox").hide();
	}else{
		$("#divBankBox").show();
		$("#iBankPay").html('￥'+Orderamount);
	}

	// 是否账户支付
	$("#checkBalance").on("click",function(){
		var $this = $(this);
		if( Predeposit>0 ){
			$("#divBankBox").toggle();
		}else{
			$this.html('');
		}
	});

	// 选择支付方式
	$("#dlPayForm dd").on("click",function(){
		$(this).addClass("checked").siblings().removeClass('checked');
		$("#hidPayName").val($(this).find("input").val());
	});

	// 立即支付
    $("#submitOK").on("click",function(){
        if( logoTrue ){
        	// 判断是否需要输入支付密码
        	// if( $("#checkBalance:has(b)").length!=0 ){
        	// 	$("#divPaypwd").show();
        	// 	return;
        	// }


            var pay_sn = $("#hidCheckSN").val();
            var payway = 'weixin';
            var integral = 0;
            var predeposit = 1;
            $.ajax({
                url: "/order/pay/create",
                type: 'POST',
                dataType: 'json',
                data: { pay_sn:pay_sn,payway:payway,integral:integral,predeposit:predeposit } //points_used=1&predeposit_used=1
            })
            .done(function(data) {
                console.log(data);
                if( data.success ){
                    out_trade_no = data.result.out_trade_no;
                    pay_state = data.result.pay_state;
                    pay_url = data.result.pay_url;
                    // if( pay_state ){
                    //     window.location.href = '/yungou/cart/shopok?id='+out_trade_no;
                    //     return;
                    // }
                    tanFun(490,237,'<div class="payment_ts">'
                        +'<h3><s class="z-arrows"></s>请在新开窗口完成支付！</h3>'
                        +'<ul>'
                            +'<li class="payment_ts_con">如您的浏览器不支持，请复制以下链接到IE浏览器打开，完成付款后跟据您的情况进行以下操作！</li>'
                            +'<li class="payment_ts_links">'+url+'</li>'
                            +'<li class="payment_ts_but"><a id="btnBuyOk" href="javascript:gotoClick();" class="pay_tipsbut">完成支付</a><a id="btnReSelect" href="javascript:gotoClick();" class="blue">支付遇到问题？返回重新选择</a></li>'
                        +'</ul>'
                    +'</div>');
                    getpayresultFun();
                    window.open(pay_url);
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

	// 支付密码验证
    $("#txtPaypwd").on("focus",function(){
    	var $this = $(this);
    	var paypwd = $(this).val();
    	var len = paypwd.length;
    	$(this).next().show();
    	if( paypwd==6 ){
    		$.ajax({
	            url: "",
	            type: 'POST',
	            dataType: 'json',
	            data: { paypwd:paypwd }
	        })
	        .done(function(data) {
	            console.log(data);
	            if( data.success ){
	                $this.next().addClass("tips_txt_Correct");
	            }else{
					$this.next().addClass("tips_txt_Wrong");
	            }
	        })
	        .fail(function() {

	        })
	        .always(function() {

	        });
    	}else{
    		$this.next().addClass("tips_txt_Wrong");
    	}
    });
    

    function getpayresultFun(){
        $.ajax({
            url: "/order/pay/getpayresult",
            type: 'POST',
            dataType: 'json',
            data: { id:out_trade_no } 
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
                // window.location.href = '/yungou/cart/shopok';
                window.location.href = '/yungou/cart/shopok?id='+out_trade_no;
            }else{
                getpayresultFun();
            }
        })
        .fail(function() {
            //alert('网络错误！')
        })
        .always(function() {

        });
    }






















})