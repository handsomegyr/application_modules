                        <!-- BEGIN VALIDATION STATES-->

						<div class="portlet box blue">

							<div class="portlet-title">

								<div class="caption"><i class="icon-reorder"></i><?php echo $this->view->formName?></div>

								<div class="tools">

									<a href="javascript:;" class="collapse"></a>

								</div>

							</div>							
							<div class="portlet-body form" style="display: block;">

								<!-- BEGIN FORM-->

								<form action="<?php echo $form_act; ?>" enctype="multipart/form-data" method="post" id="form_sample_2" class="form-horizontal form-bordered form-row-stripped">

									<div class="alert alert-error hide">

										<button class="close" data-dismiss="alert"></button>

										You have some form errors. Please check below.

									</div>

									<div class="alert alert-success hide">

										<button class="close" data-dismiss="alert"></button>

										Your form validation is successful!

									</div>
                                <?php foreach ($this->view->schemas as $key => $field) {?>
                                    <?php
                                        if(empty($field['form']['is_show'])){
                                            continue;
                                        }
                                    ?>
									<div class="control-group">
									<?php if(!isset($field['form']['partial'])){?>
                                        <?php //print_r($field);die('xxx');?>
                                        
                                        <?php if($field['form']['input_type'] == "hidden"){?>
                                        
                                        <input type="hidden" name="<?php echo ($key=='_id')?'id':$key?>" value="<?php echo $this->view->row[$key]?>">
                                        
                                        <?php }else{ ?>
                                    
										<label class="control-label"><?php echo $field['name']?> <?php if($field['validation']['required']){?><span class="required">*</span><?php }?></label>

										<div class="controls">
										<?php if($field['form']['input_type'] == "text"){?>
											<input class="span12 m-wrap" type="text" name="<?php echo $key?>" <?php if($field['validation']['required']){?>data-required="1"<?php }?> value="<?php echo $this->view->row[$key]?>"/>
                                        <?php }elseif($field['form']['input_type'] == "number"){?>
                                            <input class="span12 m-wrap" type="text" name="<?php echo $key?>" <?php if($field['validation']['required']){?>data-required="1"<?php }?> id="mask_number2" value="<?php echo $this->view->row[$key]?>"/>
                                        <?php }elseif($field['form']['input_type'] == "currency"){?>
                                            <input class="span12 m-wrap" type="text" name="<?php echo $key?>" <?php if($field['validation']['required']){?>data-required="1"<?php }?> id="mask_currency" value="<?php echo $this->view->row[$key]?>"/>
                                        <?php }elseif($field['form']['input_type'] == "decimal"){?>
                                            <input class="span12 m-wrap" type="text" name="<?php echo $key?>" <?php if($field['validation']['required']){?>data-required="1"<?php }?> id="mask_decimal" value="<?php echo $this->view->row[$key]?>"/>
                                        <?php }elseif($field['form']['input_type'] == "textarea"){?>
                                            <?php 
                                            if($field['data']['type'] == "json"){
                                            	$value= empty($this->view->row[$key])?"{}":json_encode($this->view->row[$key]);
                                            }elseif($field['data']['type'] == "array"){
                                            	$value=empty($this->view->row[$key])?"":implode(',',$this->view->row[$key]);
                                            }else{
                                                $value=nl2br($this->view->row[$key]);
                                            }
                                            ?>
                                            <textarea class="span12 m-wrap" name="<?php echo $key?>" rows="6" <?php if($field['validation']['required']){?>data-required="1"<?php }?>><?php echo $value?></textarea>
                                        <?php }elseif($field['form']['input_type'] == "datetimepicker"){?>
                                            <div class="input-append date form_datetime" data-date="<?php echo date("Y-m-d H:i:s")?>">

												<input class="m-wrap" size="16" type="text" name="<?php echo $key?>" <?php if($field['validation']['required']){?>data-required="1"<?php }?> value="<?php echo date("Y-m-d H:i:s",$this->view->row[$key]->sec)?>">

												<span class="add-on"><i class="icon-remove"></i></span>

												<span class="add-on"><i class="icon-calendar"></i></span>

											</div>
                                        <?php }elseif($field['form']['input_type'] == "select"){?>
                                            <?php if(!isset($field['form']['select']) || empty($field['form']['select']['multiple'])){?>											
											<select class="span12 m-wrap"  id="select2_<?php echo $key?>_sample6" name="<?php echo $key?>" data-placeholder="">

												<option value="">请选择...</option>

												<?php 
												if(empty($field['form']['cascade'])){
                                                    $items = is_callable($field['form']['items'])?$field['form']['items']():$field['form']['items'];
                                                }else{
                                                    $cascade=$field['form']['cascade'];
                                                    //die('$cascade'.$cascade.$this->view->row[$cascade]);
                                                    $items = is_callable($field['form']['items'])?$field['form']['items']($this->view->row[$cascade]):$field['form']['items'];
                                                }
                                                foreach ($items as $value=> $name) {?>												
												<option value="<?php echo $value?>" <?php if(in_array($value,array($this->view->row[$key]))){?>selected<?php }?>><?php echo $name?></option>
                                                <?php }?>

											</select>
                                            <?php }else{?>
                                            <select class="chosen span12"  id="select2_<?php echo $key?>_sample6" name="<?php echo $key?>[]" data-placeholder="" multiple="multiple">
                                                <option value=""></option>
                                                <?php 
                                                $items = is_callable($field['form']['items'])?$field['form']['items']():$field['form']['items'];
                                                foreach ($items as $value=> $name) {?>												
												<option value="<?php echo $value?>" <?php if(in_array($value,$this->view->row[$key])){?>selected<?php }?>><?php echo $name?></option>
                                                <?php }?>
											</select>
											<?php }?>
										<?php }elseif($field['form']['input_type'] == "select2"){?>
										  <?php if(isset($field['form']['select']) && !empty($field['form']['select']['is_remote_load'])){?>
											<select class="span12 m-wrap" id="select2_<?php echo $key?>_sample6" name="<?php echo $key?>" data-placeholder="">	
												<?php 
												if(empty($field['form']['cascade'])){
                                                    $items = is_callable($field['form']['items'])?$field['form']['items']($this->view->row[$key]):$field['form']['items'];
                                                }else{
                                                    $cascade=$field['form']['cascade'];
                                                    //die('$cascade'.$cascade.$this->view->row[$cascade]);
                                                    $items = is_callable($field['form']['items'])?$field['form']['items']($this->view->row[$cascade]):$field['form']['items'];
                                                }
                                                foreach ($items as $value=> $name) {?>												
												<option value="<?php echo $value?>" <?php if(in_array($value,array($this->view->row[$key]))){?>selected="selected"<?php }?>><?php echo $name?></option>
                                                <?php }?>
											</select>
											<?php }elseif(!isset($field['form']['select']) || empty($field['form']['select']['multiple'])){?>											
											<select class="span12 m-wrap"  id="select2_<?php echo $key?>_sample6"  name="<?php echo $key?>" data-placeholder="">

												<option value="">请选择...</option>

												<?php 
												if(empty($field['form']['cascade'])){
                                                    $items = is_callable($field['form']['items'])?$field['form']['items']():$field['form']['items'];
                                                }else{
                                                    $cascade=$field['form']['cascade'];
                                                    //die('$cascade'.$cascade.$this->view->row[$cascade]);
                                                    $items = is_callable($field['form']['items'])?$field['form']['items']($this->view->row[$cascade]):$field['form']['items'];
                                                }
                                                foreach ($items as $value=> $name) {?>												
												<option value="<?php echo $value?>" <?php if(in_array($value,array($this->view->row[$key]))){?>selected<?php }?>><?php echo $name?></option>
                                                <?php }?>

											</select>
                                            <?php }else{?>
                                            <select class="chosen span12" name="<?php echo $key?>[]" data-placeholder="" multiple="multiple">
                                                <option value=""></option>
                                                <?php 
                                                $items = is_callable($field['form']['items'])?$field['form']['items']():$field['form']['items'];
                                                foreach ($items as $value=> $name) {?>												
												<option value="<?php echo $value?>" <?php if(in_array($value,$this->view->row[$key])){?>selected<?php }?>><?php echo $name?></option>
                                                <?php }?>
											</select>
											<?php }?>
                                        <?php }elseif($field['form']['input_type'] == "radio"){?>
                                            <?php 
                                            $items = is_callable($field['form']['items'])?$field['form']['items']():$field['form']['items'];
                                            foreach ($items as $item) {?>
                                            <label class="radio"><input type="radio" name="<?php echo $key?>" value="<?php echo $item['value']?>" <?php if($this->view->row[$key] == $item['value']){?>checked<?php }?> /><?php echo $item['name']?></label>
                                            <?php }?>
										<?php }elseif($field['form']['input_type'] == "ueditor"){?>
										    <script type="text/plain" id="<?php echo $key?>" name="<?php echo $key?>" style="width: 100%; height:250px;"><?php echo $this->view->row[$key]?></script>
										<?php }elseif($field['form']['input_type'] == "ckeditor"){?>
										    <textarea class="span12 ckeditor m-wrap" name="<?php echo $key?>" rows="6"><?php echo $this->view->row[$key]?></textarea>
                                        <?php }elseif($field['form']['input_type'] == "file"){?>
                                            <div class="fileupload <?php if(empty($this->view->row[$key])){?>fileupload-new<?php }else{?>fileupload-exists<?php }?>" data-provides="fileupload">
                                                <?php if(!empty($this->view->row[$key])){?>
                                                <input type="hidden" value="" name="">
												<?php }?>
												
												<div class="fileupload-new thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">

													<img src="<?php echo $baseUrl; ?>service/file/index?id=noimg.png&w=200&h=150" style="max-height: 150px;" alt="" />

												</div>
												<div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                                    <?php 
                                                    $path ="";
                                                    if(!empty($field['data'][$field['data']['type']])){
                              	            	        $fileInfo =$field['data'][$field['data']['type']];
                              	            	        $path = empty($fileInfo['path'])?'':trim($fileInfo['path'],'/').'/';
                              	            	    }
                              	            	    ?>
                              	            	    <?php if(isset($this->view->row[$key])){?>
                                                    <img src="<?php echo $baseUrl; ?>service/file/index?upload_path=<?php echo $path?>&id=<?php echo $this->view->row[$key]?>&w=200&h=150" style="max-height: 150px;">
                                                    <?php }else{?>
                                                    <img src="<?php echo $baseUrl; ?>service/file/index?id=noimg.png&w=200&h=150" style="max-height: 150px;">
                                                    <?php }?>
                                                </div>
												<div>
													<span class="btn btn-file"><span class="fileupload-new">Select image</span>

													<span class="fileupload-exists">Change</span>

													<input type="file" name="<?php echo $key?>" class="default" /></span>

													<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>

												</div>

											</div>

                                            
										<?php }?>
										</div>
                                        
                                        <?php }?>
                                    <?php }else{
                                        $this->partial($field['form']['partial']);
                                    }?>
									</div>
									
                                <?php }?>									
									<div class="form-actions">
									
										<button type="submit" class="btn blue"><i class="icon-ok"></i> Save</button>

										<button type="button" id="cancelBtn" class="btn">Cancel</button>

									</div>

								</form>

								<!-- END FORM-->

							</div>
                        </div>
                        <?php $this->partial("partials/js/form") ?>
						<!-- END VALIDATION STATES-->