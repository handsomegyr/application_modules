					<li class="dropdown user">

						<a href="#" class="dropdown-toggle" data-toggle="dropdown">

						<img alt="" src="<?php echo $resourceUrl; ?>media/image/avatar1_small.jpg" />

						<span class="username"><?php echo $_SESSION ['admin_name']?></span>

						<i class="icon-angle-down"></i>

						</a>

						<ul class="dropdown-menu">
                            
							<li><a href="extra_profile.html"><i class="icon-user"></i> My Profile</a></li>

							<li><a href="page_calendar.html"><i class="icon-calendar"></i> My Calendar</a></li>

							<li><a href="inbox.html"><i class="icon-envelope"></i> My Inbox(3)</a></li>

							<li><a href="#"><i class="icon-tasks"></i> My Tasks</a></li>

							<li class="divider"></li>

							<li><a href="extra_lock.html"><i class="icon-lock"></i> Lock Screen</a></li>

							<li><a href="<?php echo $baseUrl; ?>admin/index/logout"><i class="icon-key"></i> Log Out</a></li>

						</ul>

					</li>
