<?php

namespace App\Install\Controllers;

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

class IndexController extends ControllerBase
{
    private $lockfile = APP_PATH . 'apps/install/config/lock';

    public function initialize()
    {
        parent::initialize();
    }

    public function indexAction()
    {

        if (file_exists($this->lockfile)) {
            die($this->lang('install_locked'));
        }

        // 数据库默认配置
        $this->assign('dbhost', '127.0.0.1');
        $this->assign('dbname', 'test1');
        $this->assign('dbuser', 'root');
        $this->assign('dbpw', 'guotingyu0324');

        // 获取环境信息
        $this->getEnvInfo2();

        // 数据库sql
        $dirfile_items = array();
        $key = 0;
        $dirList = $this->traverseDir(APP_PATH . 'apps/backend/submodules');
        if (!empty($dirList)) {
            foreach ($dirList as $dir) {
                $sqlfile = $dir . '/config/install/schema.sql';
                $dirfile_items[$key]['file'] = $sqlfile;
                if (file_exists($sqlfile)) {
                    if (is_writable($sqlfile)) {
                        $dirfile_items[$key]['status'] = 1;
                        $dirfile_items[$key]['current'] = '+r+w';
                    } else {
                        $dirfile_items[$key]['status'] = 0;
                        $dirfile_items[$key]['current'] = '+r';
                    }
                } else {
                    if ($this->dir_writeable(dirname($sqlfile))) {
                        $dirfile_items[$key]['status'] = 1;
                        $dirfile_items[$key]['current'] = '+r+w';
                    } else {
                        $dirfile_items[$key]['status'] = -1;
                        $dirfile_items[$key]['current'] = 'nofile';
                    }
                }
                $key++;
            }
        }
        $this->assign('dirfile_items', $dirfile_items);
    }

    public function buildAction()
    {
        $this->view->disable();

        try {
            $dbhost = $this->get('dbhost', '');
            if (empty($dbhost)) {
                echo $this->error(-1, '数据库服务器地址为空');
                return false;
            }
            $dbname = $this->get('dbname', '');
            if (empty($dbhost)) {
                echo $this->error(-2, '数据库名为空');
                return false;
            }
            $dbuser = $this->get('dbuser', '');
            if (empty($dbhost)) {
                echo $this->error(-3, '数据库用户名为空');
                return false;
            }
            $dbpw = $this->get('dbpw', '');
            if (empty($dbhost)) {
                echo $this->error(-4, '数据库密码为空');
                return false;
            }
            ///sleep(10);            
            if (file_exists($this->lockfile)) {
                echo $this->error(-5, $this->lang('install_locked'));
                return false;
            }
            touch($this->lockfile);

            // 创建数据库 构建表结构和数据
            $this->doMysql2($dbhost, $dbname, $dbuser, $dbpw);

            echo $this->result('OK');
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    protected function dir_writeable($dir)
    {
        $writeable = 0;
        if (!is_dir($dir)) {
            @mkdir($dir, 0777);
        }
        if (is_dir($dir)) {
            if ($fp = @fopen("$dir/test.txt", 'w')) {
                @fclose($fp);
                @unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        return $writeable;
    }

    protected function doMysql2($dbhost, $dbname, $dbuser, $dbpw)
    {
        $connection = new DbAdapter(array(
            "host" => $dbhost,
            "username" => $dbuser,
            "password" => $dbpw,
            //"dbname" => $dbname,
            "charset" => DBCHARSET,
            "collation" => DBCOLLATION,
            'options'  => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DBCHARSET . " COLLATE " . DBCOLLATION . ";",
                //\PDO::ATTR_CASE => PDO::CASE_LOWER,
            ],
        ));
        $dirList = $this->traverseDir(APP_PATH . 'apps/backend/submodules');
        if (!empty($dirList)) {
            foreach ($dirList as $dir) {
                $sqlfile = $dir . '/config/install/schema.sql';
                if (file_exists($sqlfile)) {
                    $sql = file_get_contents($sqlfile);
                    $sql = str_replace("webcms", $dbname, $sql);
                    $this->doSql($connection, $sql);
                }
            }
        }
    }

    protected function doSql($connection, $sql)
    {
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
        foreach ($ret as $query) {
            $connection->execute($query);
        }
    }

    protected function traverseDir($dir)
    {
        $dirList = array();
        if ($dir_handle = @opendir($dir)) {
            while ($filename = readdir($dir_handle)) {
                if ($filename != "." && $filename != "..") {
                    $subFile = $dir . DIRECTORY_SEPARATOR . $filename; // 要将源目录及子文件相连
                    if (is_dir($subFile)) { // 若子文件是个目录
                        $dirList[] = $subFile;
                    }
                }
            }
            closedir($dir_handle);
        }

        return $dirList;
    }

    protected function getEnvInfo2()
    {
        $env_items = array(
            'os' => array(
                'c' => 'PHP_OS',
                'r' => 'notset',
                'b' => 'unix'
            ),
            'php' => array(
                'c' => 'PHP_VERSION',
                'r' => '5.1',
                'b' => '5.3'
            ),
            'attachmentupload' => array(
                'r' => 'notset',
                'b' => '2M'
            ),
            'gdversion' => array(
                'r' => '1.0',
                'b' => '2.0'
            ),
            'diskspace' => array(
                'r' => '10M',
                'b' => 'notset'
            )
        );

        foreach ($env_items as $key => $item) {
            if ($key == 'php') {
                $env_items[$key]['current'] = PHP_VERSION;
            } elseif ($key == 'attachmentupload') {
                $env_items[$key]['current'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';
            } elseif ($key == 'gdversion') {
                $tmp = function_exists('gd_info') ? gd_info() : array();
                $env_items[$key]['current'] = empty($tmp['GD Version']) ? 'noext' : $tmp['GD Version'];
                unset($tmp);
            } elseif ($key == 'diskspace') {
                if (function_exists('disk_free_space')) {
                    $env_items[$key]['current'] = floor(disk_free_space(APP_PATH) / (1024 * 1024)) . 'M';
                } else {
                    $env_items[$key]['current'] = 'unknow';
                }
            } elseif (isset($item['c'])) {
                $env_items[$key]['current'] = constant($item['c']);
            }

            $env_items[$key]['status'] = 1;
            if ($item['r'] != 'notset' && strcmp($env_items[$key]['current'], $item['r']) < 0) {
                $env_items[$key]['status'] = 0;
            }
        }

        $error_code = 0;
        foreach ($env_items as $key => $item) {
            $status = 1;
            if ($item['r'] != 'notset') {
                if (intval($item['current']) && intval($item['r'])) {
                    if (intval($item['current']) < intval($item['r'])) {
                        $status = 0;
                        $error_code = 1;
                    }
                } else {
                    if (strcmp($item['current'], $item['r']) < 0) {
                        $status = 0;
                        $error_code = 1;
                    }
                }
            }
            $env_items[$key]['check_status'] = $status;
        }

        $this->assign('env_items_check_result', $error_code);
        $this->assign('env_items', $env_items);
    }

    protected function lang($lang_key, $force = true)
    {
        $di = \Phalcon\DI::getDefault();
        $install_config = $di->get('install_config');
        $lang = isset($install_config['lang']) ? $install_config['lang'] : array();
        return isset($lang[$lang_key]) ? $lang[$lang_key] : ($force ? $lang_key : '');
    }
}
