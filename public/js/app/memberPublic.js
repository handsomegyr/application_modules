$(function(){
	$("#div_collectlist li").hover(function(){
		$(this).find(".n-info-wrapper02").show();
		$(this).find(".n-info-wrapper01").hide();
	},function(){
		$(this).find(".n-info-wrapper01").show();
		$(this).find(".n-info-wrapper02").hide();
	});

	// $(document).on("hover",".u-select-con",function(){
	// 	$(this).addClass("state-hover");
	// 	$(this).find(".select-state").show();
	// },function(){
	// 	$(this).removeClass("state-hover");
	// 	$(this).find(".select-state").hide();
	// });

	$(".private_btn").on("click",function(){
		var _con = $(this).attr("_con");
		var username = '<div class="title">对 '+$(this).attr("username")+' 说：</div>';
		$("#pageDialogMain").prepend(username);
		tanFun("420","210",_con);
	});

	$("#ulMoneyList li").on("click",function(){
		var $this = $(this);
		$this.addClass("f-checked").siblings().removeClass("f-checked");
		$("#b_money").html($this.attr("money"));
		$("#hidMoney").val($this.attr("money"));

	});

	$(".f-pay-bank dd").on("click",function(){
		var $this = $(this);
		$this.addClass("checked").siblings().removeClass("checked");
		$("#hidPayBank").val($this.find("input").val());
	});

	$("#txtOtherMoney").on("input propertychange",function(){
		var $this = $(this);
		var val = $this.val();

		if(!isNaN(val)){
		   $("#b_money").html(val);
		   $("#hidMoney").val(val);
		}else{
			$this.html('');
		}
	});

	$("#ul_menu li").on("click",function(){
		var $this = $(this);
		var index = $(this).index();
		$this.addClass("curr").siblings().removeClass("curr");
		$(".f-pay-bank").hide();
		$(".f-pay-bank").eq(index).show();
	});


	$("#a_transfer").on("click",function(){
		tanFun("428","227",'<div class="g-need-bind clrfix"><dl><dt class="orange">需要设置支付密码才能进行转账</dt><dd class="gray9">您还未设置支付密码，请先设置支付密码再进行转账操作</dd><dd><a href="UserAuth-10.html">立即设置</a></dd></dl></div>');
	});



	// 时间处理
	var date = new Date();
    timeFun();
    function timeFun(){
        var $year = $("#sltYear .select-state");
        //获取年份
        var year = date.getFullYear();
        //年份循环
        for ( var i=0; i<=100; i++ ) {
            $year.append('<a value="'+(year-i)+'">'+(year-i)+'</a>');
        }

        var sltMonthVal = $("#sltMonth .gray6").html();
        if( sltMonthVal!="月" ){
        	thisDaysFun(sltMonthVal);
        }
    }    

    // 计算当月有少天
    function daysFun(mouth){
        //定义当月的天数；
        var days;
        //当月份为二月时，根据闰年还是非闰年判断天数
        if( mouth==2 ){
            days = date.getFullYear() % 4 == 0 ? 29 : 28;
        }
        else if(mouth == 1 || mouth == 3 || mouth == 5 || mouth == 7 || mouth == 8 || mouth == 10 || mouth == 12){
            //月份为：1,3,5,7,8,10,12 时，为大月.则天数为31；
            days=31;
        }
        else{
            //其他月份，天数为：30.
            days=30;
        }
        return days;
    }

    $("#sltMonth .select-state a").on("click",function(){
        var days = $(this).html();
        thisDaysFun(days);
    });

    // 循环天
    function thisDaysFun(days){
        $days = $("#sltDay .select-state");
        $days.html('');
        $("#sltDay .gray6").html('日<s class="u-personal"></s>');
        //天循环
        for ( var i=0; i<daysFun(days); i++ ) {
            $days.append('<a value="'+((i+1)<=9 ? "0"+(i+1) : (i+1))+'">'+((i+1)<=9 ? "0"+(i+1) : (i+1))+'</a>');
        }
    }

    	// 城市函数
	window.provinceFun = function (province,Callback){
		// if( !ajaxlack ) return; ajaxlack = false;
		$.ajax({
	        url: "/system/area/getcitys",
	        type: 'POST',
	        dataType: 'json',
	        data: { province:province }
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
	        alert('网络错误！')
	    })
	    .always(function() {
	    	ajaxlack = true;
	    });
	}

	// 城市区域函数
	window.getdistrictsFun = function(city,Callback){
		// if( !ajaxlack ) return; ajaxlack = false;
		$.ajax({
	        url: "/system/area/getdistricts",
	        type: 'POST',
	        dataType: 'json',
	        data: { city:city }
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
	        alert('网络错误！')
	    })
	    .always(function() {
	    	ajaxlack = true;
	    });
	}
})