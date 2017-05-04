<?php

class translations_model extends model
{
    
    function add($post)
    {
        
    }
    function edit($post)
    {
        
    }
    
    function fetch_all($parent_id = 0)
    {
        $data = $this->db->query('SELECT * FROM translation ORDER BY translation_id ASC');
        
        if (empty($data) && !count($data)) {
            return false;
        }
        
        $return = array();
        
        foreach ($data as $translation) {
            $return[] = $translation;
        }
        return $return;
    }
}

?>