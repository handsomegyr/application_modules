<?php

namespace App\Service\Controllers;

class FileController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 提供外部文件下载服务
     */
    public function indexAction()
    {
        // http://www.applicationmodule.com/service/file/index?id=xxxx&w=100&h=100&upload_path=post
        $uploadPath = $this->get('upload_path', '');
        $id = $this->request->get('id', array(
            'trim'
        ), '');
        $download = $this->request->get('d', array(
            'trim'
        ), false);
        $resize = $this->request->get('r', array(
            'trim'
        ), false);
        $thumbnail = $this->request->get('t', array(
            'trim'
        ), false);
        $adpter = $this->request->get('a', array(
            'trim'
        ), false);
        $width = intval($this->request->get('w', array(
            'trim'
        ), 0));
        $height = intval($this->request->get('h', array(
            'trim'
        ), 0));
        $quality = intval($this->request->get('q', array(
            'trim'
        ), 100));
        $source = $this->request->get('s', array(
            'trim'
        ), false);
        $rotate = $this->request->get('ro', array(
            'trim'
        ), 0);
        $autoRotate = $this->request->get('ar', array(
            'trim'
        ), false);

        if ($id == null) {
            header("HTTP/1.1 404 Not Found");
            return;
        }

        if (empty($uploadPath)) {
            $filename = APP_PATH . "public/upload/{$id}";
        } else {
            $filename = APP_PATH . "public/upload/{$uploadPath}/{$id}";
        }
        if (!file_exists($filename)) {
            header("HTTP/1.1 404 Not Found");
            return;
        }

        $data = file_get_contents($filename);
        $finfo = new \finfo(FILEINFO_MIME);
        $fileMime = $finfo->buffer($data);
        $fileName = $id;

        // 当检测到文件类型为空是，判断一下文件的类型
        if (empty($fileMime)) {
            // 直接输出
            goto output;
        } else {
            // 如果是图片的话
            if (strpos(strtolower($fileMime), 'image') !== false) {
                if ($source) {
                    // 直接输出
                    goto output;
                }
                try {
                    // 图片处理
                    $imagick = new \Imagick();
                    $resource = $data;
                    $imagick->readimageblob($resource);
                    if ($adpter) {
                        $imagick->cropThumbnailImage($width, $height);
                    } elseif ($thumbnail) {
                        $imagick->thumbnailImage($width, $height);
                    } else {
                        $geo = $imagick->getImageGeometry();
                        $sizeWidth = $geo['width'];
                        $sizeHeight = $geo['height'];
                        if ($width > 0 && $height > 0) {
                            if ($sizeWidth / $width > $sizeHeight / $height) {
                                $height = 0;
                            } else {
                                $width = 0;
                            }
                            $imagick->thumbnailImage($width, $height);
                        } else 
                            if ($width > 0 || $height > 0) {
                            $imagick->thumbnailImage($width, $height);
                        }
                    }

                    $backgroundColor = new \ImagickPixel();
                    $backgroundColor->setColor("rgb(255,255,255)");
                    if ($rotate != 0) {
                        $imagick->rotateimage($backgroundColor, $rotate);
                    }

                    if ($autoRotate) {
                        try {
                            $orientation = $imagick->getImageOrientation();
                            switch ($orientation) {
                                case \Imagick::ORIENTATION_BOTTOMRIGHT:
                                    $imagick->rotateimage($backgroundColor, 180); // rotate 180 degrees
                                    break;

                                case \Imagick::ORIENTATION_RIGHTTOP:
                                    $imagick->rotateimage($backgroundColor, 90); // rotate 90 degrees CW
                                    break;

                                case \Imagick::ORIENTATION_LEFTBOTTOM:
                                    $imagick->rotateimage($backgroundColor, -90); // rotate 90 degrees CCW
                                    break;
                            }
                        } catch (\Exception $e) {
                        }
                    }

                    if ($quality < 100) {
                        $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
                        $imagick->setImageCompressionQuality($quality);
                        $fileName .= '.jpg';
                        $fileMime = 'image/jpg';
                    }
                    $imagick->stripImage();
                    $data = $imagick->getimageblob();
                    $imagick->destroy();
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
        }

        output: setHeaderExpires();

        if (isset($fileMime)) {
            header('Content-Type: ' . $fileMime . ';');
        }
        if ($download) {
            header('Content-Disposition:attachment;filename="' . $fileName . '"');
        } else {
            header('Content-Disposition:filename="' . $fileName . '"');
        }
        // $this->response->setHeader("Content-Type", $fileMime);
        echo $data;

        return;
    }

    /**
     * 接受上传文件的处理
     *
     * @return string json
     */
    public function uploadAction()
    {
        // http://www.applicationmodule.com/service/file/upload?upload_path=post
        $uploadPath = $this->get('upload_path', '');
        $rst = array();
        if (!empty($_FILES)) {
            foreach ($_FILES as $field => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    if (filesize($file['tmp_name']) > 0) {
                        $fileId = getNewId() . '_' . $file['name'];
                        if (empty($uploadPath)) {
                            $destination = APP_PATH . "public/upload/{$fileId}";
                        } else {
                            makeDir(APP_PATH . "public/upload/{$uploadPath}");
                            $destination = APP_PATH . "public/upload/{$uploadPath}/{$fileId}";
                        }
                        $isOk = move_uploaded_file($file['tmp_name'], $destination);
                        if ($isOk) {
                            $rst[$field] = array(
                                'ok' => 1,
                                'err_code' => '',
                                'err' => '',
                                'id' => $fileId
                            );
                        } else {
                            $rst[$field] = array(
                                'ok' => 0,
                                'err_code' => '500',
                                'err' => '上传文件无法移动'
                            );
                        }
                    } else {
                        $rst[$field] = array(
                            'ok' => 0,
                            'err_code' => '500',
                            'err' => '上传文件Size为0字节，请检查上传文件'
                        );
                    }
                } else {
                    switch ($file['error']) {
                        case UPLOAD_ERR_INI_SIZE:
                            $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                            break;
                        case UPLOAD_ERR_FORM_SIZE:
                            $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $message = "The uploaded file was only partially uploaded";
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $message = "No file was uploaded";
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $message = "Missing a temporary folder";
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $message = "Failed to write file to disk";
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $message = "File upload stopped by extension";
                            break;
                        default:
                            $message = "Unknown upload error";
                            break;
                    }

                    $rst[$field] = array(
                        'ok' => 0,
                        'err_code' => $file['error'],
                        'err' => $message
                    );
                }
            }
        } else {
            $rst = array(
                'ok' => 0,
                'err_code' => '404',
                'err' => '未发现有效的上传文件'
            );
        }
        echo json_encode($rst, JSON_UNESCAPED_UNICODE);
        return;
    }
}
