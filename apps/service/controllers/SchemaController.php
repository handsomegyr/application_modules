<?php
namespace App\Service\Controllers;

use \Phalcon\Db\Column;

class SchemaController extends ControllerBase
{

    private $typeInfo = array(
        /**
         * Integer abstract type
         */
        Column::TYPE_INTEGER => 'Integer',
        
        /**
         * Date abstract type
         */
        Column::TYPE_DATE => 'Date',
        
        /**
         * Varchar abstract type
         */
        Column::TYPE_VARCHAR => 'Varchar',
        
        /**
         * Decimal abstract type
         */
        Column::TYPE_DECIMAL => 'Decimal',
        
        /**
         * Datetime abstract type
         */
        Column::TYPE_DATETIME => 'Datetime',
        
        /**
         * Char abstract type
         */
        Column::TYPE_CHAR => 'Char',
        
        /**
         * Text abstract data type
         */
        Column::TYPE_TEXT => 'Text',
        
        /**
         * Float abstract data type
         */
        Column::TYPE_FLOAT => 'Float',
        
        /**
         * Boolean abstract data type
         */
        Column::TYPE_BOOLEAN => 'Boolean',
        
        /**
         * Double abstract data type
         */
        Column::TYPE_DOUBLE => 'Double',
        
        /**
         * Tinyblob abstract data type
         */
        Column::TYPE_TINYBLOB => 'Tinyblob',
        
        /**
         * Blob abstract data type
         */
        Column::TYPE_BLOB => 'Blob',
        
        /**
         * Mediumblob abstract data type
         */
        Column::TYPE_MEDIUMBLOB => 'Mediumblob',
        
        /**
         * Longblob abstract data type
         */
        Column::TYPE_LONGBLOB => 'Longblob',
        
        /**
         * Big integer abstract type
         */
        Column::TYPE_BIGINTEGER => 'Big integer',
        
        /**
         * Json abstract type
         */
        Column::TYPE_JSON => 'Json',
        
        /**
         * Jsonb abstract type
         */
        Column::TYPE_JSONB => 'Jsonb'
    );

    private $bindInfo = array(
        /**
         * Bind Type Null
         */
        Column::BIND_PARAM_NULL => 'Null',
        
        /**
         * Bind Type Integer
         */
        Column::BIND_PARAM_INT => 'Integer',
        
        /**
         * Bind Type String
         */
        Column::BIND_PARAM_STR => 'String',
        
        /**
         * Bind Type Blob
         */
        Column::BIND_PARAM_BLOB => 'Blob',
        
        /**
         * Bind Type Bool
         */
        Column::BIND_PARAM_BOOL => 'Bool',
        
        /**
         * Bind Type Decimal
         */
        Column::BIND_PARAM_DECIMAL => 'Decimal'
    );

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 提供生成schema数组的服务
     */
    public function createAction()
    {
        // http://www.jizigou.com/service/schema/create?tablename=itencent_application
        // http://phalconM4local/service/schema/create?tablename=ishop4b2c_member
        $tablename = $this->request->get('tablename', array(
            'trim'
        ), '');
        
        $di = $this->getDI();
        $connection = $di['db'];
        $connection->execute("SET NAMES 'utf8mb4';");
        $tableColumns = $connection->describeColumns($tablename);
        
        $fieldInfoList = array();
        foreach ($tableColumns as $key => $column) {
            
            $fieldInfo = array();
            $fieldInfo['name'] = $column->getName();
            if (in_array($fieldInfo['name'], array(
                '_id',
                '__CREATE_TIME__',
                '__MODIFY_TIME__',
                '__REMOVED__'
            ))) {
                continue;
            }
            if (substr($fieldInfo['name'], 0, 6) == 'shopnc') {
                continue;
            }
            // die($connection->getColumnDefinition($column));
            if (in_array($column->getType(), array(
                Column::TYPE_VARCHAR,
                Column::TYPE_CHAR,
                Column::TYPE_TEXT
            ))) {
                $fieldInfo['type'] = "string";
            } else {
                $fieldInfo['type'] = isset($this->typeInfo[$column->getType()]) ? $this->typeInfo[$column->getType()] : "";
            }
            $fieldInfo['type'] = strtolower($fieldInfo['type']);
            
            $fieldInfo['size'] = $column->getSize();
            $fieldInfo['default_value'] = $column->getDefault();
            $fieldInfo['isUnsigned'] = $column->isUnsigned();
            $fieldInfo['isNotNull'] = $column->isNotNull();
            $fieldInfo['isPrimary'] = $column->isPrimary();
            $fieldInfo['isNumeric'] = $column->isNumeric();
            $fieldInfo['bindType'] = isset($this->bindInfo[$column->getBindType()]) ? $this->bindInfo[$column->getBindType()] : "";
            $fieldInfo['scale'] = $column->getScale();
            $fieldInfo['typeValues'] = $column->getTypeValues();
            $isBoolean = ($fieldInfo['type'] == 'integer') && ! empty($fieldInfo['isUnsigned']) && ! empty($fieldInfo['isNumeric']);
            $required = ! empty($fieldInfo['isNotNull']) ? 1 : 0;
            
            if ($column->getType() == Column::TYPE_INTEGER) {
                $input_type = "number";
            } elseif ($column->getType() == Column::TYPE_DECIMAL) {
                $input_type = "currency";
            } elseif ($column->getType() == Column::TYPE_FLOAT) {
                $input_type = "decimal";
            } elseif ($column->getType() == Column::TYPE_DATETIME) {
                $input_type = "datetimepicker";
            } else {
                $input_type = 'text';
            }
            
            $str = <<<EOD
schemas['{$fieldInfo['name']}'] = array(
    'name' => '{$fieldInfo['name']}',
    'data' => array(
        'type' => '{$fieldInfo['type']}',
        'length' => {$fieldInfo['size']}
    ),
    'validation' => array(
        'required' => $required
    ),
    'form' => array(
        'input_type' => '{$input_type}',
        'is_show' => true
    ),
    'list' => array(
        'is_show' => true
    ),
    'search' => array(
        'is_show' => false
    )
);
EOD;
            
            echo '$' . $str . "<br/>";
        }
        echo "<br/>";
        
        return;
    }
}

