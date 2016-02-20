$(function(){

	if( pageTrue=='newestbuyrecords' ){
		// 正在云购
		var UserBuyNewListidname = "#recordList";
		var $UserBuyNewListid = $(UserBuyNewListidname);
		// 正在云购函数
		getuserbuynewlistAjaxFun();
		function getuserbuynewlistAjaxFun(){
	        $.ajax({
	            url: "/order/service/getlastestrecord",
	            type: 'POST',
	            dataType: 'json',
	            data: { }
	        })
	        .done(function(data) {
	            console.log(data);
	            if( data.success ){
	            	var dataLi = '';
	            	var total = data.result.total;
	            	$("#num100").html(total);
	            	$.each(data.result.datas, function(i,item){
						var buyCode = item.buyCode //: "56861a56887c22014a8b4684"
						var buyID = item.buyID //: "56862e74887c226e6a8b571f"
						var buyIP = item.buyIP //: "124.77.115.195"
						var buyName = item.buyName //: "13564****96"
						var buyNum = item.buyNum //: 1
						var buyTime = item.buyTime //: "2016-01-01 15:44:59.351"
						var goodsName = item.goodsName //: "【周黑鸭旗舰店_锁鲜装】盒装鸭翅190g*2 武汉特产官方食品零食"
						var period = item.period //: "1"
						var timeCodeVal = item.timeCodeVal //: ""
						var userWeb = item.userWeb //: "56757a39887c22034a8b4596";
						dataLi += '<ul class="Record_contents">'
				            +'<li class="time">'+buyTime+'</li>'
				            +'<li class="nem">'
				                +'<a class="blue" href="/yungou/member/index?id='+userWeb+'" target="_blank">'+buyName+'</a>'
				            +'</li>'
				            +'<li class="name"><a href="/yungou/product/index?id='+buyCode+'">'+goodsName+'</a></li>'
				            +'<li class="much">'+buyNum+'人次</li>'
				        +'</ul>';			    
					});

					$UserBuyNewListid.html(dataLi);
					
	            }else{
	                alert(data.error_msg);
	            }
	        })
	        .fail(function() {
	            // alert('网络错误！')
	        })
	        .always(function() {

	        });
	    }

	    setInterval(function(){
			getuserbuynewlistAjaxFun();
		},5000);
	}



	if( pageTrue=='historybuyrecords' ){
		var spanServerTime = $("#spanServerTime").text().split(" "); //服务器时间
		spanServerTime = spanServerTime[1].split(":");

		timeForFun("#sltFirstT2",24);
		timeForFun("#sltFirstT3",60);
		timeForFun("#sltEndT2",24);
		timeForFun("#sltEndT3",60);

		function timeForFun(id,size){
			for ( var i=0; i<size;i++) {
				var i = i<=9 ? "0"+i : i;
				$(id).append('<option value="'+i+'">'+i+'</option>')
			}
		}

		$("#sltFirstT2").val(spanServerTime[0]);
		$("#sltFirstT3, #sltEndT3").val(spanServerTime[1]);
		$("#sltEndT2").val(parseInt(spanServerTime[0])+1);

		// 查询
		$("#btnQuery").on("click",function(){
			var txtFirstT1 = $("#txtFirstT1").val();
			var txtFirstT2 = $("#sltFirstT2").val();
			var txtFirstT3 = $("#sltFirstT3").val();
			var txtEndT1 = $("#txtEndT1").val();
			var txtEndT2 = $("#sltEndT2").val();
			var txtEndT3 = $("#sltEndT3").val();
			console.log(txtFirstT1,txtEndT1)

			var firstTime = txtFirstT1+' '+txtFirstT2+':'+txtFirstT3+':00';
			var ednTime = txtEndT1+' '+txtEndT2+':'+txtEndT3+':59';
			console.log(timeGetTimeFun(firstTime)>timeGetTimeFun(ednTime),timeGetTimeFun(firstTime),timeGetTimeFun(ednTime))
			if( timeGetTimeFun(firstTime)>timeGetTimeFun(ednTime) ){
				FailDialog(280,60,'开始时间不能大于结束时间！');
				return;
			}

			gethistorybuyrecordFirstDataFun(1,20,firstTime,ednTime);
		});


		gethistorybuyrecordFirstDataFun(1,20,'2016-01-01 00:00:00','2116-01-01 00:00:00');

		function gethistorybuyrecordFirstDataFun(page,limit,BTime,ETime){
			$("#recordList").html('<ul class="Recordloading"><li></li></ul>');
			var dataObj = {
				page:page,
				limit:limit,
				BTime:BTime,
				ETime:ETime
			}
			// 数据加载 第一次
			gethistorybuyrecordAjaxFun(dataObj,function(data){
				var total = data.result.total;
				gethistorybuyrecordAddDataFun(data);
				// 分页
				PageCreate('#g-pagination',total,limit,1,function(pageNumber){
					if( total!=null ){ total = null; return; }
					dataObj.page = pageNumber;
					// 翻页后数据加载
					gethistorybuyrecordAjaxFun(dataObj,function(data){
						gethistorybuyrecordAddDataFun(data);
					});
			    });
			});
		}

		// 数据插入
		function gethistorybuyrecordAddDataFun(data){
			if( data.success ){
		    	var dataLi = '';
		    	$.each(data.result.datas, function(i,item){
		    		var buyCode = item.buyCode //: "56861a56887c22014a8b4684"
					var buyID = item.buyID //: "56862e74887c226e6a8b571f"
					var buyIP = item.buyIP //: "124.77.115.195"
					var buyName = item.buyName //: "13564****96"
					var buyNum = item.buyNum //: 1
					var buyTime = item.buyTime //: "2016-01-01 15:44:59.351"
					var goodsName = item.goodsName //: "【周黑鸭旗舰店_锁鲜装】盒装鸭翅190g*2 武汉特产官方食品零食"
					var period = item.period //: "1"
					var timeCodeVal = item.timeCodeVal //: ""
					var userWeb = item.userWeb //: "56757a39887c22034a8b4596";
		    		dataLi += '<ul class="Record_contents">'
			            +'<li class="time">'+buyTime+'</li>'
			            +'<li class="nem">'
			                +'<a class="blue" href="/yungou/member/index?id='+userWeb+'" target="_blank">'+buyName+'</a>'
			            +'</li>'
			            +'<li class="name"><a href="/yungou/product/index?id='+buyCode+'">'+goodsName+'</a></li>'
			            +'<li class="much">'+buyNum+'人次</li>'
			        +'</ul>';
		    	});
				
				if( dataLi=='' ){
					$("#g-pagination").hide();
					$("#recordList").html('<ul class="Recordloading" style="background:none;text-align: center;"><li>暂无数据！</li></ul>');
				}else{
					$("#g-pagination").show();
					$("#recordList").html(dataLi+'<div class="page_nav" id="g-pagination"></div>');
				}
		    }else{

		    }
		}

		//函数
		function gethistorybuyrecordAjaxFun(dataObj,Callback){
			if( !ajaxlack ) return; ajaxlack = false;
			$.ajax({
		        url: "/order/service/gethistorybuyrecord",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        Callback(data);
		    })
		    .fail(function() {
		        //alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		}


	}



})