<?php

/**
 * Programmatically bootstrap the database
 *
 */
use \Phalcon\Db\Column, Phalcon\Db\Index, Phalcon\Db\Reference;

// $config = new Phalcon\Config\Adapter\Ini(__DIR__ . '/../apps/backend/config/config.php');
$config = include __DIR__ . '/../apps/backend/config/config.php';
$dbclass = sprintf('\Phalcon\Db\Adapter\Pdo\%s', $config->database->adapter);
$connection = new $dbclass(array(
    "host" => $config->database->host,
    "username" => $config->database->username,
    "password" => $config->database->password,
    "dbname" => $config->database->dbname
));
$connection->execute("SET NAMES 'utf8mb4';");

try {
    // $connection->begin();
    
    $tableColumns = ($connection->describeColumns("ilottery_activity"));
    // print_r($tableColumns);
    $fieldInfoList = array();
    $typeInfo = array(
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
    
    $bindInfo = array(
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
        die($connection->getColumnDefinition($column));
        $fieldInfo['type'] = isset($typeInfo[$column->getType()]) ? $typeInfo[$column->getType()] : "";
        $fieldInfo['size'] = $column->getSize();
        $fieldInfo['default_value'] = $column->getDefault();
        $fieldInfo['isUnsigned'] = $column->isUnsigned();
        $fieldInfo['isNotNull'] = $column->isNotNull();
        $fieldInfo['isPrimary'] = $column->isPrimary();
        $fieldInfo['isNumeric'] = $column->isNumeric();
        $fieldInfo['bindType'] = isset($bindInfo[$column->getBindType()]) ? $bindInfo[$column->getBindType()] : "";
        $fieldInfo['scale'] = $column->getScale();
        $fieldInfo['typeValues'] = $column->getTypeValues();
        $isBoolean = ($fieldInfo['type'] == 'Integer') && ! empty($fieldInfo['isUnsigned']) && ! empty($fieldInfo['isNumeric']);
        $required = ! empty($fieldInfo['isNotNull']) ? 1 : 0;
        
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
        'input_type' => 'text',
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
        
        echo '$' . $str . "\n";
    }
    die('');
    // $connection->commit();
} catch (Exception $e) {
    // $connection->rollback();
    echo $e->getTraceAsString();
}
