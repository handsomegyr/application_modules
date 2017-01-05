<?php
namespace App\Backend\Models;

use App\Common\Models\System\Role;

class User extends \App\Common\Models\System\User
{

    /**
     * 是否已经注册过
     *
     * @param string $username            
     * @return boolean
     */
    public function isRegisted($username)
    {
        $num = $this->count(array(
            "username" => $username
        ));
        return ($num > 0);
    }

    /**
     * 根据用户username获得用户信息
     *
     * @param string $username            
     * @return array
     */
    public function getUserByUsername($username)
    {
        $result = $this->findOne(array(
            "username" => $username
        ));
        return $result;
    }

    /**
     * 登陆处理
     *
     * @param array $user            
     * @return array
     */
    public function login(array $user)
    {
        $query = array(
            '_id' => $user['_id']
        );
        $data = array(
            '$set' => array(
                'lastip' => getIp(),
                'lasttime' => getCurrentTime()
            ),
            '$inc' => array(
                'times' => 1
            )
        );
        $this->update($query, $data);
        
        // 获取角色信息
        if (! empty($user['role'])) {
            $modelRole = new Role();
            $roleInfo = $modelRole->getInfoById($user['role']);
            $_SESSION['roleInfo'] = $roleInfo;
        } else {
            $_SESSION['roleInfo'] = array();
        }
        
        $_SESSION['admin_id'] = $user['_id'];
        $_SESSION['admin_name'] = $user['username'];
        
        return $user;
    }

    /**
     * 注册处理
     *
     * @param string $username            
     * @param string $password            
     * @return array
     */
    public function registUser($username, $password)
    {
        $userData = array();
        $userData['username'] = $username;
        $userData['password'] = $password;
        $userData['lastip'] = getIp();
        $userData['lasttime'] = getCurrentTime();
        $userData['times'] = 1;
        $userData = $this->insert($userData);
        return $userData;
    }

    /**
     * 处理用户登录和注册
     *
     * @param string $username            
     * @param string $password            
     * @return array
     */
    public function handle($username, $password)
    {
        // 用户数据登陆
        $userinfo = $this->getInfoByUsername($username);
        if (empty($userinfo)) {
            // 注册用户
            return $this->registUser($username, $password);
        } else {
            // 登录处理
            return $this->login($userinfo);
        }
    }

    /**
     * 检查用户有效性
     *
     * @param string $username            
     * @param string $password            
     * @throws Exception
     * @return array
     */
    public function checkLogin($username, $password)
    {
        /* 检查密码是否正确 */
        $query = array();
        $query['username'] = $username;
        $query['password'] = ($password);
        $userInfo = $this->findOne($query);
        if (empty($userInfo)) {
            throw new \Exception("用户名或密码有误");
        }
        return $userInfo;
    }

    /**
     * 存入COOKIES
     *
     * @param array $userInfo            
     */
    public function storeInCookies(array $userInfo)
    {
        $time = time() + 3600 * 24 * 365;
        setcookie('backend[admin_id]', $userInfo['_id'], $time, "/");
        setcookie('backend[admin_pass]', md5($userInfo['password']), $time, "/");
    }

    /**
     * 清空COOKIES
     */
    public function clearCookies()
    {
        /* 清除cookie */
        setcookie('backend[admin_id]', '', time() - 3600, "/");
        setcookie('backend[admin_pass]', '', time() - 3600, "/");
    }
}
