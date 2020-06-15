<?php
class DatabaseManager
{
	private $dbname;
	private $username;
	private $password;
	private $dbConnection;

	public function __construct($dbname, $username, $password)
	{
		$this->dbname = $dbname;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * 获取该项目对应的数据库连接
	 */
	public function getDbConnection4CompanyProject()
	{
		$di = \Phalcon\DI::getDefault();
		// 特殊处理
		if ($this->dbname == 'idb_manager') {
			$db = $di['db'];
		} else {
			if (empty($this->dbConnection)) {
				$config = $di->get('config');
				// 连接项目对应的数据库
				$this->dbConnection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
					"host" => $config->database->host,
					"username" => $this->username,
					"password" => $this->password,
					"dbname" => $this->dbname,
					"charset" => $config->database->charset,
					"collation" => $config->database->collation,
					'options'  => [
						\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config->database->charset} COLLATE {$config->database->collation};",
						//\PDO::ATTR_CASE => PDO::CASE_LOWER,
					],
				));
			}
			$db = $this->dbConnection;
		}
		return $db;
	}

	// 显示表
	public function showTables($tablename = '')
	{
		$connection = $this->getDbConnection4CompanyProject();
		//SHOW TABLE STATUS LIKE '{$tablename}'
		if (empty($tablename)) {
			$sql = <<<EOT
SHOW TABLE STATUS;
EOT;
		} else {
			$sql = <<<EOT
SHOW TABLE STATUS WHERE `Name` = '{$tablename}';
EOT;
		}
		$result1 = $connection->query($sql, array());
		$result1->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
		$list = $result1->fetchAll();
		return $list;
	}

	// 创建表
	public function createTable($tablename, $tablecomment)
	{
		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
CREATE TABLE IF NOT EXISTS `{$tablename}` (
	`_id` char(24) NOT NULL DEFAULT '' COMMENT '记录ID',			
	/*`memo` text NOT NULL  COMMENT '备注', */
	`__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
	`__CREATE_USER_ID__` char(24) NOT NULL DEFAULT '' COMMENT '创建操作者ID',
	`__CREATE_USER_NAME__` varchar(50) NOT NULL DEFAULT '' COMMENT '创建操作者名',
	`__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
	`__MODIFY_USER_ID__` char(24) NOT NULL DEFAULT '' COMMENT '修改操作者ID',
	`__MODIFY_USER_NAME__` varchar(50) NOT NULL DEFAULT '' COMMENT '修改操作者名',
	`__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
	`__REMOVE_TIME__` datetime DEFAULT NULL COMMENT '删除时间',
	`__REMOVE_USER_ID__` char(24) NOT NULL DEFAULT '' COMMENT '删除操作者ID',
	`__REMOVE_USER_NAME__` varchar(50) NOT NULL DEFAULT '' COMMENT '删除操作者名',
	PRIMARY KEY (`_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='{$tablecomment}';
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	// 复制表和结构和索引，但不生成记录
	public function cloneTable($tablename, $like_table)
	{
		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
CREATE TABLE IF NOT EXISTS `{$tablename}` LIKE `{$like_table}`;
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	// 修改表名 
	public function alterTableName($tablename, $new_tablename)
	{
		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
ALTER TABLE `{$tablename}` RENAME TO `{$new_tablename}`;
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	// 修改表注释
	public function alterTableComment($tablename, $tablecomment)
	{
		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
ALTER TABLE `{$tablename}` COMMENT '{$tablecomment}';
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	// 删除表
	public function dropTable($tablename)
	{
		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
DROP TABLE IF EXISTS `{$tablename}`;
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	// 显示表字段
	public function showColumns($tablename, $fieldname = '')
	{
		$connection = $this->getDbConnection4CompanyProject();
		if (empty($fieldname)) {
			$sql = <<<EOT
SHOW FULL COLUMNS FROM `{$tablename}`;
EOT;
		} else {
			$sql = <<<EOT
SHOW FULL COLUMNS FROM `{$tablename}` WHERE `Field` = '{$fieldname}';
EOT;
		}
		$result1 = $connection->query($sql, array());
		$result1->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
		$list = $result1->fetchAll();
		return $list;
	}

	// 增加表字段
	public function addColumn($tablename, $fieldname, $fieldcomment, $afterFieldName, $fieldType, $len = 255)
	{
		$sqlColumnType = $this->getSqlColumnTypeByFieldType($fieldType, $len);

		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
ALTER TABLE `{$tablename}` ADD COLUMN `{$fieldname}` {$sqlColumnType} COMMENT '{$fieldcomment}' AFTER `{$afterFieldName}`;
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	// 修改表字段
	public function changeColumn($tablename, $fieldname, $fieldcomment, $afterFieldName, $fieldType, $len = 255, $oldfieldname = "")
	{
		$sqlColumnType = $this->getSqlColumnTypeByFieldType($fieldType, $len);

		$connection = $this->getDbConnection4CompanyProject();
		if (empty($oldfieldname) || ($oldfieldname == $fieldname)) {
			$sql = <<<EOT
ALTER TABLE `{$tablename}` MODIFY COLUMN `{$fieldname}` {$sqlColumnType} COMMENT '{$fieldcomment}' AFTER `{$afterFieldName}`;
EOT;
		} else {
			$sql = <<<EOT
ALTER TABLE `{$tablename}` CHANGE COLUMN `{$oldfieldname}` `{$fieldname}` {$sqlColumnType} COMMENT '{$fieldcomment}' AFTER `{$afterFieldName}`;
EOT;
		}

		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	// 删除表字段
	public function dropColumn($tablename, $fieldname)
	{
		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
ALTER TABLE `{$tablename}` DROP COLUMN `{$fieldname}`;
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}


	// 显示表索引
	public function showIndexes($tablename, $indexName = '')
	{
		$connection = $this->getDbConnection4CompanyProject();
		if (empty($indexName)) {
			$sql = <<<EOT
SHOW INDEX FROM `{$tablename}`;
EOT;
		} else {
			$sql = <<<EOT
SHOW INDEX FROM `{$tablename}` WHERE `Key_name` = '{$indexName}';
EOT;
		}
		$result1 = $connection->query($sql, array());
		$result1->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
		$list = $result1->fetchAll();
		return $list;
	}

	// 增加表索引
	public function addIndex($tablename, $indexName, $keys, $options)
	{
		// 索引名
		$sqlIndex = $this->getSqlIndex($indexName, $keys, $options);

		// 查看有没有对应的索引
		$list = $this->showIndexes($tablename, $indexName);
		if (!empty($list)) {
			return false;
		}

		$connection = $this->getDbConnection4CompanyProject();

		$sql = <<<EOT
ALTER TABLE `{$tablename}` ADD {$sqlIndex};
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	// 删除表索引
	public function dropIndex($tablename, $indexName)
	{
		// 查看有没有对应的索引
		$list = $this->showIndexes($tablename, $indexName);
		if (empty($list)) {
			return false;
		}

		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
ALTER TABLE `{$tablename}` DROP KEY `{$indexName}`;
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	protected function getSqlIndex($indexName, array $keys, array $options = array('index_type' => 'BTREE', 'index_key_type' => ''))
	{
		// UNIQUE KEY `NewIndex1`(`user_id2`(191),`activity_id`) USING BTREE,
		// FULLTEXT KEY `NewIndex2`(`activity_id`);		 
		$sqlIndex = '';

		//UNIQUE FULLTEXT 
		if (!empty($options['index_key_type'])) {
			$sqlIndex .= " {$options['index_key_type']} KEY ";
		} else {
			$sqlIndex .= " KEY ";
		}

		// 索引名
		$sqlIndex .= "`{$indexName}`";

		// 索引字段
		$arrFields = array();
		foreach ($keys as $field => $value) {
			if (true || empty($value)) {
				$arrFields[] = "`{$field}`(191)";
			} else {
				$arrFields[] = "`{$field}`({$value})";
			}
		}
		$sqlIndex .= ("(" . implode(",", $arrFields) . ")");

		// 如果是FULLTEXT类型的索引 那么就不能有USING
		if (!empty($options['index_key_type']) && $options['index_key_type'] == 'FULLTEXT') {
			$sqlIndex .= " ";
		} else {
			if (!empty($options['index_type'])) {
				$sqlIndex .= " USING {$options['index_type']} ";
			} else {
				$sqlIndex .= " USING BTREE ";
			}
		}

		return $sqlIndex;
	}

	// 获取一条数据 
	public function getOne($tablename)
	{
		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
SELECT * FROM `{$tablename}` LIMIT 1;
EOT;
		$result1 = $connection->query($sql, array());
		$result1->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
		$info = $result1->fetch();
		return $info;
	}

	// 数据的导入
	public function importCsvData($tablename, $csvFilePath)
	{
		$connection = $this->getDbConnection4CompanyProject();
		$sql = <<<EOT
load data infile '{$csvFilePath}' into table {$tablename} fields terminated by ',';
EOT;
		$dbret1 = $connection->execute($sql, array());
		return $dbret1;
	}

	protected function getSqlColumnTypeByFieldType($fieldType, $len)
	{
		$sqlColumnType = "";
		switch ($fieldType) {
			case 'textfield':
				$sqlColumnType = "VARCHAR({$len}) NOT NULL DEFAULT ''";
				break;
			case 'textareafield':
				$sqlColumnType = "TEXT NOT NULL";
				break;
			case 'numberfield':
				$sqlColumnType = "INT(11) NOT NULL DEFAULT 0";
			case 'unsignednumberfield':
				$sqlColumnType = "INT(11) UNSIGNED NOT NULL DEFAULT 0";
				break;
			case 'boolfield':
				$sqlColumnType = "TINYINT(1) UNSIGNED NOT NULL DEFAULT 0";
				break;
			case 'arrayfield':
				$sqlColumnType = "TEXT NOT NULL";
				break;
			case 'documentfield':
				$sqlColumnType = "TEXT NOT NULL";
				break;
			case 'htmleditor':
				$sqlColumnType = "TEXT NOT NULL";
				break;
			case 'ueditor':
				$sqlColumnType = "TEXT NOT NULL";
				break;
			case 'datefield':
				$sqlColumnType = "DATETIME NOT NULL";
				break;
			case 'filefield':
				$sqlColumnType = "VARCHAR(255) NOT NULL DEFAULT ''";
				break;
			case '2dfield':
				$sqlColumnType = "TEXT NOT NULL";
				break;
			case 'md5field':
				$sqlColumnType = "VARCHAR(190) NOT NULL DEFAULT ''";
				break;
			case 'sha1field':
				$sqlColumnType = "VARCHAR(190) NOT NULL DEFAULT ''";
				break;
			default:
				$sqlColumnType = "VARCHAR(255) NOT NULL DEFAULT ''";
				break;
		}
		return $sqlColumnType;
	}
}
