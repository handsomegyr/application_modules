<?php

namespace App\Backend\Submodules\System\Controllers;

use LogViewer;

/**
 * @title({name="Logviewer管理"})
 *
 * @name Logviewer管理
 */
class LogviewerController extends \App\Backend\Controllers\FormController
{

    protected $objLogViewer = null;
    public function initialize()
    {
        parent::initialize();
        require_once APP_PATH . '/library/LogViewer.php';
    }

    protected function getName()
    {
        return 'Log Viewer';
    }

    protected function getModel()
    {
        return array();
    }

    /**
     * @title({name="显示列表页面"})
     *
     * @name 显示列表页面
     */
    public function listAction()
    {
        try {
            $offset = $this->request->get('offset', array(
                'trim',
                'string'
            ), '0');
            $offset = intval($offset);

            $defaultLogFile = date('Y-m-d') . '.log';
            $logfile = $this->request->get('file', array(
                'trim',
                'string'
            ), $defaultLogFile);
            if (empty($logfile)) {
                $logfile = $defaultLogFile;
            }
            if (!file_exists(APP_PATH . 'public/logs/' . $logfile)) {
                $logfile = null;
            }
            $viewer = new LogViewer(APP_PATH . 'public/logs/', $logfile);
            // 'logs'      => $viewer->fetch($offset),
            // 'logFiles'  => $viewer->getLogFiles(),
            // 'fileName'  => $viewer->file,
            // 'end'       => $viewer->getFilesize(),
            // 'tailPath'  => route('log-viewer-tail', ['file' => $viewer->file]),
            // 'prevUrl'   => $viewer->getPrevPageUrl(),
            // 'nextUrl'   => $viewer->getNextPageUrl(),
            // 'filePath'  => $viewer->getFilePath(),
            // 'size'      => static::bytesToHuman($viewer->getFilesize()),
            // 'file'      => $file,

            $logs = $viewer->fetch($offset);
            $logFiles = $viewer->getLogFiles();
            $fileName  = $viewer->file;
            $size  = bytesToHuman($viewer->getFilesize());
            $filePath = $viewer->getFilePath();
            $modified = filemtime($filePath);
            $modified = date('Y-m-d H:i:s', $modified);
            $prevUrl = $viewer->getPrevPageUrl();
            $nextUrl = $viewer->getNextPageUrl();
            $end = $viewer->getFilesize();
            
            $this->view->setVar('logs', $logs);
            $this->view->setVar('logFiles', $logFiles);
            $this->view->setVar('fileName', $fileName);
            $this->view->setVar('size', $size);
            $this->view->setVar('modified', $modified);
            $this->view->setVar('prevUrl', $prevUrl);
            $this->view->setVar('nextUrl', $nextUrl);
            $this->view->setVar('end', (empty($end) ? 0 : $end));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="删除文件"})
     *
     * @name 删除文件
     */
    public function deleteAction()
    {
        // http://www.myapplicationmodule.com/admin/system/logviewer/delete?file=xxx.log
        try {
            $this->view->disable();

            $file = $this->request->get('file', array(
                'trim',
                'string'
            ), '');

            $viewer = new LogViewer(APP_PATH . 'public/logs/', $file);
            $viewer->deleteLogFile();
            $this->makeJsonResult('', '删除成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="删除所有文件"})
     *
     * @name 删除所有文件
     */
    public function deleteallAction()
    {
        // http://www.myapplicationmodule.com/admin/system/logviewer/deleteall
        try {
            $this->view->disable();
            $viewer = new LogViewer(APP_PATH . 'public/logs/');
            $viewer->deleteAll();
            $this->makeJsonResult('', '删除所有文件成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="观察文件"})
     *
     * @name 观察文件
     */
    public function tailAction()
    {
        // http://www.myapplicationmodule.com/admin/system/logviewer/tail?offset=xxx&file=xxx
        try {
            $this->view->disable();
            $offset = $this->request->get('offset', array(
                'trim',
                'string'
            ), '0');
            $offset = intval($offset);

            $defaultLogFile = date('Y-m-d') . '.log';
            $logfile = $this->request->get('file', array(
                'trim',
                'string'
            ), $defaultLogFile);

            $viewer = new LogViewer(APP_PATH . 'public/logs/', $logfile);
            list($pos, $logs) = $viewer->tail($offset);
            // return compact('pos', 'logs');
            $this->makeJsonResult('', '观察成功', compact('pos', 'logs'));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
