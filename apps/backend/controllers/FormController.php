<?php
namespace App\Backend\Controllers;

use App\Backend\Models\Input;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Between;

/**
 * @title({name="表管理"})
 *
 * @name 表管理
 */
class FormController extends \App\Backend\Controllers\ControllerBase
{

    protected function getName()
    {
        return '';
    }

    protected function getPartials4List()
    {
        return array();
    }

    protected $firstLetterDatas = array(
        array(
            'name' => 'A',
            'value' => 'A'
        ),
        array(
            'name' => 'B',
            'value' => 'B'
        ),
        array(
            'name' => 'C',
            'value' => 'C'
        ),
        array(
            'name' => 'D',
            'value' => 'D'
        ),
        array(
            'name' => 'E',
            'value' => 'E'
        ),
        array(
            'name' => 'F',
            'value' => 'F'
        ),
        array(
            'name' => 'G',
            'value' => 'G'
        ),
        array(
            'name' => 'H',
            'value' => 'H'
        ),
        array(
            'name' => 'I',
            'value' => 'I'
        ),
        array(
            'name' => 'J',
            'value' => 'J'
        ),
        array(
            'name' => 'K',
            'value' => 'K'
        ),
        array(
            'name' => 'L',
            'value' => 'L'
        ),
        array(
            'name' => 'M',
            'value' => 'M'
        ),
        array(
            'name' => 'N',
            'value' => 'N'
        ),
        array(
            'name' => 'O',
            'value' => 'O'
        ),
        array(
            'name' => 'P',
            'value' => 'P'
        ),
        array(
            'name' => 'Q',
            'value' => 'Q'
        ),
        array(
            'name' => 'R',
            'value' => 'R'
        ),
        array(
            'name' => 'S',
            'value' => 'S'
        ),
        array(
            'name' => 'T',
            'value' => 'T'
        ),
        array(
            'name' => 'U',
            'value' => 'U'
        ),
        array(
            'name' => 'V',
            'value' => 'V'
        ),
        array(
            'name' => 'W',
            'value' => 'W'
        ),
        array(
            'name' => 'X',
            'value' => 'X'
        ),
        array(
            'name' => 'Y',
            'value' => 'Y'
        ),
        array(
            'name' => 'Z',
            'value' => 'Z'
        )
    );

    protected $trueOrFalseDatas = array(
        array(
            'name' => '是',
            'value' => '1'
        ),
        array(
            'name' => '否',
            'value' => '0'
        )
    );

    protected function getSchemas()
    {
        $schemas = array();
        $schemas['_id'] = array(
            'name' => 'ID',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'hidden',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            )
        );
        
        $schemas['__CREATE_TIME__'] = array(
            'name' => '创建时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['__MODIFY_TIME__'] = array(
            'name' => '修改时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['__REMOVED__'] = array(
            'name' => '是否删除',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => false,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getFilterInput()
    {
        $files = array();
        // Check if the user has uploaded files
        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                $files[$file->getKey()] = $file;
            }
        }
        
        $schemas = $this->getSchemas();
        $input = new Input();
        $input->id = $this->request->get('id', array(
            'trim',
            'string'
        ), '');
        foreach ($schemas as $key => $field) {
            if (empty($field['form']['is_show'])) {
                continue;
            }
            
            $input->addSchema($key, $field);
            // 文件的话,专门处理
            if ($field['data']['type'] == "file") {
                // 存在的话
                unset($input->$key);
                // print_r($files);
                if (isset($files[$key])) {
                    $file = $files[$key];
                    if ($file->isUploadedFile()) {
                        $fileId = $file->getName(); // myMongoId(new \MongoId());
                        $path = "";
                        if (! empty($field['data']['file'])) {
                            $fileInfo = $field['data']['file'];
                            $path = empty($fileInfo['path']) ? '' : trim($fileInfo['path'], '/') . '/';
                        }
                        $uploadPath = APP_PATH . "public/upload/{$path}";
                        makeDir($uploadPath);
                        $destination = "{$uploadPath}{$fileId}";
                        // die($destination);
                        
                        $file->moveTo($destination);
                        $input->$key = $fileId;
                    }
                } else {
                    // 检查request中是否有该字段
                    if ($this->request->has($key)) {
                        // 如果有的话,说明是删除了文件.
                        $input->$key = "";
                    }
                }
                // die('bbbb');
            } else {
                $filters = array(
                    'trim',
                    'string'
                );
                $defaultValue = "";
                if ($field['data']['type'] == "integer") {
                    $filters = array(
                        'trim',
                        'int'
                    );
                    $defaultValue = 0;
                } elseif ($field['data']['type'] == "boolean") {
                    $filters = array(
                        'trim',
                        'int'
                    );
                    $defaultValue = false;
                } elseif ($field['data']['type'] == "array") {
                    $filters = array(
                        'trim'
                    );
                    $defaultValue = "";
                } elseif ($field['data']['type'] == "json") {
                    $filters = array(
                        'trim'
                    );
                    $defaultValue = "";
                } elseif ($field['data']['type'] == "html") {
                    $filters = array(
                        'trim'
                    );
                    $defaultValue = "";
                }
                $input->$key = $this->request->get($key, $filters, $defaultValue);
            }
        }
        
        $input->isValid = function ($fieldName = null) use($input, $schemas) {
            $data = $this->request->get();
            $validation = new Validation();
            
            foreach ($schemas as $key => $field) {
                if (empty($field['form']['is_show'])) {
                    continue;
                }
                if (! empty($field['validation']['required'])) {
                    $validation->add($key, new PresenceOf(array(
                        'message' => "The {$key} is required"
                    )));
                }
            }
            
            $messages = $validation->validate($data);
            $messages = $messages->filter($fieldName);
            $input->messages = $messages;
            if (! empty($messages)) {
                return false;
            } else {
                return true;
            }
        };
        
        $input->getMessages = function () use($input, $schemas) {
            return empty($input->messages) ? array() : $input->messages;
        };
        
        return $input;
    }

    protected function getListFilterInput()
    {
        $columns = $this->request->get('columns', null, array(
            0 => array(
                'data' => '_id',
                'name' => '_id',
                'search' => array(
                    'value' => '',
                    'regex' => ''
                )
            )
        ));
        
        $draw = $this->request->get('draw', "int", 1);
        $order = $this->request->get('order', null, array(
            0 => array(
                'column' => 0,
                'dir' => 'asc'
            )
        ));
        $start = $this->request->get('start', "int", 0);
        $length = $this->request->get('length', "int", 10);
        $search = $this->request->get('search', null, array(
            'value' => '',
            'regex' => ''
        ));
        
        $input = new Input();
        $input->draw = $draw;
        $input->page = $start / $length + 1;
        $input->page_size = $length;
        $input->sort_by = $columns[$order[0]['column']]['name'];
        $input->sort_order = $order[0]['dir'];
        
        $schemas = $this->getSchemas();
        foreach ($schemas as $key => $field) {
            if (empty($field['search']['is_show'])) {
                continue;
            }
            $input->addSchema($key, $field);
            
            $filters = array(
                'trim',
                'string'
            );
            $defaultValue = "";
            if ($field['data']['type'] == "integer") {
                $filters = array(
                    'trim'
                );
                $defaultValue = 0;
            } elseif ($field['data']['type'] == "array") {
                $filters = array(
                    'trim'
                );
                $defaultValue = "";
            }
            
            $input->$key = $this->request->get($key, $filters, $defaultValue);
        }
        $input->isValid = function () {
            return true;
        };
        
        $input->getMessages = function () {
            return array();
        };
        
        return $input;
    }

    public function initialize()
    {
        parent::initialize();
        
        $this->view->setVar('formName', $this->getName());
        $this->view->setVar('schemas', $this->getSchemas());
        $this->view->setVar('partials4List', $this->getPartials4List());
    }

    /**
     * @title({name="显示列表页面"})
     *
     * @name 显示列表页面
     */
    public function listAction()
    {
        try {
            $this->view->setVar('defaultSort', $this->getModel()
                ->getDefaultSort());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="数据导出"})
     *
     * @name 显示列表页面
     */
    public function exportAction()
    {
        try {
            $input = $this->getListFilterInput();
            
            // 根据检索条件获取列表
            $list = $this->getModel()->getAllList($input);
            $this->export($list);
            exit();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="排序、分页、查询"})
     *
     * @name 排序、分页、查询
     */
    public function queryAction()
    {
        try {
            $this->view->disable();
            
            $input = $this->getListFilterInput();
            if (! $input->isValid()) {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            
            // 根据检索条件获取列表
            $list = $this->getList($input);
            
            // 将列表数据按照画面要求进行显示
            $list = $this->getList4Show($input, $list);
            
            $datas = array(
                'draw' => $input->draw,
                'recordsTotal' => $list['record_count'],
                'recordsFiltered' => $list['record_count'],
                'data' => $list['data']
            );
            
            echo (json_encode($datas));
            return true;
        } catch (\Exception $e) {
            $datas = array(
                'draw' => $input->draw,
                'error' => $e->getMessage()
            );
            echo (json_encode($datas));
            return false;
        }
    }

    /**
     * @title({name="显示添加页面"})
     *
     * @name 显示添加页面
     */
    public function addAction()
    {
        try {
            /* 初始化、取得 菜单信息 */
            $row = $this->getModel()->getEmptyRow($this->getFilterInput());
            $this->view->setVar('row', $row);
            $this->view->setVar('form_act', $this->getUrl("insert"));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="追加"})
     *
     * @name 追加
     */
    public function insertAction()
    {
        try {
            $input = $this->getFilterInput();
            if ($input->isValid()) {
                // 在进行插入处理之前进行检查
                $this->validate4Insert($input, array());
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            // insert
            $this->insert($input, array());
            /* 添加链接 */
            $link[0]['text'] = '继续添加' . $this->getName();
            $link[0]['href'] = $this->getUrl("add");
            $link[1]['text'] = '返回' . $this->getName() . '列表';
            $link[1]['href'] = $this->getUrl("list");
            $this->sysMsg($this->getName() . '添加成功!', 0, $link);
        } catch (\Exception $e) {
            die($e->getMessage());
            throw $e;
        }
    }

    /**
     * @title({name="显示编辑页面"})
     *
     * @name 显示编辑页面
     */
    public function editAction()
    {
        try {
            $input = $this->getFilterInput();
            if ($input->isValid("id")) {
                // get exist
                $row = $this->getModel()->getInfoById($input->id);
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            $this->view->setVar('row', $row);
            $this->view->setVar('form_act', $this->getUrl("update"));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="更新"})
     *
     * @name 更新
     */
    public function updateAction()
    {
        try {
            $input = $this->getFilterInput();
            if ($input->isValid()) {
                // get exist
                $row = $this->getModel()->getInfoById($input->id);
                if (empty($row)) {
                    throw new \Exception("更新的数据为空");
                }
                // 在进行更新处理之前进行检查
                $this->validate4Update($input, $row);
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            // update
            $this->update($input, $row);
            /* 添加链接 */
            $link[0]['text'] = '返回' . $this->getName() . '列表';
            $link[0]['href'] = $this->getUrl("list");
            $this->sysMsg($this->getName() . '编辑成功!', 0, $link);
        } catch (\Exception $e) {
            die($e->getMessage());
            throw $e;
        }
    }

    /**
     * @title({name="删除"})
     *
     * @name 删除
     */
    public function removeAction()
    {
        try {
            $this->view->disable();
            
            $input = $this->getFilterInput();
            
            if ($input->isValid("id")) {
                // 在进行删除处理之前进行检查
                $this->validate4Delete($input, array());
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            
            // delete
            $this->delete($input, array());
            
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getList(\App\Backend\Models\Input $input)
    {
        $list = $this->getModel()->getList($input);
        $this->view->setVar('list', $list['data']);
        $this->view->setVar('filter', $list['filter']);
        $this->view->setVar('record_count', $list['record_count']);
        $this->view->setVar('page_count', $list['page_count']);
        
        return $list;
    }

    protected function getModel()
    {
        return null;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        // foreach ($list['data'] as &$item) {
        // $item['show_name'] = str_repeat('&nbsp;', $item['level'] * 4) . $item['name'];
        // }
        return $list;
    }

    protected function validate4Insert(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        // $this->getModel()->checkName($input->id, $input->pid, $input->name);
    }

    protected function insert(\App\Backend\Models\Input $input, $row)
    {
        $this->getModel()->processInsertOrUpdate($input, $row);
    }

    protected function validate4Update(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        // $this->getModel()->checkName($input->id, $input->pid, $input->name);
        
        // /* 还有子菜单，不能更改 */
        // if ($input->pid != $row['pid']) {
        // $this->getModel()->checkIsLeaf($input->id);
        // }
    }

    protected function update(\App\Backend\Models\Input $input, $row)
    {
        $this->getModel()->processInsertOrUpdate($input, $row);
    }

    protected function validate4Delete(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        /* 还有子菜单，不能删除 */
        // $this->getModel()->checkIsLeaf($input->id);
    }

    protected function delete(\App\Backend\Models\Input $input, $row)
    {
        $this->getModel()->remove(array(
            '_id' => $input->id
        ));
    }

    protected function export(array $dataList)
    {
        $excel = array();
        
        $fields = array();
        $schemas = $this->getSchemas();
        foreach ($schemas as $key => $field) {
            if (empty($field['export']['is_show'])) {
                continue;
            }
            $fields[] = $field['name'];
        }
        $excel['title'] = array_values($fields);
        if (empty($excel['title'])) {
            die('请设置导出的字段');
        }
        $datas = array();
        foreach ($dataList as $data) {
            $item = array();
            foreach ($schemas as $key => $field) {
                if (empty($field['export']) || empty($field['export']['is_show'])) {
                    continue;
                }
                if ($field['data']['type'] == 'datetime') {
                    $item[] = date("Y-m-d H:i:s", $data[$key]->sec);
                } elseif ($field['data']['type'] == 'json') {
                    if (! empty($data[$key])) {
                        $values = array();
                        if (! empty($field['export']) && ! empty($field['export']['fields'])) {
                            foreach ($field['export']['fields'] as $f) {
                                $values[] = isset($data[$key][$f]) ? $data[$key][$f] : "";
                            }
                        }
                        if (! empty($values)) {
                            $item[] = implode(" ", $values);
                        } else {
                            $item[] = "";
                        }
                    } else {
                        $item[] = "";
                    }
                } else {
                    $item[] = $data[$key];
                }
            }
            $datas[] = $item;
        }
        $excel['result'] = $datas;
        
        $fileName = date('YmdHis');
        arrayToCVS($fileName, $excel);
        
        // $zip = new \ZipArchive();
        // $filename = "data_export_" . date('YmdHis', time()) . '.zip'; // 随机文件名
        // $zipname = sys_get_temp_dir() . "/" . $filename;
        
        // if (! file_exists($zipname)) {
        // $zip->open($zipname, ZipArchive::OVERWRITE); // 创建一个空的zip文件
        // $zip->addFile($file1, iconv("UTF-8", "GB2312", '订单文件' . $extension));
        // $zip->close();
        
        // ob_end_clean();
        // // 打开文件
        // $file = fopen($zipname, "r");
        // // 返回的文件类型
        // Header("Content-type: application/octet-stream");
        // // 按照字节大小返回
        // Header("Accept-Ranges: bytes");
        // // 返回文件的大小
        // Header("Accept-Length: " . filesize($zipname));
        // // 这里对客户端的弹出对话框，对应的文件名
        // Header("Content-Disposition: attachment; filename=" . $filename);
        // // 修改之前，一次性将数据传输给客户端
        // echo fread($file, filesize($zipname));
        // // 修改之后，一次只传输1024个字节的数据给客户端
        // // 向客户端回送数据
        // $buffer = 1024; //
        // // 判断文件是否读完
        // while (! feof($file)) {
        // // 将文件读入内存
        // $file_data = fread($file, $buffer);
        // // 每次向客户端回送1024个字节的数据
        // echo $file_data;
        // }
        // fclose($file);
        // unlink($zipname); // 下载完成后要主动删除
        // exit();
        // }
    }

    /**
     * 获取微信客户端对象
     * 
     * @return \Weixin\Client
     */
    protected function getWeixin()
    {
        $config = $this->getDI()->get('config');
        $appid = isset($_GET['appid']) ? trim($_GET['appid']) : $config['weixin']['appid'];
        
        $modelWeixinApplication = new \App\Weixin\Models\Application();
        $appWeixinConfig = $modelWeixinApplication->getTokenByAppid($appid);
        
        $weixin = new \Weixin\Client();
        $weixin->setAccessToken('3YgWolF5jpZMCh5OU2ZuBuSq3x6LXW1KoSGTZoy0jtKS4W2Xo_ZFkgBDufgHUZ4UicDlRqAQnP8CAfSfBOzZZ1mxk17iubvhYQHbhQgAx1gtZIgxr9DmD-2uS_0cMiiKQGFiADAIYX');
        // if (! empty($appWeixinConfig['access_token'])) {
        // $weixin->setAccessToken($appWeixinConfig['access_token']);
        // }
        
        return $weixin;
    }
}