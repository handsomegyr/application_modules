                <div class="row-fluid">

					<div class="span12 page-500">

						<div class=" number">

						<?php if($this->view->msg_type==0){ //information ?>
						      200
                        <?php } elseif ($this->view->msg_type==1){ //warning?>
                            200
                        <?php } elseif ($this->view->msg_type==2){ //confirm?>
                            200
                        <?php } else{ ?>
							500
                        <?php }?>
						</div>

						<div class=" details">

							<h3><?php echo $msg_detail; ?></h3>
							<p>
								<?php if($this->view->auto_redirect){echo '如果您不做出选择，将在 <span id="spanSeconds">3</span> 秒后跳转到第一个链接地址。';}?><br />
                            </p>
							
							<p>
								<?php foreach ($this->view->links as $link) {?>
                                <a href="<?php echo $link['href']; ?>" <?php if(isset($link['target'])){?>target="<?php echo $link['target']; ?>" <?php }?>><?php echo $link['text']; ?></a>
                                <?php }?>
							</p>

						</div>

					</div>

				</div>
				
