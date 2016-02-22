require("../libs/jquery.fly.min"); //购物车飞入效果
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