<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD -->

<head>
	<meta property="qc:admins" content="665756775235364146636" />
	<meta charset="utf-8" />

	<title>Metronic | Admin Template</title>

	<meta content="width=device-width, initial-scale=1.0" name="viewport" />

	<meta content="" name="description" />

	<meta content="" name="author" />

	<!-- BEGIN GLOBAL MANDATORY STYLES -->

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/bootstrap.min.css"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/bootstrap-responsive.min.css"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/font-awesome.min.css"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/style-metro.css"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/style.css"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/style-responsive.css"/>

	<link rel="stylesheet" type="text/css"  href="<?php echo $resourceUrl; ?>media/css/default.css"id="style_color"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/uniform.default.css"/>

	<!-- END GLOBAL MANDATORY STYLES -->
	
    <!-- BEGIN PAGE LEVEL STYLES -->

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/bootstrap-fileupload.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/jquery.gritter.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/chosen.css" />     
	<!--<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/select2_metro.css" /> -->
	<link rel="stylesheet" type="text/css" href="<?php echo $commonResourceUrl; ?>select2/dist/css/select2.min.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/jquery.tagsinput.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/clockface.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/bootstrap-wysihtml5.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/datepicker.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/timepicker.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/colorpicker.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/bootstrap-toggle-buttons.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/daterangepicker.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/datetimepicker.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/multi-select-metro.css" />
	
	<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.4/css/jquery.dataTables.css" /> -->
	<link rel="stylesheet" type="text/css" href="<?php echo $commonResourceUrl; ?>DataTables-1.10.9/media/css/jquery.dataTables.min.css" />
	
	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/search.css" />
	
	<!-- <link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/DT_bootstrap.css" /> -->
	
	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/jquery.gritter.css"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/daterangepicker.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/fullcalendar.css"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/jqvmap.css"media="screen"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/jquery.easy-pie-chart.css" media="screen"/>
	
	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/error.css"/>
	
	<link rel="stylesheet" type="text/css" href="<?php echo $resourceUrl; ?>media/css/bootstrap-modal.css"/>

	<!-- END PAGE LEVEL STYLES -->
	
	<link rel="shortcut icon" href="<?php echo $resourceUrl; ?>media/image/favicon.ico" />
	
	<script>
    var thirdPartys = {};
    </script>
	
</head>

<!-- END HEAD -->

<!-- BEGIN BODY -->

<body class="page-header-fixed">

	<!-- BEGIN HEADER -->

	<div class="header navbar navbar-inverse navbar-fixed-top">

		<!-- BEGIN TOP NAVIGATION BAR -->

		<div class="navbar-inner">

			<div class="container-fluid">

				<!-- BEGIN LOGO -->
                <?php $this->partial("partials/header/top_navigation_bar/logo") ?>
				<!-- END LOGO -->

				<!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <?php $this->partial("partials/header/top_navigation_bar/responsive_menu_toggler") ?>
                <!-- END RESPONSIVE MENU TOGGLER -->            

				<!-- BEGIN TOP NAVIGATION MENU --> 
				
				<ul class="nav pull-right">

					<!-- BEGIN NOTIFICATION DROPDOWN -->
                    <?php $this->partial("partials/header/top_navigation_bar/top_navigation_menu/notification_dropdown") ?>
					<!-- END NOTIFICATION DROPDOWN -->

					<!-- BEGIN INBOX DROPDOWN -->
                    <?php $this->partial("partials/header/top_navigation_bar/top_navigation_menu/inbox_dropdown") ?>
					<!-- END INBOX DROPDOWN -->

					<!-- BEGIN TODO DROPDOWN -->
                    <?php $this->partial("partials/header/top_navigation_bar/top_navigation_menu/todo_dropdown") ?>
					<!-- END TODO DROPDOWN -->

					<!-- BEGIN USER LOGIN DROPDOWN -->
					<?php $this->partial("partials/header/top_navigation_bar/top_navigation_menu/user_login_dropdown") ?>
					<!-- END USER LOGIN DROPDOWN -->

				</ul>

				<!-- END TOP NAVIGATION MENU --> 

			</div>

		</div>

		<!-- END TOP NAVIGATION BAR -->

	</div>

	<!-- END HEADER -->

	<!-- BEGIN CONTAINER -->   

	<div class="page-container row-fluid">

		<!-- BEGIN SIDEBAR -->
		<div class="page-sidebar nav-collapse collapse">

			<!-- BEGIN SIDEBAR MENU -->        

            <?php $this->partial("partials/container/sidebar/sidebar_menu") ?>

			<!-- END SIDEBAR MENU -->

		</div>

		<!-- END SIDEBAR -->

		<!-- BEGIN PAGE -->

		<div class="page-content">

			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			<div id="portlet-config" class="modal hide">

				<div class="modal-header">

					<button data-dismiss="modal" class="close" type="button"></button>

					<h3>portlet Settings</h3>

				</div>

				<div class="modal-body">

					<p>Here will be a configuration form</p>

				</div>

			</div>

			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			<!-- BEGIN PAGE CONTAINER-->

			<div class="container-fluid">

				<!-- BEGIN PAGE HEADER-->

				<div class="row-fluid">

					<div class="span12">

						<!-- BEGIN STYLE CUSTOMIZER -->						
                        <?php $this->partial("partials/container/page/page_container/page_header/style_customizer") ?>
						<!-- END BEGIN STYLE CUSTOMIZER --> 

						<!-- BEGIN PAGE TITLE & BREADCRUMB-->
                        <?php $this->partial("partials/container/page/page_container/page_header/page_title") ?>
						
                        <?php $this->partial("partials/container/page/page_container/page_header/breadcrumb") ?>
                        
						<!-- END PAGE TITLE & BREADCRUMB-->

					</div>

				</div>

				<!-- END PAGE HEADER-->

				<!-- BEGIN PAGE CONTENT-->

				<div class="row-fluid">

					<div class="span12">

						<!-- Blank page content goes here -->
						
						<?php echo $this->getContent(); ?>
	
					</div>

				</div>

				<!-- END PAGE CONTENT-->

			</div>

			<!-- END PAGE CONTAINER--> 

		</div>

		<!-- END PAGE -->    

	</div>

	<!-- END CONTAINER -->

	<!-- BEGIN FOOTER -->
    <?php $this->partial("partials/footer") ?>
	<!-- END FOOTER -->

	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->

	<!-- BEGIN CORE PLUGINS -->
	<!-- <script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery-1.10.1.min.js"></script> -->
    <!-- <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script> -->
    <script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery-1.11.1.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery-migrate-1.2.1.min.js"></script>

	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery-ui-1.10.1.custom.min.js"></script>  
	
	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap.min.js"></script>

	<!--[if lt IE 9]>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/excanvas.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/respond.min.js"></script>  

	<![endif]-->   

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.slimscroll.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.blockui.min.js"></script>  

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.cookie.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.uniform.min.js" ></script>

	<!-- END CORE PLUGINS -->
    
    <!-- BEGIN PAGE LEVEL PLUGINS -->

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.validate.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/additional-methods.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->    

	<!-- BEGIN PAGE LEVEL PLUGINS -->

	<!--<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/ckeditor.js"></script>-->  

	<!-- ckeditor start -->
	<script type="text/javascript" charset="utf-8" src="<?php echo $commonResourceUrl; ?>ckeditor/ckeditor.js"></script>
    <!-- ckeditor end -->
    
    <!-- ueditor start -->
	<script type="text/javascript" charset="utf-8" src="<?php echo $commonResourceUrl; ?>ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo $commonResourceUrl; ?>ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo $commonResourceUrl; ?>ueditor/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="<?php echo $commonResourceUrl; ?>ueditor/lang/zh-cn/zh-cn.js"></script>
    <!-- ueditor end -->
    
	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap-fileupload.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/chosen.jquery.min.js"></script>

	<!-- <script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/select2.min.js"></script>-->
	<script type="text/javascript" src="<?php echo $commonResourceUrl; ?>select2/dist/js/select2.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/wysihtml5-0.3.0.js"></script> 

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap-wysihtml5.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.tagsinput.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.toggle.buttons.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap-datepicker.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap-datetimepicker.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/clockface.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/date.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/daterangepicker.js"></script> 

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap-colorpicker.js"></script>  

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap-timepicker.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.inputmask.bundle.min.js"></script>   

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.input-ip-address-control-1.0.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.multi-select.js"></script> 

	<!--<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.dataTables.min.js"></script>-->
	<!--<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/DT_bootstrap.js"></script>-->
	<!--<script type="text/javascript" src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js" ></script>-->
	<script type="text/javascript" src="<?php echo $commonResourceUrl; ?>DataTables-1.10.9/media/js/jquery.dataTables.min.js"></script>
	
	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.vmap.js"></script>   

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.vmap.russia.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.vmap.world.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.vmap.europe.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.vmap.germany.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.vmap.usa.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.vmap.sampledata.js"></script>  

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.flot.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.flot.resize.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.pulsate.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.gritter.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/fullcalendar.min.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.easy-pie-chart.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/jquery.sparkline.min.js"></script> 
	
	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap-modal.js"></script>

	<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/bootstrap-modalmanager.js" ></script>
		
	<!-- END PAGE LEVEL PLUGINS -->
	
	
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
        
	<!-- <script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/app.js"></script> -->
    <!--<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/form-validation.js"></script> --> 
	<!--<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/form-components.js"></script> -->
	<!--<script type="text/javascript" src="<?php echo $resourceUrl; ?>media/js/table-advanced.js"></script>  -->
	
	<!-- END PAGE LEVEL SCRIPTS -->
	<?php $this->partial("partials/js/app") ?>
	<script>

		jQuery(document).ready(function() {   
		   if(typeof App=='object'){
			   App.init();
		   }
			
		   //FormValidation.init();
		   if(typeof FormComponents=='object'){
			   FormComponents.init();
		   }
		   //TableAdvanced.init();
		   
		   if(typeof FormValidation=='object'){
			   FormValidation.init();
		   }
		   if(typeof Search=='object'){
			   Search.init();
		   }
		   if(typeof List=='object'){
			   List.init();
		   }
		   
		   for (x in thirdPartys){
				thirdPartys[x].init();
		   }
		   
		   if(typeof Index=='object'){
    		   Index.init();
    
    		   Index.initJQVMAP(); // init index page's custom scripts
    
    		   Index.initCalendar(); // init index page's custom scripts
    
    		   Index.initCharts(); // init index page's custom scripts
    
    		   Index.initChat();
    
    		   Index.initMiniCharts();
    
    		   Index.initDashboardDaterange();
    
    		   Index.initIntro();
		   }
		});
	</script> 
	
	<?php $this->partial("partials/js/message") ?>   
	<!-- END JAVASCRIPTS -->

<!-- <script type="text/javascript">  var _gaq = _gaq || [];  _gaq.push(['_setAccount', 'UA-37564768-1']);  _gaq.push(['_setDomainName', 'keenthemes.com']);  _gaq.push(['_setAllowLinker', true]);  _gaq.push(['_trackPageview']);  (function() {    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;    ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  })();</script> -->
</body>

<!-- END BODY -->

</html>
