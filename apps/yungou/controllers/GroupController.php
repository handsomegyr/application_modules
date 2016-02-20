<?php
namespace Webcms\Yungou\Controllers;

/**
 * 云购
 * 云购圈
 *
 * @author Kan
 *        
 */
class GroupController extends ControllerBase
{

    private $modelArticle = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->setLayout('index');
        $this->modelArticle = new \Webcms\Article\Models\Article();
    }

    /**
     * 新闻
     */
    public function newsAction()
    {
        $id = $this->get('id', '');
        $newsInfo = $this->modelArticle->getInfoById($id);
        $this->assign('newsInfo', $newsInfo);
    }
}

