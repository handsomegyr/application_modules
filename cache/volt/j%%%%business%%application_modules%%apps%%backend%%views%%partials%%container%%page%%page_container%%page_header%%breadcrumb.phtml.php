<?php
$url1= "index/index";
$url2= $this->view->controllerName."/".$this->view->actionName;

$title = "";
$smalltitle = "";

if ($this->view->moduleName == "admin/system") {
    $title = "系统管理";
    if ($this->view->controllerName == "menu") {
        $smalltitle .= "菜单";
    }elseif ($this->view->controllerName == "role") {
        $smalltitle .= "角色";
    }elseif ($this->view->controllerName == "user") {
        $smalltitle .= "用户";
    }
}elseif ($this->view->moduleName == "admin/lottery") {
    $title = "抽奖管理";
    if ($this->view->controllerName == "activity") {
        $smalltitle .= "活动管理";
    }elseif ($this->view->controllerName == "code") {
        $smalltitle .= "奖品券码管理";
    }elseif ($this->view->controllerName == "exchange") {
        $smalltitle .= "中奖管理";
    }elseif ($this->view->controllerName == "identity") {
        $smalltitle .= "参与者管理";
    }elseif ($this->view->controllerName == "limit") {
        $smalltitle .= "限制管理";
    }elseif ($this->view->controllerName == "prize") {
        $smalltitle .= "奖品管理";
    }elseif ($this->view->controllerName == "rule") {
        $smalltitle .= "概率管理";
    }elseif ($this->view->controllerName == "source") {
        $smalltitle .= "访问来源管理";
    }
}elseif ($this->view->moduleName == "admin/weixin") {
    $title = "微信管理";
    if ($this->view->controllerName == "keyword") {
        $smalltitle .= "关键词管理";
    }elseif ($this->view->controllerName == "reply") {
        $smalltitle .= "回复管理";
    }elseif ($this->view->controllerName == "replytype") {
        $smalltitle .= "回复类型管理";
    }elseif ($this->view->controllerName == "menu") {
        $smalltitle .= "自定义菜单管理";
    }elseif ($this->view->controllerName == "menutype") {
        $smalltitle .= "菜单类型管理";
    }elseif ($this->view->controllerName == "application") {
        $smalltitle .= "应用管理";
    }elseif ($this->view->controllerName == "user") {
        $smalltitle .= "用户管理";
    }elseif ($this->view->controllerName == "source") {
        $smalltitle .= "原始数据管理";
    }elseif ($this->view->controllerName == "msgtype") {
        $smalltitle .= "消息类型管理";
    }elseif ($this->view->controllerName == "qrcode") {
        $smalltitle .= "二维码推广场景管理";
    }elseif ($this->view->controllerName == "scene") {
        $smalltitle .= "二维码场景管理";
    }elseif ($this->view->controllerName == "notkeyword") {
        $smalltitle .= "非关键字管理";
    }elseif ($this->view->controllerName == "page") {
        $smalltitle .= "自定义页面管理";
    }elseif ($this->view->controllerName == "gender") {
        $smalltitle .= "性别管理";
    }elseif ($this->view->controllerName == "scripttracking") {
        $smalltitle .= "执行时间跟踪统计管理";
    }elseif ($this->view->controllerName == "callbackurls") {
        $smalltitle .= "回调地址安全域名管理";
    }elseif ($this->view->controllerName == "subscribeuser") {
        $smalltitle .= "关注用户管理";
    }
}else{
    if ($this->view->controllerName == "index" && $this->view->actionName == "index") {
        $title = "Dashbord";
        $smalltitle = "";
    } 
}

if ($this->view->actionName == "list") {
    $smalltitle .= "列表";
}elseif ($this->view->actionName == "add") {
    $smalltitle .= "追加";
}elseif ($this->view->actionName == "edit") {
    $smalltitle .= "编辑";
}
?>

                        <ul class="breadcrumb">

							<li>

								<i class="icon-home"></i>

								<a href="<?php echo $baseUrl; ?>admin/index/index">管理中心</a> 
                                <?php if(!empty($title)){?>
								<i class="icon-angle-right"></i>
                                <?php }?>
							</li>
							<?php if(!empty($title)){?>
							<li>
								<a href="<?php echo $baseUrl; ?><?php echo $moduleName; ?>/<?php echo $url1; ?>"><?php echo $title?></a>
                                <?php if(!empty($smalltitle)){?>
								<i class="icon-angle-right"></i>
                                <?php }?>
							</li>
							<?php if(!empty($smalltitle)){?>
							<li><a href="<?php echo $baseUrl; ?><?php echo $moduleName; ?>/<?php echo $url2; ?>"><?php echo $smalltitle?></a></li>
                            <?php }?>
							<?php }?>
						</ul>