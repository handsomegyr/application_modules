<?php 
use Webcms\Backend\Models\System\Menu;

$requestUrl=$this->view->moduleName.'/'.$this->view->controllerName;
$menu_list = ! empty($_SESSION['roleInfo']) ? $_SESSION['roleInfo']['menu_list'] : array();

$modelMenu = new Menu();
$menus = $modelMenu->getPrivilege($menu_list,$requestUrl);

if('admin/lottery/activity' == $requestUrl){
}else{
}

$icons1 = array(
    0=>"icon-cogs",
    1=>"icon-bookmark-empty",
    2=>"icon-briefcase",
    3=>"icon-table",
    4=>"icon-sitemap",
    5=>"icon-user"
);

$icons2 = array(
    0=>"icon-comments",
    1=>"icon-coffee",
    2=>"icon-time",
    3=>"icon-envelope-alt",
    4=>"icon-group",
    5=>"icon-user"
);
?>

			<ul class="page-sidebar-menu">

				<li>

					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->

					<div class="sidebar-toggler hidden-phone"></div>

					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->

				</li>

				<li>

					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->

					<form class="sidebar-search">

						<div class="input-box">

							<a href="javascript:;" class="remove"></a>

							<input type="text" placeholder="Search..." />

							<input type="button" class="submit" value=" " />

						</div>

					</form>

					<!-- END RESPONSIVE QUICK SEARCH FORM -->

				</li>

				<li class="start ">

					<a href="<?php echo $baseUrl; ?>admin/index/index">

					<i class="icon-home"></i> 

					<span class="title">Dashboard</span>

					</a>

				</li>
                <?php $index = 0;?>
                <?php foreach ($menus as $menukey=> $menu) {?>
                <?php if(true || !empty($menu['cando']) ){?>
                
                <li class="<?php if(!empty($menu['is_active'])){?>active<?php }?> ">

					<a href="javascript:;">

					<i class="<?php echo $icons1[$index % 6]?>"></i> 

					<span class="title"><?php echo ($menu['name'])?></span>

					<span class="arrow <?php if(!empty($menu['is_active'])){?>open<?php }?>"></span>

					</a>

					<ul class="sub-menu">
					    <?php $index2 = 0;?>					
                        <?php foreach ($menu['priv'] as $priv) {?>                        
                        <?php if(true || !empty($priv['cando'])){?>
						<li class="<?php if(!empty($priv['is_active'])){?>active<?php }?> " >

							<a href="<?php echo $baseUrl; ?><?php echo $priv['url']?>">

							<i class="<?php echo $icons2[$index2 % 6]?>"></i>

							<?php echo ($priv['name'])?></a>

						</li>
                        <?php }?>
                        <?php $index2++?>
                        <?php }?>
					</ul>

				</li>
				<?php }?>				
                <?php $index++?>
                <?php }?>

			</ul>                        