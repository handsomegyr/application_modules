<?php

namespace App\Backend\Controllers;

use App\Backend\Models\Input;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Between;
use Phalcon\Filter\FilterFactory;

/**
 * @title({name="表管理"})
 *
 * @name 表管理
 */
class FormController extends \App\Backend\Controllers\ControllerBase
{
    // 是否只读
    protected $readonly = false;

    protected function getName()
    {
        return '';
    }

    protected function getPartials4List()
    {
        return array();
    }

    // protected $firstLetterDatas = array(
    //     array(
    //         'name' => 'A',
    //         'value' => 'A'
    //     ),
    //     array(
    //         'name' => 'B',
    //         'value' => 'B'
    //     ),
    //     array(
    //         'name' => 'C',
    //         'value' => 'C'
    //     ),
    //     array(
    //         'name' => 'D',
    //         'value' => 'D'
    //     ),
    //     array(
    //         'name' => 'E',
    //         'value' => 'E'
    //     ),
    //     array(
    //         'name' => 'F',
    //         'value' => 'F'
    //     ),
    //     array(
    //         'name' => 'G',
    //         'value' => 'G'
    //     ),
    //     array(
    //         'name' => 'H',
    //         'value' => 'H'
    //     ),
    //     array(
    //         'name' => 'I',
    //         'value' => 'I'
    //     ),
    //     array(
    //         'name' => 'J',
    //         'value' => 'J'
    //     ),
    //     array(
    //         'name' => 'K',
    //         'value' => 'K'
    //     ),
    //     array(
    //         'name' => 'L',
    //         'value' => 'L'
    //     ),
    //     array(
    //         'name' => 'M',
    //         'value' => 'M'
    //     ),
    //     array(
    //         'name' => 'N',
    //         'value' => 'N'
    //     ),
    //     array(
    //         'name' => 'O',
    //         'value' => 'O'
    //     ),
    //     array(
    //         'name' => 'P',
    //         'value' => 'P'
    //     ),
    //     array(
    //         'name' => 'Q',
    //         'value' => 'Q'
    //     ),
    //     array(
    //         'name' => 'R',
    //         'value' => 'R'
    //     ),
    //     array(
    //         'name' => 'S',
    //         'value' => 'S'
    //     ),
    //     array(
    //         'name' => 'T',
    //         'value' => 'T'
    //     ),
    //     array(
    //         'name' => 'U',
    //         'value' => 'U'
    //     ),
    //     array(
    //         'name' => 'V',
    //         'value' => 'V'
    //     ),
    //     array(
    //         'name' => 'W',
    //         'value' => 'W'
    //     ),
    //     array(
    //         'name' => 'X',
    //         'value' => 'X'
    //     ),
    //     array(
    //         'name' => 'Y',
    //         'value' => 'Y'
    //     ),
    //     array(
    //         'name' => 'Z',
    //         'value' => 'Z'
    //     )
    // );

    // protected $trueOrFalseDatas = array(
    //     array(
    //         'name' => '是',
    //         'value' => '1'
    //     ),
    //     array(
    //         'name' => '否',
    //         'value' => '0'
    //     )
    // );

    protected $firstLetterDatas = array(
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
        'D' => 'D',
        'E' => 'E',
        'F' => 'F',
        'G' => 'G',
        'H' => 'H',
        'I' => 'I',
        'J' => 'J',
        'K' => 'K',
        'L' => 'L',
        'M' => 'M',
        'N' => 'N',
        'O' => 'O',
        'P' => 'P',
        'Q' => 'Q',
        'R' => 'R',
        'S' => 'S',
        'T' => 'T',
        'U' => 'U',
        'V' => 'V',
        'W' => 'W',
        'X' => 'X',
        'Y' => 'Y',
        'Z' => 'Z'
    );
    protected $trueOrFalseDatas = array(
        '0' => '否',
        '1' => '是',
    );

    protected $methodDatas = array(
        'GET' => 'GET',
        'POST' => 'POST',
        'PUT' => 'PUT',
        'DELETE' => 'DELETE',
        'OPTIONS' => 'OPTIONS',
        'PATCH' => 'PATCH',
        'LINK' => 'LINK',
        'UNLINK' => 'UNLINK',
        'COPY' => 'COPY',
        'HEAD' => 'HEAD',
        'PURGE' => 'PURGE'
    );

    private function sortSchemas($schemas)
    {
        //$idSchema = $schemas['_id'];
        if (strtolower($this->actionName) == 'add') {
            $schemas['__CREATE_TIME__']['form']['is_show'] = false;
            $schemas['__MODIFY_TIME__']['form']['is_show'] = false;
        } elseif (strtolower($this->actionName) == 'edit') {
            $schemas['__CREATE_TIME__']['form']['is_show'] = false;
            $schemas['__MODIFY_TIME__']['form']['is_show'] = true;
            $schemas['__MODIFY_TIME__']['form']['readonly'] = true;
        }

        //unset($schemas['_id']);
        foreach ($schemas as $key => &$field) {

            if (empty($field['list']['name'])) {
                $field['list']['name'] = $field['name'];
            }
            if (!isset($field['list']['render'])) {
                if (
                    $field['data']['type'] == "file" ||
                    $field['data']['type'] == "multifile"
                ) {
                    $field['list']['render'] = 'img';
                }
            }

            if (!empty($field['list']['is_editable']) && empty($field['list']['ajax'])) {
                $field['list']['ajax'] = 'update?_field_edt_=' . $key;
            }

            if ($field['data']['type'] == 'json') {
                $field['data']['defaultValue'] = '{}';
            } elseif ($field['data']['type'] == 'array') {
                $field['data']['defaultValue'] = '';
            } elseif ($field['data']['type'] == 'file') {
                $field['data']['length'] = 255;
                if (!isset($field['data']['file']) || !isset($field['data']['file']['path'])) {
                    $objModel = $this->getModel();
                    if (!empty($objModel)) {
                        $field['data']['file']['path'] = $objModel->getUploadPath();
                    }
                }
            }

            // 根据请求地址进行特殊的处理
            if (in_array($this->actionName, array('add'))) {
                if (!empty($field['form']['getFormSettings'])) {
                }
            }

            if (empty($field['form']['name'])) {
                $field['form']['name'] = $field['name'];
            }

            if (!empty($this->readonly)) {
                $field['form']['readonly'] = true;
            }

            if (empty($field['form']['placeholder'])) {
                $field['form']['placeholder'] = "输入 " . $field['form']['name'];
            }

            if ($field['form']['input_type'] == 'image' || $field['form']['input_type'] == 'multipleImage') {
                $field['form'][$field['form']['input_type']]['type'] = 'image';
                $field['form'][$field['form']['input_type']]['filetype'] = '';
            }

            if (empty($field['search']['name'])) {
                $field['search']['name'] = $field['name'];
            }
            if (empty($field['search']['placeholder'])) {
                $field['search']['placeholder'] =  "输入 " . $field['search']['name'];
            }

            if (empty($field['search']['input_type'])) {
                $field['search']['input_type'] =  $field['form']['input_type'];
            }

            if (empty($field['search']['items'])) {
                $field['search']['items'] =  $field['form']['items'];
            }
            if (!isset($field['search']['is_show'])) {
                $field['search']['is_show'] = $field['form']['is_show'];
                if (empty($field['search']['is_show'])) {
                    $field['search']['is_show'] = $field['list']['is_show'];
                }
            }

            if (empty($field['export']['name'])) {
                $field['export']['name'] = $field['form']['name'];
            }
            if (!isset($field['export']['is_show'])) {
                $field['export']['is_show'] = $field['form']['is_show'];
                if (empty($field['export']['is_show'])) {
                    $field['export']['is_show'] = $field['list']['is_show'];
                }
            }
        }

        return $schemas;
    }

    protected function getHeaderTools()
    {
        $tools = array();
        return $this->getHeaderTools2($tools);
    }

    protected function getHeaderTools2($tools)
    {
        return $tools;
    }

    protected function getRowTools()
    {
        $tools = array();
        return $this->getRowTools2($tools);
    }

    protected function getRowTools2($tools)
    {
        return $tools;
    }

    protected function getFormTools()
    {
        $tools = array();
        return $this->getFormTools2($tools);
    }

    protected function getFormTools2($tools)
    {
        return $tools;
    }

    private $schemas = array();
    private function getSchemas()
    {
        if (!empty($this->schemas)) {
            return $this->schemas;
        }

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
                'is_show' => true,
                'width' => '27px'
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas = $this->getSchemas2($schemas);

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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['__CREATE_USER_ID__'] = array(
            'name' => '创建操作者ID',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false,
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['__CREATE_USER_NAME__'] = array(
            'name' => '创建操作者名称',
            'data' => array(
                'type' => 'string',
                'length' => '100'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false,
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
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
                'readonly' => true,
                'is_show' => false
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

        $schemas['__MODIFY_USER_ID__'] = array(
            'name' => '修改操作者ID',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false,
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['__MODIFY_USER_NAME__'] = array(
            'name' => '修改操作者名称',
            'data' => array(
                'type' => 'string',
                'length' => '100'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false,
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
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

        $schemas['__REMOVE_TIME__'] = array(
            'name' => '删除时间',
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
                'readonly' => true,
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['__REMOVE_USER_ID__'] = array(
            'name' => '删除操作者ID',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false,
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['__REMOVE_USER_NAME__'] = array(
            'name' => '删除操作者名称',
            'data' => array(
                'type' => 'string',
                'length' => '100'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false,
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        return $this->sortSchemas($schemas);
    }

    protected function getSchemas2($schemas)
    {
        return $schemas;
    }

    protected function getFilterInput($fields4change = array())
    {
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

        // print_r($files);
        // die('xxxxxx');
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
            if (!empty($fields4change)) {
                if (!in_array($key, $fields4change)) {
                    continue;
                }
            }
            $input->addSchema($key, $field);
            // 文件的话,专门处理
            if ($field['data']['type'] == "file") {
                // 存在的话
                unset($input->$key);
                // print_r($files);
                if (isset($files[$key])) {
                    $file = $files[$key];
                    $fileId = $this->uploadFile($file, $field);
                    if (!empty($fileId)) {
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
            } elseif ($field['data']['type'] == "multifile") {
                // 存在的话
                unset($input->$key);
                // print_r($files);
                if (isset($files[$key])) {
                    $fileList4Field = array();
                    foreach ($files[$key] as $idx => $file) {
                        $fileId = $this->uploadFile($file, $field);
                        if (!empty($fileId)) {
                            $fileList4Field[] = $fileId;
                        }
                    }
                    if (!empty($fileList4Field)) {
                        $input->$key = $fileList4Field;
                    }
                } else {
                    // 检查request中是否有该字段
                    if ($this->request->has($key)) {
                        // 如果有的话,说明是删除了文件.
                        $input->$key = array();
                    }
                }
                // die('cccc');
            } else {
                $filters = array(
                    'trim',
                    // 'string'
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

                // 将元素中空值的排除掉
                if ($field['data']['type'] == "array") {
                    if (!empty($input->$key)) {
                        if (is_string($input->$key)) {
                            $input->$key = \json_decode($input->$key, true);
                        }
                        // var_dump($input->$key);
                        // die($key);
                        $input->$key = array_filter($input->$key, function ($element) {
                            $element = trim($element);
                            return strlen($element) > 0;
                        });
                    } else {
                        $input->$key = array();
                    }
                }
            }
        }

        $input->isValid = function ($fieldName = null) use ($input, $schemas) {
            $data = $this->request->get();
            $validation = new Validation();
            foreach ($schemas as $key => $field) {
                if (empty($field['form']['is_show'])) {
                    continue;
                }
                if (!empty($field['validation']['required']) && !in_array($field['data']['type'], array('file', 'multifile'))) {
                    $validation->add($key, new PresenceOf(array(
                        'message' => "【{$field['form']['name']}】字段是必填项!"
                    )));
                }
            }

            $messages = $validation->validate($data);
            if (!empty($fieldName)) {
                $errmsglist = $messages->filter($fieldName);
            } else {
                $errmsglist = array();
                if (!empty($messages)) {
                    foreach ($messages as $key => $message) {
                        $errmsglist[$key] = $message;
                    }
                }
            }
            $input->messages = $errmsglist;
            if (!empty($errmsglist)) {
                return false;
            } else {
                return true;
            }
        };

        $input->getMessages = function () use ($input, $schemas) {
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

        // 保存在会话中
        $_SESSION['search_filter'] = array(); //$input->getFilter();
        return $input;
    }

    public function initialize()
    {
        parent::initialize();

        // 检查token
        if ($this->request->isAjax() && $this->request->isPost()) {
            $token = $this->request->get('_token', array(
                'trim'
            ), '');
            $this->checkToken($token);
        }

        // ajax请求的话
        if ($this->request->isAjax()) {
            $this->view->disable();
        } elseif (!in_array(strtolower($this->actionName), array('list', 'add', 'edit'))) {
            $this->view->disable();
        }

        $this->view->setVar('readonly', $this->readonly);
        $this->view->setVar('schemas', array());
        $this->view->setVar('formName', $this->getName());
        // headerTools
        $this->view->setVar('headerTools', array());
        // RowTools
        $this->view->setVar('rowTools', array());
        // FormTools
        $this->view->setVar('formTools', array());
        $this->view->setVar('partials4List', array());
    }

    /**
     * @title({name="显示列表页面"})
     *
     * @name 显示列表页面
     */
    public function listAction()
    {
        try {
            $this->view->setVar('schemas', $this->getSchemas());
            $this->view->setVar('partials4List', $this->getPartials4List());
            // headerTools
            $this->view->setVar('headerTools', $this->getHeaderTools());
            // RowTools
            $this->view->setVar('rowTools', $this->getRowTools());
            $this->view->setVar('defaultSort', $this->getModel()
                ->getDefaultSort());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="数据导出"})
     *
     * @name 数据导出
     */
    public function exportAction()
    {
        try {
            $input = $this->getListFilterInput();

            // 根据检索条件获取列表
            resetTimeMemLimit();
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
            unset($_SESSION['toastr']);

            $input = $this->getListFilterInput();
            if (!$input->isValid()) {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }

            // 根据检索条件获取列表
            $list = $this->getList($input);
            // 保存一下从数据库中取出的数据行
            $orginalDataList = array();
            if (!empty($list['data'])) {
                $schemas = $this->getSchemas();
                foreach ($list['data'] as &$item) {
                    $orginalDataList[$item['id']] = $item;
                    $item = $this->returnRecord4ListShow($item, $schemas);

                    // 权限控制相关
                    // 编辑和删除按钮默认都是显示的
                    $item['op']['edit'] = true;
                    $item['op']['remove'] = true;

                    // 返回哪些rowtools可以操作
                    $rowTools = $this->getRowTools();
                    if (!empty($rowTools)) {
                        foreach ($rowTools as $opkey => $tool) {
                            $is_can = false;
                            $dataRow = isset($orginalDataList[$item['id']]) ? $orginalDataList[$item['id']] : array();
                            if (!empty($tool['is_show'])) {
                                $is_can = is_callable($tool['is_show']) ? $tool['is_show']($dataRow) : true;
                            }
                            $item['op'][$opkey] = $is_can;
                        }
                    }
                }
            }

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
            $this->view->setVar('schemas', $this->getSchemas());
            // FormTools
            $this->view->setVar('formTools', $this->getFormTools());
            /* 初始化、取得 菜单信息 */
            $row = $this->getModel()->getEmptyRow($this->getFilterInput());
            foreach ($row as $field => $value) {
                $row[$field] = $this->adjustDataTime4Show($value);
            }
            $this->view->setVar('row', $row);
            $this->view->setVar('form_act', $this->getUrl("insert"));
            $this->view->setVar('list_url', $this->getUrl("list"));
        } catch (\Exception $e) {
            // throw $e;
            $_SESSION['admin_error']['errorMsg'] = $e->getMessage();
            return $this->_redirect($this->getUrl("show500", "error", "admin"));
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
            // /* 添加链接 */
            // $link[0]['text'] = '继续添加' . $this->getName();
            // $link[0]['href'] = $this->getUrl("add");
            // $link[1]['text'] = '返回' . $this->getName() . '列表';
            // $link[1]['href'] = $this->getUrl("list");
            // $this->sysMsg($this->getName() . '添加成功!', 0, $link);

            unset($_SESSION['toastr']);
            $_SESSION['toastr']['type'] = "success";
            $_SESSION['toastr']['message'] = '添加成功!';
            $_SESSION['toastr']['options'] = array();

            $this->makeJsonResult($this->getUrl("list"));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
            // die($e->getMessage());
            // throw $e;
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
            $this->view->setVar('schemas', $this->getSchemas());
            // FormTools
            $this->view->setVar('formTools', $this->getFormTools());
            $input = $this->getFilterInput();
            if ($input->isValid("id")) {
                // get exist
                $row = $this->getModel()->getInfoById($input->id);
                if (empty($row)) {
                    throw new \Exception('数据未找到');
                }
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            foreach ($row as $field => $value) {
                $row[$field] = $this->adjustDataTime4Show($value);
            }
            $this->view->setVar('row', $row);
            $this->view->setVar('form_act', $this->getUrl("update"));
            $this->view->setVar('list_url', $this->getUrl("list"));
        } catch (\Exception $e) {
            // throw $e;
            $_SESSION['admin_error']['errorMsg'] = $e->getMessage();
            return $this->_redirect($this->getUrl("show500", "error", "admin"));
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
            $fields4change = array();

            // 如果指定了某个字段需要更新的话
            $field = $this->request->get('_field_edt_', array(
                'trim',
                'string'
            ), '');
            if (!empty($field)) {
                $fields4change[] = $field;
            }
            $__MODIFY_TIME__ = $this->request->get('__MODIFY_TIME__', array(
                'trim',
                'string'
            ), '');
            $input = $this->getFilterInput($fields4change);
            if ($input->isValid()) {
                // get exist
                $row = $this->getModel()->getInfoById($input->id);
                if (empty($row)) {
                    throw new \Exception("更新的数据为空");
                }
                if (!empty($__MODIFY_TIME__)) {
                    // 检查更新时间是否已经改变 避免并发
                    if (date('Y-m-d H:i:s', $row['__MODIFY_TIME__']->sec) != $__MODIFY_TIME__) {
                        throw new \Exception("该数据已被他人修改过");
                    }
                }
                // 在进行更新处理之前进行检查
                $this->validate4Update($input, $row);
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            // update
            if (empty($field)) {

                $this->update($input, $row);
                // 处理多图片或多文件的排序
                $filesort = $this->request->get("_file_sort_", array(
                    'trim',
                ), array());
                if (!empty($filesort)) {
                    // 获取最新的数据信息
                    $newRow = $this->getModel()->getInfoById($input->id);
                    $updateData = array();
                    foreach ($filesort as $field => $sortvalue) {
                        if (!empty($sortvalue)) {
                            $updateValue = explode(',', $sortvalue);
                            if (!empty($updateValue)) {
                                // 检查是否还存在这些文件 该情况发生在 用户先进行了文件排序 然后直接删除了文件
                                $is_exist = true;
                                foreach ($updateValue as $file) {
                                    if (!in_array($file, $newRow[$field])) {
                                        $is_exist = false;
                                        break;
                                    }
                                }
                                if ($is_exist) {
                                    $updateData[$field] = $updateValue;
                                }
                            }
                        }
                    }
                    if (!empty($updateData)) {
                        $query = array(
                            '_id' => $input->id,
                        );
                        $this->getModel()->update($query, array(
                            '$set' => $updateData
                        ));
                    }
                }

                // /* 添加链接 */
                // $link[0]['text'] = '返回' . $this->getName() . '列表';
                // $link[0]['href'] = $this->getUrl("list");

                // Using session flash
                // $this->flashSession->success('Your information was stored correctly!');

                unset($_SESSION['toastr']);
                $_SESSION['toastr']['type'] = "success";
                $_SESSION['toastr']['message'] = '保存成功!';
                $_SESSION['toastr']['options'] = array();

                $this->makeJsonResult($this->getUrl("list"));
            } else {
                $updateData = array();
                $updateData[$field] = $input->$field;

                $query = array(
                    '_id' => $input->id
                );
                if (!empty($__MODIFY_TIME__)) {
                    $query['__MODIFY_TIME__'] = $__MODIFY_TIME__;
                }
                $this->getModel()->update($query, array(
                    '$set' => $updateData
                ));
                $this->makeJsonResult(stripslashes($input->$field));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
            //die($e->getMessage());
            //throw $e;
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

            $this->makeJsonResult($this->getUrl("list"), '删除成功！');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="删除文件"})
     *
     * @name 删除
     */
    public function removefileAction()
    {
        try {
            $id = $this->request->get('id', array(
                'trim',
                'string'
            ), '');

            $field = $this->request->get('_field_del_', array(
                'trim',
                'string'
            ), '');

            $key = $this->request->get('key', array(
                'trim',
                'string'
            ), '');

            if (!empty($key)) {
                $info = $this->getModel()->getInfoById($id);
                if (!empty($info) && !empty($info[$field])) {
                    //throw new \Exception($messageInfo);
                    // var_dump($info[$field]);
                    // die($field);

                    if (is_array($info[$field])) {
                        $updateValue = array_filter($info[$field], function ($v) use ($key) {
                            return $v != $key;
                        });
                        if (!empty($updateValue)) {
                            $updateValue = array_values($updateValue);
                        } else {
                            $updateValue = array();
                        }
                        // print_r($updateValue);
                        // die($field);
                    } else {
                        $updateValue  =  str_replace($key,  "",  $info[$field]);
                    }
                    $updateData = array();
                    $updateData[$field] = $updateValue;
                    $this->getModel()->update(array(
                        '_id' => $id
                    ), array(
                        '$set' => $updateData
                    ));
                }
            }

            // {"status":true,"message":"\u5220\u9664\u6210\u529f !"}
            $res = array(
                'status' => true,
                'message' => "删除文件成功"
            );
            $this->response->setJsonContent($res)->send();
            // $this->makeJsonResult();
        } catch (\Exception $e) {
            $res = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            $this->response->setJsonContent($res)->send();
            //$this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="追加成功消息页面"})
     *
     * @name 追加成功消息页面
     */
    protected function sysmsg4insertAction()
    {
        try {
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
     * @title({name="更新成功消息页面"})
     *
     * @name 更新成功消息页面
     */
    protected function sysmsg4updateAction()
    {
        try {
            /* 添加链接 */
            $link[0]['text'] = '返回' . $this->getName() . '列表';
            $link[0]['href'] = $this->getUrl("list");
            $this->sysMsg($this->getName() . '编辑成功!', 0, $link);
        } catch (\Exception $e) {
            die($e->getMessage());
            throw $e;
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
        if (empty($_SESSION['admin_id'])) {
            throw new \Exception('后台操作用户未登录');
        }

        $data = $input->getFormData(true);
        // $this->setPhql(true);
        if (empty($row) || empty($row['_id'])) {
            return $this->getModel()->insert($data);
        } else {
            throw new \Exception("生成操作的数据不合法");
        }
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
        if (empty($_SESSION['admin_id'])) {
            throw new \Exception('后台操作用户未登录');
        }

        $data = $input->getFormData(true);
        // $this->setPhql(true);
        if (empty($row) || empty($row['_id'])) {
            throw new \Exception("更新操作的数据不合法");
        } else {
            $query['_id'] = $row['_id'];
            // $this->setDebug(true);
            return $this->getModel()->update($query, array(
                '$set' => $data
            ));
        }
    }

    protected function validate4Delete(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        /* 还有子菜单，不能删除 */
        // $this->getModel()->checkIsLeaf($input->id);
    }

    protected function delete(\App\Backend\Models\Input $input, $row)
    {
        if (empty($_SESSION['admin_id'])) {
            throw new \Exception('后台操作用户未登录');
        }
        $query = array(
            '_id' => $input->id
        );
        $this->getModel()->remove($query);
    }

    // 将数据库的一行转换成画面显示所需的一行
    protected function returnRecord4ListShow($item, $schemas)
    {
        foreach ($item as $field => $value) {
            if ($value instanceof \MongoDate || $value instanceof \MongoTimestamp) {
                $value = $this->adjustDataTime4Show($value);
                $item[$field] = $value;
            }
            // 如果是数组或对象
            if (is_array($value) || is_object($value)) {
                // 如果是空值那么就是空字符串
                if (empty($value)) {
                    $value = "";
                } else {
                    $value = \json_encode($value);
                }
                $item[$field] = $value;
            }
            $column = new \App\Backend\Models\Column($field, $value, $schemas, $item, $this->url->getBaseUri());
            // 扩展设置
            if (!empty($schemas[$field])) {
                if (!empty($schemas[$field]['list']['is_show'])) {
                    if (!empty($schemas[$field]['list']['extensionSettings'])) {
                        if (is_callable($schemas[$field]['list']['extensionSettings'])) {
                            $value4Show = call_user_func_array($schemas[$field]['list']['extensionSettings'], [$column, $this]);
                            //$value = $schemas[$field]['list']['extensionSettings']($column, null);
                            if (!is_null($value4Show)) {
                                $value = $value4Show;
                                $item[$field] = $value;
                            }
                        }
                    }
                }
            }
            // $value = htmlentities($value, ENT_QUOTES, "UTF-8");
        }
        return $item;
    }

    protected function export(array $dataList)
    {
        $excel = array();

        $fields = array();
        $schemas = $this->getSchemas();
        foreach ($schemas as $key => $field) {
            if (!isset($field['export']['is_show'])) {
                $field['export']['is_show'] = empty($field['form']['is_show']) ? false : $field['form']['is_show'];
            }
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
                if (!isset($field['export']['is_show'])) {
                    $field['export']['is_show'] = empty($field['form']['is_show']) ? false : $field['form']['is_show'];
                }
                if (empty($field['export']) || empty($field['export']['is_show'])) {
                    continue;
                }
                if ($field['data']['type'] == 'datetime') {
                    $item[] = $this->adjustDataTime4Show($data[$key]);
                } elseif ($field['data']['type'] == 'json') {
                    if (!empty($data[$key])) {
                        if (!empty($field['export']['fields'])) {
                            $values = array();
                            foreach ($field['export']['fields'] as $f) {
                                $values[] = isset($data[$key][$f]) ? $data[$key][$f] : "";
                            }
                            if (!empty($values)) {
                                $item[] = implode(" ", $values);
                            } else {
                                $item[] = "";
                            }
                        } else {
                            $item[] = \json_encode($data[$key]);
                        }
                    } else {
                        $item[] = "";
                    }
                } elseif ($field['data']['type'] == 'multifile') {
                    if (!empty($data[$key])) {
                        $item[] = implode(",", $data[$key]);
                    } else {
                        $item[] = "";
                    }
                } elseif ($field['data']['type'] == 'array') {
                    if (!empty($data[$key])) {
                        $item[] = implode(",", $data[$key]);
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

    protected function showModal($title, $fields = array(), $row = array())
    {
        $this->view->disable();
        // $this->disableLayout();
        $viewClass = $this->view->getVar("viewClass");
        $viewClass['form-group'] = "form-group";
        $viewClass['label'] = "";
        $viewClass['field'] = "";

        $data = array(
            'title' => $title,
            'modal_id' => \uniqid(),
            'fields' => $fields,
            'row' => $row,
            'viewClass' => $viewClass
        );
        $this->view->setVars($data);
        // $data['content'] = $this->view->getRender('blackuser', 'modal', $data);
        \ob_start();
        $this->view->partial("partials/modal");
        $data['content'] = \ob_get_clean();
        return $this->makeJsonResult($data, '弹窗显示成功');
    }

    protected function uploadFile($file, $field)
    {
        if ($file->isUploadedFile()) {
            // $fileId = $file->getName();
            $ext = strtolower($file->getExtension());
            $fileId = myMongoId(new \MongoId()) . "." . $ext;

            $path = "";
            if (!empty($field['data']['file'])) {
                $fileInfo = $field['data']['file'];
                $path = empty($fileInfo['path']) ? '' : trim($fileInfo['path'], '/') . '/';
            }
            $uploadPath = APP_PATH . "public/upload/{$path}";
            makeDir($uploadPath);
            $destination = "{$uploadPath}{$fileId}";
            $file->moveTo($destination);
            return $fileId;
        } else {
            return '';
        }
    }

    protected function getWeixinConfig()
    {
        $config = $this->getDI()->get('config');
        $appid = isset($_GET['appid']) ? trim($_GET['appid']) : $config['weixin']['appid'];
        $modelWeixinApplication = new \App\Weixin\Models\Application();
        $appWeixinConfig = $modelWeixinApplication->getTokenByAppid($appid);
        return $appWeixinConfig;
    }

    /**
     * 获取微信客户端对象
     *
     * @return \Weixin\Client
     */
    protected function getWeixin()
    {
        $appWeixinConfig = $this->getWeixinConfig();

        $weixin = new \Weixin\Client();
        $weixin->setAccessToken('5r40in-A3AT16qI_9c6Mg96spYMnEeOim5GraIudD6ArSvI40YEfGyu00Eo_mZXRoOPrIZUibVj14NPdz6DOccc0thB-8mDRWXpV-if5_jJLCJmNWKomdEEPMf4IT-NNXTVhABAPLN');
        // if (! empty($appWeixinConfig['access_token'])) {
        // $weixin->setAccessToken($appWeixinConfig['access_token']);
        // }
        return $weixin;
    }

    /**
     * 获取签名
     *
     * @param string $card_id            
     * @param string $code            
     * @param string $openid            
     * @param number $outer_id            
     * @param number $balance            
     * @return array
     */
    protected function getSignature($card_id, $code, $openid, $outer_id = 0, $balance = 0)
    {
        // api_ticket、timestamp、card_id、code、openid、balance
        $appWeixinConfig = $this->getWeixinConfig();
        // $api_ticket = $appWeixinConfig['wx_card_api_ticket'];
        $api_ticket = '';

        $timestamp = (string) time();
        $outer_id = (string) $outer_id;
        $balance = (string) $balance;

        $objSignature = new \Weixin\Model\Signature();
        $objSignature->add_data($api_ticket);
        $objSignature->add_data($timestamp);
        $objSignature->add_data($card_id);
        $objSignature->add_data($code);
        $objSignature->add_data($openid);
        if (!empty($balance)) {
            $objSignature->add_data($balance);
        }
        if (!empty($outer_id)) {
            $objSignature->add_data($outer_id);
        }

        $signature = $objSignature->get_signature();
        $card_ext = array(
            "code" => $code,
            "openid" => $openid,
            "timestamp" => $timestamp,
            "signature" => $signature
        );
        if (!empty($outer_id)) {
            $card_ext["outer_id"] = $outer_id;
        }
        if (!empty($balance)) {
            $card_ext["balance"] = $balance;
        }
        return array(
            'signature' => $signature,
            'timestamp' => $timestamp,
            'card_ext' => json_encode($card_ext)
        );
    }

    protected function adjustDataTime4Show($value)
    {
        if ($value instanceof \MongoDate || $value instanceof \MongoTimestamp) {
            $value = date("Y-m-d H:i:s", $value->sec);
        }
        if ($value == '0000-00-00 00:00:00' || $value == '0001-01-01 00:00:00') {
            $value = "";
        }
        return $value;
    }
}
