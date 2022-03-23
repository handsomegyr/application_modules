<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\Project;
use App\Backend\Submodules\Company\Models\Component;
use App\Cronjob\Models\Task;
use App\Backend\Submodules\Database\Models\Project as DBProject;

/**
 * @title({name="项目管理"})
 *
 * @name 项目管理
 */
class ProjectController extends \App\Backend\Controllers\FormController
{
    // 是否只读
    // protected $readonly = true;

    private $modelProject;
    private $modelComponent;
    private $modelTask;
    private $modelDbProject;

    protected $COMPANY_CUT_TASKTYPE = 1;
    protected $NGINX_SERVER_DOMAIN = ".myweb.com";

    public function initialize()
    {
        $this->modelProject = new Project();
        $this->modelComponent = new Component();
        $this->modelTask = new Task();
        $this->modelDbProject = new DBProject();

        $this->componentList = $this->modelComponent->getAll();

        parent::initialize();
    }

    private $componentList = null;

    protected function getRowTools2($tools)
    {
        $tools['buildprojectsettings'] = array(
            'title' => '构建项目环境',
            'action' => 'buildprojectsettings',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code']) && !empty($row['db_pwd']) && !empty($row['db_name'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['rsyncdevtotest'] = array(
            'title' => '同步测试',
            'action' => 'rsyncdevtotest',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['publishtesttoprod'] = array(
            'title' => '发布正式',
            'action' => 'publishtesttoprod',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['updateurl'] = array(
            'title' => '修改url',
            'action' => 'updateurl',
            'process_without_modal' => false,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['grantpriv'] = array(
            'title' => '数据库用户授权',
            'action' => 'grantpriv',
            'process_without_modal' => false,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['checkcollation'] = array(
            'title' => '检查表及列的字符集',
            'action' => 'checkcollation',
            'process_without_modal' => false,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['buildcomponent'] = array(
            'title' => '构建组件',
            'action' => 'buildcomponent',
            'process_without_modal' => false,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['buildprojectsettings'] = array(
            'title' => '构建项目环境',
            'action' => 'buildprojectsettings',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code']) && !empty($row['db_pwd']) && !empty($row['db_name'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="构建项目环境"})
     *
     * @name 构建项目环境
     */
    public function buildprojectsettingsAction()
    {
        // http://www.myapplicationmodule.com/admin/company/project/buildprojectsettings?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelProject->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 创建数据库
            $this->createDatabase($data['db_name']);

            //数据库用户授权
            $this->grantDatabaseUser($data['db_name'], $data['project_code'], $data['db_pwd']);

            // 登录一个任务
            $taskContent = array();
            $taskContent['project_code'] = $data['project_code'];
            $taskContent['project_id'] = $data['_id'];
            $taskContent['db_name'] = $data['db_name'];
            $taskContent['db_pwd'] = $data['db_pwd'];
            $taskContent['process_list'] = 'create_project';
            $taskInfo = $this->modelTask->log($this->COMPANY_CUT_TASKTYPE, $taskContent);
            $res['taskInfo'] = $taskInfo;
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="同步测试"})
     *
     * @name 同步测试
     */
    public function rsyncdevtotestAction()
    {
        // http://www.myapplicationmodule.com/admin/company/project/rsyncdevtotest?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelProject->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 登录一个任务
            $taskContent = array();
            $taskContent['project_code'] = $data['project_code'];
            $taskContent['project_id'] = $data['_id'];
            $taskContent['process_list'] = 'rsync_dev_to_test';
            $taskInfo = $this->modelTask->log($this->COMPANY_CUT_TASKTYPE, $taskContent);
            $res['taskInfo'] = $taskInfo;
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="发布正式"})
     *
     * @name 发布正式
     */
    public function publishtesttoprodAction()
    {
        // http://www.myapplicationmodule.com/admin/company/project/publishtesttoprod?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelProject->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 登录一个任务
            $taskContent = array();
            $taskContent['project_code'] = $data['project_code'];
            $taskContent['project_id'] = $data['_id'];
            $taskContent['process_list'] = 'publish_test_to_prod';
            $taskInfo = $this->modelTask->log($this->COMPANY_CUT_TASKTYPE, $taskContent);
            $res['taskInfo'] = $taskInfo;
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="修改url"})
     * 修改url
     *
     * @name 修改url
     */
    public function updateurlAction()
    {
        // http://www.myapplicationmodule.com/admin/company/project/updateurl?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelProject->getInfoById($id);
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
                $fields['project_code'] = array(
                    'name' => '项目编号',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['project_name'] = array(
                    'name' => '项目名称',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['git_url'] = array(
                    'name' => '项目GIT地址',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => false,
                    ),
                );
                $fields['svn_url'] = array(
                    'name' => '项目SVN地址',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => false,
                    ),
                );
                $fields['test_url'] = array(
                    'name' => '测试地址',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => false,
                    ),
                );
                $fields['product_url'] = array(
                    'name' => '正式地址',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => false,
                    ),
                );
                $fields['oss_url'] = array(
                    'name' => 'OSS地址',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => false,
                    ),
                );
                $fields['cdn_url'] = array(
                    'name' => 'CDN地址',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => false,
                    ),
                );

                $title = "修改url";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理
                $git_url = trim($this->request->get('git_url'));
                $svn_url = trim($this->request->get('svn_url'));
                $test_url = trim($this->request->get('test_url'));
                $product_url = trim($this->request->get('product_url'));
                $oss_url = trim($this->request->get('oss_url'));
                $cdn_url = trim($this->request->get('cdn_url'));
                $updateData = array(
                    'git_url' => $git_url,
                    'svn_url' => $svn_url,
                    'test_url' => $test_url,
                    'product_url' => $product_url,
                    'oss_url' => $oss_url,
                    'cdn_url' => $cdn_url
                );
                $this->modelProject->update(array('_id' => $id), array('$set' => $updateData));
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功修改');
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="数据库用户授权"})
     * 数据库用户授权
     *
     * @name 数据库用户授权
     */
    public function grantprivAction()
    {
        // http://www.myapplicationmodule.com/admin/company/project/grantpriv?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelProject->getInfoById($id);
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
                $fields['project_code'] = array(
                    'name' => '项目编号',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['project_name'] = array(
                    'name' => '项目名称',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['db_name'] = array(
                    'name' => '数据库名称',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['db_user'] = array(
                    'name' => '数据库用户',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => false,
                    ),
                );

                $title = "数据库用户授权";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理  
                $db_user = trim($this->request->get('db_user'));
                if (empty($db_user)) {
                    return $this->makeJsonError("数据库用户为空");
                }
                // 创建数据库用户和权限
                $db_name = $row['db_name'];
                $this->grantDatabaseUser($db_name, $db_user, $db_user);
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="构建组件"})
     * 构建组件
     *
     * @name 构建组件
     */
    public function buildcomponentAction()
    {
        // http://www.myapplicationmodule.com/admin/company/project/buildcomponent?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelProject->getInfoById($id);
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
                $fields['project_code'] = array(
                    'name' => '项目编号',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['project_name'] = array(
                    'name' => '项目名称',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['db_name'] = array(
                    'name' => '数据库名称',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['components'] = array(
                    'name' => '组件列表',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'checkbox',
                        // 'readonly' => true,
                        'checkbox' => array(
                            'isCheckAll' => true
                        ),
                        'is_show' => true,
                        'readonly' => false,
                        'items' => $this->componentList,
                    ),
                );
                // print_r($row);
                // die('xxxxxxxxx');
                $title = "构建组件";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理  
                $project_components = $this->request->get('components');
                if (empty($project_components)) {
                    return $this->makeJsonError("组件未指定");
                }
                if (!is_array($project_components)) {
                    $project_components = array($project_components);
                }
                $project_components2 = array();
                $isDbSqlExist = false;
                $isZipFileExist = false;
                foreach ($project_components as $componentId) {
                    if (!empty($componentId)) {
                        $componentInfo = $this->modelComponent->getInfoById($componentId);
                        if (empty($componentInfo)) {
                            return $this->makeJsonError("id:{$componentId}的组件不存在");
                        }
                        if (empty($componentInfo['is_publish'])) {
                            return $this->makeJsonError("id:{$componentId}的组件还是未发布的状态");
                        }
                        if (!empty($componentInfo['db_sql'])) {
                            $isDbSqlExist = true;
                        }
                        if (!empty($componentInfo['zip_file'])) {
                            $isZipFileExist = true;
                        }
                        $project_components2[strval($componentId)] = $componentInfo;
                    }
                }
                $project_components = $project_components2;
                if (empty($project_components)) {
                    return $this->makeJsonError("组件未指定");
                }
                // 如果需要处理数据库操作的话
                if ($isDbSqlExist) {
                    // 创建数据库
                    $db_name = $row['db_name'];
                    // 数据库转换
                    foreach ($project_components as $componentInfo) {
                        $this->createComponents($db_name, $componentInfo['db_sql']);
                    }
                }

                $ret = array();
                $errorList = array();

                // 成功的话
                if (empty($ret)) {
                    $success = true;
                    $updateData = array();
                    $updateData['components'] = \App\Common\Utils\Helper::myJsonEncode(array_keys($project_components));
                    $updateData['last_upload_time'] = getCurrentTime();
                    $this->modelProject->update(array('_id' => $id), array('$set' => $updateData));
                } else {
                    $success = false;
                }

                if ($success) {
                    // 如果需要下载zip文件的话
                    if ($isZipFileExist) {
                        // $tmp = \tempnam(\sys_get_temp_dir(), 'zip_');
                        $uploadPath = $this->modelProject->getUploadPath();
                        $fileName = $row['project_code'] . '_component_' . date("YmdHis") . '.zip';
                        $tmp = APP_PATH . "public/upload/{$uploadPath}/{$fileName}";
                        $zip = new \ZipArchive();
                        $res = $zip->open($tmp, \ZipArchive::CREATE);
                        if ($res === true) {
                            foreach ($project_components as $componentInfo) {
                                if (empty($componentInfo['zip_file'])) {
                                    continue;
                                }
                                // 获取config内容
                                $uploadPath4Component = $this->modelComponent->getUploadPath();
                                $zip_file = APP_PATH . "public/upload/{$uploadPath4Component}/{$componentInfo['zip_file']}";
                                $fileStrRet = file_get_contents($zip_file);
                                $filename = tempnam(sys_get_temp_dir(), $componentInfo['code'] . '_' . uniqid() . "_");
                                $fp = fopen($filename, 'w');
                                fwrite($fp,  $fileStrRet);
                                fclose($fp);
                                $zip->addFile($filename, $componentInfo['code'] . '.zip');
                                // unlink($filename);
                            }
                            $zip->close();
                            $url = $this->url->get("service/file/index") . "?upload_path={$uploadPath}&id={$fileName}";
                            return $this->makeJsonResult(array('then' => array('action' => 'download', 'value' => $url)), '操作成功:');
                        } else {
                            return $this->makeJsonError("Zip文件创建失败");
                        }
                    } else {
                        return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
                    }
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="检查表及列的字符集"})
     * 检查表及列的字符集
     *
     * @name 检查表及列的字符集
     */
    public function checkcollationAction()
    {
        // http://www.myapplicationmodule.com/admin/company/project/checkcollation?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelProject->getInfoById($id);
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
                $fields['project_code'] = array(
                    'name' => '项目编号',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['project_name'] = array(
                    'name' => '项目名称',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['db_name'] = array(
                    'name' => '数据库名',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                // print_r($row);
                // die('xxxxxxxxx');
                $title = "检查表及列的字符集";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理  
                $db_name = $this->request->get('db_name');
                if (empty($db_name)) {
                    return $this->makeJsonError("数据库未指定");
                }

                $connection = $this->getConnection4Db($db_name);

                $show_tables_sql = "SHOW TABLE STATUS";
                $result1 = $connection->query($show_tables_sql, array());
                $result1->setFetchMode(MYDB_FETCH_OBJ);
                $all_tables = $result1->fetchAll();

                $tables_info = array();
                foreach ($all_tables as $table) {
                    if (!empty($table->Collation)) {
                        if ($table->Collation != 'utf8mb4_unicode_ci') {
                            $arr = [
                                'table_name' => $table->Name,
                                'collation' => $table->Collation
                            ];
                            $tables_info[$table->Name] = $arr;
                        }
                    }
                    $column_arr = [];

                    $sql = "SHOW FULL COLUMNS FROM " . $table->Name;
                    $result2 = $connection->query($sql, array());
                    $result2->setFetchMode(MYDB_FETCH_OBJ);
                    $show_table_columns = $result2->fetchAll();

                    foreach ($show_table_columns as $column) {
                        if (!empty($column->Collation)) {
                            if ($column->Collation != 'utf8mb4_unicode_ci') {
                                $arr = [
                                    'table_name' => $table->Name,
                                    'collation' => $table->Collation
                                ];
                                $tables_info[$table->Name] = $arr;
                                $arr = [
                                    'field' => $column->Field,
                                    'type' => $column->Type,
                                    'collation' => $column->Collation
                                ];
                                array_push($column_arr, $arr);
                            }
                        }
                    }
                    if (!empty($column_arr)) {
                        $tables_info[$table->Name]['fields'] = $column_arr;
                    }
                }
                if (empty($tables_info)) {
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '恭喜你,未找到utf8mb4_unicode_ci以外的表或字段');
                } else {
                    $uploadPath = $this->modelProject->getUploadPath();
                    $fileName = $row['project_code'] . '_checkcollation_' . $db_name . '_' . date("YmdHis") . '.txt';
                    $tmp = APP_PATH . "public/upload/{$uploadPath}/{$fileName}";
                    file_put_contents($tmp, \json_encode(array_values($tables_info)));
                    $url = $this->url->get("service/file/index") . "?upload_path={$uploadPath}&id={$fileName}";
                    return $this->makeJsonResult(array('then' => array('action' => 'download', 'value' => $url)), '很遗憾,找到utf8mb4_unicode_ci以外的表或字段:');
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;

        $schemas['project_code'] = array(
            'name' => '项目编号',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候显示
                        $settings['is_show'] = true;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
        $schemas['operation_code'] = array(
            'name' => '运维编号',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候显示
                        $settings['is_show'] = true;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
        $schemas['project_name'] = array(
            'name' => '项目名称',
            'data' => array(
                'type' => 'string',
                'length' => 100,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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
        $schemas['isSystem'] = array(
            'name' => '是否公司内部项目',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                // 'input_type' => 'radio',
                'input_type' => 'switch',
                // 'readonly' => true,
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['language'] = array(
            'name' => '开发语言',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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
        $schemas['db_name'] = array(
            'name' => '测试数据库名',
            'data' => array(
                'type' => 'string',
                'length' => 100,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候显示
                        $settings['is_show'] = true;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
                'is_show' => false
            )
        );
        $schemas['db_pwd'] = array(
            'name' => '测试数据库密码',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候显示
                        $settings['is_show'] = true;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
                'is_show' => false
            )
        );
        $schemas['db_name_product'] = array(
            'name' => '正式数据库名',
            'data' => array(
                'type' => 'string',
                'length' => 100,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候显示
                        $settings['is_show'] = false;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
                'is_show' => false
            )
        );
        $schemas['db_pwd_product'] = array(
            'name' => '正式数据库密码',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候显示
                        $settings['is_show'] = false;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
                'is_show' => false
            )
        );
        $schemas['redis_db_id'] = array(
            'name' => 'REDIS数据库ID',
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
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => false
            )
        );
        $schemas['ae'] = array(
            'name' => 'AE信息',
            'data' => array(
                'type' => 'array',
                'length' => 1024,
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                // 'input_type' => 'select', 
                'select' => array(
                    'multiple' => true
                ),
                'input_type' => 'checkbox',
                // 'readonly' => true,
                'checkbox' => array(
                    'isCheckAll' => true
                ),
                'is_show' => true,
                'items' => array('AE1' => 'AE1', 'AE2' => 'AE2', 'AE3' => 'AE3', 'AE4' => 'AE4'),
                'help' => '多个AE用逗号间隔'
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => array('AE1' => 'AE1', 'AE2' => 'AE2', 'AE3' => 'AE3', 'AE4' => 'AE4'),
            ),
            'search' => array(
                // 'input_type' => 'select',
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['executives'] = array(
            'name' => '执行人信息',
            'data' => array(
                'type' => 'array',
                'length' => 1024,
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                // 'input_type' => 'select',
                // 'select' => array(
                //     'multiple' => true
                // ),
                'input_type' => 'listbox',
                'readonly' => true,
                'is_show' => true,
                'items' => array('PE1' => 'PE1', 'PE2' => 'PE2', 'PE3' => 'PE3', 'PE4' => 'PE4'),
                'help' => '多个执行人用逗号间隔',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => array('PE1' => 'PE1', 'PE2' => 'PE2', 'PE3' => 'PE3', 'PE4' => 'PE4'),
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['git_url'] = array(
            'name' => '项目GIT地址',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
        $schemas['svn_url'] = array(
            'name' => '项目SVN地址',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
        $schemas['dev_url'] = array(
            'name' => '开发地址',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '多个地址用逗号间隔',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    }
                    return $settings;
                }
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
        $schemas['test_url'] = array(
            'name' => '测试地址',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '多个地址用逗号间隔',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    }
                    return $settings;
                }
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
        $schemas['product_url'] = array(
            'name' => '正式地址',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '多个地址用逗号间隔',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    }
                    return $settings;
                }
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
        $schemas['oss_url'] = array(
            'name' => 'OSS地址',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '多个地址用逗号间隔',
                // 'extensionSettings' => function ($column, $Grid) {
                //     $settings = array();
                //     $row = $column->getRow();
                //     if (empty($row->_id)) {
                //         // 新增的时候不显示
                //         $settings['is_show'] = false;
                //     }
                //     return $settings;
                // }
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
                // 'is_editable' => Admin::user()->isRole('administrator')
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        $schemas['cdn_url'] = array(
            'name' => 'CDN地址',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '多个地址用逗号间隔',
                // 'extensionSettings' => function ($column, $Grid) {
                //     $settings = array();
                //     $row = $column->getRow();
                //     if (empty($row->_id)) {
                //         // 新增的时候不显示
                //         $settings['is_show'] = false;
                //     }
                //     return $settings;
                // }
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
                // 'is_editable' => Admin::user()->isRole('administrator')
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['server_id'] = array(
            'name' => '项目所在服务器',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
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
        $schemas['dm_names'] = array(
            'name' => '项目域名',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
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
        $schemas['components'] = array(
            'name' => '组件列表',
            'data' => array(
                'type' => 'array',
                'length' => 1024,
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'select' => array(
                    'multiple' => false
                ),
                // 'input_type' => 'checkbox',
                // 'checkbox' => array(
                //     'isCheckAll' => true
                // ),
                'is_show' => false,
                'items' => array(),
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
                'items' => array(),
            ),
            'search' => array(
                // 'input_type' => 'select',
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $enabledDatas = array(
            0 => '否',
            1 => '同步',
            2 => '全量同步',
            3 => '目录同步'
        );
        $schemas['enabled'] = array(
            'name' => '允许发布',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $enabledDatas
            ),
            'list' => array(
                'is_show' => true,
                'render' => '',
                'items' => $enabledDatas,
                'editable_type' => 'select',
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'select',
                'items' => $enabledDatas
            ),
            'export' => array(
                'is_show' => true
            )
        );
        //上线状态:0开发中,1部署中,2已上线,3已下线
        $onlineOptions = array();
        $onlineOptions['0'] = '开发中';
        $onlineOptions['1'] = '部署中';
        $onlineOptions['2'] = '已上线';
        $onlineOptions['3'] = '已下线';
        $schemas['online'] = array(
            'name' => '上线状态',
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
                'items' => $onlineOptions
            ),
            'list' => array(
                'is_show' => true,
                'render' => '',
                'items' => $onlineOptions
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'select',
                'items' => $onlineOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['last_upload_time'] = array(
            'name' => '最后发布时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
                'is_show' => false
            )
        );
        $schemas['description'] = array(
            'name' => '项目备注',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
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
                'is_show' => false
            )
        );
        $schemas['svn_log'] = array(
            'name' => 'SVN日志',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['include_folder'] = array(
            'name' => '同步目录',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false,
                'items' => '',
                'help' => '用逗号间隔'
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
                'is_show' => false
            )
        );
        $schemas['exclude_folder'] = array(
            'name' => '排除目录',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false,
                'items' => '',
                'help' => '用逗号间隔'
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
                'is_show' => false
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '项目管理';
    }

    protected function getModel()
    {
        return $this->modelProject;
    }

    protected function validate4Insert(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        $this->getModel()->checkProjectCode($input->id, $input->project_code);
        $this->getModel()->checkOperationCode($input->id, $input->operation_code);
        //svn://192.168.81.129/p001
        $input->svn_url = "svn://192.168.81.129/" . $input->project_code;

        //$project_code . "_dev" . $this->NGINX_SERVER_DOMAIN
        $input->dev_url = $input->project_code . "_dev" . $this->NGINX_SERVER_DOMAIN;
        $input->test_url = $input->project_code . "_test" . $this->NGINX_SERVER_DOMAIN;
        $input->product_url = $input->project_code . $this->NGINX_SERVER_DOMAIN;
    }

    protected function setDefaultQuery(\App\Backend\Models\Input $input)
    {
        if (isset($_SESSION['roleInfo'])) {
            $roleAlias = $_SESSION['roleInfo']['alias'];
        } else {
            $roleAlias = 'guest';
        }
        if ($roleAlias != 'superAdmin') {
            $queryCondtions = array(
                '$exp' => " ( exists (select * from icompany_project_user where `icompany_project_user`.`project_id` = `icompany_project`.`_id` and `icompany_project_user`.`user_id` = '{$_SESSION['admin_id']}' and `icompany_project_user`.`__REMOVED__` = 0) ) "
            );
            // $queryCondtions = array(
            //     'id' => array('$in' => array())
            // );
            $input->setDefaultQuery($queryCondtions);
        }
        return $input;
    }

    protected function insert(\App\Backend\Models\Input $input, $row)
    {
        try {
            $this->modelDbProject->begin();

            // 新建一条公司项目记录
            $newInfo = parent::insert($input, $row);

            // 创建一条公司项目记录所对应的idb管理用的数据库记录
            $data = array();
            $data['company_project_id'] = $newInfo['_id'];
            $data['name'] = $newInfo['project_name'];
            $data['dbname'] = $newInfo['project_code'];
            $data['sn'] = $newInfo['db_pwd'];
            $data['desc'] = $newInfo['description'];
            $data['isSystem'] = $newInfo['isSystem'];
            $this->modelDbProject->insert($data);

            $this->modelDbProject->commit();
            return $newInfo;
        } catch (\Exception $e) {
            $this->modelDbProject->rollback();
            throw $e;
        }
    }

    // 创建数据库
    protected function createDatabase($db_name)
    {
        $di = \Phalcon\DI::getDefault();
        $connection = $di['db4admin'];
        $dbret1 = $connection->execute("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return $dbret1;
    }

    // 创建数据库用户
    protected function grantDatabaseUser($db_name, $db_user, $db_pwd)
    {
        //GRANT SELECT,INSERT,UPDATE,REFERENCES,DELETE,CREATE,DROP,ALTER,INDEX,TRIGGER,CREATE VIEW,SHOW VIEW,EXECUTE,ALTER ROUTINE,CREATE ROUTINE,CREATE TEMPORARY TABLES,LOCK TABLES,EVENT ON `210616fg0882`.* TO 'lichenglong'@'%';

        /*高级选项*/
        /*服务器权限设置*/
        /*数据库权限设置*/
        // 检查用户是否存在
        $di = \Phalcon\DI::getDefault();
        $connection = $di['db4admin'];
        // 检查用户是否存在
        $dbUserInfo = $connection->fetchOne("SELECT * FROM user where User='{$db_user}' and Host='%'", MYDB_FETCH_ASSOC);
        // print_r($typeInfo);
        if (empty($dbUserInfo)) {
            // 创建数据库的用户
            $dbret2 = $connection->execute("CREATE USER '{$db_user}'@'%' IDENTIFIED BY '{$db_pwd}'", array());
            // 如果是失败的话
            if (empty($dbret2)) {
            }
            // 授权数据库的用户
            $dbret3 = $connection->execute("GRANT SELECT,INSERT,UPDATE,REFERENCES,DELETE,CREATE,ALTER,DROP,INDEX,TRIGGER,CREATE VIEW,SHOW VIEW,EXECUTE,ALTER ROUTINE,CREATE ROUTINE,CREATE TEMPORARY TABLES,LOCK TABLES,EVENT ON `{$db_name}`.* TO '{$db_user}'@'%' IDENTIFIED BY '{$db_user}' WITH GRANT OPTION", array());
            // $dbret3 = $connection->execute("GRANT ALL PRIVILEGES ON `{$db_name}`.* TO '{$db_user}'@'%' IDENTIFIED BY '{$db_user}' WITH GRANT OPTION", array());
            // 如果是失败的话
            if (empty($dbret3)) {
            };
            // 刷新
            $connection->execute("FLUSH PRIVILEGES");
        }
    }

    // 创建组件
    protected function createComponents($db_name, $sql)
    {
        $sqlList = $this->getSqlList($sql);
        if (!empty($sqlList)) {
            $connection = $this->getConnection4Db($db_name);
            // $dbUserInfo = $connection->fetchOne("SELECT * FROM user", MYDB_FETCH_ASSOC);
            // print_r($dbUserInfo);
            // die('xxxxxxxxx');
            $dbret1 = true;
            if (!empty($dbret1)) {
                foreach ($sqlList as $query) {
                    // 查找是否有危险的操作
                    $isFound = $this->sqlFind(array(
                        'delete', 'drop', 'update', 'replace',
                        'truncate', 'rename', 'alter', 'call',
                        'revoke', 'grant', 'kill'
                        // , 'set'
                    ), $query);
                    // 如果没有的话就执行
                    if (!$isFound) {
                        $dbret3 = $connection->execute($query, array());
                        if (empty($dbret3)) {
                            throw new \Exception('创建组件发生了错误,sql:' . $query);
                        }
                    }
                }
            } else {
                throw new \Exception('创建组件发生了错误,sql:');
            }
        }
    }

    protected function getSqlList($sql)
    {
        $sql = trim($sql);
        if (empty($sql)) {
            return array();
        }
        $sql = str_replace("\r\n", "\n", $sql);
        $sql = str_replace("\r", "\n", $sql);

        $num = 0;
        foreach (explode(";\n", trim($sql)) as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            foreach ($queries as $query) {
                // $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
                $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }

    protected function sqlFind($sqlArr, $sql)
    {
        foreach ($sqlArr as  $value) {
            if (stripos($sql, $value) !== false) {
                throw new \Exception("We found '$value' in '$sql'");
                return true;
            }
        }
        return false;
    }

    protected function getConnection4Db($db_name)
    {
        $di = \Phalcon\DI::getDefault();
        $config = $di->get('config');
        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            //$connection = new \App\Common\Models\Base\Mysql\Pdo\DbAdapter(array(
            "host" => $config->database4admin->host,
            "username" => $config->database4admin->username,
            "password" => $config->database4admin->password,
            "dbname" => $db_name,
            "charset" => $config->database4admin->charset,
            "collation" => $config->database4admin->collation,
            'options'  => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config->database4admin->charset} COLLATE {$config->database4admin->collation};",
                //\PDO::ATTR_CASE => PDO::CASE_LOWER,
            ],
        ));
        return $connection;
    }
}
