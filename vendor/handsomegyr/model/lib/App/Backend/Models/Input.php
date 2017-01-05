<?php
namespace App\Backend\Models;

class Input extends \stdClass
{

    protected $filter = NULL;

    protected $schemas = array();

    /**
     * 过滤信息
     */
    public function getFilter()
    {
        if (empty($this->filter)) {
            $schemas = $this->getSchemas();
            $this->filter = array();
            if (! empty($schemas)) {
                foreach ($schemas as $key => $field) {
                    $this->filter[$key] = urldecode(trim($this->$key));
                }
            }
            
            $this->filter['sort_by'] = trim($this->sort_by);
            $this->filter['sort_order'] = trim($this->sort_order);
            /* 分页大小 */
            // 每页显示数量
            if (isset($this->page_size) && intval($this->page_size) > 0) {
                $this->filter['page_size'] = intval($this->page_size);
            } else {
                $this->filter['page_size'] = 10;
            }
            
            // 当前页数
            $this->filter['page'] = (empty($this->page) || intval($this->page) <= 0) ? 1 : intval($this->page);
            
            // offset
            $this->filter['start'] = ($this->filter['page'] - 1) * $this->filter['page_size'];
        }
        return $this->filter;
    }

    public function setRecordCount($record_count)
    {
        /* page 总数 */
        $this->filter['record_count'] = $record_count;
        $this->filter['page_count'] = (! empty($this->filter['record_count'])) ? ceil($this->filter['record_count'] / $this->filter['page_size']) : 1;
        
        /* 边界处理 */
        if ($this->filter['page'] > $this->filter['page_count']) {
            $this->filter['page'] = $this->filter['page_count'];
        }
        // setcookie('backend[lastfilter]', urlencode(serialize($this->filter)), time() + 600);
    }

    public function getSchemas()
    {
        return $this->schemas;
    }

    public function addSchema($key, array $field)
    {
        $this->schemas[$key] = $field;
    }

    function __call($method, $args)
    {
        if (isset($this->$method) && is_callable($this->$method))
            return call_user_func_array($this->$method, $args);
        else
            throw new \Exception("{$method} is not set or callable", - 1);
    }

    /**
     * 根据画面条件获取查询条件
     *
     * @return array
     */
    public function getQuery()
    {
        $filter = $this->getFilter();
        $schemas = $this->getSchemas();
        
        $where = array();
        if (! empty($schemas)) {
            foreach ($schemas as $key => $field) {
                if (isset($filter[$key])) {
                    if (strlen($filter[$key]) > 0) {
                        if ($field['data']['type'] == "string" && $key != '_id') {
                            $where[$key] = new \MongoRegex('/' . urldecode($filter[$key]) . '/i');
                        } elseif ($field['data']['type'] == "datetime") {
                            $datetime = urldecode($filter[$key]);
                            $datatimeArr = explode('|', $datetime);
                            if (! empty($datatimeArr[0])) {
                                $where[$key]['$gte'] = getCurrentTime(strtotime($datatimeArr[0]));
                            }
                            if (! empty($datatimeArr[1])) {
                                $where[$key]['$lte'] = getCurrentTime(strtotime($datatimeArr[1]));
                            }
                        } else {
                            $where[$key] = urldecode($filter[$key]);
                        }
                    }
                }
            }
        }
        return $where;
    }

    public function getSort()
    {
        $filter = $this->getFilter();
        // 排序方式
        $sort = array();
        $sort[$filter['sort_by']] = ('desc' == strtolower($filter['sort_order'])) ? - 1 : 1;
        return $sort;
    }

    public function getOffset()
    {
        $filter = $this->getFilter();
        return $filter['start'];
    }

    public function getLimit()
    {
        $filter = $this->getFilter();
        return $filter['page_size'];
    }

    public function getFormData($is_update = true)
    {
        $schemas = $this->getSchemas();
        $data = array();
        if (! empty($schemas)) {
            foreach ($schemas as $key => $field) {
                if ($field['data']['type'] == "string") {
                    
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = urldecode(trim($this->$key));
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : "";
                    }
                } elseif ($field['data']['type'] == "integer") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = intval($this->$key);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : 0;
                    }
                } elseif ($field['data']['type'] == "float") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = floatval($this->$key);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : 0.0;
                    }
                } elseif ($field['data']['type'] == "decimal") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = doubleval($this->$key);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : 0.00;
                    }
                } elseif ($field['data']['type'] == "datetime") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = getCurrentTime(strtotime($this->$key));
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : getCurrentTime();
                    }
                } elseif ($field['data']['type'] == "boolean") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = empty($this->$key) ? false : true;
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : false;
                    }
                } elseif ($field['data']['type'] == "json") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = json_decode(urldecode(trim($this->$key)), true);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : array();
                    }
                } elseif ($field['data']['type'] == "array") {
                    if (isset($this->$key) && $is_update) {
                        if (is_array($this->$key)) {
                            $data[$key] = $this->$key;
                        } else {
                            throw new \ErrorException("{$key} is not array", - 99);
                        }
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : array();
                    }
                } elseif ($field['data']['type'] == "html") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = trim($this->$key);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : '';
                    }
                } elseif ($field['data']['type'] == "file") {
                    unset($data[$key]);
                    if (isset($this->$key)) {
                        $data[$key] = trim($this->$key);
                    }
                } else {
                    $data[$key] = "";
                }
            }
        }
        
        if ($is_update) {
            unset($data['_id']);
        } else {
            $data['_id'] = "";
        }
        return $data;
    }
}