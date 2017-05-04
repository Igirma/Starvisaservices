<?php

class users_model extends model
{
    function fetch_rights()
    {
        $sql = 'SELECT * FROM `rights`';
        $data = $this->db->query($sql);
        
        return $data;
    }
    
    function get_modules_by_rights_id($rights_id)
    {
        $sql = 'SELECT * FROM `rights_module` WHERE `rights_module`.rights_id = ? ORDER BY `rights_module`.module_id ASC';
        return $this->db->query($sql, array(
            $rights_id
        ));
    }
    
    function get_module_by_module_id($module_id)
    {
        $sql = 'SELECT * FROM `module` WHERE `module`.module_id = ?';
        $data = $this->db->query($sql, array(
            $module_id
        ));
        
        if (!isset($data[0]))
            return false;
        
        return $data[0];
    }
    
    function get_permissions_by_rights_module_id($rights_module_id)
    {
        $sql = 'SELECT * FROM `permission` WHERE `permission`.rights_module_id = ?';
        $data = $this->db->query($sql, array(
            $rights_module_id
        ));
        
        if (!isset($data[0]))
            return false;
        
        return $data[0];
    }
    
    function fetch_all()
    {
        $sql = 'SELECT * FROM `user`';
        $data['users'] = $this->db->query($sql);
        
        foreach($data['users'] as $k => $v) {
            $sql = 'SELECT * FROM `rights` WHERE `rights`.rights_id = ?';
            $result = $this->db->query($sql, array(
                $data['users'][$k]['rights_id']
            ));
            $data['users'][$k]['rights'] = $result[0];
            
            $sql = 'SELECT * FROM `user_language` WHERE `user_language`.user_id = ?';
            $data['users'][$k]['languages'] = $this->db->query($sql, array(
                $data['users'][$k]['user_id']
            ));
        }
        
        return $data;
    }
    
    function update_overview($post)
    {
        foreach($post['active'] as $k => $v) {
            $this->db->query('UPDATE `user` SET `user`.active = ? WHERE `user`.user_id = ?', array(
                $v,
                $k
            ));
        }
    }
    
    function fetch($user_id)
    {
        $sql = 'SELECT * FROM `user` WHERE `user`.user_id = ?';
        $data = $this->db->query($sql, array(
            $user_id
        ));
        
        foreach($data as $k => $v) {
            $sql = 'SELECT * FROM `user_language` WHERE `user_language`.user_id = ?';
            $data[$k]['languages'] = $this->db->query($sql, array(
                $data[$k]['user_id']
            ));
        }
        
        if (!isset($data[0]))
            return false;
        
        return $data[0];
    }
    
    function add($post)
    {
        $sql = '
        INSERT INTO `user`
        (
          rights_id,
          username,
          email,
          password,
          firstname,
          infix,
          lastname		
        )
        VALUES
        (
          :rights_id,
          :username,
          :email,
          :password,
          :firstname,
          :infix,
          :lastname
        )
        ';
        
        $this->db->query($sql, array(
            'rights_id' => $post['rights_id'],
            'username' => strtolower($post['username']),
            'email' => $post['email'],
            'password' => sha1($post['password']),
            'firstname' => ucfirst($post['firstname']),
            'infix' => $post['infix'],
            'lastname' => ucfirst($post['lastname'])
        ));
    }
    
    function edit($post)
    {
        
        $user_id = $post['user_id'];
        $password = $post['password'];
        $password_check = $post['password_check'];
        
        if($password == $password_check && str_replace(" ", "", $password_check) != '') {
            $sql = '
            UPDATE `user`
            SET
              `user`.password = :password
            WHERE `user`.user_id = :user_id
            ';
            
            $this->db->query($sql, array(
                'password' => sha1($post['password_check']),
                'user_id' => $post['user_id']
            ));
        }
        
        $sql = '
        UPDATE `user`
        SET
          `user`.rights_id = :rights_id,
          `user`.username = :username,
          `user`.email = :email,
          `user`.firstname = :firstname,
          `user`.infix = :infix,
          `user`.lastname = :lastname
        WHERE `user`.user_id = :user_id
        ';
        
        $this->db->query($sql, array(
            'rights_id' => $post['rights_id'],
            'username' => strtolower($post['username']),
            'email' => $post['email'],
            'firstname' => ucfirst($post['firstname']),
            'infix' => $post['infix'],
            'lastname' => ucfirst($post['lastname']),
            'user_id' => $post['user_id']
        ));
        
    }
    
    function delete($id)
    {
        $sql = 'DELETE FROM `user` WHERE `user`.user_id = ?';
        $this->db->query($sql, array(
            $id
        ));
        $sql = 'DELETE FROM `user_language` WHERE `user_language`.user_id = ?';
        $this->db->query($sql, array(
            $id
        ));
    }
    
    function set_permission($user_id, $language_id, $permission)
    {
        if($permission == 'on') {
            $sql = 'INSERT INTO `user_language` (user_id, language_id) VALUES (:user_id, :language_id)';
            $this->db->query($sql, array(
                'user_id' => $user_id,
                'language_id' => $language_id
            ));
        } elseif($permission == 'off') {
            $sql = 'DELETE FROM `user_language` WHERE `user_language`.user_id = :user_id AND `user_language`.language_id = :language_id';
            $this->db->query($sql, array(
                'user_id' => $user_id,
                'language_id' => $language_id
            ));
        }
    }
    
}

?>