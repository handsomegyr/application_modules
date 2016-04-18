<?php
namespace App\Backend\Controllers;

/**
 * @title({name="错误中心"})
 *
 * @name 错误中心
 */
class ErrorController extends \App\Backend\Controllers\ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Oops!');
        parent::initialize();
    }

    /**
     * @title({name="显示403错误页面"})
     *
     * @name 显示403错误页面
     */
    public function show404Action()
    {
        $this->disableLayout();
    }

    /**
     * @title({name="显示401错误页面"})
     *
     * @name 显示401错误页面
     */
    public function show401Action()
    {
        $this->disableLayout();
    }

    /**
     * @title({name="显示500错误页面"})
     *
     * @name 显示500错误页面
     */
    public function show500Action()
    {
        $this->disableLayout();
    }

    /**
     */
    public function messageAction()
    {}
}
