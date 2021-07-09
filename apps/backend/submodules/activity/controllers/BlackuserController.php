<?php

namespace App\Backend\Submodules\Activity\Controllers;

use App\Backend\Submodules\Activity\Models\BlackUser;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="活动黑名单用户管理"})
 *
 * @name 活动黑名单用户管理
 */
class BlackuserController extends \App\Backend\Controllers\FormController
{

    // protected $readonly = true;

    private $modelBlackUser;

    private $modelActivity;

    public function initialize()
    {
        $this->modelBlackUser = new BlackUser();
        $this->modelActivity = new Activity();

        $this->activityList = $this->modelActivity->getAll();
        parent::initialize();
    }

    private $activityList = null;

    protected function getHeaderTools2($tools)
    {
        $tools['exportcsv'] = array(
            'title' => 'Csv导出',
            'action' => 'exportcsv',
            // 'is_show' => true,
            'is_show' => function () {
                return true;
            },
            'is_export' => true,
            'icon' => '',
        );
        $tools['importcsv'] = array(
            'title' => 'Csv导入',
            'action' => 'importcsv',
            // 'is_show' => true,
            'is_show' => function () {
                return true;
            },
            'is_export' => false,
            'icon' => 'fa-upload',
        );

        return $tools;
    }

    protected function getRowTools2($tools)
    {
        $tools['exchangeactivity'] = array(
            'title' => '修改所属活动',
            'action' => 'exchangeactivity',
            // 'is_show' => true,
            'is_show' => function ($row) {
                return true;
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['exchangeactivity'] = array(
            'title' => '修改所属活动',
            'action' => 'exchangeactivity',
            // 'is_show' => true,
            'is_show' => function ($row) {
                return true;
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getSchemas2($schemas)
    {
        $schemas['activity_id'] = array(
            'name' => '活动名称',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->activityList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->activityList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->activityList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
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
        return '活动黑名单用户';
    }

    protected function getModel()
    {
        return $this->modelBlackUser;
    }

    /**
     * @title({name="csv导出"})
     * csv导出
     *
     * @name csv导出
     */
    public function exportcsvAction()
    {
        // http://www.myapplicationmodule.com/admin/activity/blackuser/exportcsv
        try {
            $input = $this->getListFilterInput();

            // 根据检索条件获取列表
            resetTimeMemLimit();
            $dataList = $this->getModel()->getAllList($input);

            $excel = array();

            $fields = array();
            $fields[] = "用户ID";
            $fields[] = "所属活动";
            $excel['title'] = array_values($fields);

            $datas = array();
            foreach ($dataList as $data) {
                $item = array();
                $item[] = $data["user_id"];
                $item[] = $data["activity_id"];
                $datas[] = $item;
            }
            $excel['result'] = $datas;
            $fileName = date('YmdHis');
            arrayToCVS($fileName, $excel);
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="csv导入"})
     * csv导入
     *
     * @name csv导入
     */
    public function importcsvAction()
    {
        // http://www.myapplicationmodule.com/admin/activity/blackuser/importcsv
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
                $newName = $file->getName();
                //$newName = 'order_' . time() . '.' . $ext;

                // 5.数据上传导入处理 上传导入表中
                try {
                    //return $this->makeJsonError("文件path:" . $file->getPath());
                    $csv = file_get_contents($file->getTempName());
                    // csv to array
                    $arr = csv2arr($csv);
                    unset($csv); // 释放内存

                    //return $this->makeJsonError(\json_encode($arr));

                    $data_num = 0;
                    $line_no = 0;

                    // DB::beginTransaction();
                    $this->modelBlackUser->begin();

                    foreach ($arr as $idx => $content) {
                        $line_no++;
                        // 如果第一行是头
                        if ($idx === 0 && 1) { //!empty($settings['firstrow_is_header'])
                            $line_no--;
                            continue;
                        }

                        //print_r($row);
                        $now = time();
                        // 将数据导入到表中
                        //用户ID	所属活动ID
                        $user_id = trim($content[0]);
                        $activity_id = trim($content[1]);

                        // 检查是否已经导入到表了
                        $userInfo = $this->modelBlackUser->findOne(array(
                            'user_id' => $user_id,
                            'activity_id' => $activity_id
                        ));

                        // 如果没有
                        if (empty($userInfo)) {
                            // 创建
                            $userInfo = array(
                                'activity_id' => $activity_id,
                                'user_id' => $user_id
                            );
                            $this->modelBlackUser->insert($userInfo);
                            $data_num++;
                        } else {
                            // 如果已存在不做处理
                        }
                    }
                    // DB::commit();
                    $this->modelBlackUser->commit();

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    //return $this->response()->success('已成功上传,上传记录数:' . $data_num)->refresh();
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功上传,上传记录数:' . $data_num);
                } catch (\Exception $e) {
                    // DB::rollback();
                    $this->modelBlackUser->rollback();
                    return $this->makeJsonError("文件导入发生错误:" . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="修改所属活动"})
     * 修改所属活动
     *
     * @name 修改所属活动
     */
    public function exchangeactivityAction()
    {
        // http://www.myapplicationmodule.com/admin/activity/blackuser/exchangeactivity?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelBlackUser->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = array();
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );
                $fields['user_id'] = array(
                    'name' => '用户ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['activity_id'] = array(
                    'name' => '所属活动',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->modelActivity->getAll(),
                        'readonly' => true,
                    ),
                );
                $fields['exchange_activity_id'] = array(
                    'name' => '变更所属活动',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->modelActivity->getAll()
                    ),
                );

                $title = "修改所属活动";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理  
                $activity_id = trim($this->request->get('exchange_activity_id'));
                if (empty($activity_id)) {
                    return $this->makeJsonError("活动ID未指定");
                }
                $user_id = $row['user_id'];

                // 检查是否已经存在了
                $userInfo = $this->modelBlackUser->findOne(array(
                    'user_id' => $user_id,
                    'activity_id' => $activity_id
                ));

                // 如果没有
                if (empty($userInfo)) {
                    // 创建
                    $userInfo = array(
                        'activity_id' => $activity_id
                    );
                    $this->modelBlackUser->update(array('_id' => $id), array('$set' => $userInfo));
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功修改');
                } else {
                    // 如果已存在不做处理
                    return $this->makeJsonError("该用户已在该活动下存在");
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
