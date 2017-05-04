<?php

class rights_model extends model
{
    function add_right($post)
    {
        $this->db->query('INSERT INTO `rights` (`rights`.name) VALUES (?)', array($post['name']));

        $rights_id = $this->db->last_insert_id;
        
        $modules = $this->db->query('SELECT * FROM `module`');
        
        foreach($modules as $module) {
            $this->db->query('INSERT INTO `rights_module` (`rights_module`.rights_id, `rights_module`.module_id) VALUES (?, ?)', array($rights_id, $module['module_id']));

            $rights_module_id = $this->db->last_insert_id;

            $this->db->query('INSERT INTO `permission` (`permission`.rights_module_id) VALUES (?)', array($rights_module_id));
        }
    }
    
    function fetch_rights()
    {
        $rights = $this->db->query('SELECT * FROM `rights`');
        
        if (empty($rights) && !count($rights)) {
            return false;
        }
        foreach($rights as $k => $v) {
            $rights[$k]['users'] = $this->db->query('SELECT * FROM `user` WHERE `user`.rights_id = ?', array($v['rights_id']));
        }
        return $rights;
      }
}

?>