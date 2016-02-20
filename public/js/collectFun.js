$(function(){
    var no = '<i class="t-icon sc-icon ng-box-bg sc-icon-past"></i>已关注';
    var yes = '<i class="t-icon sc-icon ng-box-bg"></i>关注';
    // 检查是否关注
    if( pageTrue=="product" ){
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
        logoFun(function(e){
            if(e){
                if( $this.hasClass("has-in") ){
                    delcollectgoodsAjaxFun(goodsid,function(data){
                        if( data.success ){
                            $this.removeClass('has-in').html(no);
                        }
                    });
                }else{
                    addcollectgoodsAjaxFun(goodsid,function(data){
                        if( data.success ){
                            $this.addClass('has-in').html(yes);
                        }
                    });
                }
            }else{
                tanLoginFun(url);
            }
        });
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