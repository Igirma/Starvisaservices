<?php

class module_model extends model
{
    function add_module($post, $rights)
    {
        $sql = 'INSERT INTO `module` (name, dirname) VALUES (:name, :dirname)';
        $this->db->query($sql, array(
            'name' => $post['name'],
            'dirname' => $post['dirname']
        ));
        
        $module_id = $this->db->last_insert_id;
        
        foreach($rights as $right) {
            $sql = 'INSERT INTO `rights_module` (rights_id, module_id) VALUES (:rights_id, :module_id)';
            $this->db->query($sql, array(
                'rights_id' => $right['rights_id'],
                'module_id' => $module_id
            ));

            $rights_module_id = $this->db->last_insert_id;

            $sql = 'INSERT INTO `permission` (rights_module_id) VALUES (:rights_module_id)';
            $this->db->query($sql, array(
                'rights_module_id' => $rights_module_id
            ));
        }
    }
    
}

?>