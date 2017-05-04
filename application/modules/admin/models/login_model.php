<?php

class login_model extends model
{
    function login($salt, $post)
    {
        $sql = '
        SELECT `user`.*, `rights`.name AS permission 
        FROM `user` 
        INNER JOIN `rights`
        ON `rights`.rights_id = `user`.rights_id 
        WHERE `user`.username = ? 
        AND `user`.password = ?
        AND `user`.active = 1
        LIMIT 1
        ';
        
        $user = $this->db->query($sql, array(
            $_POST['username'],
            sha1($_POST['password'])
        ));
        
        if($this->db->num_rows == 0) {
            return false;
        }
        
        $sql = '
        UPDATE `user` 
        SET
          `user`.login_salt = :login_salt,
          `user`.last_login = :last_login,
          `user`.ip = :ip
        WHERE `user`.user_id = :user_id
        ';

        $this->db->query($sql, array(
            'login_salt' => $salt,
            'last_login' => time(),
            'user_id' => $user[0]['user_id'],
            'ip' => $_SERVER['REMOTE_ADDR']
        ));

        $_SESSION['login_salt'] = $salt;
        $_SESSION['user_id'] = $user[0]['user_id'];
        $_SESSION['username'] = $user[0]['username'];
        $_SESSION['permission'] = $user[0]['permission'];

        return true;
    }
    
    function check_login()
    {
        if(!isset($_SESSION['login_salt'])) {
            return false;
        }
        
        $sql = '
        SELECT 
          `user`.login_salt,
          `user`.ip
        FROM `user`
        WHERE `user`.login_salt = ?
        AND `user`.ip = ?
        ';
        
        $this->db->query($sql, array(
            $_SESSION['login_salt'],
            $_SERVER['REMOTE_ADDR']
        ));
        
        if($this->db->num_rows == 0) {
            return false;
        }
        
        return true;
        
    }
}

?>