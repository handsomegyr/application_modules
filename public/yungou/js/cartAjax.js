$(function(){


    /**********************************************右侧购物车开始********************************************/
    rCartFun();
    function rCartFun(){
        $(".r-plus, .r-less, .modify, .modify, .z-ygrc, #cart_shower").off("click");
        $(".modify").off("keyup");
        // 增加商品数量
        $(".r-plus").on("click",function(){
            var $this = $(this);
            var val = $this.siblings("input").val();
            var goodsid = $this.siblings("input").attr("goodsid");
            cartNumFun(goodsid,1,function(){
                var jg = (eval(val)+1);
                $this.siblings("input").val(jg);
                $this.parents("dl").find(".jiage").html(jg);
                $this.parents("dl").find(".shuliang").html(jg);
                cartListNum();
            });
        });

        // 减少商品数量
        $(".r-less").on("click",function(){
            var $this = $(this);
            var val = $this.siblings("input").val();
            var goodsid = $this.siblings("input").attr("goodsid");
            if( val<=1 ) return;
            cartNumFun(goodsid,1,function(){
                var jg = (eval(val)+1);
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
            var val = $this.val();
            cartNumFun(goodsid,$this.val(),function(){
                $this.parents("dl").find(".jiage").html(jg);
                $this.parents("dl").find(".shuliang").html(jg);
                cartListNum();
            });
        });

        // 删除
        $("#cart_shower .delete-close").on("click", function(){
            var goodsid = $(this).attr("goodsid");
            $(this).parents("dl").addClass("deleteDl");
            cartClearFun(goodsid,function(){
                $(".deleteDl").remove();
                var cartNum = eval($("#cart_shower dl").length);
                $(".u-float-list em").html(cartNum);  
            });
        });

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
        logoFun(function(e){
            if( e ){
                window.location.href = '/yungou/cart/list';
            }else{
                tanLoginFun('/yungou/cart/list');
            }
        });
    });
    /**********************************************右侧购物车结束********************************************/


    // 加入购物车
    var ajaxOK = true;
    $("#ulGoodsList .u-cart, .consume-addcar").on("click",function(){
        if( !ajaxOK ) return; ajaxOK = false;
        var $this = $(this);
        var goodsid = $this.attr('goodsid');
        var $dl = $("#cart_shower dl");
        var goodsImg = $this.attr("_src");
        cartAddFun(goodsid,1,function(data){
            ajaxOK = true;
            var exist = false;
            var eq;
            for( var i=0; i<$dl.length; i++ ) {
                eq = $dl.eq(i);
                if( goodsid==eq.attr('goodsid') ) exist = true;
            }
            if( exist ){
                exist = false;
                var num = parseInt(eq.find(".shuliang").html())+1;
                eq.find("input").val(num);
                eq.find(".shuliang").html(num);
                eq.find(".jiage").html(num);
            }else{
                $("#cart_shower").append('<dl goodsid="'+goodsid+'">'
                        +'<dd>'
                            +'<a href="javascript:;" target="_blank">'
                                +'<img src="'+goodsImg+'">'
                            +'</a>'
                        +'</dd>'
                        +'<dd class="z-ygrc">'
                            +'<p class="two-row" style="display: block;">'
                                +'<cite>'
                                    +'<em>集资购人次：</em><em class="shuliang">1</em>'
                                +'</cite>'
                                +'<cite>'
                                    +'<em>小计：</em>￥<em class="jiage">1</em>.00'
                                +'</cite>'
                            +'</p>'
                            +'<p class="three-row" style="display: none;">'
                                +'<span class="gray6">剩余 57人次</span>'
                                +'<span class="modify">'
                                    +'<a href="javascript:;" class="r-less">-</a>'
                                    +'<input type="text" value="1" goodsid="'+goodsid+'">'
                                    +'<a href="javascript:;" class="r-plus">+</a>'
                                +'</span>'
                                +'<span id="miniTips"></span>'
                            +'</p>'
                        +'</dd>'
                        +'<dd class="z-close">'
                            +'<a href="javascript:;" title="删除" class="delete-close transparent-png" goodsid="'+goodsid+'"></a>'
                        +'</dd>'
                    +'</dl>');
            }  
            rCartFun();

            var cartNum = eval($("#cart_shower dl").length);
            $(".u-float-list em").html(cartNum);    
        });
    });

    


    // 立即结算
    $("#btnGoPay").on("click",function(){
        logoFun(function(e){
            if( e ){
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
    });

    // 立即支付
    $("#submitOK").on("click",function(){
        logoFun(function(e){
            if( e ){
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
                        if( pay_state ){
                            window.location.href = '/yungou/cart/shopok?id='+out_trade_no;
                            return;
                        }
                        tanFun(490,237,'<div class="payment_ts">'
                            +'<h3><s class="z-arrows"></s>请在新开窗口完成支付！</h3>'
                            +'<ul>'
                                +'<li class="payment_ts_con">如您的浏览器不支持，请复制以下链接到IE浏览器打开，完成付款后跟据您的情况进行以下操作！</li>'
                                +'<li class="payment_ts_links">'+url+'</li>'
                                +'<li class="payment_ts_but"><a id="btnBuyOk" href="javascript:gotoClick();" class="pay_tipsbut">完成支付</a><a id="btnReSelect" href="javascript:gotoClick();" class="blue">支付遇到问题？返回重新选择</a></li>'
                            +'</ul>'
                        +'</div>');
                        getwxpayresultFun();
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
    });
    

    function getwxpayresultFun(){
        $.ajax({
            url: "/order/pay/getwxpayresult",
            type: 'POST',
            dataType: 'json',
            data: { id:out_trade_no } 
        })
        .done(function(data) {
            console.log(data);
            if( data.success ){
                // window.location.href = '/yungou/cart/shopok';
            }else{
                getwxpayresultFun();
            }
        })
        .fail(function() {
            //alert('网络错误！')
        })
        .always(function() {

        });
    }



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

    // 删除购物车商品
    function cartClearFun(goodsid,Callback){
        $.ajax({
            url: "/order/cart/clear",
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
            //alert('网络错误！')
        })
        .always(function() {

        });
    }


























})