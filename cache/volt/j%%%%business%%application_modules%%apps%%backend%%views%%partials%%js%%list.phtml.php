<script>
var List = function () {

    return {
    	url : '<?php echo $baseUrl; ?><?php echo $moduleName; ?>/<?php echo $controllerName; ?>',
	    imagepath : '<?php echo $baseUrl; ?>backend/',
    	compileFilter:function() {
    		var data = new Object;
    		/**
    		for (var i in List.filter)
    		{
    			if (typeof(List.filter[i]) != "function" && typeof(List.filter[i]) != "undefined")
    			{
    				data[i] = encodeURIComponent(List.filter[i]);
    			}
    		}**/
    		
    		return data;
    	},
    	remove:function(id, cfm, opt) {
    		opt = "remove";

    		if (confirm(cfm))
    		{
    			var data = List.compileFilter();
    			data.id = encodeURIComponent(id);
    			$.ajax({
    				type: 'POST',
    				url: List.url + "/" + opt,
    				data:  data,
    				success: function(result){
        				if (result.message)
        				{
        					alert(result.message);
        				}
        				if (result.error == 0)
        				{
        					$('#example').DataTable().draw();
        				}
        			 },
    				dataType: "json"
    			});
    		}
    	},
    	toggle:function(obj, act, id, fieldname) {
    		val = ($(obj).text() =='yes') ? 0 : 1;
    		
    		var data = {}; 
    		data.fieldname = encodeURIComponent(val);
    		data.id = encodeURIComponent(id);
    		
    		$.ajax({
    			type: 'POST',
    			url: List.url + "/" + act,
    			data:  data,
    			success: function(result){
    				if (result.message)
    				{
    					alert(result.message);
    				}
    				if (result.error == 0)
    				{
        				text = (result.content > 0) ? 'yes' : 'no';
        				css = (result.content > 0) ? 'success' : 'danger';
    					$(obj).text(text);
    					$(obj).attr('class','label label-'+css);
    				}
    			 },
    			dataType: "json"
    		});
    	},
        edit:function(obj, act, id, fieldname) {
        	var tag = obj.firstChild.tagName;
        	if (typeof(tag) != "undefined" && tag.toLowerCase() == "input")
        	{
        		return;
        	}
        	
        	/* 保存原始的内容 */
        	var org = $(obj).html();
        	var val = $(obj).text();
        	
        	/* 创建一个输入框 */
        	var txt = $('<input type = "text" />');
        	txt.val(val);
        	txt.css("width", (obj.offsetWidth + 12) + "px");
        	/* 编辑区输入事件处理函数 */
        	txt.keypress(function(event){
        		if (event.which == 13)//enter
        		{
        			$(event.target).blur();
        			return false;
        		}
        		if (event.which == 27)//esc
        		{
        			$(event.target).parent().html(org);
        		}
        	});
        	/* 编辑区失去焦点的处理函数 */
        	txt.blur(function(event){
        		var newval = $.trim($(event.target).val());
        		if (newval.length > 0 && newval != val )
        		{
        			var data = new Object; 
        			data[fieldname] = encodeURIComponent(newval);
        			data.id = encodeURIComponent(id);

        			$.ajax({
        				type: 'POST',
        				url: List.url + "/" + act,
        				data:  data,
        				success: function(res){
        					if (res.message)
        					{
        						alert(res.message);
        					}
        					$(event.target).parent().html((res.error == 0) ? res.content : org);
        				 },
        				dataType: "json"
        			});
        		}
        		else
        		{
        			$(event.target).parent().html(org);
        		}
        	});
        	/* 隐藏对象中的内容，并将输入框加入到对象中 */
        	$(obj).empty();
        	$(obj).append(txt);
        	txt.focus();
        },
        call:function(id, cfm, opt) {
    		
    		if (confirm(cfm))
    		{
    			var data = List.compileFilter();
    			data.id = encodeURIComponent(id);
    			$.ajax({
    				type: 'POST',
    				url: List.url + "/" + opt,
    				data:  data,
    				success: function(result){
        				console.info(result);
        				if (result.message)
        				{
        					alert(result.message);
        				}
        				if (result.error == 0)
        				{							
        					$('#example').DataTable().draw();
        				}
        			 },
    				dataType: "json"
    			});
    		}
    	},
        //main function to initiate the module
        init: function () {
			
        	$('#sample_editable_1_new').click(function (e) {
                e.preventDefault();
                window.location.href = "<?php echo $baseUrl; ?><?php echo $moduleName; ?>/<?php echo $controllerName; ?>/add";           
            });

        	$('#sample_editable_1_export_csv').click(function (e) {
                e.preventDefault();
				var d = {};
			   <?php foreach ($this->view->schemas as $key => $field) {?> 
			   <?php if(!empty($field['search']['is_show'])){?>
			   <?php if(!empty($field['search']['condition_type']) && $field['search']['condition_type']=='period'){?>			   
			   d.<?php echo $key?> = encodeURIComponent($('#search_<?php echo $key?>_from').val()+'|'+$('#search_<?php echo $key?>_to').val());
			   <?php }else{?>
			   d.<?php echo $key?> = encodeURIComponent($('#search_<?php echo $key?>').val());
			   <?php }?>
			   <?php }?>
			   <?php }?>
			   var p = "";
			   $.each( d, function(i, n){
				  p +=("&"+i+"="+n);
				});
                window.location.href = "<?php echo $baseUrl; ?><?php echo $moduleName; ?>/<?php echo $controllerName; ?>/export?m=csv"+p;           
            });
            
        	//https://datatables.net/reference/option/#Features
      	   $('#example').dataTable( {
      		    //"dom": '<"top"i>rt<"bottom"flp><"clear">',
      		    //"jQueryUI": true,
      		    "dom": 'rt<"clear"ilp>',
        		//dom: 'Bfrtip',
      		    //dom": '<"H"lfr>t<"F"ip>',
          		"buttons": [ 'csv', 'excel', 'pdf', 'print' ],
      		    "processing": true,
         	    "serverSide": true,
      		    "searching":false,
         	    "paging": true,
         	    "ordering":true,
         	    "order": [ <?php echo $orderIdx?>, '<?php echo $orderBy?>' ],
      	   	    "displayStart": 0,
      	   	    "lengthMenu": [ 1, 5, 10, 15, 100 ],
      	   	    "lengthChange": true,
         	    "pageLength": 30,
      	   	    "pagingType": "full_numbers",	   	        
      	   	    "ajax": {
      	   	        "url": "<?php echo $baseUrl; ?><?php echo $moduleName; ?>/<?php echo $controllerName; ?>/query",
      	   	        "type": 'POST',
      		   	    "data": function ( d ) {
           		   	   <?php foreach ($this->view->schemas as $key => $field) {?> 
              		   <?php if(!empty($field['search']['is_show'])){?>
              		   <?php if(!empty($field['search']['condition_type']) && $field['search']['condition_type']=='period'){?>
    		           d.<?php echo $key?> = encodeURIComponent($('#search_<?php echo $key?>_from').val()+'|'+$('#search_<?php echo $key?>_to').val());
					   <?php }else{?>
					   d.<?php echo $key?> = encodeURIComponent($('#search_<?php echo $key?>').val());
    		           <?php }?>
    		           <?php }?>
    		           <?php }?>
    		        }
      	   	    },
     	        "columns": [
      	        <?php foreach ($this->view->schemas as $key => $field) {?>
        	    <?php if(!empty($field['list']['is_show'])){?>
     	            {
  	   	            "data": "<?php echo isset($field['list']['list_data_name'])?$field['list']['list_data_name']:$key ?>" ,
  	   	            "name" :"<?php echo $key ?>",
  	   	            <?php if($field['data']['type'] == "boolean" && isset($field['list']['list_type']) && $field['list']['list_type'] == 1){ ?>
                    "render": function ( data, type, full, meta ) {
                         //console.info(data);
                       if (data == 1) {
                              return '<span <?php if(isset($field['list']['ajax'])){?>onclick="List.toggle(this, \'<?php echo $field['list']['ajax'] ?>\', \''+full['_id']+'\', \'<?php echo $key ?>\')"<?php }?>class="label label-success">yes</span>';
                          } else {
                              return '<span <?php if(isset($field['list']['ajax'])){?>onclick="List.toggle(this, \'<?php echo $field['list']['ajax'] ?>\', \''+full['_id']+'\', \'<?php echo $key ?>\')"<?php }?>class="label label-danger">no</span>';
                          };
                      }
  	  	   	       <?php }elseif($field['data']['type'] == "file" && isset($field['list']['render']) && $field['list']['render'] == 'img'){ ?>
  	               "render": function ( data, type, full, meta ) {
  	                    //console.info(data);
    	            	<?php 
    	            	$path = "";
                        if(!empty($field['data'][$field['data']['type']])){
                	       $fileInfo =$field['data'][$field['data']['type']];
                	       $path = empty($fileInfo['path'])?'':trim($fileInfo['path'],'/').'/';
                	    }
   	            	    ?>
      	            	return '<img src="<?php echo $baseUrl; ?>service/file/index?upload_path=<?php echo trim($path,'/') ?>&id='+data+'&w=200&h=150" style="max-height: 150px;" alt="">';
  	                 }
  	  	   	       <?php }elseif(isset($field['list']['items'])){ ?>
  	               "render": function ( data, type, full, meta ) {
  	                   //console.info(data);
    	            	 if (false) {
                        	 return '<span class="label label-info"></span>';
    	            	 <?php 
                         $items = is_callable($field['list']['items'])?$field['list']['items']():$field['list']['items'];
                         ?>
                         <?php foreach ($items as $item) {?>
                         }else if (data == '<?php echo $item['value']?>') {
                        	 return '<span class="label label-info"><?php echo $item['name']?></span>';
                         <?php } ?>               
                         } else {
                        	 return '<span class="label label-info"></span>';
                         };
  	                 }
    	           <?php }elseif(isset($field['list']['ajax'])){ ?>
                   "render": function ( data, type, full, meta ) {
                        return '<span onclick="List.edit(this, \'<?php echo $field['list']['ajax'] ?>\', \''+full['_id']+'\', \'<?php echo $key ?>\')" >'+data+'</span>';
                     }
   	                <?php }?>
    	  	   	   },
      	        <?php }?>
  		   	    <?php }?>
      	  		   	{ 
    	   	            "data": "operation",
    	   	            "orderable":false,
    	   	            "searchable":false,
       	   	            "render": function ( data, type, full, meta ) {
                            var id = full['_id'];
                     	    var editOp = '<a href="<?php echo $baseUrl; ?><?php echo $moduleName; ?>/<?php echo $controllerName; ?>/edit?id='+id+'" class="btn mini purple"><i class="icon-edit"></i> Edit</a>';
                     	    var deleteOp = '<a href="javascript:;" onclick="List.remove(\''+id+'\', \'你确定要删除这条记录吗？\')" class="btn mini black"><i class="icon-trash"></i> Delete</a>';
                     	    return editOp +'<br/>'+ deleteOp;
                        }
    		   	    }
     	        ]
     	    } );
        }

    };

}();
</script>