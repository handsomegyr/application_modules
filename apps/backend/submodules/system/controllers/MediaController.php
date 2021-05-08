<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="媒体管理"})
 *
 * @name 媒体管理
 */
class MediaController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return '媒体';
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
            $view1 = $this->request->get('view', array(
                'trim',
                'string'
            ), 'table');
            $this->view->setVar('view1', $view1);
            $path1 = $this->request->get('path', array(
                'trim',
                'string'
            ), '');
            $this->view->setVar('path1', $path1);

            $files = array();
            $dir = APP_PATH . 'public/upload';
            if (!empty($path1)) {
                $dir = APP_PATH . 'public/upload/' . $path1;
                $pathArr = explode('/', $path1);
            }
            if (empty($pathArr)) {
                $pathArr = array();
            }
            $this->view->setVar('pathArr', $pathArr);

            $fileOrDirs = glob($dir . '/*');

            $i = 0;
            foreach ($fileOrDirs as $file) {
                // 超过一万的文件就不显示
                if ($i > 10000) {
                    break;
                }
                $pathinfo = pathinfo($file);
                /*
                Array
                (
                [dirname] => .
                [basename] => test1.txt
                [extension] => txt
                [filename] => test1
                )
                */
                $fileInfo = array();
                $fileInfo['is_dir'] = false;
                $fileInfo['size'] = "";
                $fileInfo['name'] = $pathinfo['basename'];
                $fileInfo['dir'] = str_replace(APP_PATH . 'public/upload', "", $pathinfo['dirname']);
                $fileInfo['path'] = ltrim($fileInfo['dir'] . '/' . $fileInfo['name'], '/');
                $fileInfo['has_img'] = false;
                $fileInfo['file_icon'] = 'fa-file';

                if (!empty($pathinfo['extension'])) {
                    if (in_array($pathinfo['extension'], array('txt', 'log'))) {
                        $fileInfo['file_icon'] = 'fa-file-text-o';
                    } elseif (in_array($pathinfo['extension'], array('rar', 'zip'))) {
                        $fileInfo['file_icon'] = 'fa-file-zip-o';
                    } elseif (in_array($pathinfo['extension'], array('asp', 'jsp', 'php'))) {
                        $fileInfo['file_icon'] = 'fa-code';
                    } elseif (in_array($pathinfo['extension'], array('pdf'))) {
                        $fileInfo['file_icon'] = 'fa-file-pdf-o';
                    }
                }

                $finfo = new \finfo(FILEINFO_MIME);
                $fileMime = @$finfo->file($file);

                // 如果是图片的话
                if (is_string($fileMime) && !empty($fileMime) && strpos(strtolower($fileMime), 'image') !== false) {
                    $fileInfo['has_img'] = true;
                }
                if (is_dir($file)) {
                    $modified = dirmtime($file);
                    $fileInfo['is_dir'] = true;
                } else {
                    $modified = filemtime($file);
                    $fileInfo['size'] = \formatBytes(filesize($file));
                    $i++;
                }
                $fileInfo['modified'] = date('Y-m-d H:i:s', $modified);
                $files[] = $fileInfo;
            }
            // if (!empty($path1)) {
            //     print_r($files);
            //     die('path1:' . $path1);
            // }
            $this->view->setVar('files', $files);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="下载文件"})
     *
     * @name 下载文件
     */
    public function downloadAction()
    {
        $this->view->disable();
        // http://www.myapplicationmodule.com/admin/system/media/download?file=561c7ed57f50eaf809000029
        $file = $this->request->get('file', array(
            'trim',
            'string'
        ), '');

        if (empty($file)) {
            header("HTTP/1.1 404 Not Found");
            return;
        }

        $filename = APP_PATH . "public/upload/{$file}";
        if (!file_exists($filename)) {
            header("HTTP/1.1 404 Not Found");
            return;
        }

        $data = file_get_contents($filename);
        $finfo = new \finfo(FILEINFO_MIME);
        $fileMime = $finfo->buffer($data);

        $pathinfo = pathinfo($filename);
        $fileName = $pathinfo['basename'];

        setHeaderExpires();

        if (isset($fileMime)) {
            header('Content-Type: ' . $fileMime . ';');
        }
        header('Content-Disposition:attachment;filename="' . $fileName . '"');
        echo $data;
        return;
    }

    /**
     * @title({name="创建目录"})
     *
     * @name 创建目录
     */
    public function folderAction()
    {
        // http://www.myapplicationmodule.com/admin/system/media/folder?name=gyr&dir=
        try {
            $this->view->disable();
            $name = $this->request->get('name', array(
                'trim',
                'string'
            ), '');
            $dir = $this->request->get('dir', array(
                'trim',
                'string'
            ), '');

            if (empty($name)) {
                throw new \Exception('目录名未设置');
            }

            $dir = rtrim(APP_PATH . "public/upload/{$dir}", '/');
            $dir = $dir . "/" . $name;
            makeDir($dir, 0777);
            $this->makeJsonResult('', '创建成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="上传文件"})
     *
     * @name 上传文件
     */
    public function uploadAction()
    {
        // http://www.myapplicationmodule.com/admin/system/media/upload?files[]=(binary)=gyr&dir=&view=
        try {
            $this->view->disable();

            $dir = $this->request->get('dir', array(
                'trim',
                'string'
            ), '');
            $view1 = $this->request->get('view', array(
                'trim',
                'string'
            ), 'table');

            $files = array();
            // Check if the user has uploaded files
            if ($this->request->hasFiles() == true) {
                foreach ($this->request->getUploadedFiles() as $file) {
                    $key4File = $file->getKey();
                    $key4FileArr = explode('.', $key4File);
                    // 如果没有xxx.0 xxx.1的话
                    if (!isset($key4FileArr[1])) {
                        $files[$key4File] = $file;
                    } else {
                        $files[$key4FileArr[0]][str_pad($key4FileArr[1],  3,  "0",  STR_PAD_LEFT)] = $file;
                    }
                }
            }

            $key = 'files';
            $field['data']['file']['path'] = $dir;

            $fileList4Field = array();
            foreach ($files[$key] as $file) {
                $fileId = $this->uploadFile($file, $field);
                if (!empty($fileId)) {
                    $fileList4Field[] = $fileId;
                }
            }
            // $url = $this->getUrl("list");
            // $url = $url . "&path=" . $dir . "&view=" . $view1;
            // $this->_redirect($url);
            // return;
            $this->makeJsonResult('', '上传成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="删除文件"})
     *
     * @name 删除文件
     */
    public function deleteAction()
    {
        // http://www.myapplicationmodule.com/admin/system/media/delete?files=gyr/6093c22269dc0a5a25006b47.png
        try {
            $this->view->disable();

            $files = $this->request->get('files', array(
                'trim',
                'string'
            ), '');
            $files = explode(',', $files);
            foreach ($files as $file) {
                $file = trim($file);
                if (strlen($file) > 0) {
                    # code...
                    $dir = rtrim(APP_PATH . "public/upload/{$file}", '/');
                    unlink($dir);
                }
            }
            $this->makeJsonResult('', '删除成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="重命名或移动文件"})
     *
     * @name 重命名或移动文件
     */
    public function moveAction()
    {
        // http://www.myapplicationmodule.com/admin/system/media/move?path=files/image.rar&new=files/image1.rar
        try {
            $this->view->disable();
            $new = $this->request->get('new', array(
                'trim',
                'string'
            ), '');
            $path = $this->request->get('path', array(
                'trim',
                'string'
            ), '');

            if (strlen($path) < 1) {
                throw new \Exception('path未设置');
            }

            if (strlen($new) < 1) {
                throw new \Exception('new未设置');
            }

            $dir1 = rtrim(APP_PATH . "public/upload/{$path}", '/');

            if ($new == $path) {
                throw new \Exception('文件名未发生改变');
            }
            $dir2 = rtrim(APP_PATH . "public/upload/{$new}", '/');
            if (!file_exists($dir1)) {
                throw new \Exception('未找到文件');
            }
            if (file_exists($dir2)) {
                throw new \Exception('文件已存在');
            }
            rename($dir1, $dir2);
            $this->makeJsonResult('', '修改成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
