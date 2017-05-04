<?php

class colors_model extends model
{
    function fetch_all()
    {
        $sql = '
          SELECT * FROM `colors`
          INNER JOIN `colors_content`
          ON `colors`.colors_id = `colors_content`.colors_id
          WHERE `colors_content`.language_id = ?
          ORDER BY `colors`.colors_id ASC
        ';
        
        $data['colors'] = $this->db->query($sql, array(
            $this->config->item('default_language')
        ));
        return $data;
    }
    
    function fetch($colors_id, $language_id)
    {
        $sql = '
          SELECT * FROM `colors`, `colors_content`
          WHERE `colors`.colors_id = `colors_content`.colors_id
          AND `colors`.colors_id = ?
          AND `colors_content`.language_id = ?
        ';

        $r = $this->db->query($sql, array(
            $colors_id,
            $language_id
        ));
        if (!isset($r[0]))
            return false;

        $data['colors'] = $r[0];

        return $data;
    }
    
    function add($post)
    {
        $this->db->query('INSERT INTO `colors` ( color ) VALUES ( ? )', array(
            $post['colors']['color']
        ));
        
        $id = $this->db->last_insert_id;
        
        $sql = '
        INSERT INTO `colors_content`
        (
          colors_id,
          language_id,
          title,
          sub_active
        )
        VALUES
        (
          :colors_id,
          :language_id,
          :title,
          :sub_active
        )';
        
        $this->db->query($sql, array(
            'colors_id' => $id,
            'language_id' => $this->config->item('default_language'),
            'title' => $post['colors']['title'],
            'sub_active' => $post['colors']['sub_active']
        ));
        
        $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array(
            $this->config->item('default_language')
        ));

        if (empty($languages) && !count($languages))
            return $id;

        foreach ($languages as $language) {
            $sql = '
            INSERT INTO `colors_content`
            (
              colors_id,
              language_id,
              sub_active
            )
            VALUES
            (
              :colors_id,
              :language_id,
              :sub_active
            )';
            
            $this->db->query($sql, array(
                'colors_id' => $id,
                'language_id' => $language['language_id'],
                'sub_active' => 0
            ));
        }

        return $id;
    }
    
    function edit($post, $id, $language_id)
    {
        $sql = '
        UPDATE `colors`, `colors_content`
        SET
          `colors_content`.title = :title,
          `colors`.color = :color,
          `colors_content`.sub_active = :sub_active
        WHERE `colors`.colors_id = :colors_id
        AND `colors_content`.colors_id = :colors_id
        AND `colors_content`.language_id = :language_id
        ';

        $this->db->query($sql, array(
            'title' => $post['colors']['title'],
            'color' => $post['colors']['color'],
            'colors_id' => $id,
            'language_id' => $language_id,
            'sub_active' => $post['colors']['sub_active']
        ));
    }
    
    function update_overview($post)
    {
        foreach ($post['active'] as $k => $v) {
            $this->db->query('UPDATE `colors` SET `colors`.active = ? WHERE `colors`.colors_id = ? LIMIT 1', array($v, $k));
        }
        return true;
    }

    function delete($id)
    {
        $this->db->query('DELETE FROM `colors` WHERE `colors`.colors_id = ?', array($id));
        $this->db->query('DELETE FROM `colors_content` WHERE `colors_content`.colors_id = ?', array($id));
        return true;
    }

}

?>