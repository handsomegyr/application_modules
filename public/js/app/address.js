$(function(){
 
	// 下拉框
	$(document).on(click,".select-xiala",function(){
		$(".select-xiala").next(".select-state").hide();
		$(this).next(".select-state").toggle();
	});

	$(document).on(click, ".select-state a", function(){
		var text = $(this).text();
		var val = $(this).attr("value");
		var $parent = $(this).parent().parent();
		$(this).parent(".select-state").hide();
		$parent.find("input").val(val);
		$parent.find(".gray6").html(text+'<s class="u-personal"></s>');
	});

	// 城市接口
	$(document).on("click","#selProvinceID .select-state a",function(){
		var province = $(this).attr("value"); //城市
		$("#selCityID input, #selCountyID input").val('');
		provinceFun(province,function(data){
			$("#selCityID .select-state").html('');
			$("#selCityID .select-xiala, #selCountyID .select-xiala").html('---请选择---<s class="u-personal"></s>');
			for( var v in data.result ){
				$("#selCityID .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
			}
		});
	});

	// 城市区接口
	$(document).on(click, "#selCityID .select-state a",function(){
		var province = $(this).attr("value"); //城市
		$("#selCountyID input").val('');
		getdistrictsFun(province,function(data){
			$("#selCountyID .select-state").html('');
			$("#selCountyID .select-xiala").html('---请选择---<s class="u-personal"></s>');
			for( var v in data.result ){
				$("#selCountyID .select-state").append('<a href="javascript:;" value="'+v+'">'+data.result[v]+'</a>');
			}
		});
	});

	// 默认地址选择
	$(document).on("click",".set-default",function(){
		var $this = $(this);
		if( $this.hasClass("z-click") ){
			$this.removeClass("z-click");
			$("#a_default").attr({"value":"0"});
		}else{
			$this.addClass("z-click");
			$("#a_default").attr({"value":"1"});
		}
	});

	// 提交
	$(document).on("click","#btnSaveAddress",function(){
		var id = $(this).attr("_id");
		var province = $("#selProvinceID").find("input").val() //省份
		var city = $("#selCityID").find("input").val() //城市
		var district = $("#selCountyID").find("input").val() //区
		var address = $("#txtAddress").val(); //详细地址
		var txtPostCode = $("#txtPostCode").val(); //邮政编码
		var name = $("#txtConsignee").val(); //收货人
		var txtTel = $("#txtTel").val(); //固定电话
		var txtMobile = $("#txtMobile").val(); //手机号码
		var is_default = $("#a_default").attr("value");//默认地址


		if( province==""||city==""||district=="" ){
			$("#selProvinceID").parents("li").find(".orange").show();
			return;
		}

		if( address=="" ){
			$("#txtAddress").parents("li").find(".orange").show();
			return;
		}else{
			$("#txtAddress").parents("li").find(".orange").hide();
		}

		if( name=="" ){
			$("#txtConsignee").parents("li").find(".orange").show();
			return;
		}else{
			$("#txtConsignee").parents("li").find(".orange").hide();
		}

		if( address=="" ){
			$("#txtAddress").parents("li").find(".orange").show();
			return;
		}else{
			$("#txtAddress").parents("li").find(".orange").hide();
		}

		if( txtMobile=="" ){
			$("#txtMobile").parents("li").find(".orange").show();
			return;
		}else{
			$("#txtMobile").parents("li").find(".orange").hide();
		}

		var dataObj = {
			id:id,
			name:name,
			province:province,
			city:city,
			district:district,
			address:address,
			zipcode:txtPostCode,
			telephone:txtTel,
			mobile:txtMobile,
			is_default:is_default
		}
		saveaddressFun(dataObj);
	});

	// 地址增加修改
	window.saveaddressFun = function (dataObj){
		$.ajax({
	        url: "/member/service/saveaddress",
	        type: 'POST',
	        dataType: 'json',
	        data: dataObj
	    })
	    .done(function(data) {
	        console.log(data);
	        if( data.success ){
	            FailDialog(162,60,"设置成功",window.location.href);
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

	// 设置默认地址
	window.setmembercontactdefaultFun = function(dataObj){
		$.ajax({
	        url: "/member/service/setmembercontactdefault",
	        type: 'POST',
	        dataType: 'json',
	        data: dataObj
	    })
	    .done(function(data) {
	        console.log(data);
	        if( data.success ){
	            FailDialog(162,60,"设置成功",window.location.href);
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

	// 删除地址
	window.deleteaddressFun = function (dataObj){
		$.ajax({
	        url: "/member/service/deleteaddress",
	        type: 'POST',
	        dataType: 'json',
	        data: dataObj
	    })
	    .done(function(data) {
	        console.log(data);
	        if( data.success ){
	            FailDialog(162,60,"删除成功",window.location.href);
	        }else{
	            FailDialog(162,60,"删除失败");
	        }
	    })
	    .fail(function() {
	        alert('网络错误！')
	    })
	    .always(function() {
	        ajaxlack = true;
	    });
	}
});