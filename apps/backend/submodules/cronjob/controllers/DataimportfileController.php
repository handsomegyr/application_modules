<?php

namespace App\Backend\Submodules\Cronjob\Controllers;

use App\Backend\Submodules\Cronjob\Models\DataImport\File as DataImportFile;

/**
 * @title({name="导入文件"})
 *
 * @name 导入文件
 */
class DataimportfileController extends \App\Backend\Controllers\FormController
{
    private $modelFile;

    public function initialize()
    {
        $this->modelFile = new DataImportFile();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['importcsv'] = array(
            'title' => 'Csv导入',
            'action' => 'importcsv',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-upload',
        );

        return $tools;
    }

    /**
     * @title({name="csv导入"})
     * csv导入
     *
     * @name csv导入
     */
    public function importcsvAction()
    {
        // http://www.applicationmodule.com/admin/cronjob/dataimportfile/importcsv
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = array();
                $fields['csv_file'] = array(
                    'name' => 'csv上传文件',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "csv导入";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理
                if (empty($exts)) {
                    $exts =  [
                        'csv'
                    ];
                }
                if (empty($sizes)) {
                    $sizes = array(
                        // 1024K
                        'max' => 1024 * 1024
                    );
                }

                $uploadFiles = array();
                // 1 Check if the user has uploaded files
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        $uploadFiles[$file->getKey()] = $file;
                    }
                }
                //return $this->makeJsonError(\json_encode($uploadFiles));                
                if (empty($uploadFiles) || !isset($uploadFiles['csv_file'])) {
                    return $this->makeJsonError("没有上传导入文件2");
                }
                $file = $uploadFiles['csv_file'];
                $fileError = $file->getError();
                if (!empty($fileError)) {
                    return $this->makeJsonError("导入文件上传失败，错误码：{$fileError}");
                }
                if (!$file->isUploadedFile()) {
                    return $this->makeJsonError("导入文件还未成功上传");
                }

                // 2 先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片后缀的数组内
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, $exts)) {
                    return $this->makeJsonError("文件类型不合法");
                }

                // 3 文件大小 字节单位
                $fSize = $file->getSize();
                if ($fSize > $sizes['max']) {
                    return $this->makeJsonError("文件大小过大{$fSize}");
                }

                //return $this->makeJsonError("文件path:" . $file->getTempName());

                // 4.将文件取一个新的名字
                // $newName = $file->getName();
                $ext = strtolower($file->getExtension());
                $newName = myMongoId(new \MongoId()) . "." . $ext;
                // return $this->makeJsonError(APP_PATH . 'data/upload/' . $newName);
                // 5.移动文件,并修改名字
                if ($file->moveTo(APP_PATH . '/data/upload/' . $newName)) {
                    $newFilePath = APP_PATH . '/data/upload/' . $newName; // 返回一个地址

                    // \Artisan::call('cronjob:import_file', [
                    //     'csvfilename' => $newName
                    // ]);

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    // return $this->response()->success('已成功上传,新文件地址为' . $newFilePath)->refresh();
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功上传,新文件地址为' . $newFilePath);
                } else {
                    return $this->makeJsonError("文件移动发生错误");
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['cron_time'] = array(
            'name' => '执行时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['data_file'] = array(
            'name' => '数据文件',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'name' => '文件信息',
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    //display()方法来通过传入的回调函数来处理当前列的值：
                    return $column->display(function () {
                        $itemList = array();
                        $itemList[] = "<b>数据文件:</b>" . $this->data_file;
                        $itemList[] = "<b>flag文件:</b>" . $this->flag_file;
                        $itemList[] = "<b>Lock文件:</b>" . $this->lock_file;
                        return implode('<br/>', $itemList);
                    });
                }
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['flag_file'] = array(
            'name' => 'flag文件',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['lock_file'] = array(
            'name' => 'Lock文件',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['log_time'] = array(
            'name' => '记录时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['data_count'] = array(
            'name' => '数据条数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['process_total'] = array(
            'name' => '处理条数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $statusItems  = array();
        $statusItems['0'] = '未处理';
        $statusItems['1'] = '已上传';
        $statusItems['2'] = '已完成';

        $schemas['status'] = array(
            'name' => '处理状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $statusItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $statusItems
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'select',
                'items' => $statusItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['desc'] = array(
            'name' => '处理描述',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['returnback_cronjobId'] = array(
            'name' => '回滚计划任务ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '导入文件';
    }

    protected function getModel()
    {
        return $this->modelFile;
    }
}
